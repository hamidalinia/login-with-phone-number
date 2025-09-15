<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

trait Frontend_Functions
{
    function idehweb_render_login_form_on_all_pages()
    {
        // Get the stored option from the settings
        $options = get_option('idehweb_lwp_settings');

        // Check if the option is enabled
        if (isset($options['idehweb_show_form_all_pages']) && $options['idehweb_show_form_all_pages'] == '1') {
            // Check if it's not the "my-account" page
            if (function_exists('is_account_page'))
                if (!is_page('my-account') && !is_account_page()) {
                    // Render the login/register form using a shortcode
                    echo do_shortcode('[idehweb_lwp]'); // Replace with your actual shortcode
                }
        }
    }

    function remove_woodmart_default_sidebar($page)
    {
        remove_action('woodmart_before_wp_footer', 'woodmart_sidebar_login_form', 160);
        add_action('woodmart_before_wp_footer', array(&$this, 'add_lwp_to_woodmart_sidebar'), 160);

    }

    function add_lwp_to_woodmart_sidebar($page)
    {
        $position = is_rtl() ? 'left' : 'right';
        $wrapper_classes = '';
        global $wp;

        $wrapper_classes .= ' wd-' . $position;
        if (function_exists('is_account_page'))
            if (!(basename($wp->request) === 'my-account' && is_account_page())) {
                ?>
                <div class="login-form-side wd-side-hidden woocommerce<?php echo esc_attr($wrapper_classes); ?>">
                    <div class="wd-heading">
                        <span class="title"><?php esc_html_e('Sign in', 'login-with-phone-number'); ?></span>
                        <div class="close-side-widget wd-action-btn wd-style-text wd-cross-icon">
                            <a href="#" rel="nofollow"><?php esc_html_e('Close', 'login-with-phone-number'); ?></a>
                        </div>
                    </div>
                    <?php echo do_shortcode('[idehweb_lwp]'); ?>
                </div>
                <?php
            }

    }


    function lwp_add_phonenumber_field($user)
    {
        $phn = get_the_author_meta('phone_number', $user->ID);
        ?>
        <h3><?php esc_html_e('Personal Information', 'login-with-phone-number'); ?></h3>

        <table class="form-table">
            <tr>
                <th><label for="phone_number"><?php esc_html_e('phone_number', 'login-with-phone-number'); ?></label>
                </th>
                <td>
                    <input type="text"

                           step="1"
                           id="phone_number"
                           name="phone_number"
                           value="<?php echo esc_attr($phn); ?>"
                           class="regular-text"
                    />

                </td>
            </tr>
        </table>
        <?php
    }

    function lwp_update_phonenumber_field($user_id)
    {
        if (!current_user_can('edit_user', $user_id)) {
            return false;
        }

// Verify nonce
        if (!isset($_POST['_wpnonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['_wpnonce'])), 'update-user_' . $user_id)) {
            return false;
        }

// Check if phone_number exists before using it
        if (!isset($_POST['phone_number'])) {
            return false;
        }

        $phone_number = sanitize_text_field(wp_unslash($_POST['phone_number']));
        update_user_meta($user_id, 'phone_number', $phone_number);
    }
    function lwp_custom_css()
    {
        if (class_exists(LWP_PRO::class)) {
//            $LWP_PRO = new LWP_PRO;
            global $LWP_PRO;
            $LWP_PRO->lwp_style();
        }
    }

    function enqueue_scripts()
    {
        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_redirect_url'])) $options['idehweb_redirect_url'] = home_url();
        if (!isset($options['idehweb_default_gateways'])) $options['idehweb_default_gateways'] = ['custom'];
        if (!isset($options['idehweb_use_custom_gateway'])) $options['idehweb_use_custom_gateway'] = '1';
        if (!isset($options['idehweb_firebase_api'])) $options['idehweb_firebase_api'] = '';
        if (!isset($options['idehweb_firebase_config'])) $options['idehweb_firebase_config'] = '';
        if (!isset($options['idehweb_enable_timer_on_sending_sms'])) $options['idehweb_enable_timer_on_sending_sms'] = '1';
        if (!isset($options['idehweb_timer_count'])) $options['idehweb_timer_count'] = '60';
        if (!isset($options['idehweb_close_button'])) $options['idehweb_close_button'] = '0';
        if (!isset($options['idehweb_position_form'])) $options['idehweb_position_form'] = '0';

//        if (!isset($options['idehweb_default_gateways'])) $options['idehweb_default_gateways'] = '';
        if (!is_array($options['idehweb_default_gateways'])) {
            $options['idehweb_default_gateways'] = [];
        }
        $current_user = wp_get_current_user();
        $localize = array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'redirecturl' => $options['idehweb_redirect_url'],
            'UserId' => 0,
            'UserName' => is_user_logged_in() ? $current_user->display_name : '',
            'IsLoggedIn' => is_user_logged_in(),
            'loadingmessage' => __('please wait...', 'login-with-phone-number'),
            'timer' => $options['idehweb_enable_timer_on_sending_sms'],
            'timer_count' => $options['idehweb_timer_count'],
            'sticky' => $options['idehweb_position_form'],
            'message_running_recaptcha' => __('running recaptcha...', 'login-with-phone-number')
        );

        wp_enqueue_style('idehweb-lwp', plugins_url('/styles/login-with-phonenumber.css', dirname(__FILE__)),
            array(), '1.8.53', 'all');


        wp_enqueue_script('idehweb-lwp-validate-script', plugins_url('/scripts/jquery.validate.js', dirname(__FILE__)), array('jquery'), '1.8.53', true);


        wp_enqueue_script('idehweb-lwp', plugins_url('/scripts/login-with-phonenumber.js', dirname(__FILE__)), array('jquery'), '1.8.53', true);


        if ($options['idehweb_use_custom_gateway'] == '1' && in_array('firebase', $options['idehweb_default_gateways'])) {
            wp_enqueue_script('lwp-google-recaptcha', 'https://www.google.com/recaptcha/api.js', array(), false, true);
            wp_enqueue_script('lwp-firebase', 'https://www.gstatic.com/firebasejs/7.21.0/firebase-app.js', array(), false, true);
            wp_enqueue_script('lwp-firebase-auth', 'https://www.gstatic.com/firebasejs/7.21.0/firebase-auth.js', array(), false, true);

            wp_enqueue_script('lwp-firebase-sender', plugins_url('/scripts/firebase-sender.js', dirname(__FILE__)), array('jquery'), '1.8.53', true);

            $localize['firebase_api'] = $options['idehweb_firebase_api'];
        }

        $localize['close_button'] = $options['idehweb_close_button'];
        $localize['nonce'] = wp_create_nonce('lwp_login');
        wp_localize_script('idehweb-lwp', 'idehweb_lwp', $localize);
        if ($options['idehweb_use_custom_gateway'] == '1' && in_array('firebase', $options['idehweb_default_gateways'])) {
            $options['idehweb_firebase_config'] = $this->setting_clean_firebase_config_code($options['idehweb_firebase_config']);
            wp_add_inline_script('idehweb-lwp', '' . htmlspecialchars_decode($options['idehweb_firebase_config']));
        }


        // integrate intl-tel-input
        // get allowed countries
        $onlyCountries = [];
        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_country_codes'])) $options['idehweb_country_codes'] = ["uk"];
        if (!isset($options['idehweb_country_codes_default'])) $options['idehweb_country_codes_default'] = "";
        $country_codes = $this->get_country_code_options();
        foreach ($country_codes as $country) {
            $rr = in_array($country["code"], $options['idehweb_country_codes']);
            if ($rr) $onlyCountries[] = $country["code"];
        }
// get initial/default country, and make sure it exists in allowed counties
        $initialCountry = $options['idehweb_country_codes_default'];
        $initialCountry = in_array($initialCountry, $onlyCountries) ? $initialCountry : '';

        $lwp_settings_localization = get_option('idehweb_lwp_settings_localization');
        if (!isset($lwp_settings_localization['idehweb_localization_disable_placeholder'])) $lwp_settings_localization['idehweb_localization_disable_placeholder'] = "0";
        $idehweb_localization_disable_placeholder = ($lwp_settings_localization['idehweb_localization_disable_placeholder'] == "1");

        wp_enqueue_style('lwp-intltelinput-style', plugins_url('/styles/intlTelInput.min.css', dirname(__FILE__)),
            array(),
            '1.8.53',
            'all');
        wp_add_inline_style('lwp-intltelinput-style', '.iti { width: 100%; }#lwp_username{font-size: 20px;}');
//
        wp_enqueue_script('lwp-intltelinput-script',
            plugins_url('/scripts/intlTelInput.min.js', dirname(__FILE__)),
            array(), '1.8.53', true);
// Inline initialization
        wp_add_inline_script(
            'lwp-intltelinput-script',
            '(function(){
        document.addEventListener("DOMContentLoaded", function() {
            var input = document.querySelector("#lwp_phone");
            if(input){
                window.intlTelInput(input, {
                    utilsScript: "' . esc_url(plugins_url('/scripts/utils.js', dirname(__FILE__))) . '",
                    hiddenInput: "lwp_username",
                    autoPlaceholder:"' . ($idehweb_localization_disable_placeholder ? "off" : "polite") . '",
                    onlyCountries: ' . wp_json_encode($onlyCountries) . ',
                    initialCountry: "' . esc_html($initialCountry) . '",
                });
            }
        });
    })();'
        );


    }

    function idehweb_lwp_verify_email($atts)
    {

        extract(shortcode_atts(array(
            'redirect_url' => ''
        ), $atts));
        ob_start();
        $options = get_option('idehweb_lwp_settings');
        $localizationoptions = get_option('idehweb_lwp_settings_localization');

        if (class_exists(LWP_PRO::class)) {
//            $LWP_PRO = new LWP_PRO;
            global $LWP_PRO;
            $image_id = $LWP_PRO->lwp_logo();
        }
        if (!isset($image_id)) $image_id = 0;
        if (!isset($options['idehweb_sms_login'])) $options['idehweb_sms_login'] = '1';
        if (!isset($options['idehweb_enable_accept_terms_and_condition'])) $options['idehweb_enable_accept_terms_and_condition'] = '1';
        if (!isset($options['idehweb_term_and_conditions_link'])) $options['idehweb_term_and_conditions_link'] = '#';
        if (!isset($options['idehweb_term_and_conditions_text'])) $options['idehweb_term_and_conditions_text'] = __('By submitting, you agree to the Terms and Privacy Policy', 'login-with-phone-number');
        else $options['idehweb_term_and_conditions_text'] = ($options['idehweb_term_and_conditions_text']);
        if (!isset($options['idehweb_term_and_conditions_default_checked'])) $options['idehweb_term_and_conditions_default_checked'] = '0';
        if (!isset($options['idehweb_email_login'])) $options['idehweb_email_login'] = '1';
        if (!isset($options['idehweb_password_login'])) $options['idehweb_password_login'] = '1';
        if (!isset($options['idehweb_redirect_url'])) $options['idehweb_redirect_url'] = '';
        if (!isset($options['idehweb_login_message'])) $options['idehweb_login_message'] = 'Welcome, You are logged in...';
        if (!isset($options['idehweb_country_codes'])) $options['idehweb_country_codes'] = [];
        if (!isset($options['idehweb_position_form'])) $options['idehweb_position_form'] = '0';
        if (!isset($options['idehweb_auto_show_form'])) $options['idehweb_auto_show_form'] = '1';
        if (!isset($options['idehweb_email_force_after_phonenumber'])) $options['idehweb_email_force_after_phonenumber'] = true;
        if (!isset($options['idehweb_close_button'])) $options['idehweb_close_button'] = '0';
        if (!isset($options['idehweb_default_gateways'])) $options['idehweb_default_gateways'] = ['custom'];
        if (!is_array($options['idehweb_default_gateways'])) {
            $options['idehweb_default_gateways'] = [];
        }

        if (!isset($localizationoptions['idehweb_localization_placeholder_of_phonenumber_field'])) $localizationoptions['idehweb_localization_placeholder_of_phonenumber_field'] = '';
        if (!isset($localizationoptions['idehweb_localization_title_of_login_form'])) $localizationoptions['idehweb_localization_title_of_login_form'] = '';
        if (!isset($localizationoptions['idehweb_localization_title_of_login_form_email'])) $localizationoptions['idehweb_localization_title_of_login_form_email'] = '';

        $class = '';
        if ($options['idehweb_position_form'] == '1') {
            $class = 'lw-sticky';
        }
        $theClasses = '';
        if ($options['idehweb_default_gateways'][0])
            $theClasses = $options['idehweb_default_gateways'][0];

        $is_user_logged_in = is_user_logged_in();
        if ($is_user_logged_in) {

            if ($options['idehweb_email_force_after_phonenumber']) {
                $ecclass = 'display:block';
                $user = wp_get_current_user();

                ?>
                <?php if (empty($user->user_email)) {
                    ?>
                    <form id="lwp_verify_email" class="ajax-auth" action="loginemail"
                          style="<?php echo esc_attr($ecclass); ?>"
                          method="post">
                        <?php
                        if (intval($image_id) > 0) {
                            $image = wp_get_attachment_image($image_id, 'full', false, array('class' => 'lwp_media-logo-image'));
                            echo '<div class="lwp_logo_parent">' . wp_kses_post($image) . '</div>';
                        }
                        ?>

                        <p class="status"></p>
                        <?php wp_nonce_field('lwp-ajax-login-with-email-nonce', 'security'); ?>
                        <label class="lwp_labels"
                               for="lwp_email"><?php echo esc_html__('Your email:', 'login-with-phone-number'); ?></label>
                        <input type="email" class="required lwp_email the_lwp_input" name="lwp_email"
                               placeholder="<?php echo esc_html__('Please enter your email', 'login-with-phone-number'); ?>">

                        <button class="submit_button auth_email" type="submit">
                            <?php echo esc_html__('Submit', 'login-with-phone-number'); ?>
                        </button>
                    </form>
                    <form id="lwp_activate_email" data-method="email"
                          class="ajax-auth lwp-register-form-i email" action="activate"
                          method="post">
                        <div class="lh1"><?php echo esc_html__('Activation', 'login-with-phone-number'); ?></div>
                        <p class="status"></p>
                        <?php wp_nonce_field('lwp-ajax-activate-nonce', 'security'); ?>
                        <div class="lwp_top_activation">
                            <div class="lwp_timer"></div>


                        </div>
                        <label class="lwp_labels"
                               for="lwp_scode"><?php echo esc_html__('Security code', 'login-with-phone-number'); ?></label>
                        <input type="text" class="required lwp_scode" name="lwp_scode" placeholder="ـ ـ ـ ـ ـ ـ">

                        <button class="submit_button auth_secCode">
                            <?php echo esc_html__('Activate', 'login-with-phone-number'); ?>
                        </button>
                        <button class="submit_button lwp_didnt_r_c lwp_disable  <?php echo esc_attr($theClasses); ?>"
                                type="button">
                            <?php echo esc_html__('Send code again', 'login-with-phone-number'); ?>
                        </button>
                        <hr class="lwp_line"/>
                        <div class="lwp_bottom_activation">
                            <a class="lwp_change_el" href="#">
                                <?php echo esc_html__('Change email?', 'login-with-phone-number'); ?>
                            </a>
                        </div>
                        <?php if ($options['idehweb_close_button'] == "0") { ?>
                            <a class="close" href="">(x)</a>
                        <?php } ?>

                    </form>
                <?php } else {
                    echo esc_html($user->user_email);
                    ?>

                    <?php
                } ?>

            <?php } ?>
            <?php
        }
        return ob_get_clean();
    }

    function idehweb_lwp_metas($vals)
    {

        $atts = shortcode_atts(array(
            'email' => false,
            'phone_number' => true,
            'username' => false,
            'nicename' => false

        ), $vals);
        ob_start();
        $user = wp_get_current_user();
        if (!isset($atts['username'])) $atts['username'] = false;
        if (!isset($atts['nicename'])) $atts['nicename'] = false;
        if (!isset($atts['email'])) $atts['email'] = false;
        if (!isset($atts['phone_number'])) $atts['phone_number'] = true;
        if ($atts['username'] == 'true') {
            echo '<div class="lwp user_login">' . esc_html($user->user_login) . '</div>';
        }
        if ($atts['nicename'] == 'true') {
            echo '<div class="lwp user_nicename">' . esc_html($user->user_nicename) . '</div>';

        }
        if ($atts['email'] == 'true') {
            echo '<div class="lwp user_email">' . esc_html($user->user_email) . '</div>';

        }
        if ($atts['phone_number'] == 'true') {
            echo '<div class="lwp user_email">' . esc_html(get_user_meta($user->ID, 'phone_number', true)) . '</div>';
        }
        return ob_get_clean();
    }


    function shortcode($atts)
    {

        extract(shortcode_atts(array(
            'redirect_url' => ''
        ), $atts));
        ob_start();
        $options = get_option('idehweb_lwp_settings');
        $localizationoptions = get_option('idehweb_lwp_settings_localization');
        $idehweb_pro = get_option('idehweb_lwp_settings_registration_fields');


        if (!isset($idehweb_pro['idehweb_registration_fields_status'])) $idehweb_pro['idehweb_registration_fields_status'] = '0';
        if (!isset($idehweb_pro['idehweb_registration_fields'])) $idehweb_pro['idehweb_registration_fields'] = [];


        if (class_exists(LWP_PRO::class)) {
//            $LWP_PRO = new LWP_PRO;
            global $LWP_PRO;
            $image_id = $LWP_PRO->lwp_logo();
        }
        if (!isset($image_id)) $image_id = 0;
        if (!isset($options['idehweb_sms_login'])) $options['idehweb_sms_login'] = '1';
        if (!isset($options['idehweb_enable_accept_terms_and_condition'])) $options['idehweb_enable_accept_terms_and_condition'] = '1';
        if (!isset($options['idehweb_term_and_conditions_link'])) $options['idehweb_term_and_conditions_link'] = '#';
        if (!isset($options['idehweb_term_and_conditions_text'])) $options['idehweb_term_and_conditions_text'] = __('By submitting, you agree to the Terms and Privacy Policy', 'login-with-phone-number');
        else $options['idehweb_term_and_conditions_text'] = ($options['idehweb_term_and_conditions_text']);
        if (!isset($options['idehweb_term_and_conditions_default_checked'])) $options['idehweb_term_and_conditions_default_checked'] = '0';
        if (!isset($options['idehweb_email_login'])) $options['idehweb_email_login'] = '1';
        if (!isset($options['idehweb_password_login'])) $options['idehweb_password_login'] = '1';
        if (!isset($options['idehweb_redirect_url'])) $options['idehweb_redirect_url'] = '';
        if (!isset($options['idehweb_login_message'])) $options['idehweb_login_message'] = 'Welcome, You are logged in...';
        if (!isset($options['idehweb_country_codes'])) $options['idehweb_country_codes'] = [];
        if (!isset($options['idehweb_position_form'])) $options['idehweb_position_form'] = '0';
        if (!isset($options['idehweb_auto_show_form'])) $options['idehweb_auto_show_form'] = '1';
        if (!isset($options['idehweb_email_force_after_phonenumber'])) $options['idehweb_email_force_after_phonenumber'] = true;
        if (!isset($options['idehweb_close_button'])) $options['idehweb_close_button'] = '0';
        if (!isset($options['idehweb_default_gateways'])) $options['idehweb_default_gateways'] = ['custom'];
        if (!is_array($options['idehweb_default_gateways'])) {
            $options['idehweb_default_gateways'] = [];
        }
        if (!isset($options['idehweb_length_of_activation_code'])) $options['idehweb_length_of_activation_code'] = '6';

        if (!isset($localizationoptions['idehweb_localization_placeholder_of_phonenumber_field'])) $localizationoptions['idehweb_localization_placeholder_of_phonenumber_field'] = '';
        if (!isset($localizationoptions['idehweb_localization_firebase_option_title'])) $localizationoptions['idehweb_localization_firebase_option_title'] = '';
        if (!isset($localizationoptions['idehweb_localization_custom_option_title'])) $localizationoptions['idehweb_localization_custom_option_title'] = '';
        if (!isset($localizationoptions['idehweb_localization_title_of_login_form'])) $localizationoptions['idehweb_localization_title_of_login_form'] = '';
        if (!isset($localizationoptions['idehweb_localization_title_of_login_form_email'])) $localizationoptions['idehweb_localization_title_of_login_form_email'] = '';
        if (!isset($localizationoptions['idehweb_localization_custom_option_title'])) $localizationoptions['idehweb_localization_custom_option_title'] = '';
        if (!isset($localizationoptions['idehweb_localization_ultramessage_option_title'])) $localizationoptions['idehweb_localization_ultramessage_option_title'] = '';

        $class = '';
        if ($options['idehweb_position_form'] == '1') {
            $class = 'lw-sticky';
        }
        $theClasses = '';
        if ($options['idehweb_default_gateways'][0])
            $theClasses = $options['idehweb_default_gateways'][0];

        $is_user_logged_in = is_user_logged_in();
        if (!$is_user_logged_in) {
            ?>
            <?php
//            echo 'idehweb_position_form:';
//
//            print_r($options['idehweb_position_form']);
//            echo 'idehweb_auto_show_form:';
//            print_r($options['idehweb_auto_show_form']);
            if (($options['idehweb_position_form'] == '0' && $options['idehweb_auto_show_form'] == '0') || ($options['idehweb_position_form'] == '1' && $options['idehweb_auto_show_form'] == '1')) {
                ?>
                <a id="show_login" class="show_login"
                   style="display: none"
                   data-sticky="<?php echo esc_attr($options['idehweb_position_form']); ?>"><?php echo esc_html__('login', 'login-with-phone-number'); ?></a>
                <?php
            }
            ?>

            <div class="lwp_forms_login <?php echo esc_attr($class); ?>">
                <?php
                if ($options['idehweb_sms_login']) {
                    if ($options['idehweb_email_login']) {
                        $cclass = 'display:block';
                    } else if (!$options['idehweb_email_login']) {
                        $cclass = 'display:block';
                    }
                    if (($options['idehweb_position_form'] == '1' && $options['idehweb_auto_show_form'] == '0')) {
                        $cclass = 'display:none';
                    }
                    ?>
                    <form id="lwp_login" class="ajax-auth lwp-login-form-i <?php echo esc_attr($theClasses); ?>"
                          data-method="<?php echo esc_attr($theClasses); ?>" action="login"
                          style="<?php echo esc_attr($cclass); ?>"
                          method="post">
                        <?php
                        if (intval($image_id) > 0) {
                            $image = wp_get_attachment_image($image_id, 'full', false, array('class' => 'lwp_media-logo-image'));
                            // Use wp_kses_post() on the entire string
                            echo wp_kses_post('<div class="lwp_logo_parent">' . $image . '</div>');
                        }
                        ?>
                        <div class="lh1"><?php echo isset($localizationoptions['idehweb_localization_status']) ? esc_html($localizationoptions['idehweb_localization_title_of_login_form']) : (esc_html__('Login / register', 'login-with-phone-number')); ?></div>
                        <p class="status"></p>
                        <?php wp_nonce_field('ajax-login-nonce', 'security'); ?>
                        <div class="lwp-form-box">
                            <label class="lwp_labels"
                                   for="lwp_phone"><?php echo esc_html__('Phone number', 'login-with-phone-number'); ?></label>
                            <?php
                            //                    $country_codes = $this->get_country_code_options();
                            ?>
                            <div class="lwp-form-box-bottom">
                                <input type="hidden" id="lwp_country_codes">
                                <input type="tel" id="lwp_phone"
                                       class="required lwp_username the_lwp_input"
                                       placeholder="<?php echo ($localizationoptions['idehweb_localization_placeholder_of_phonenumber_field'])
                                           ? esc_attr($localizationoptions['idehweb_localization_placeholder_of_phonenumber_field'])
                                           : ''; ?>">
                            </div>
                        </div>
                        <?php if ($options['idehweb_enable_accept_terms_and_condition'] == '1') { ?>
                            <div class="accept_terms_and_conditions">
                                <input class="required lwp_check_box" type="checkbox" name="lwp_accept_terms"
                                    <?php echo(($options['idehweb_term_and_conditions_default_checked'] == '1') ? 'checked="checked"' : ''); ?>>
                                <a href="<?php echo esc_url($options['idehweb_term_and_conditions_link']); ?>">
                                    <span class="accept_terms_and_conditions_text"><?php echo esc_html($options['idehweb_term_and_conditions_text']); ?></span>
                                </a>
                            </div>
                        <?php } ?>
                        <div class="lwp_otp_gateways">
                            <?php
                            if (count($options['idehweb_default_gateways']) > 1)
                                foreach ($options['idehweb_default_gateways'] as $key => $gateway) {
                                    ?>
                                    <span class="lwp-radio-otp">
        <input type="radio" name="otp-method"
               value="<?php echo esc_attr($gateway); ?>" <?php echo(($key == 0) ? 'checked="checked"' : ''); ?> />
        <label for="<?php echo esc_attr($gateway); ?>">
            <?php
            if ($gateway == "firebase" && isset($localizationoptions['idehweb_localization_status']) && isset($localizationoptions['idehweb_localization_firebase_option_title'])) {
                echo esc_html($localizationoptions['idehweb_localization_firebase_option_title']) ?: esc_html($gateway);
            } else if ($gateway == "custom" && isset($localizationoptions['idehweb_localization_status']) && isset($localizationoptions['idehweb_localization_custom_option_title'])) {
                echo esc_html($localizationoptions['idehweb_localization_custom_option_title']);
            } else if ($gateway == "ultramessage" && isset($localizationoptions['idehweb_localization_status']) && isset($localizationoptions['idehweb_localization_ultramessage_option_title'])) {
                echo esc_html($localizationoptions['idehweb_localization_ultramessage_option_title']);
            } else {
                echo esc_html($gateway);
            }
            ?>
        </label>
    </span>
                                    <?php
                                }
                            ?>

                        </div>

                        <button class="submit_button auth_phoneNumber" type="submit">
                            <?php echo esc_html__('Submit', 'login-with-phone-number'); ?>
                        </button>
                        <?php
                        if ($options['idehweb_email_login']) {
                            ?>
                            <button class="submit_button auth_with_email secondaryccolor" type="button">
                                <?php echo esc_html__('Login with email', 'login-with-phone-number'); ?>
                            </button>
                        <?php } ?>
                        <div class="lwp_sso_gateways">
                            <?php
                            $sso_rows = [];
                            $sso_rows = apply_filters('lwp_add_to_sso_gateways', $sso_rows);
                            if ($sso_rows) {
                                foreach ($sso_rows as $key => $sso) {
                                    if (isset($sso['html'])) {
                                        echo wp_kses_post($sso['html']);
                                    }
                                }
                            }
                            ?>

                        </div>
                        <?php if ($options['idehweb_close_button'] == "0") { ?>
                            <a class="close" href="">(x)</a>
                        <?php } ?>
                    </form>
                <?php } ?>
                <?php
                if ($options['idehweb_email_login']) {
                    $ecclass = 'display:none';
                    if (($options['idehweb_position_form'] == '1' && $options['idehweb_auto_show_form'] == '0')) {
                        $ecclass = 'display:none';
                    }
                    ?>
                    <form id="lwp_login_email" class="ajax-auth" action="loginemail" style="<?php echo esc_attr($ecclass); ?>"
                          method="post">
                        <?php
                        if (intval($image_id) > 0) {
                            $image = wp_get_attachment_image($image_id, 'full', false, array('class' => 'lwp_media-logo-image'));
                            echo wp_kses_post('<div class="lwp_logo_parent">' . $image . '</div>');
                        }
                        ?>
                        <div class="lh1"><?php echo isset($localizationoptions['idehweb_localization_status']) ? esc_html($localizationoptions['idehweb_localization_title_of_login_form_email']) : (esc_html__('Login / register', 'login-with-phone-number')); ?></div>
                        <p class="status"></p>
                        <?php wp_nonce_field('lwp-ajax-login-with-email-nonce', 'security'); ?>
                        <label class="lwp_labels"
                               for="lwp_email"><?php echo esc_html__('Your email:', 'login-with-phone-number'); ?></label>
                        <input type="email" class="required lwp_email the_lwp_input" name="lwp_email"
                               placeholder="<?php echo esc_attr__('Please enter your email', 'login-with-phone-number'); ?>">      <?php if ($options['idehweb_enable_accept_terms_and_condition'] == '1') { ?>
                            <div class="accept_terms_and_conditions">

                                <input class="required lwp_check_box lwp_accept_terms_email" type="checkbox"
                                       name="lwp_accept_terms_email" <?php echo(($options['idehweb_term_and_conditions_default_checked'] == '1') ? 'checked="checked"' : ''); ?> >
                                <a href="<?php echo esc_url($options['idehweb_term_and_conditions_link']); ?>">
                                    <span class="accept_terms_and_conditions_text"><?php echo esc_html($options['idehweb_term_and_conditions_text']); ?></span>
                                </a>
                            </div>
                        <?php } ?>
                        <button class="submit_button auth_email" type="submit">
                            <?php echo esc_html__('Submit', 'login-with-phone-number'); ?>
                        </button>
                        <?php
                        if ($options['idehweb_sms_login']) {
                            ?>
                            <button class="submit_button auth_with_phoneNumber secondaryccolor" type="button">
                                <?php echo esc_html__('Login with phone number', 'login-with-phone-number'); ?>
                            </button>
                        <?php } ?>
                        <?php if ($options['idehweb_close_button'] == "0") { ?>
                            <a class="close" href="">(x)</a>
                        <?php } ?>
                    </form>
                <?php } ?>
                <?php if ($idehweb_pro['idehweb_registration_fields_status']) { ?>
                    <form id="lwp_update_extra_fields" data-method="<?php echo esc_attr($theClasses); ?>"
                          class="ajax-auth <?php echo esc_attr($theClasses); ?>" action="update_password" method="post">

                        <div class="lh1"><?php echo esc_html__('Update data', 'login-with-phone-number'); ?></div>
                        <p class="status"></p>
                        <?php wp_nonce_field('ajax-login-nonce', 'security'); ?>
                        <div class="lwp-inside-form">
                            <?php

                            if (class_exists(LWP_PRO::class)) {
                                $ROptions = get_option('idehweb_lwp_settings_registration_fields');
                                if (!isset($ROptions['idehweb_registration_fields'])) $ROptions['idehweb_registration_fields'] = [];
                                foreach ($ROptions['idehweb_registration_fields'] as $key => $fi) {
//                                    print_r($fi);
                                    ?>
                                    <?php

                                    if ($fi['value'] == "role") {
                                        ?>
                                        <div class="lwp-inside-form-input">
                                            <div class="accept_terms_and_conditions" style="
    display: flex;
    justify-content: space-around;
">
                                                <div class="choos-rol" style="
    display: flex;
">
                                                    <input class="required lwp_check_box" type="radio" name="role"
                                                           value="subscriber">
                                                    <label for="subscriber" class="role_text"
                                                           style="margin-left:0px; margin-right: 5px">Subscriber</label>

                                                </div>
                                                <div class="choos-rol" style="
    display: flex;
">

                                                    <input class="required lwp_check_box" type="radio" name="role"
                                                           value="partner">
                                                    <label for="partner" class="role_text" style="margin-left:0px">Partner</label>
                                                </div>
                                            </div>

                                        </div>
                                        <?php
                                    } else {

                                        ?>
                                        <div class="lwp-inside-form-input">
                                            <label class="lwp_labels"
                                                   for="<?php echo esc_attr($fi['value']); ?>"><?php echo esc_html($fi['label']); ?>
                                                :</label>
                                            <input type="text" class="required lwp_auth_<?php echo esc_attr($fi['value']); ?>"
                                                   name="<?php echo esc_attr($fi['name']); ?>" value="<?php echo esc_attr($fi['value']); ?>"
                                                   placeholder="<?php echo esc_attr($fi['label']); ?>">
                                        </div>
                                        <?php
                                    }
                                }
                            }
                            ?>

                        </div>

                        <button class="submit_button auth_email" type="submit">
                            <?php echo esc_html__('Update', 'login-with-phone-number'); ?>
                        </button>
                        <?php if ($options['idehweb_close_button'] == "0") { ?>
                            <a class="close" href="">(x)</a>
                        <?php } ?>
                    </form>
                <?php } ?>
                <form id="lwp_activate" data-method="<?php echo esc_attr($theClasses); ?>"
                      class="ajax-auth lwp-register-form-i <?php echo esc_attr($theClasses); ?>" action="activate" method="post">
                    <div class="lh1"><?php echo esc_html__('Activation', 'login-with-phone-number'); ?></div>
                    <p class="status"></p>
                    <?php wp_nonce_field('lwp-ajax-activate-nonce', 'security'); ?>
                    <div class="lwp_top_activation">
                        <div class="lwp_timer"></div>


                    </div>
                    <div class="lwp_scode_parent">
                        <label class="lwp_labels"
                               for="lwp_scode"><?php echo esc_html__('Security code', 'login-with-phone-number'); ?></label>
                        <input type="text" class="required lwp_scode" autocomplete="one-time-code" inputmode="numeric"
                               maxlength="<?php echo esc_attr(($options['idehweb_length_of_activation_code'])); ?>"
                               pattern="[0-9]{<?php echo esc_attr(($options['idehweb_length_of_activation_code'])); ?>}"
                               name="lwp_scode" id="lwp_scode">
                    </div>
                    <button class="submit_button auth_secCode">
                        <?php echo esc_html__('Activate', 'login-with-phone-number'); ?>
                    </button>
                    <button class="submit_button lwp_didnt_r_c lwp_disable  <?php echo esc_attr($theClasses); ?>" type="button">
                        <?php echo esc_html__('Send code again', 'login-with-phone-number'); ?>
                    </button>
                    <hr class="lwp_line"/>
                    <div class="lwp_bottom_activation">
                        <a class="lwp_change_pn" href="#">
                            <?php echo esc_html__('Change phone number?', 'login-with-phone-number'); ?>
                        </a>
                        <a class="lwp_change_el" href="#">
                            <?php echo esc_html__('Change email?', 'login-with-phone-number'); ?>
                        </a>
                    </div>
                    <?php if ($options['idehweb_close_button'] == "0") { ?>
                        <a class="close" href="">(x)</a>
                    <?php } ?>
                </form>

                <?php
                if ($options['idehweb_password_login']) {
                    ?>
                    <form id="lwp_update_password" data-method="<?php echo esc_attr($theClasses); ?>"
                          class="ajax-auth <?php echo esc_attr($theClasses); ?>" action="update_password" method="post">

                        <div class="lh1"><?php echo esc_html__('Update password', 'login-with-phone-number'); ?></div>
                        <p class="status"></p>
                        <?php wp_nonce_field('ajax-login-nonce', 'security'); ?>
                        <div class="lwp-inside-form">
                            <?php

                            if (class_exists(LWP_PRO::class)) {
                                $ROptions = get_option('idehweb_lwp_settings_registration_fields');
                                if (!isset($ROptions['idehweb_registration_fields'])) $ROptions['idehweb_registration_fields'] = [];
                                foreach ($ROptions['idehweb_registration_fields'] as $key => $fi) {
//                                    print_r($fi['children']);
                                    ?>
                                    <?php

                                    if ($fi['name'] == "role") {
                                        $children = $fi['children'];
                                        $children = json_decode($children, true);
                                        ?>
                                        <div class="lwp-inside-form-input lwp-extra-input">
                                            <div class="accept_terms_and_conditions" style="
    display: flex;
    justify-content: space-around;
">
                                                <?php

                                                foreach ($children as $key2 => $ch) {

                                                    ?>
                                                    <div class="choos-rol" style="display: flex;">
                                                        <input class="required lwp_check_box" type="radio"
                                                               checked="<?php echo esc_attr((!empty($fi['value']) && $fi['value'] == $ch['value']) ? ("true") : "false"); ?>"
                                                               name="<?php echo esc_attr($fi['name']); ?>"
                                                               value="<?php echo esc_attr($ch['value']); ?>">
                                                        <label for="<?php echo esc_attr($ch['value']); ?>"
                                                               class="role_text"
                                                               style="margin-left:0px; margin-right: 5px"><?php echo esc_attr($ch['label']); ?></label>

                                                    </div>
                                                <?php } ?>
                                            </div>

                                        </div>
                                        <?php
                                    } else {

                                        ?>
                                        <div class="lwp-inside-form-input lwp-extra-input">
                                            <label class="lwp_labels"
                                                   for="<?php echo esc_attr($fi['value']); ?>"><?php echo esc_html($fi['label']); ?>
                                                :</label>
                                            <input type="text" class="lwp_auth_<?php echo esc_attr($fi['value']); ?>"
                                                   name="<?php echo esc_attr($fi['name']); ?>" value="<?php echo esc_attr($fi['value']); ?>"
                                                   placeholder="<?php echo esc_attr($fi['label']); ?>">
                                        </div>
                                        <?php
                                    }
                                }
                            }
                            ?>
                            <div class="lwp-inside-form-input">
                                <label class="lwp_labels"
                                       for="lwp_email"><?php echo esc_html__('Enter new password:', 'login-with-phone-number'); ?></label>
                                <input type="password" class="required lwp_up_password" name="lwp_up_password"
                                       placeholder="<?php echo esc_attr__('Please choose a password', 'login-with-phone-number'); ?>">
                            </div>
                        </div>

                        <button class="submit_button auth_email" type="submit">
                            <?php echo esc_html__('Update', 'login-with-phone-number'); ?>
                        </button>
                        <?php if ($options['idehweb_close_button'] == "0") { ?>
                            <a class="close" href="">(x)</a>
                        <?php } ?>
                    </form>
                    <form id="lwp_enter_password" class="ajax-auth" action="enter_password" method="post">

                        <div class="lh1"><?php echo esc_html__('Enter password', 'login-with-phone-number'); ?></div>
                        <p class="status"></p>
                        <?php wp_nonce_field('lwp-ajax-enter-password-nonce', 'security'); ?>
                        <div class="lwp-inside-form">
                            <?php
                            //
                            //                            if (class_exists(LWP_PRO::class)) {
                            //                                $ROptions = get_option('idehweb_lwp_settings_registration_fields');
                            //                                if (!isset($ROptions['idehweb_registration_fields'])) $ROptions['idehweb_registration_fields'] = [];
                            //                                foreach ($ROptions['idehweb_registration_fields'] as $key => $fi) {
                            ////                                    print_r($fi);
                            //                                    ?>
                            <!--                                    <div class="lwp-inside-form-input">-->
                            <!--                                        <label class="lwp_labels"-->
                            <!--                                               for="-->
                            <?php //echo $fi['value']; ?><!--">--><?php //echo $fi['label']; ?><!--:</label>-->
                            <!--                                        <input type="text" class="required lwp_auth_-->
                            <?php //echo $fi['value']; ?><!--"-->
                            <!--                                               name="-->
                            <?php //echo $fi['value']; ?><!--"-->
                            <!--                                               placeholder="-->
                            <?php //echo $fi['label']; ?><!--">-->
                            <!--                                    </div>-->
                            <!--                                    --><?php
                            //                                }
                            //                            }
                            ?>
                            <div class="lwp-inside-form-input">
                                <label class="lwp_labels"
                                       for="lwp_email"><?php echo esc_html__('Your password:', 'login-with-phone-number'); ?></label>
                                <input type="password" class="required lwp_auth_password" name="lwp_auth_password"
                                       placeholder="<?php echo esc_attr__('Please enter your password', 'login-with-phone-number'); ?>">
                            </div>
                        </div>

                        <button class="submit_button login_with_pass" type="submit">
                            <?php echo esc_html__('Login', 'login-with-phone-number'); ?>
                        </button>
                        <button class="submit_button forgot_password <?php echo esc_attr($theClasses); ?>" type="button">
                            <?php echo esc_html__('Forgot password', 'login-with-phone-number'); ?>
                        </button>
                        <hr class="lwp_line"/>
                        <div class="lwp_bottom_activation">

                            <a class="lwp_change_pn" href="#">
                                <?php echo esc_html__('Change phone number?', 'login-with-phone-number'); ?>
                            </a>
                            <a class="lwp_change_el" href="#">
                                <?php echo esc_html__('Change email?', 'login-with-phone-number'); ?>
                            </a>
                        </div>
                        <?php if ($options['idehweb_close_button'] == "0") { ?>
                            <a class="close" href="">(x)</a>
                        <?php } ?>
                    </form>
                <?php } ?>
            </div>
            <?php
        } else {
            if ($options['idehweb_redirect_url'])
                wp_redirect(esc_url($options['idehweb_redirect_url']));
            else if ($options['idehweb_login_message'])
                echo esc_html($options['idehweb_login_message']);
            ?>

            <?php
        }
        return ob_get_clean();
    }

}