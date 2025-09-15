<?php
/*
Plugin Name: OTP Login With Phone Number, OTP Verification
Plugin URI: https://idehweb.com/product/login-with-phone-number-in-wordpress/
Description: Login with phone number - sending sms - activate user by phone number - limit pages to login - register and login with ajax - modal
Version: 1.8.55
Author: Hamid Alinia - idehweb
Author URI: https://idehweb.com/
Text Domain: login-with-phone-number
Domain Path: /languages
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

if (!defined("ABSPATH"))
    exit;



require_once plugin_dir_path(__FILE__) . 'inc/admin-functions.php';
require_once plugin_dir_path(__FILE__) . 'inc/frontend-functions.php';
require_once plugin_dir_path(__FILE__) . 'inc/ajax-handlers.php';
require_once plugin_dir_path(__FILE__) . 'inc/helper-functions.php';
require_once plugin_dir_path(__FILE__) . 'gateways/class-lwp-custom-api.php';

class idehwebLwp
{

    use Admin_Functions;
    use Frontend_Functions;
    use Ajax_Handlers;
    use Helper_Functions;

    function __construct()
    {
        add_action('init', array($this, 'idehweb_lwp_textdomain'));
        add_action('admin_init', array($this, 'admin_init'));
        add_action('admin_menu', array($this, 'admin_menu'));
        add_action('admin_footer', array($this, 'admin_footer'));
        add_action('admin_notices', array($this, 'check_sms_gateway_configuration_notice'));
        add_action('admin_enqueue_scripts', array($this, 'lwp_load_wp_media_files'));
        add_action('wp_ajax_lwp_media_get_image', array($this, 'lwp_media_get_image'));

        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('show_user_profile', array($this, 'lwp_add_phonenumber_field'));
        add_action('edit_user_profile', array($this, 'lwp_add_phonenumber_field'));
        add_action('personal_options_update', array($this, 'lwp_update_phonenumber_field'));
        add_action('edit_user_profile_update', array($this, 'lwp_update_phonenumber_field'));
        add_action('wp_head', array($this, 'lwp_custom_css'));
        add_action('pre_user_query', array($this, 'lwp_pre_user_query_for_phone_number'));
        add_action('wp_footer', array($this, 'idehweb_render_login_form_on_all_pages'));
        add_action('woodmart_before_wp_footer', array($this, 'remove_woodmart_default_sidebar'), 1);

        add_action('wp_ajax_idehweb_lwp_merge_old_woocommerce_users', array($this, 'idehweb_lwp_merge_old_woocommerce_users'));
        add_action('wp_ajax_idehweb_lwp_auth_customer', array($this, 'idehweb_lwp_auth_customer'));
        add_action('wp_ajax_idehweb_lwp_auth_customer_with_website', array($this, 'idehweb_lwp_auth_customer_with_website'));
        add_action('wp_ajax_idehweb_lwp_activate_customer', array($this, 'idehweb_lwp_activate_customer'));
        add_action('wp_ajax_idehweb_lwp_check_credit', array($this, 'idehweb_lwp_check_credit'));
        add_action('wp_ajax_idehweb_lwp_get_shop', array($this, 'idehweb_lwp_get_shop'));
        add_action('wp_ajax_lwp_ajax_login', array($this, 'lwp_ajax_login'));
        add_action('wp_ajax_lwp_update_password_action', array($this, 'lwp_update_password_action'));
        add_action('wp_ajax_lwp_enter_password_action', array($this, 'lwp_enter_password_action'));
        add_action('wp_ajax_lwp_ajax_login_with_email', array($this, 'lwp_ajax_login_with_email'));
        add_action('wp_ajax_lwp_ajax_verify_with_email', array($this, 'lwp_ajax_verify_with_email'));
        add_action('wp_ajax_lwp_ajax_register', array($this, 'lwp_ajax_register'));
        add_action('wp_ajax_lwp_activate_email', array($this, 'lwp_activate_email'));
        add_action('wp_ajax_lwp_forgot_password', array($this, 'lwp_forgot_password'));
        add_action('wp_ajax_lwp_verify_domain', array($this, 'lwp_verify_domain'));
        add_action('wp_ajax_nopriv_lwp_verify_domain', array($this, 'lwp_verify_domain'));
        add_action('wp_ajax_nopriv_lwp_ajax_login', array($this, 'lwp_ajax_login'));
        add_action('wp_ajax_nopriv_lwp_ajax_login_with_email', array($this, 'lwp_ajax_login_with_email'));
        add_action('wp_ajax_nopriv_lwp_ajax_verify_with_email', array($this, 'lwp_ajax_verify_with_email'));
        add_action('wp_ajax_nopriv_lwp_ajax_register', array($this, 'lwp_ajax_register'));
        add_action('wp_ajax_nopriv_lwp_activate_email', array($this, 'lwp_activate_email'));
        add_action('wp_ajax_nopriv_lwp_update_password_action', array($this, 'lwp_update_password_action'));
        add_action('wp_ajax_nopriv_lwp_enter_password_action', array($this, 'lwp_enter_password_action'));
        add_action('wp_ajax_nopriv_lwp_forgot_password', array($this, 'lwp_forgot_password'));
        add_action('wp_ajax_lwp_set_countries', array($this, 'lwp_set_countries'));

        add_action('activated_plugin', array($this, 'lwp_activation_redirect'));

        add_shortcode('idehweb_lwp', array($this, 'shortcode'));
        add_shortcode('idehweb_lwp_metas', array($this, 'idehweb_lwp_metas'));
        add_shortcode('idehweb_lwp_verify_email', array($this, 'idehweb_lwp_verify_email'));
        add_action('set_logged_in_cookie', array($this, 'my_update_cookie'));

        add_filter('manage_users_columns', array($this, 'lwp_modify_user_table'));
        add_filter('manage_users_custom_column', array($this, 'lwp_modify_user_table_row'), 10, 3);
        add_filter('manage_users_sortable_columns', array($this, 'lwp_make_registered_column_sortable'));
        add_filter('woocommerce_locate_template', array($this, 'lwp_addon_woocommerce_login'), 1, 3);
        add_filter('learn-press/override-templates', function () {
            return true;
        }, 1);
        add_filter('learn_press_locate_template', array($this, 'lwp_addon_learnpress_login'), 1, 3);
    }
}

new idehwebLwp();