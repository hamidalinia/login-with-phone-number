<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
trait Admin_Functions
{

    function admin_menu()
    {

        $icon_url = 'dashicons-smartphone';
        $page_hook = add_menu_page(
            __('login setting', 'login-with-phone-number'),
            __('login setting', 'login-with-phone-number'),
            'manage_options',
            'idehweb-lwp',
            array(&$this, 'settings_page'),
            $icon_url
        );
        $page_hook_styles = add_submenu_page('idehweb-lwp', __('Style settings', 'login-with-phone-number'), __('Style Settings', 'login-with-phone-number'), 'manage_options', 'idehweb-lwp-styles', array(&$this, 'style_settings_page'));
        add_submenu_page('idehweb-lwp', __('Text & localization', 'login-with-phone-number'), __('Text & localization', 'login-with-phone-number'), 'manage_options', 'idehweb-lwp-localization', array(&$this, 'localization_settings_page'));
        add_action('admin_print_styles-' . $page_hook, array(&$this, 'admin_custom_css'));
        add_action('admin_print_styles-' . $page_hook_styles, array(&$this, 'admin_custom_css'));
        wp_enqueue_script('idehweb-lwp-admin-select2-js',
            plugins_url('/scripts/select2.full.min.js',
                dirname(__FILE__)),
            array('jquery'),
            true,
            true);
        wp_enqueue_script('idehweb-lwp-admin-chat-js',
            plugins_url('/scripts/chat.js',
                dirname(__FILE__)),
            array('jquery'),
            true,
            true);

    }
    function admin_custom_css()
    {
        wp_enqueue_style('idehweb-lwp-admin',
            plugins_url('/styles/lwp-admin.css',
                dirname(__FILE__)),
            array(),
            '1.8.53','all');

        wp_enqueue_style('idehweb-lwp-admin-select2-style',
            plugins_url('/styles/select2.min.css',
                dirname(__FILE__)),
            array(),
            '1.8.53','all');
    }

    function admin_footer()
    {
        $screen = get_current_screen();
        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_online_support'])) $options['idehweb_online_support'] = '1';
        if (!isset($options['idehweb_usage_tracking'])) $options['idehweb_usage_tracking'] = '1';

        $is_opted_in = $options['idehweb_usage_tracking'] === '1';

//
        if (
            $is_opted_in && isset($screen->id) && $screen->id === 'toplevel_page_idehweb-lwp'
        ) {
            ?>
            <script type="text/javascript">
                (function (c, l, a, r, i, t, y) {
                    c[a] = c[a] || function () {
                        (c[a].q = c[a].q || []).push(arguments)
                    };
                    t = l.createElement(r);
                    t.async = 1;
                    t.src = "https://www.clarity.ms/tag/rvomfxbn04";
                    y = l.getElementsByTagName(r)[0];
                    y.parentNode.insertBefore(t, y);
                })(window, document, "clarity", "script", "rvomfxbn04");
            </script>
            <?php
        }

        if ($options['idehweb_online_support'] == '1' && isset($screen->id) && $screen->id === 'toplevel_page_idehweb-lwp') {
            ?>
            <script type="text/javascript">window.makecrispactivate = 1;</script>
            <?php
        }
    }

    function setting_idehweb_style_background()
    {
        $options = get_option('idehweb_lwp_settings_styles');
        if (!isset($options['idehweb_styles_background'])) $options['idehweb_styles_background'] = '';
        else $options['idehweb_styles_background'] = sanitize_text_field($options['idehweb_styles_background']);
        $image_id = $options['idehweb_styles_background'];
        if (intval($image_id) > 0) {
            $image = wp_get_attachment_image($image_id, 'medium', false, array('id' => 'lwp_media-preview-background-image'));
        } else {
            $image = '<img id="lwp_media-preview-background-image" src="' . plugins_url('../images/default-background.png', __FILE__) . '" />';
        }
//        echo $image;
        echo wp_kses_post($image);

        ?>
        <input type="hidden" name="idehweb_lwp_settings_styles[idehweb_styles_background]" id="lwp_media_background_id"
               value="<?php echo esc_attr($image_id); ?>" class="regular-text"/>
        <input type='button' class="button-primary"
               value="<?php esc_attr_e('Select an image', 'login-with-phone-number'); ?>"
               id="lwp_media_background_manager"/> <?php
    }



    function setting_idehweb_style_background_opacity()
    {
        $options = get_option('idehweb_lwp_settings_styles');
        if (!isset($options['idehweb_styles_background_opacity'])) $options['idehweb_styles_background_opacity'] = '';
        else $options['idehweb_styles_background_opacity'] = sanitize_text_field($options['idehweb_styles_background_opacity']);

        echo '<input type="text" name="idehweb_lwp_settings_styles[idehweb_styles_background_opacity]" class="regular-text" value="' . esc_attr($options['idehweb_styles_background_opacity']) . '" />
		<p class="description">' . esc_html__('value between 0 - 1', 'login-with-phone-number') . '</p>';
    }
    function setting_idehweb_style_background_size()
    {
        $options = get_option('idehweb_lwp_settings_styles');
        if (!isset($options['idehweb_styles_background_size'])) $options['idehweb_styles_background_size'] = '';
        else $options['idehweb_styles_background_size'] = sanitize_text_field($options['idehweb_styles_background_size']);

        echo '<input type="text" name="idehweb_lwp_settings_styles[idehweb_styles_background_size]" class="regular-text" value="' . esc_attr($options['idehweb_styles_background_size']) . '" />
		<p class="description">' . esc_html__('ex: cover, contain, 100%, 100px ...', 'login-with-phone-number') . '</p>';
    }
    function setting_idehweb_style_button_border_color()
    {
        $options = get_option('idehweb_lwp_settings_styles');
        if (!isset($options['idehweb_styles_button_border_color'])) $options['idehweb_styles_button_border_color'] = '#009b9a';
        else $options['idehweb_styles_button_border_color'] = sanitize_text_field($options['idehweb_styles_button_border_color']);

        echo '<input type="color" name="idehweb_lwp_settings_styles[idehweb_styles_button_border_color]" class="regular-text" value="' . esc_attr($options['idehweb_styles_button_border_color']) . '" />
		<p class="description">' . esc_html__('button border color', 'login-with-phone-number') . '</p>';
    }



    function setting_idehweb_style_button_border_radius()
    {
        $options = get_option('idehweb_lwp_settings_styles');
        if (!isset($options['idehweb_styles_button_border_radius'])) $options['idehweb_styles_button_border_radius'] = 'inherit';
        else $options['idehweb_styles_button_border_radius'] = sanitize_text_field($options['idehweb_styles_button_border_radius']);

        echo '<input type="text" name="idehweb_lwp_settings_styles[idehweb_styles_button_border_radius]" class="regular-text" value="' . esc_attr($options['idehweb_styles_button_border_radius']) . '" />
		<p class="description">' . esc_html__('0px 0px 0px 0px', 'login-with-phone-number') . '</p>';
    }

    function setting_idehweb_style_button_border_width()
    {
        $options = get_option('idehweb_lwp_settings_styles');
        if (!isset($options['idehweb_styles_button_border_width'])) $options['idehweb_styles_button_border_width'] = 'inherit';
        else $options['idehweb_styles_button_border_width'] = sanitize_text_field($options['idehweb_styles_button_border_width']);

        echo '<input type="text" name="idehweb_lwp_settings_styles[idehweb_styles_button_border_width]" class="regular-text" value="' . esc_attr($options['idehweb_styles_button_border_width']) . '" />
		<p class="description">' . esc_html__('0px 0px 0px 0px', 'login-with-phone-number') . '</p>';
    }


    function setting_idehweb_style_button_padding()
    {
        $options = get_option('idehweb_lwp_settings_styles');
        if (!isset($options['idehweb_styles_button_padding'])) $options['idehweb_styles_button_padding'] = '';
        else $options['idehweb_styles_button_padding'] = sanitize_text_field($options['idehweb_styles_button_padding']);

        echo '<input type="text" name="idehweb_lwp_settings_styles[idehweb_styles_button_padding]" class="regular-text" value="' . esc_attr($options['idehweb_styles_button_padding']) . '" />
		<p class="description">' . esc_html__('0px 0px 0px 0px', 'login-with-phone-number') . '</p>';
    }


    function setting_idehweb_style_button_background_color2()
    {
        $options = get_option('idehweb_lwp_settings_styles');
        if (!isset($options['idehweb_styles_button_background2'])) $options['idehweb_styles_button_background2'] = '#009b9a';
        else $options['idehweb_styles_button_background2'] = sanitize_text_field($options['idehweb_styles_button_background2']);

        echo '<input type="color" name="idehweb_lwp_settings_styles[idehweb_styles_button_background2]" class="regular-text" value="' . esc_attr($options['idehweb_styles_button_background2']) . '" />
		<p class="description">' . esc_html__('secondary button background color', 'login-with-phone-number') . '</p>';
    }
    function setting_idehweb_style_button_border_color2()
    {
        $options = get_option('idehweb_lwp_settings_styles');
        if (!isset($options['idehweb_styles_button_border_color2'])) $options['idehweb_styles_button_border_color2'] = '#009b9a';
        else $options['idehweb_styles_button_border_color2'] = sanitize_text_field($options['idehweb_styles_button_border_color2']);

        echo '<input type="color" name="idehweb_lwp_settings_styles[idehweb_styles_button_border_color2]" class="regular-text" value="' . esc_attr($options['idehweb_styles_button_border_color2']) . '" />
		<p class="description">' . esc_html__('secondary button border color', 'login-with-phone-number') . '</p>';
    }


    function setting_idehweb_style_button_border_radius2()
    {
        $options = get_option('idehweb_lwp_settings_styles');
        if (!isset($options['idehweb_styles_button_border_radius2'])) $options['idehweb_styles_button_border_radius2'] = 'inherit';
        else $options['idehweb_styles_button_border_radius2'] = sanitize_text_field($options['idehweb_styles_button_border_radius2']);

        echo '<input type="text" name="idehweb_lwp_settings_styles[idehweb_styles_button_border_radius2]" class="regular-text" value="' . esc_attr($options['idehweb_styles_button_border_radius2']) . '" />
		<p class="description">' . esc_html__('0px 0px 0px 0px', 'login-with-phone-number') . '</p>';
    }


    function setting_idehweb_style_button_border_width2()
    {
        $options = get_option('idehweb_lwp_settings_styles');
        if (!isset($options['idehweb_styles_button_border_width2'])) $options['idehweb_styles_button_border_width2'] = 'inherit';
        else $options['idehweb_styles_button_border_width2'] = sanitize_text_field($options['idehweb_styles_button_border_width2']);
        echo '<input type="text" name="idehweb_lwp_settings_styles[idehweb_styles_button_border_width2]" class="regular-text" value="' . esc_attr($options['idehweb_styles_button_border_width2']) . '" />
		<p class="description">' . esc_html__('0px 0px 0px 0px', 'login-with-phone-number') . '</p>';
    }


    function setting_idehweb_style_button_text_color2()
    {
        $options = get_option('idehweb_lwp_settings_styles');
        if (!isset($options['idehweb_styles_button_text_color2'])) $options['idehweb_styles_button_text_color2'] = '#ffffff';
        else $options['idehweb_styles_button_text_color2'] = sanitize_text_field($options['idehweb_styles_button_text_color2']);
        echo '<input type="color" name="idehweb_lwp_settings_styles[idehweb_styles_button_text_color2]" class="regular-text" value="' . esc_attr($options['idehweb_styles_button_text_color2']) . '" />
		<p class="description">' . esc_html__('secondary button text color', 'login-with-phone-number') . '</p>';
    }

    function setting_idehweb_style_input_background_color()
    {
        $options = get_option('idehweb_lwp_settings_styles');
        if (!isset($options['idehweb_styles_input_background'])) $options['idehweb_styles_input_background'] = '#009b9a';
        else $options['idehweb_styles_input_background'] = sanitize_text_field($options['idehweb_styles_input_background']);
        echo '<input type="color" name="idehweb_lwp_settings_styles[idehweb_styles_input_background]" class="regular-text" value="' . esc_attr($options['idehweb_styles_input_background']) . '" />
		<p class="description">' . esc_html__('input background color', 'login-with-phone-number') . '</p>';
    }

    function setting_idehweb_style_input_border_color()
    {
        $options = get_option('idehweb_lwp_settings_styles');
        if (!isset($options['idehweb_styles_input_border_color'])) $options['idehweb_styles_input_border_color'] = '#009b9a';
        else $options['idehweb_styles_input_border_color'] = sanitize_text_field($options['idehweb_styles_input_border_color']);

        echo '<input type="color" name="idehweb_lwp_settings_styles[idehweb_styles_input_border_color]" class="regular-text" value="' . esc_attr($options['idehweb_styles_input_border_color']) . '" />
		<p class="description">' . esc_html__('input border color', 'login-with-phone-number') . '</p>';
    }

    function setting_idehweb_style_input_border_radius()
    {
        $options = get_option('idehweb_lwp_settings_styles');
        if (!isset($options['idehweb_styles_input_border_radius'])) $options['idehweb_styles_input_border_radius'] = 'inherit';
        else $options['idehweb_styles_input_border_radius'] = sanitize_text_field($options['idehweb_styles_input_border_radius']);
        echo '<input type="text" name="idehweb_lwp_settings_styles[idehweb_styles_input_border_radius]" class="regular-text" value="' . esc_attr($options['idehweb_styles_input_border_radius']) . '" />
		<p class="description">' . esc_html__('0px 0px 0px 0px', 'login-with-phone-number') . '</p>';
    }

    function setting_idehweb_style_input_border_width()
    {
        $options = get_option('idehweb_lwp_settings_styles');
        if (!isset($options['idehweb_styles_input_border_width'])) $options['idehweb_styles_input_border_width'] = '1px';
        else $options['idehweb_styles_input_border_width'] = sanitize_text_field($options['idehweb_styles_input_border_width']);

        echo '<input type="text" name="idehweb_lwp_settings_styles[idehweb_styles_input_border_width]" class="regular-text" value="' . esc_attr($options['idehweb_styles_input_border_width']) . '" />
		<p class="description">' . esc_html__('0px 0px 0px 0px', 'login-with-phone-number') . '</p>';
    }
    function setting_idehweb_style_input_text_color()
    {
        $options = get_option('idehweb_lwp_settings_styles');
        if (!isset($options['idehweb_styles_input_text_color'])) $options['idehweb_styles_input_text_color'] = '#000000';
        echo '<input type="color" name="idehweb_lwp_settings_styles[idehweb_styles_input_text_color]" class="regular-text" value="' . esc_attr($options['idehweb_styles_input_text_color']) . '" />
		<p class="description">' . esc_html__('input text color', 'login-with-phone-number') . '</p>';
    }

    function setting_idehweb_style_input_padding()
    {
        $options = get_option('idehweb_lwp_settings_styles');
        if (!isset($options['idehweb_styles_input_padding'])) $options['idehweb_styles_input_padding'] = '';
        else $options['idehweb_styles_input_padding'] = sanitize_text_field($options['idehweb_styles_input_padding']);

        echo '<input type="text" name="idehweb_lwp_settings_styles[idehweb_styles_input_padding]" class="regular-text" value="' . esc_attr($options['idehweb_styles_input_padding']) . '" />
		<p class="description">' . esc_html__('0px 0px 0px 0px', 'login-with-phone-number') . '</p>';
    }

    function setting_idehweb_style_input_placeholder_color()
    {
        $options = get_option('idehweb_lwp_settings_styles');
        if (!isset($options['idehweb_styles_input_placeholder_color'])) $options['idehweb_styles_input_placeholder_color'] = '#000000';
        echo '<input type="color" name="idehweb_lwp_settings_styles[idehweb_styles_input_placeholder_color]" class="regular-text" value="' . esc_attr($options['idehweb_styles_input_placeholder_color']) . '" />
		<p class="description">' . esc_html__('input placeholder color', 'login-with-phone-number') . '</p>';
    }

    function setting_idehweb_style_box_background_color()
    {
        $options = get_option('idehweb_lwp_settings_styles');
        if (!isset($options['idehweb_styles_box_background_color'])) $options['idehweb_styles_box_background_color'] = '#ffffff';
        else $options['idehweb_styles_box_background_color'] = sanitize_text_field($options['idehweb_styles_box_background_color']);
        echo '<input type="color" name="idehweb_lwp_settings_styles[idehweb_styles_box_background_color]" class="regular-text" value="' . esc_attr($options['idehweb_styles_box_background_color']) . '" />
		<p class="description">' . esc_html__('box background color', 'login-with-phone-number') . '</p>';
    }

    function setting_idehweb_style_labels_text_color()
    {
        $options = get_option('idehweb_lwp_settings_styles');
        if (!isset($options['idehweb_styles_labels_text_color'])) $options['idehweb_styles_labels_text_color'] = '#000000';
        else $options['idehweb_styles_labels_text_color'] = sanitize_text_field($options['idehweb_styles_labels_text_color']);

        echo '<input type="color" name="idehweb_lwp_settings_styles[idehweb_styles_labels_text_color]" class="regular-text" value="' . esc_attr($options['idehweb_styles_labels_text_color']) . '" />
		<p class="description">' . esc_html__('label text color', 'login-with-phone-number') . '</p>';
    }
    function setting_idehweb_style_labels_font_size()
    {
        $options = get_option('idehweb_lwp_settings_styles');
        if (!isset($options['idehweb_styles_labels_font_size'])) $options['idehweb_styles_labels_font_size'] = 'inherit';
        else $options['idehweb_styles_labels_font_size'] = sanitize_text_field($options['idehweb_styles_labels_font_size']);

        echo '<input type="text" name="idehweb_lwp_settings_styles[idehweb_styles_labels_font_size]" class="regular-text" value="' . esc_attr($options['idehweb_styles_labels_font_size']) . '" />
		<p class="description">' . esc_html__('13px', 'login-with-phone-number') . '</p>';
    }

    function setting_idehweb_style_title_color()
    {
        $options = get_option('idehweb_lwp_settings_styles');
        if (!isset($options['idehweb_styles_title_color'])) $options['idehweb_styles_title_color'] = '#000000';
        else $options['idehweb_styles_title_color'] = sanitize_text_field($options['idehweb_styles_title_color']);
        echo '<input type="color" name="idehweb_lwp_settings_styles[idehweb_styles_title_color]" class="regular-text" value="' . esc_attr($options['idehweb_styles_title_color']) . '" />
		<p class="description">' . esc_html__('label text color', 'login-with-phone-number') . '</p>';
    }

    function setting_idehweb_style_title_font_size()
    {
        $options = get_option('idehweb_lwp_settings_styles');
        if (!isset($options['idehweb_styles_title_font_size'])) $options['idehweb_styles_title_font_size'] = 'inherit';
        else $options['idehweb_styles_title_font_size'] = sanitize_text_field($options['idehweb_styles_title_font_size']);
        echo '<input type="text" name="idehweb_lwp_settings_styles[idehweb_styles_title_font_size]" class="regular-text" value="' . esc_attr($options['idehweb_styles_title_font_size']) . '" />
		<p class="description">' . esc_html__('20px', 'login-with-phone-number') . '</p>';
    }


    function setting_idehweb_sms_login()
    {
        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_sms_login'])) $options['idehweb_sms_login'] = '1';
        $display = 'inherit';
        if (!isset($options['idehweb_phone_number'])) $options['idehweb_phone_number'] = '';
        if (!$options['idehweb_phone_number']) {
            $display = 'none';
        }
        echo '<input  type="hidden" name="idehweb_lwp_settings[idehweb_sms_login]" value="0" />
		<label><input type="checkbox" id="idehweb_lwp_settings_idehweb_sms_login" name="idehweb_lwp_settings[idehweb_sms_login]" value="1"' . (($options['idehweb_sms_login']) ? ' checked="checked"' : '') . ' />' . esc_html__('I want user login with phone number', 'login-with-phone-number') . '</label>';

    }

    function setting_country_code()
    {
        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_country_codes'])) $options['idehweb_country_codes'] = ["uk"];
        $country_codes = $this->get_country_code_options();
//        print_r($options['idehweb_country_codes']);
        ?>
        <select name="idehweb_lwp_settings[idehweb_country_codes][]" id="idehweb_country_codes" multiple>
            <?php
            foreach ($country_codes as $country) {
                $rr = in_array($country["code"], $options['idehweb_country_codes']);
                echo '<option value="' . esc_attr($country["code"]) . '" ' . ($rr ? ' selected="selected"' : '') . '>' . esc_html($country['label']) . '</option>';
            }
            ?>
        </select>
        <?php

    }
    function setting_country_code_default()
    {
        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_country_codes_default'])) $options['idehweb_country_codes_default'] = "";
        $country_codes = $this->get_country_code_options();
//        print_r($country_codes);

        ?>
        <select name="idehweb_lwp_settings[idehweb_country_codes_default]" id="idehweb_country_codes_default">
            <option selected="selected" value="">select default country</option>
            <?php
            if ($options['idehweb_country_codes'])
                foreach ($country_codes as $country) {
                    if (in_array($country["code"], $options['idehweb_country_codes'])) {
                        $rr = ($country["code"] == $options['idehweb_country_codes_default']);
                        echo '<option value="' . esc_attr($country["code"]) . '" ' . ($rr ? ' selected="selected"' : '') . '>' . esc_html($country['label']) . '</option>';
                    } else {

                    }
                }
            ?>
        </select>
        <!--        <p class="description">note: if you change accepted countries, you update this after save.</p>-->
        <?php

    }
    function setting_idehweb_token()
    {
        $options = get_option('idehweb_lwp_settings');
        $display = 'inherit';
        if (!isset($options['idehweb_token'])) $options['idehweb_token'] = '';
        if (!isset($options['idehweb_phone_number'])) $options['idehweb_phone_number'] = '';
        if (!$options['idehweb_phone_number']) {
            $display = 'none';
        }
        echo '<input id="lwp_token_api_key" type="text" name="idehweb_lwp_settings[idehweb_token]" class="regular-text" value="' . esc_attr($options['idehweb_token']) . '" />
		<p class="description">' . esc_html__('enter api key', 'login-with-phone-number') . '</p>';

    }

    function setting_default_gateways()
    {
        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_default_gateways'])) {
            $options['idehweb_default_gateways'] = ['custom'];
        }

        $gateways = [
            ["value" => "firebase", "label" => __("Firebase (Google)", 'login-with-phone-number')],
            ["value" => "custom", "label" => __("Custom (Config Your Gateway)", 'login-with-phone-number')],
//            ["value" => "twilio", "label" => __("Twilio (PRO)", 'login-with-phone-number')],
//            ["value" => "whatsapp", "label" => __("Whatsapp Meta (PRO)", 'login-with-phone-number')],
//            ["value" => "ultramsg", "label" => __("Ultramsg - Whatsapp third-party (PRO)", 'login-with-phone-number')],
//            ["value" => "telegram", "label" => __("Telegram (PRO)", 'login-with-phone-number')],
//            ["value" => "alibabacloud", "label" => __("Alibabacloud (PRO)", 'login-with-phone-number')],
//            ["value" => "2factor", "label" => __("2factor (PRO)", 'login-with-phone-number')],
//            ["value" => "farazsms", "label" => __("Farazsms (PRO)", 'login-with-phone-number')],
//            ["value" => "kavenegar", "label" => __("Kavenegar (PRO)", 'login-with-phone-number')],
//            ["value" => "mellipayamak", "label" => __("Mellipayamak (PRO)", 'login-with-phone-number')],
//            ["value" => "smsir", "label" => __("SMS.ir (PRO)", 'login-with-phone-number')],
//            ["value" => "messageBird", "label" => __("MessageBird (PRO)", 'login-with-phone-number')],
//            ["value" => "msg91", "label" => __("Msg91 (PRO)", 'login-with-phone-number')],
//            ["value" => "mshastra", "label" => __("Mshastra (PRO)", 'login-with-phone-number')],
//            ["value" => "netgsm", "label" => __("Netgsm (PRO)", 'login-with-phone-number')],
//            ["value" => "taqnyat", "label" => __("Taqnyat (PRO)", 'login-with-phone-number')],
//            ["value" => "textlocal", "label" => __("Textlocal (PRO)", 'login-with-phone-number')],
//            ["value" => "trustsignal", "label" => __("Trustsignal (PRO)", 'login-with-phone-number')],
//            ["value" => "vonage", "label" => __("Vonage (PRO)", 'login-with-phone-number')],
//            ["value" => "system", "label" => __("System default", 'login-with-phone-number')],

        ];

        $gateways = apply_filters('lwp_add_to_default_gateways', $gateways);
// Sort gateways by the first letter of the label.
        usort($gateways, function ($a, $b) {
            return strcasecmp($a['label'][0], $b['label'][0]);
        });
        //        $affected_rows = [];
//        $affected_rows = apply_filters('lwp_add_to_default_gateways', $affected_rows);
//        if (!isset($options['idehweb_default_gateways'])) $options['idehweb_default_gateways'] = ['firebase'];
//        $gateways = [
//            ["value" => "firebase", "label" => __("Firebase (Google)", 'login-with-phone-number')],
//            ["value" => "custom", "label" => __("Custom (Config Your Gateway)", 'login-with-phone-number')],
//            ["value" => "twilio", "label" => __("Twilio (Pro)", 'login-with-phone-number')],
//        ];
//        $gateways = array_merge($gateways, $affected_rows);
        ?>
        <div class="idehweb_default_gateways_wrapper">

            <select name="idehweb_lwp_settings[idehweb_default_gateways][]" id="idehweb_default_gateways" multiple>
                <?php
                foreach ($gateways as $gateway) {
                    $rr = false;
                    if (!is_array($options['idehweb_default_gateways'])) {
                        $options['idehweb_default_gateways'] = [];
                    }
                    if (in_array($gateway["value"], $options['idehweb_default_gateways'])) {
//                    if (($gateway["value"] == $options['idehweb_default_gateways'])) {
                        $rr = true;
                    }
                    echo '<option value="' . esc_attr($gateway["value"]) . '" ' . ($rr ? ' selected="selected"' : '') . '>' . esc_html($gateway['label']) . '</option>';
                }
                ?>
            </select>
        </div>
        <?php

    }


    function setting_firebase_api()
    {

        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_firebase_api'])) $options['idehweb_firebase_api'] = '';

        echo '<input type="text" name="idehweb_lwp_settings[idehweb_firebase_api]" class="regular-text" value="' . esc_attr($options['idehweb_firebase_api']) . '" />
		<p class="description">' . esc_html__('enter Firebase api', 'login-with-phone-number') . ' - <a  href="'.esc_url('https://idehweb.com/send-10000-free-otp-sms-with-firebase-in-login-with-phone-number-wordpress-plugin/').'" target="_blank">' . esc_html__('Firebase config help - documentation', 'login-with-phone-number') . '</a></p>';
    }

    function setting_firebase_config()
    {
        // Fetch saved options
        $options = get_option('idehweb_lwp_settings');

        // If the Firebase config option doesn't exist, initialize it as an empty string
        if (!isset($options['idehweb_firebase_config'])) {
            $options['idehweb_firebase_config'] = '';
        } else {
            // Sanitize the input value to avoid any malicious code injection
            $options['idehweb_firebase_config'] = sanitize_textarea_field($options['idehweb_firebase_config']);

            // Clean the Firebase config code (if needed)
            $options['idehweb_firebase_config'] = $this->setting_clean_firebase_config_code($options['idehweb_firebase_config']);
        }

        // Display the Firebase config in the textarea (escape it for output to the HTML)
        echo '<textarea name="idehweb_lwp_settings[idehweb_firebase_config]" class="regular-text">' . esc_textarea($options['idehweb_firebase_config']) . '</textarea>
    <p class="description">' . esc_html__('Enter Firebase config', 'login-with-phone-number') . '</p>';
    }

    function setting_clean_firebase_config_code($str)
    {
        // If $str is not a string, return false
        if (!is_string($str) || empty($str)) {
            return false;
        }

        // Split the input at "const firebaseConfig"
        $exploded = explode("const firebaseConfig", $str);
        $array_length = count($exploded);

        // Remove unwanted JavaScript code
        $otem = str_replace(
            ['javascript', 'alert', 'document', 'cookie', 'script'],
            '',
            $exploded[$array_length - 1]
        );

        // Clean the code after "};"
        $explodedLast = explode("};", $otem);
        $otem = $explodedLast[0] . "}";

        // Remove unnecessary spaces and newlines
        $otem = str_replace(['{ ', ' }'], ['{', '}'], $otem);
        $otem = preg_replace('!\s+!', ' ', $otem);
        $otem = str_replace(["\r", "\n", "\r\n"], " ", $otem);

        // Safely parse the code by trimming and returning JSON
        $explodedwithoteq = explode("=", $otem);
        $beObj = trim($explodedwithoteq[count($explodedwithoteq) - 1]);

        // Return the cleaned code as JSON
        $beObj = $this->return_json($beObj);

        // Return the cleaned firebase config string
        return "const firebaseConfig = " . $beObj . ";";
    }
    function return_json($str)
    {
        // Return null if input string is empty
        if (empty($str)) {
            return null;
        }

        // Try to decode the string as JSON and return it directly if valid
        $r_data = json_decode($str, true);
        if ($r_data && json_last_error() === JSON_ERROR_NONE) {
            return json_encode($r_data, JSON_UNESCAPED_SLASHES); // Return valid JSON
        }

        // Initialize an empty array to store Firebase config
        $obj = [];

        // Use regular expressions to extract Firebase configuration details
        preg_match('/apiKey: "([^"]+)"/', $str, $m0);
        if (!empty($m0[1])) $obj["apiKey"] = sanitize_text_field($m0[1]);

        preg_match('/authDomain: "([^"]+)"/', $str, $j0);
        if (!empty($j0[1])) $obj["authDomain"] = sanitize_text_field($j0[1]);

        preg_match('/projectId: "([^"]+)"/', $str, $h0);
        if (!empty($h0[1])) $obj["projectId"] = sanitize_text_field($h0[1]);

        preg_match('/storageBucket: "([^"]+)"/', $str, $d0);
        if (!empty($d0[1])) $obj["storageBucket"] = sanitize_text_field($d0[1]);

        preg_match('/messagingSenderId: "([^"]+)"/', $str, $x0);
        if (!empty($x0[1])) $obj["messagingSenderId"] = sanitize_text_field($x0[1]);

        preg_match('/appId: "([^"]+)"/', $str, $a0);
        if (!empty($a0[1])) $obj["appId"] = sanitize_text_field($a0[1]);

        // Return the extracted data as a JSON string, escaping special characters
        return json_encode($obj, JSON_UNESCAPED_SLASHES);
    }

//    function setting_clean_firebase_config_code($str)
//    {
//        // If $str is not a string, return false
//        if (!is_string($str) || empty($str)) {
//            return false;
//        }
//
//        $exploded = explode("const firebaseConfig", $str);
//        $array_length = count($exploded);
//
//
//        $otem = str_replace('javascript', '', $exploded[$array_length - 1]);
//        $otem = str_replace('alert', '', $otem);
//        $otem = str_replace('document', '', $otem);
//        $otem = str_replace('cookie', '', $otem);
//        $otem = str_replace('script', '', $otem);
////        print_r('$otem1');
////        print_r($exploded);
//        $explodedLast = explode("};", $otem);
//        $otem = $explodedLast[0] . "}";
//        $otem = str_replace('{ ', '{', $otem);
//        $otem = str_replace(' }', '}', $otem);
//        $searches = array("\r", "\n", "\r\n");
//        $otem = str_replace($searches, " ", $otem);
//        $otem = preg_replace('!\s+!', ' ', $otem);
//        $explodedwithoteq = explode("=", $otem);
//        $beObj = trim($explodedwithoteq[count($explodedwithoteq) - 1]);
//        $beObj = $this->return_json($beObj);
//        return "const firebaseConfig = " . $beObj . ";";
//    }


    function setting_idehweb_phone_number()
    {
        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_phone_number'])) $options['idehweb_phone_number'] = '';
        if (!isset($options['idehweb_phone_number_ccode'])) $options['idehweb_phone_number_ccode'] = '';
        ?>
        <div class="idehweb_phone_number_ccode_wrap">
            <select name="idehweb_lwp_settings[idehweb_phone_number_ccode]" id="idehweb_phone_number_ccode"
                    data-placeholder="<?php esc_attr_e('Choose a country...', 'login-with-phone-number'); ?>">
                <?php
                $country_codes = $this->get_country_code_options();

                foreach ($country_codes as $country) {
                    echo '<option value="' . esc_attr($country["code"]) . '" ' . (($options['idehweb_phone_number_ccode'] == $country["code"]) ? ' selected="selected"' : '') . ' >+' . esc_html($country['value']) . ' - ' . esc_html($country["code"]) . '</option>';
                }
                ?>
            </select>
            <?php
            echo '<input placeholder="' . esc_attr__('Ex: 9120539945', 'login-with-phone-number') . '" type="text" name="idehweb_lwp_settings[idehweb_phone_number]" id="lwp_phone_number" class="regular-text" value="' . esc_attr($options['idehweb_phone_number']) . '" />';
            ?>
        </div>
        <?php
        echo '<input type="text"  name="idehweb_lwp_settings[idehweb_secod]" id="lwp_secod" class="regular-text" style="display:none" value="" placeholder="_ _ _ _ _ _"   />';
        ?>
        <button type="button" class="button-primary auth i35"
                value="<?php esc_attr_e('Authenticate', 'login-with-phone-number'); ?>"><?php esc_html_e('Activate SMS login', 'login-with-phone-number'); ?></button>
        <button type="button" class="button-primary activate i34" style="display: none"
                value="<?php esc_attr_e('Activate', 'login-with-phone-number'); ?>"><?php esc_html_e('Activate account', 'login-with-phone-number'); ?></button>

        <?php
    }




    function settings_get_site_url()
    {
        $url = get_site_url();
        $disallowed = array('http://', 'https://', 'https://www.', 'http://www.', 'www.');
        foreach ($disallowed as $d) {
            if (strpos($url, $d) === 0) {
                return str_replace($d, '', $url);
            }
        }
        return $url;

    }


    function setting_custom_api_url()
    {

        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_custom_api_url'])) $options['idehweb_custom_api_url'] = '';

        echo '<input type="text" name="idehweb_lwp_settings[idehweb_custom_api_url]" class="regular-text" value="' . esc_attr($options['idehweb_custom_api_url']) . '" />
		<p class="description">' . esc_html__('enter custom url', 'login-with-phone-number') . ' - <a  href="'.esc_url('https://idehweb.com/how-to-set-up-a-custom-gateway/').'" target="_blank">' . esc_html__('Custom config help - documentation', 'login-with-phone-number') . '</a></p>';
    }

    function setting_custom_api_method()
    {

        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_custom_api_method'])) $options['idehweb_custom_api_method'] = '';
        else $options['idehweb_custom_api_method'] = sanitize_textarea_field($options['idehweb_custom_api_method']);
//        print_r($options['idehweb_custom_api_method']);
        ?>
        <select name="idehweb_lwp_settings[idehweb_custom_api_method]" id="idehweb_custom_api_method">
            <?php
            foreach (['GET', 'POST'] as $gateway) {
                $rr = false;
//                if(is_array($options['idehweb_default_gateways']))
                if (($gateway == $options['idehweb_custom_api_method'])) {
                    $rr = true;
                }
                echo '<option value="' . esc_attr($gateway) . '" ' . ($rr ? ' selected="selected"' : '') . '>' . esc_html($gateway) . '</option>';
            }
            ?>
        </select>
        <?php
        echo '<p class="description">' . esc_html__('enter request method', 'login-with-phone-number') . '</p>';
    }

    function setting_custom_api_header()
    {

        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_custom_api_header'])) $options['idehweb_custom_api_header'] = '';
        else $options['idehweb_custom_api_header'] = sanitize_textarea_field($options['idehweb_custom_api_header']);

        echo '<textarea name="idehweb_lwp_settings[idehweb_custom_api_header]" class="regular-text">' . esc_attr($options['idehweb_custom_api_header']) . '</textarea>
		<p class="description">' . esc_html__('enter header of request in json', 'login-with-phone-number') . '</p>';
    }


    function setting_custom_api_body()
    {

        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_custom_api_body'])) $options['idehweb_custom_api_body'] = '';
        else $options['idehweb_custom_api_body'] = sanitize_textarea_field($options['idehweb_custom_api_body']);

        echo '<textarea name="idehweb_lwp_settings[idehweb_custom_api_body]" class="regular-text">' . esc_attr($options['idehweb_custom_api_body']) . '</textarea>
		<p class="description">' . esc_html__('enter body of request in json', 'login-with-phone-number') . '</p>';
    }

    function setting_custom_api_smstext()
    {

        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_custom_api_smstext'])) $options['idehweb_custom_api_smstext'] = '';
        else $options['idehweb_custom_api_smstext'] = sanitize_textarea_field($options['idehweb_custom_api_smstext']);

        echo '<textarea name="idehweb_lwp_settings[idehweb_custom_api_smstext]" class="regular-text">' . esc_attr($options['idehweb_custom_api_smstext']) . '</textarea>
		<p class="description">' . esc_html__('enter smstext , you can use ${code}', 'login-with-phone-number') . '</p>';
    }

    function setting_idehweb_email_login()
    {
        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_email_login'])) $options['idehweb_email_login'] = '1';
        $display = 'inherit';
        if (!isset($options['idehweb_phone_number'])) $options['idehweb_phone_number'] = '';
        if (!$options['idehweb_phone_number']) {
            $display = 'none';
        }
        echo '<input  type="hidden" name="idehweb_lwp_settings[idehweb_email_login]" value="0" />
		<label><input type="checkbox" name="idehweb_lwp_settings[idehweb_email_login]" value="1"' . (($options['idehweb_email_login']) ? ' checked="checked"' : '') . ' />' . esc_html__('I want user login with email', 'login-with-phone-number') . '</label>';

    }


    function setting_idehweb_email_force()
    {
        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_email_force_after_phonenumber'])) $options['idehweb_email_force_after_phonenumber'] = '1';

        echo '<input  type="hidden" name="idehweb_lwp_settings[idehweb_email_force_after_phonenumber]" value="0" />
		<label><input type="checkbox" name="idehweb_lwp_settings[idehweb_email_force_after_phonenumber]" value="1"' . (($options['idehweb_email_force_after_phonenumber']) ? ' checked="checked"' : '') . ' />' . esc_html__('I want user enter email after verifying phone number', 'login-with-phone-number') . '</label>';

    }



    function setting_idehweb_user_registration()
    {
        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_user_registration'])) $options['idehweb_user_registration'] = '0';

        echo '<input type="hidden" name="idehweb_lwp_settings[idehweb_user_registration]" value="0" />
		<label><input type="checkbox" name="idehweb_lwp_settings[idehweb_user_registration]" value="1"' . (($options['idehweb_user_registration']) ? ' checked="checked"' : '') . ' />' . esc_html__('I want to enable registration', 'login-with-phone-number') . '</label>';

    }



    function setting_idehweb_password_login()
    {
        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_password_login'])) {
            $options['idehweb_password_login'] = '1';
        }

        $display = 'inherit';
        if (!isset($options['idehweb_phone_number'])) {
            $options['idehweb_phone_number'] = '';
        }
        if (!$options['idehweb_phone_number']) {
            $display = 'none';
        }

        echo '<input type="hidden" name="idehweb_lwp_settings[idehweb_password_login]" value="0" />
    <label><input type="checkbox" name="idehweb_lwp_settings[idehweb_password_login]" value="1"' . (($options['idehweb_password_login']) ? ' checked="checked"' : '') . ' />' . esc_html__('I want user login with password too', 'login-with-phone-number') . '</label>';
    }

    function setting_idehweb_url_redirect()
    {
        $options = get_option('idehweb_lwp_settings');
        $display = 'inherit';
        if (!isset($options['idehweb_redirect_url'])) $options['idehweb_redirect_url'] = '';
        if (!isset($options['idehweb_phone_number'])) $options['idehweb_phone_number'] = '';
        if (!$options['idehweb_phone_number']) {
            $display = 'none';
        }
        echo '<input id="lwp_redirect_url" type="text" name="idehweb_lwp_settings[idehweb_redirect_url]" class="regular-text" value="' . esc_attr($options['idehweb_redirect_url']) . '" />
		<p class="description">' . esc_html__('enter redirect url', 'login-with-phone-number') . '</p>';

    }

    function setting_idehweb_length_of_activation_code()
    {
        $options = get_option('idehweb_lwp_settings');

        if (!isset($options['idehweb_length_of_activation_code'])) $options['idehweb_length_of_activation_code'] = '6';

        echo '<input type="text" name="idehweb_lwp_settings[idehweb_length_of_activation_code]" class="regular-text" value="' . esc_attr($options['idehweb_length_of_activation_code']) . '" />
		<p class="description">' . esc_html__('enter length of activation code', 'login-with-phone-number') . '</p>';

    }
    function setting_idehweb_login_message()
    {
        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_login_message'])) $options['idehweb_login_message'] = 'Welcome, You are logged in...';
        echo '<input id="lwp_login_messages" type="text" name="idehweb_lwp_settings[idehweb_login_message]" class="regular-text" value="' . esc_attr($options['idehweb_login_message']) . '" />
		<p class="description">' . esc_html__('enter login message', 'login-with-phone-number') . '</p>';

    }


    function idehweb_use_phone_number_for_username()
    {
        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_use_phone_number_for_username'])) $options['idehweb_use_phone_number_for_username'] = '0';

        echo '<input type="hidden" name="idehweb_lwp_settings[idehweb_use_phone_number_for_username]" value="0" />
		<label><input type="checkbox" id="idehweb_lwp_settings_use_phone_number_for_username" name="idehweb_lwp_settings[idehweb_use_phone_number_for_username]" value="1"' . (($options['idehweb_use_phone_number_for_username']) ? ' checked="checked"' : '') . ' />' . esc_html__('I want to set phone number as username and nickname', 'login-with-phone-number') . '</label>';

    }



    function setting_default_username()
    {
        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_default_username'])) $options['idehweb_default_username'] = 'user';

        echo '<input id="lwp_default_username" type="text" name="idehweb_lwp_settings[idehweb_default_username]" class="regular-text" value="' . esc_attr($options['idehweb_default_username']) . '" />
		<p class="description">' . esc_html__('Default username', 'login-with-phone-number') . '</p>';

    }

    function setting_default_nickname()
    {
        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_default_nickname'])) $options['idehweb_default_nickname'] = 'user';


        echo '<input id="lwp_default_nickname" type="text" name="idehweb_lwp_settings[idehweb_default_nickname]" class="regular-text" value="' . esc_attr($options['idehweb_default_nickname']) . '" />
		<p class="description">' . esc_html__('Default nickname', 'login-with-phone-number') . '</p>';

    }


    function idehweb_enable_timer_on_sending_sms()
    {
        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_enable_timer_on_sending_sms'])) $options['idehweb_enable_timer_on_sending_sms'] = '1';

        echo '<input type="hidden" name="idehweb_lwp_settings[idehweb_enable_timer_on_sending_sms]" value="0" />
		<label><input type="checkbox" id="idehweb_lwp_settings_enable_timer_on_sending_sms" name="idehweb_lwp_settings[idehweb_enable_timer_on_sending_sms]" value="1"' . (($options['idehweb_enable_timer_on_sending_sms']) ? ' checked="checked"' : '') . ' />' . esc_html__('I want to enable timer after user entered phone number and clicked on submit', 'login-with-phone-number') . '</label>';

    }
    function setting_timer_count()
    {
        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_timer_count'])) $options['idehweb_timer_count'] = '60';


        echo '<input id="lwp_timer_count" type="text" name="idehweb_lwp_settings[idehweb_timer_count]" class="regular-text" value="' . esc_attr($options['idehweb_timer_count']) . '" />
		<p class="description">' . esc_html__('Timer count', 'login-with-phone-number') . '</p>';

    }



    function idehweb_enable_accept_term_and_conditions()
    {
        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_enable_accept_terms_and_condition'])) $options['idehweb_enable_accept_terms_and_condition'] = '1';

        echo '<input type="hidden" name="idehweb_lwp_settings[idehweb_enable_accept_terms_and_condition]" value="0" />
		<label><input type="checkbox" id="idehweb_enable_accept_terms_and_condition" name="idehweb_lwp_settings[idehweb_enable_accept_terms_and_condition]" value="1"' . (($options['idehweb_enable_accept_terms_and_condition']) ? ' checked="checked"' : '') . ' />' . esc_html__('I want to show some terms & conditions for user to accept it, when he/she wants to register ', 'login-with-phone-number') . '</label>';

    }

    function setting_term_and_conditions_text()
    {

        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_term_and_conditions_text'])) $options['idehweb_term_and_conditions_text'] = esc_html__('By submitting, you agree to the Terms and Privacy Policy', 'login-with-phone-number');
        else $options['idehweb_term_and_conditions_text'] = ($options['idehweb_term_and_conditions_text']);
        echo '<textarea name="idehweb_lwp_settings[idehweb_term_and_conditions_text]" class="regular-text">' . esc_attr($options['idehweb_term_and_conditions_text']) . '</textarea>
		<p class="description">' . esc_html__('enter term and condition accepting text', 'login-with-phone-number') . '</p>';
    }

    function setting_term_and_conditions_link()
    {

        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_term_and_conditions_link'])) $options['idehweb_term_and_conditions_link'] = esc_html__('#', 'login-with-phone-number');
        else $options['idehweb_term_and_conditions_link'] = ($options['idehweb_term_and_conditions_link']);
        echo '<textarea name="idehweb_lwp_settings[idehweb_term_and_conditions_link]" class="regular-text">' . esc_attr($options['idehweb_term_and_conditions_link']) . '</textarea>
		<p class="description">' . esc_html__('enter term and condition link', 'login-with-phone-number') . '</p>';
    }

    function setting_term_and_conditions_default_checked()
    {
        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_term_and_conditions_default_checked'])) $options['idehweb_term_and_conditions_default_checked'] = '1';

        echo '<input type="hidden" name="idehweb_lwp_settings[idehweb_term_and_conditions_default_checked]" value="0" />
		<label><input type="checkbox" id="idehweb_term_and_conditions_default_checked" name="idehweb_lwp_settings[idehweb_term_and_conditions_default_checked]" value="1"' . (esc_attr($options['idehweb_term_and_conditions_default_checked']) ? ' checked="checked"' : '') . ' />' . esc_html__('Accept/Check by default. ', 'login-with-phone-number') . '</label>';
    }

    function setting_default_role()
    {
        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_default_role'])) {
            $options['idehweb_default_role'] = "";
        }
        $roles = $this->get_roles();
        ?>
        <select name="<?php echo class_exists(LWP_PRO::class) ? 'idehweb_lwp_settings[idehweb_default_role]' : ''; ?>"
                id="idehweb_default_role">
            <option selected="selected" value=""><?php echo esc_html__('select default role', 'login-with-phone-number'); ?></option>
            <?php

            foreach ($roles as $role) {
                $rr = ($role["role"] == $options['idehweb_default_role']);
                echo '<option value="' . esc_attr($role["role"]) . '" ' . ($rr ? ' selected="selected"' : '') . '>' . esc_html($role['name']) . '</option>';
            }
            ?>
        </select>

        <?php
        echo wp_kses_post($this->setting_idehweb_pro_label());
    }


    function setting_idehweb_style_logo()
    {
        $options = get_option('idehweb_lwp_settings_styles');
        if (!isset($options['idehweb_styles_logo'])) $options['idehweb_styles_logo'] = '';
        else $options['idehweb_styles_logo'] = sanitize_text_field($options['idehweb_styles_logo']);
        $image_id = $options['idehweb_styles_logo'];
        if (intval($image_id) > 0) {
            // Change with the image size you want to use
            $image = wp_get_attachment_image($image_id, 'medium', false, array('id' => 'lwp_media-preview-image'));
        } else {
            // Some default image
            $image = '<img id="lwp_media-preview-image" src="' . plugins_url('../images/default-logo.png', __FILE__) . '" />';
        }
        // Use wp_kses_post() to sanitize and allow the image tag to be rendered.
        echo wp_kses_post($image); ?>
        <input type="hidden" name="idehweb_lwp_settings_styles[idehweb_styles_logo]" id="lwp_media_image_id"
               value="<?php echo esc_attr($image_id); ?>" class="regular-text"/>
        <input type='button' class="button-primary"
               value="<?php esc_attr_e('Select an image', 'login-with-phone-number'); ?>"
               id="lwp_media_media_manager"/> <?php
    }

    function idehweb_close_button()
    {
        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_close_button'])) $options['idehweb_close_button'] = '0';

        echo '<input type="hidden" name="idehweb_lwp_settings[idehweb_close_button]" value="0" />
		<label><input type="checkbox" name="idehweb_lwp_settings[idehweb_close_button]" value="1"' . (($options['idehweb_close_button']) ? ' checked="checked"' : '') . ' />' . esc_html__('I want disable closing action and (x) button on pop up and force user to login', 'login-with-phone-number') . '</label>';

    }

    function idehweb_auto_show_form()
    {
        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_auto_show_form'])) $options['idehweb_auto_show_form'] = '1';

        echo '<input type="hidden" name="idehweb_lwp_settings[idehweb_auto_show_form]" class="idehweb_lwp_auto_show_form"  value="0" />
		<label><input type="checkbox" name="idehweb_lwp_settings[idehweb_auto_show_form]" class="idehweb_lwp_auto_show_form"  value="1"' . (($options['idehweb_auto_show_form']) ? ' checked="checked"' : '') . ' />' . esc_html__('I want the form shows automatically with out clicking any button, also you can use class "lwp-open-form"', 'login-with-phone-number') . '</label>';

    }



    function idehweb_position_form()
    {
        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_position_form'])) $options['idehweb_position_form'] = '0';

        echo '<input type="hidden" name="idehweb_lwp_settings[idehweb_position_form]" class="idehweb_lwp_position_form" value="0" />
		<label><input type="checkbox" name="idehweb_lwp_settings[idehweb_position_form]" class="idehweb_lwp_position_form" value="1"' . (($options['idehweb_position_form']) ? ' checked="checked"' : '') . ' />' . esc_html__('I want form shows on page in fix position', 'login-with-phone-number') . '</label>';

    }

    function idehweb_show_form_all_pages()
    {
        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_show_form_all_pages'])) $options['idehweb_show_form_all_pages'] = '0';

        echo '<input type="hidden" name="idehweb_lwp_settings[idehweb_show_form_all_pages]" class="idehweb_show_form_all_pages" value="0" />';
        echo '<label><input type="checkbox" name="idehweb_lwp_settings[idehweb_show_form_all_pages]" class="idehweb_show_form_all_pages" value="1"' . (($options['idehweb_show_form_all_pages']) ? ' checked="checked"' : '') . ' />' . esc_html__('I want the login/register form to show on all pages', 'login-with-phone-number') . '</label>';

    }
    public function check_sms_gateway_configuration_notice($page)
    {
        // Get the settings
        $options = get_option('idehweb_lwp_settings');

        // Check if the 'idehweb_default_gateways' is set and if it's 'system'
        if (!isset($options['idehweb_default_gateways']) || empty($options['idehweb_default_gateways']) || $options['idehweb_default_gateways'][0] == 'custom') {
            // Check if API key is not filled for 'system'
            $apiKey = isset($options['idehweb_system_api_key']) ? esc_attr($options['idehweb_system_api_key']) : '';

            if (empty($apiKey)) {
                // Show admin notice if the API key is empty
                ?>
                <div class="notice notice-warning is-dismissible">
                    <p><?php printf(
                        /* translators: %1$s: Opening anchor tag for gateway settings, %2$s: Closing anchor tag. */
                            esc_html__('Warning: To enable login via phone number, you need to activate an SMS gateway. For a more efficient and cost-effective solution, consider using the WhatsApp OTP gateway. Check out our WhatsApp packages for more details. %1$sClick here to configure your gateway settings.%2$s', 'login-with-phone-number'), '<a href="' . esc_url(admin_url('admin.php?page=idehweb-lwp#lwp-tab-gateway-settings')) . '" target="_blank">', '</a>'); ?>
                    </p>
                </div>
                <?php
            }
        }
    }

    function lwp_load_wp_media_files($page)
    {

        $localize = array(
            'ajaxurl' => admin_url('admin-ajax.php'),
        );
        $localize['nonce'] = wp_create_nonce('lwp_admin_nonce');

        if ($page == 'login-setting_page_idehweb-lwp-styles') {
            wp_enqueue_media();
            wp_enqueue_script('idehweb-lwp-admin-media-script', plugins_url('/scripts/lwp-admin-style.js', dirname(__FILE__)), array('jquery'), true, true);
            wp_localize_script('idehweb-lwp-admin-media-script', 'lwp_admin_vars', $localize);
        }
        if ($page == 'toplevel_page_idehweb-lwp') {
            wp_enqueue_script('idehweb-lwp-admin-media-script', plugins_url('/scripts/lwp-admin.js', dirname(__FILE__)), array('jquery'), true, true);
            wp_localize_script('idehweb-lwp-admin-media-script', 'lwp_admin_vars', $localize);
        }
    }

    function lwp_media_get_image($page)
    {
        // Verify the nonce. The function handles the sanitization and exit on failure.
        check_ajax_referer('lwp_admin_nonce', 'nonce');

        if (isset($_GET['id'])) {
            $image = wp_get_attachment_image(filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT), 'medium', false, array('id' => 'myprefix-preview-image'));
            $data = array(
                'image' => $image,
            );
            wp_send_json_success($data);
        } else {
            wp_send_json_error();
        }
    }
    function admin_init()
    {
        $this->settings_register();
        $this->style_register();
        $this->localization_register();




        $options = get_option('idehweb_lwp_settings');
        $style_options = get_option('idehweb_lwp_settings_styles');
        if (!$style_options) {
            $style_options = [];
        }

        if (!isset($options['idehweb_token'])) $options['idehweb_token'] = '';
        if (!isset($style_options['idehweb_styles_status'])) $style_options['idehweb_styles_status'] = '0';

        add_settings_section('idehweb-lwp-styles', '', array(&$this, 'section_intro'), 'idehweb-lwp-styles');
        add_settings_section('idehweb-lwp-localization', '', array(&$this, 'section_intro'), 'idehweb-lwp-localization');

        add_settings_field('idehweb_styles_status', esc_html__('Enable custom styles', 'login-with-phone-number'), array(&$this, 'setting_idehweb_style_enable_custom_style'), 'idehweb-lwp-styles', 'idehweb-lwp-styles', ['label_for' => '', 'class' => 'ilwplabel']);
        add_settings_field('idehweb_show_form_all_pages', __('Show login/register form in all pages', 'login-with-phone-number'), array(&$this, 'idehweb_show_form_all_pages'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel lwp-tab-form-settings']);
        add_settings_field('idehweb_position_form', __('Enable fix position', 'login-with-phone-number'), array(&$this, 'idehweb_position_form'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel lwp-tab-form-settings']);
        add_settings_field('idehweb_auto_show_form', __('Enable auto pop up form', 'login-with-phone-number'), array(&$this, 'idehweb_auto_show_form'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel related-to-position-fixed lwp-tab-form-settings']);
        add_settings_field('idehweb_close_form', __('Disable close (X) button', 'login-with-phone-number'), array(&$this, 'idehweb_close_button'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel related-to-position-fixed lwp-tab-form-settings']);

        if ($style_options['idehweb_styles_status']) {
            add_settings_field('idehweb_styles_logo', __('Logo', 'login-with-phone-number'), array(&$this, 'setting_idehweb_style_logo'), 'idehweb-lwp-styles', 'idehweb-lwp-styles', ['label_for' => '', 'class' => 'ilwplabel']);
            add_settings_field('idehweb_styles_background', __('Fix background', 'login-with-phone-number'), array(&$this, 'setting_idehweb_style_background'), 'idehweb-lwp-styles', 'idehweb-lwp-styles', ['label_for' => '', 'class' => 'ilwplabel']);
            add_settings_field('idehweb_styles_background_opacity', __('fix background opacity', 'login-with-phone-number'), array(&$this, 'setting_idehweb_style_background_opacity'), 'idehweb-lwp-styles', 'idehweb-lwp-styles', ['label_for' => '', 'class' => 'ilwplabel']);
            add_settings_field('idehweb_styles_background_size', __('fix background size', 'login-with-phone-number'), array(&$this, 'setting_idehweb_style_background_size'), 'idehweb-lwp-styles', 'idehweb-lwp-styles', ['label_for' => '', 'class' => 'ilwplabel']);


            add_settings_field('idehweb_styles_title', __('Primary button', 'login-with-phone-number'), array(&$this, 'section_title'), 'idehweb-lwp-styles', 'idehweb-lwp-styles', ['label_for' => '', 'class' => 'ilwplabel']);
            add_settings_field('idehweb_styles_button_background', __('button background color', 'login-with-phone-number'), array(&$this, 'setting_idehweb_style_button_background_color'), 'idehweb-lwp-styles', 'idehweb-lwp-styles', ['label_for' => '', 'class' => 'ilwplabel']);
            add_settings_field('idehweb_styles_button_border_color', __('button border color', 'login-with-phone-number'), array(&$this, 'setting_idehweb_style_button_border_color'), 'idehweb-lwp-styles', 'idehweb-lwp-styles', ['label_for' => '', 'class' => 'ilwplabel']);
            add_settings_field('idehweb_styles_button_border_radius', __('button border radius', 'login-with-phone-number'), array(&$this, 'setting_idehweb_style_button_border_radius'), 'idehweb-lwp-styles', 'idehweb-lwp-styles', ['label_for' => '', 'class' => 'ilwplabel']);
            add_settings_field('idehweb_styles_button_border_width', __('button border width', 'login-with-phone-number'), array(&$this, 'setting_idehweb_style_button_border_width'), 'idehweb-lwp-styles', 'idehweb-lwp-styles', ['label_for' => '', 'class' => 'ilwplabel']);
            add_settings_field('idehweb_styles_button_text_color', __('button text color', 'login-with-phone-number'), array(&$this, 'setting_idehweb_style_button_text_color'), 'idehweb-lwp-styles', 'idehweb-lwp-styles', ['label_for' => '', 'class' => 'ilwplabel']);
            add_settings_field('idehweb_styles_button_padding', __('button padding', 'login-with-phone-number'), array(&$this, 'setting_idehweb_style_button_padding'), 'idehweb-lwp-styles', 'idehweb-lwp-styles', ['label_for' => '', 'class' => 'ilwplabel']);

            add_settings_field('idehweb_styles_title2', __('Secondary button', 'login-with-phone-number'), array(&$this, 'section_title'), 'idehweb-lwp-styles', 'idehweb-lwp-styles', ['label_for' => '', 'class' => 'ilwplabel']);

            add_settings_field('idehweb_styles_button_background2', __('secondary button background color', 'login-with-phone-number'), array(&$this, 'setting_idehweb_style_button_background_color2'), 'idehweb-lwp-styles', 'idehweb-lwp-styles', ['label_for' => '', 'class' => 'ilwplabel']);
            add_settings_field('idehweb_styles_button_border_color2', __('secondary button border color', 'login-with-phone-number'), array(&$this, 'setting_idehweb_style_button_border_color2'), 'idehweb-lwp-styles', 'idehweb-lwp-styles', ['label_for' => '', 'class' => 'ilwplabel']);
            add_settings_field('idehweb_styles_button_border_radius2', __('secondary button border radius', 'login-with-phone-number'), array(&$this, 'setting_idehweb_style_button_border_radius2'), 'idehweb-lwp-styles', 'idehweb-lwp-styles', ['label_for' => '', 'class' => 'ilwplabel']);
            add_settings_field('idehweb_styles_button_border_width2', __('secondary button border width', 'login-with-phone-number'), array(&$this, 'setting_idehweb_style_button_border_width2'), 'idehweb-lwp-styles', 'idehweb-lwp-styles', ['label_for' => '', 'class' => 'ilwplabel']);
            add_settings_field('idehweb_styles_button_text_color2', __('secondary button text color', 'login-with-phone-number'), array(&$this, 'setting_idehweb_style_button_text_color2'), 'idehweb-lwp-styles', 'idehweb-lwp-styles', ['label_for' => '', 'class' => 'ilwplabel']);


            add_settings_field('idehweb_styles_title3', __('Inputs', 'login-with-phone-number'), array(&$this, 'section_title'), 'idehweb-lwp-styles', 'idehweb-lwp-styles', ['label_for' => '', 'class' => 'ilwplabel']);

            add_settings_field('idehweb_styles_input_background', __('input background color', 'login-with-phone-number'), array(&$this, 'setting_idehweb_style_input_background_color'), 'idehweb-lwp-styles', 'idehweb-lwp-styles', ['label_for' => '', 'class' => 'ilwplabel']);
            add_settings_field('idehweb_styles_input_border_color', __('input border color', 'login-with-phone-number'), array(&$this, 'setting_idehweb_style_input_border_color'), 'idehweb-lwp-styles', 'idehweb-lwp-styles', ['label_for' => '', 'class' => 'ilwplabel']);
            add_settings_field('idehweb_styles_input_border_radius', __('input border radius', 'login-with-phone-number'), array(&$this, 'setting_idehweb_style_input_border_radius'), 'idehweb-lwp-styles', 'idehweb-lwp-styles', ['label_for' => '', 'class' => 'ilwplabel']);
            add_settings_field('idehweb_styles_input_border_width', __('input border width', 'login-with-phone-number'), array(&$this, 'setting_idehweb_style_input_border_width'), 'idehweb-lwp-styles', 'idehweb-lwp-styles', ['label_for' => '', 'class' => 'ilwplabel']);
            add_settings_field('idehweb_styles_input_text_color', __('input text color', 'login-with-phone-number'), array(&$this, 'setting_idehweb_style_input_text_color'), 'idehweb-lwp-styles', 'idehweb-lwp-styles', ['label_for' => '', 'class' => 'ilwplabel']);
            add_settings_field('idehweb_styles_input_padding', __('input padding', 'login-with-phone-number'), array(&$this, 'setting_idehweb_style_input_padding'), 'idehweb-lwp-styles', 'idehweb-lwp-styles', ['label_for' => '', 'class' => 'ilwplabel']);
            add_settings_field('idehweb_styles_input_placeholder_color', __('input placeholder color', 'login-with-phone-number'), array(&$this, 'setting_idehweb_style_input_placeholder_color'), 'idehweb-lwp-styles', 'idehweb-lwp-styles', ['label_for' => '', 'class' => 'ilwplabel']);

            add_settings_field('idehweb_styles_title4', __('Box', 'login-with-phone-number'), array(&$this, 'section_title'), 'idehweb-lwp-styles', 'idehweb-lwp-styles', ['label_for' => '', 'class' => 'ilwplabel']);
            add_settings_field('idehweb_styles_box_background_color', __('box background color', 'login-with-phone-number'), array(&$this, 'setting_idehweb_style_box_background_color'), 'idehweb-lwp-styles', 'idehweb-lwp-styles', ['label_for' => '', 'class' => 'ilwplabel']);


            add_settings_field('idehweb_styles_title5', __('Labels', 'login-with-phone-number'), array(&$this, 'section_title'), 'idehweb-lwp-styles', 'idehweb-lwp-styles', ['label_for' => '', 'class' => 'ilwplabel']);
            add_settings_field('idehweb_styles_labels_text_color', __('label text color', 'login-with-phone-number'), array(&$this, 'setting_idehweb_style_labels_text_color'), 'idehweb-lwp-styles', 'idehweb-lwp-styles', ['label_for' => '', 'class' => 'ilwplabel']);
            add_settings_field('idehweb_styles_labels_font_size', __('label font size', 'login-with-phone-number'), array(&$this, 'setting_idehweb_style_labels_font_size'), 'idehweb-lwp-styles', 'idehweb-lwp-styles', ['label_for' => '', 'class' => 'ilwplabel']);


            add_settings_field('idehweb_styles_title6', __('Titles', 'login-with-phone-number'), array(&$this, 'section_title'), 'idehweb-lwp-styles', 'idehweb-lwp-styles', ['label_for' => '', 'class' => 'ilwplabel']);
            add_settings_field('idehweb_styles_title_color', __('title color', 'login-with-phone-number'), array(&$this, 'setting_idehweb_style_title_color'), 'idehweb-lwp-styles', 'idehweb-lwp-styles', ['label_for' => '', 'class' => 'ilwplabel']);
            add_settings_field('idehweb_styles_title_font_size', __('title font size', 'login-with-phone-number'), array(&$this, 'setting_idehweb_style_title_font_size'), 'idehweb-lwp-styles', 'idehweb-lwp-styles', ['label_for' => '', 'class' => 'ilwplabel']);


        }

        add_settings_section('idehweb-lwp', '', array(&$this, 'section_intro'), 'idehweb-lwp');

        add_settings_field('idehweb_sms_login', __('Enable phone number login', 'login-with-phone-number'), array(&$this, 'setting_idehweb_sms_login'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel lwp-tab-general-settings']);

        add_settings_field('idehweb_token', __('Enter api key', 'login-with-phone-number'), array(&$this, 'setting_idehweb_token'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel alwaysDisplayNone']);
        add_settings_field('idehweb_country_codes', __('Country code accepted in front', 'login-with-phone-number'), array(&$this, 'setting_country_code'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel related_to_phone_number_login lwp-tab-general-settings']);
        add_settings_field('idehweb_country_codes_default', __('Default Country', 'login-with-phone-number'), array(&$this, 'setting_country_code_default'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel related_to_phone_number_login lwp-tab-general-settings']);
        add_settings_field('idehweb_store_number_with_country_code', __('Store numbers with country code', 'login-with-phone-number'), array(&$this, 'setting_idehweb_store_number_with_country_code'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel lwp-tab-general-settings']);

        add_settings_field('idehweb_default_gateways', __('sms default gateway', 'login-with-phone-number'), array(&$this, 'setting_default_gateways'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel related_to_defaultgateway lwp-tab-gateway-settings']);

        add_settings_field('idehweb_firebase_api', __('Firebase api', 'login-with-phone-number'), array(&$this, 'setting_firebase_api'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel related_to_firebase lwp-tab-gateway-settings']);
        add_settings_field('idehweb_firebase_config', __('Firebase config', 'login-with-phone-number'), array(&$this, 'setting_firebase_config'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel related_to_firebase lwp-tab-gateway-settings']);
        add_settings_field('idehweb_custom_api_url', __('Custom api url', 'login-with-phone-number'), array(&$this, 'setting_custom_api_url'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel related_to_custom lwp-tab-gateway-settings']);
        add_settings_field('idehweb_custom_api_method', __('Custom api method', 'login-with-phone-number'), array(&$this, 'setting_custom_api_method'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel related_to_custom lwp-tab-gateway-settings']);
        add_settings_field('idehweb_custom_api_header', __('Custom api header', 'login-with-phone-number'), array(&$this, 'setting_custom_api_header'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel related_to_custom lwp-tab-gateway-settings']);
        add_settings_field('idehweb_custom_api_body', __('Custom api body', 'login-with-phone-number'), array(&$this, 'setting_custom_api_body'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel related_to_custom lwp-tab-gateway-settings']);
        add_settings_field('idehweb_custom_api_smstext', __('Custom api sms text', 'login-with-phone-number'), array(&$this, 'setting_custom_api_smstext'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel related_to_custom lwp-tab-gateway-settings']);
        do_action('idehweb_custom_fields');

        add_settings_field('idehweb_lwp_space', '', array(&$this, 'setting_idehweb_lwp_space'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel idehweb_lwp_mgt100']);
        add_settings_field('idehweb_email_login', __('Enable email login', 'login-with-phone-number'), array(&$this, 'setting_idehweb_email_login'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel lwp-tab-general-settings']);
        add_settings_field('idehweb_email_force_after_phonenumber', __('Force to get email after phone number', 'login-with-phone-number'), array(&$this, 'setting_idehweb_email_force'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel lwp-tab-general-settings']);
        add_settings_field('idehweb_lwp_space2', '', array(&$this, 'setting_idehweb_lwp_space'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel idehweb_lwp_mgt100']);

        add_settings_field('idehweb_user_registration', __('Enable user registration', 'login-with-phone-number'), array(&$this, 'setting_idehweb_user_registration'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel lwp-tab-general-settings']);
        add_settings_field('idehweb_password_login', __('Enable password login', 'login-with-phone-number'), array(&$this, 'setting_idehweb_password_login'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel lwp-tab-form-settings']);
        add_settings_field('idehweb_redirect_url', __('Enter redirect url', 'login-with-phone-number'), array(&$this, 'setting_idehweb_url_redirect'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel lwp-tab-general-settings']);
        add_settings_field('idehweb_length_of_activation_code', __('Enter length of activation code', 'login-with-phone-number'), array(&$this, 'setting_idehweb_length_of_activation_code'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel lwp-tab-general-settings']);
        add_settings_field('idehweb_login_message', __('Enter login message', 'login-with-phone-number'), array(&$this, 'setting_idehweb_login_message'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel lwp-tab-general-settings']);
        add_settings_field('idehweb_use_phone_number_for_username', __('use phone number for username', 'login-with-phone-number'), array(&$this, 'idehweb_use_phone_number_for_username'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel lwp-tab-general-settings']);
        add_settings_field('idehweb_default_username', __('Default username', 'login-with-phone-number'), array(&$this, 'setting_default_username'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel related_to_upnfu lwp-tab-general-settings']);
        add_settings_field('idehweb_default_nickname', __('Default nickname', 'login-with-phone-number'), array(&$this, 'setting_default_nickname'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel related_to_upnfu lwp-tab-general-settings']);
        add_settings_field('idehweb_enable_timer_on_sending_sms', __('Enable timer', 'login-with-phone-number'), array(&$this, 'idehweb_enable_timer_on_sending_sms'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel lwp-tab-general-settings']);
        add_settings_field('idehweb_timer_count', __('Timer count', 'login-with-phone-number'), array(&$this, 'setting_timer_count'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel related_to_entimer lwp-tab-general-settings']);

        add_settings_field('idehweb_enable_accept_terms_and_condition', __('Enable accept term & conditions', 'login-with-phone-number'), array(&$this, 'idehweb_enable_accept_term_and_conditions'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel lwp-tab-form-settings']);
        add_settings_field('idehweb_term_and_conditions_text', __('Text of term & conditions part', 'login-with-phone-number'), array(&$this, 'setting_term_and_conditions_text'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel related-to-accept-terms lwp-tab-form-settings']);
        add_settings_field('idehweb_term_and_conditions_link', __('Link of term & conditions', 'login-with-phone-number'), array(&$this, 'setting_term_and_conditions_link'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel related-to-accept-terms  lwp-tab-form-settings']);
        add_settings_field('idehweb_term_and_conditions_default_checked', __('Check term & conditions by default?', 'login-with-phone-number'), array(&$this, 'setting_term_and_conditions_default_checked'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel related-to-accept-terms lwp-tab-form-settings']);

        add_settings_field('idehweb_default_role', __('Default Role', 'login-with-phone-number'), array(&$this, 'setting_default_role'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel lwp-tab-general-settings']);

        add_settings_field('idehweb_lwp_space3', '', array(&$this, 'setting_idehweb_lwp_space'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel idehweb_lwp_mgt100']);
        add_settings_field('idehweb_lwp_instructions', __('Shortcode and Template Tag', 'login-with-phone-number'), array(&$this, 'setting_instructions'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel lwp-tab-installation-settings']);
        add_settings_field('idehweb_online_support', __('Enable online support', 'login-with-phone-number'), array(&$this, 'idehweb_online_support'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel lwp-tab-installation-settings']);
        add_settings_field('idehweb_usage_tracking', __('Enable usage tracking', 'login-with-phone-number'), array(&$this, 'idehweb_usage_tracking'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel lwp-tab-installation-settings']);

        add_settings_field('idehweb_localization_disable_placeholder', __('Disable automatic placeholder', 'login-with-phone-number'), array(&$this, 'setting_idehweb_localization_disable_automatic_placeholder'), 'idehweb-lwp-localization', 'idehweb-lwp-localization', ['label_for' => '', 'class' => 'ilwplabel']);
        add_settings_field('idehweb_localization_status', __('Enable localization', 'login-with-phone-number'), array(&$this, 'setting_idehweb_localization_enable_custom_localization'), 'idehweb-lwp-localization', 'idehweb-lwp-localization', ['label_for' => '', 'class' => 'ilwplabel']);
        add_settings_field('idehweb_localization_title_of_login_form', __('Title of login form (with phone number)', 'login-with-phone-number'), array(&$this, 'setting_idehweb_localization_of_login_form'), 'idehweb-lwp-localization', 'idehweb-lwp-localization', ['label_for' => '', 'class' => 'ilwplabel']);
        add_settings_field('idehweb_localization_title_of_login_form1', __('Title of login form (with email)', 'login-with-phone-number'), array(&$this, 'setting_idehweb_localization_of_login_form_email'), 'idehweb-lwp-localization', 'idehweb-lwp-localization', ['label_for' => '', 'class' => 'ilwplabel']);
        add_settings_field('idehweb_localization_placeholder_of_phonenumber_field', __('Placeholder of phone number field', 'login-with-phone-number'), array(&$this, 'setting_idehweb_localization_placeholder_of_phonenumber_field'), 'idehweb-lwp-localization', 'idehweb-lwp-localization', ['label_for' => '', 'class' => 'ilwplabel']);
        add_settings_field('idehweb_localization_firebase_option_title', __('Firebase option title', 'login-with-phone-number'), array(&$this, 'setting_idehweb_localization_firebase_option_title'), 'idehweb-lwp-localization', 'idehweb-lwp-localization', ['label_for' => '', 'class' => 'ilwplabel']);
        add_settings_field('idehweb_localization_custom_option_title', __('Custom option title', 'login-with-phone-number'), array(&$this, 'setting_idehweb_localization_custom_option_title'), 'idehweb-lwp-localization', 'idehweb-lwp-localization', ['label_for' => '', 'class' => 'ilwplabel']);


    }


    function settings_register()
    {
        register_setting('idehweb-lwp', 'idehweb_lwp_settings', array(
                'sanitize_callback' => array($this, 'settings_validate'),
            ));
    }
    function style_register()
    {
        register_setting('idehweb-lwp-styles', 'idehweb_lwp_settings_styles', array(
            'sanitize_callback' => array($this, 'settings_validate'),
        ));
    }
    function settings_page()
    {
        // Add nonce verification when form is submitted
        if (isset($_POST['submit']) && !empty($_POST['lwp_admin_nonce_field'])) {
            if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['lwp_admin_nonce_field'])), 'lwp_admin_nonce')) {
                wp_die('Security check failed');
            }
        }
        $options = get_option('idehweb_lwp_settings');

        if (!isset($options['idehweb_phone_number'])) $options['idehweb_phone_number'] = '';
        if (!isset($options['idehweb_token'])) $options['idehweb_token'] = '';
        if (!isset($options['idehweb_online_support'])) $options['idehweb_online_support'] = '1';


        ?>
        <div class="wrap">
            <div class="lwp_modal lwp-d-none">
                <div class="lwp_modal_header">
                    <div class="lwp_l"></div>
                    <div class="lwp_r">
                        <button class="lwp_close">x</button>
                    </div>
                </div>
                <div class="lwp_modal_body">
                    <ul>
                        <li><?php esc_html_e("1. create a page and name it login or register or what ever", 'login-with-phone-number'); ?></li>
                        <li>
                            <?php esc_html_e("2. copy this shortcode <code>[idehweb_lwp]</code> and paste in the page you created at step 1", 'login-with-phone-number'); ?>
                        </li>
                        <li><?php
                            esc_html_e("3. now, that is your login page. check your login page with other device or browser that you are not logged in!", 'login-with-phone-number');
                            ?>
                        </li>
                        <li><?php esc_html_e("for more information visit: ", 'login-with-phone-number'); ?><a target="_blank"
                                                                                                      href="https://idehweb.com/product/login-with-phone-number-in-wordpress/"><?php esc_html_e('Login with phone number', 'login-with-phone-number'); ?></a>
                        </li>
                    </ul>
                </div>
                <div class="lwp_modal_footer">
                    <button class="lwp_button"><?php esc_html_e('got it', 'login-with-phone-number'); ?></button>
                </div>
            </div>
            <div class="lwp_modal_overlay lwp-d-none"></div>
            <div class="lwp-wrap-left">


                <div id="icon-themes" class="icon32"></div>
                <h2 style="margin-bottom: 10px;"><?php esc_html_e('Login with phone number settings', 'login-with-phone-number'); ?></h2>
                <?php
                if (isset($_GET['settings-updated'])) {
                    $settings_updated = sanitize_text_field(wp_unslash($_GET['settings-updated']));

                    if ($settings_updated) {
                        ?>
                        <div id="setting-error-settings_updated" class="updated settings-error">
                            <p><strong><?php esc_html_e('Settings saved.', 'login-with-phone-number'); ?></strong></p>
                        </div>
                        <?php
                    }
                }
                ?>
                <form action="options.php" method="post" id="iuytfrdghj" class="lwp-setting-page-main">
                    <?php
                    // Add the nonce field for verification
                    wp_nonce_field('lwp_admin_nonce', 'lwp_admin_nonce_field');
                    ?>

                    <div class="lwp-tabs-wrapper">
                        <div class="lwp-tabs-list">
                            <a class="lwp-tab-item" href="#lwp-tab-general-settings"
                               data-tab="lwp-tab-general-settings"><?php esc_html_e('General', 'login-with-phone-number'); ?></a>
                            <a class="lwp-tab-item" href="#lwp-tab-gateway-settings"
                               data-tab="lwp-tab-gateway-settings"><?php esc_html_e('Gateway', 'login-with-phone-number'); ?></a>
                            <a class="lwp-tab-item" href="#lwp-tab-form-settings"
                               data-tab="lwp-tab-form-settings"><?php esc_html_e('Form', 'login-with-phone-number'); ?></a>
                            <a class="lwp-tab-item" href="#lwp-tab-installation-settings"
                               data-tab="lwp-tab-installation-settings"><?php esc_html_e('Installation', 'login-with-phone-number'); ?></a>
                            <!--                            <a class="lwp-tab-item" href="#lwp-tab-documentation-settings"-->
                            <!--                               data-tab="lwp-tab-documentation-settings">-->
                            <?php //esc_html_e('documentation', 'login-with-phone-number');
                            ?><!--</a>-->

                        </div>
                        <div class="lwp-tabs-content">

                            <?php settings_fields('idehweb-lwp'); ?>
                            <?php do_settings_sections('idehweb-lwp'); ?>
                        </div>
                    </div>
                    <p class="submit">
                        <span id="wkdugchgwfchevg3r4r"></span>
                    </p>
                    <p class="submit">
                        <span id="oihdfvygehv"></span>
                    </p>
                    <p class="submit">

                        <input type="submit" class="button-primary"
                               value="<?php esc_html_e('Save Changes', 'login-with-phone-number'); ?>"/></p>

                    <?php
                    if (empty($options['idehweb_token'])) {
                        ?>

                    <?php } ?>
                </form>
                <!--                     style="display: none"
                -->
                <div class="lwp-guid-popup lwp-open"
                     style="display: none"
                >
                    <div class="lwp-guid-popup-bg">
                    </div>
                    <div class="lwp-guid-popup-content">
                        <div class="lwp-guid-popup-page lwp-guid-popup-home lwp-gp-active">
                            <div class="lwp-label lwp-font-size-18">
                                <?php esc_html_e('Please, Answer us to help you setup this plugin:', 'login-with-phone-number'); ?>
                            </div>
                            <div class="lwp-answer-fields lwp-radios">
                                <div class="lwp-radio">
                                      <input type="radio" id="lwp-radio1" name="lwp_users_location"
                                             value="special-countries">
                                    <label for="lwp-radio1"><?php esc_html_e('My website users come from special countries', 'login-with-phone-number'); ?></label>
                                </div>
                                <div class="lwp-radio">
                                      <input type="radio" id="lwp-radio2" name="lwp_users_location" value="one-country">
                                    <label for="lwp-radio2"><?php esc_html_e('My website users come from one country', 'login-with-phone-number'); ?></label>
                                </div>
                                <div class="lwp-radio">
                                      <input type="radio" id="lwp-radio3" name="lwp_users_location"
                                             value="international-users">
                                    <label for="lwp-radio3"><?php esc_html_e('I am working internationally, my website users come from many countries', 'login-with-phone-number'); ?></label>
                                </div>
                            </div>
                        </div>
                        <div class="lwp-guid-popup-page lwp-special-countries">

                            <div class="lwp-guid-popup-top-bar">
                                <button class="lwp-guid-popup-back"><?php esc_html_e('Back', 'login-with-phone-number'); ?></button>
                            </div>
                            <div class="lwp-label lwp-font-size-18">
                                <?php esc_html_e('Please, Choose the countries your users come from:', 'login-with-phone-number'); ?>
                            </div>
                            <div class="lwp-answer-fields lwp-select">
                                <?php
                                $country_codes = $this->get_country_code_options();
                                //        print_r($options['idehweb_country_codes']);
                                ?>
                                <select id="lwp_idehweb_country_codes" multiple>
                                    <?php
                                    foreach ($country_codes as $country) {
//                                        $rr = in_array($country["code"], $options['idehweb_country_codes']);
                                        echo '<option value="' . esc_attr($country["code"]) . '" >' . esc_html($country['label']) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="lwp-guid-popup-page lwp-one-country">
                            <div class="lwp-guid-popup-top-bar">
                                <button class="lwp-guid-popup-back"><?php esc_html_e('Back', 'login-with-phone-number'); ?></button>
                            </div>
                            <?php
                            $country_codes = $this->get_country_code_options();
                            //        print_r($options['idehweb_country_codes']);
                            ?>
                            <select id="lwp_idehweb_country_codes_guid">
                                <?php
                                foreach ($country_codes as $country) {
//                                        $rr = in_array($country["code"], $options['idehweb_country_codes']);
                                    echo '<option value="' . esc_attr($country["code"]) . '" >' . esc_html($country['label']) . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="lwp-guid-popup-page lwp-international-users">
                            <div class="lwp-guid-popup-top-bar">
                                <button class="lwp-guid-popup-back"><?php esc_html_e('Back', 'login-with-phone-number'); ?></button>
                            </div>
                            <div class="lwp-label lwp-font-size-15">
                                <?php esc_html_e('Use international gateways like Firebase, Twilio or...', 'login-with-phone-number'); ?>
                                <br/>
                                <?php esc_html_e('You can even use multiple gateways at once. So you let your customers to choose the gateway they want to get sms from.', 'login-with-phone-number'); ?>
                                <br/>
                                <?php esc_html_e('Firebase is free.', 'login-with-phone-number'); ?>
                                <br/>
                                <?php esc_html_e('Also you can buy other sms gateways from add-ons part.', 'login-with-phone-number'); ?>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <?php
            if (!class_exists(LWP_PRO::class)) {
                ?>

                <div class="lwp-wrap-right">
                    <?php
                    $locale = get_locale();
                    if ($locale == 'fa_IR') {
                        ?>
                        <a style="margin-top: 10px;display:block"
                           href="<?php echo esc_url("https://webruno.ir/?utm_source=lwp-plugin&utm_medium=banner-plugin-lwp&utm_campaign=plugin-install"); ?>"
                           target="_blank">
                            <img style="width: 100%;max-width: 100%"
                                 src="<?php echo esc_url(plugins_url('../images/web-design.gif', __FILE__)) ?>"/>
                        </a>
                        <a style="margin-top: 10px;display:block"
                           href="<?php echo esc_url("https://idehweb.ir/%D8%A2%D9%85%D9%88%D8%B2%D8%B4-%D9%86%D9%8F%D8%B5%D8%A8-%D8%A7%D9%81%D8%B2%D9%88%D9%86%D9%87-%D9%88%D8%B1%D9%88%D8%AF-%D8%A8%D8%A7-%D8%B4%D9%85%D8%A7%D8%B1%D9%87-%D9%85%D9%88%D8%A8%D8%A7%DB%8C%D9%84-%D8%AF"); ?>"
                           target="_blank">
                            <img style="width: 100%;max-width: 100%"
                                 src="<?php echo esc_url(plugins_url('../images/login-with-phone-number-for-iran.gif', __FILE__)) ?>"/>
                        </a>

<!--                        <a style="margin-top: 10px;display:block"-->
<!--                           href="--><?php //echo esc_url("https://idehweb.ir/product/%D9%82%D8%A7%D9%84%D8%A8-%D9%88%D8%B1%D8%AF%D9%BE%D8%B1%D8%B3%DB%8C-%D9%86%D9%88%D8%AF%DB%8C-%D9%88%D8%A8/?utm_source=lwp-plugin&utm_medium=banner-nodeeweb&utm_campaign=plugin-install"); ?><!--"-->
<!--                           target="_blank">-->
<!--                            <img style="width: 100%;max-width: 100%"-->
<!--                                 src="--><?php //echo esc_url(plugins_url('../images/nodeweb-theme-wordpress.gif', __FILE__)) ?><!--"/>-->
<!--                        </a>-->

                        <?php
                    } else {
                        ?>
                        <a href="<?php echo esc_url("https://idehweb.com/product/login-with-phone-number-in-wordpress/?utm_source=lwp-plugin&utm_medium=banner-lwp&utm_campaign=plugin-install"); ?>"
                           target="_blank">
                            <img style="width: 100%;max-width: 100%"
                                 src="<?php echo esc_url(plugins_url('../images/login-with-phone-number-en-final1.gif', dirname(__FILE__))) ?>"/>
                        </a>

                        <a style="margin-top: 10px;display:block"
                           href="<?php echo esc_url("https://idehweb.com/product/nodeeweb-wordpress-theme/?utm_source=lwp-plugin&utm_medium=banner-nodeeweb&utm_campaign=plugin-install"); ?>"
                           target="_blank">
                            <img style="width: 100%;max-width: 100%"
                                 src="<?php echo esc_url(plugins_url('../images/nodeeweb-wordpress-theme.png', dirname(__FILE__))) ?>"/>
                        </a>
                        <?php
                    }
                    ?>
                </div>
            <?php } ?>



            <script>
                <?php

                ?>
                jQuery(function ($) {
                    $('#lwp_idehweb_country_codes').on("select2:select", function (e) {
                        // var value = e.params.data;
                        let selectedValues = $('#lwp_idehweb_country_codes').select2('data');
                        // let selectedValues=$('#lwp_idehweb_country_codes').find(':selected');
                        console.log('selectedValues', selectedValues);
                        // Using {id,text} format
                    });
                    $('body').on('click', '.lwp-guid-popup-bg', function (e) {
                        $('.lwp-guid-popup.lwp-open').removeClass('lwp-open')
                    });
                    $('body').on('click', '.lwp-guid-popup-back', function (e) {
                        $('.lwp-guid-popup-page.lwp-gp-active').removeClass('lwp-gp-active');
                        $('.lwp-guid-popup-page.lwp-guid-popup-home').addClass('lwp-gp-active')

                    });
                    $('input[name="lwp_users_location"]').click(function (e) {
                        var lwp_users_location = $(this).val();
                        $('.lwp-guid-popup-page.lwp-gp-active').removeClass('lwp-gp-active');
                        $('.lwp-' + lwp_users_location).addClass('lwp-gp-active')
                        console.log('lwp_users_location', lwp_users_location);
                    })
                    var idehweb_country_codes = $("#idehweb_country_codes");
                    var lwp_idehweb_country_codes = $("#lwp_idehweb_country_codes");
                    var idehweb_phone_number_ccodeG = '1';
                    $(window).load(function () {

                        $("#idehweb_phone_number_ccode").select2();
                        idehweb_country_codes.select2();
                        lwp_idehweb_country_codes.select2();
                        $("#idehweb_default_gateways").select2();
                        // $(".idehweb_default_gateways_wrapper ul.select2-selection__rendered").sortable({
                        //     containment: 'parent',
                        //
                        //     stop: function (event, ui) {
                        //         var formData = [];
                        //         var _li = $('.idehweb_default_gateways_wrapper li.select2-selection__choice');
                        //         _li.each(function (idx) {
                        //             var currentObj = $(this);
                        //             var data = currentObj.text();
                        //             data = data.substr(1, data.length);
                        //             formData.push({name: data, value: currentObj.val()})
                        //         })
                        //         console.log(formData)
                        //     },
                        //     update: function () {
                        //         var _li = $('.idehweb_default_gateways_wrapper li');
                        //         // _li.removeAttr("value");
                        //         _li.each(function (idx) {
                        //             var currentObj = $(this);
                        //             console.log(currentObj.text());
                        //             $(this).attr("value", idx + 1);
                        //         })
                        //     }
                        // });


                        <?php
                        //                        if (empty($options['idehweb_token'])) {
                        ?>
                        // $('.authwithwebsite').click();
                        <?php
                        //                        }
                        ?>

                    });

                    // var edf2 = $('#idehweb_lwp_settings_use_phone_number_for_username');

                    var idehweb_body = $('body');


                    idehweb_body.on('click', '.lwp_more_help', function () {
                        createTutorial();
                    });
                    idehweb_body.on('click', '.lwp_close , .lwp_button', function (e) {
                        e.preventDefault();
                        $('.lwp_modal').remove();
                        $('.lwp_modal_overlay').remove();
                        localStorage.setItem('ldwtutshow', 1);
                    });


                    var ldwtutshow = localStorage.getItem('ldwtutshow');
                    if (ldwtutshow === null) {
                        // createTutorial();
                        if (typeof idehweb_lwp !== "undefined" && idehweb_lwp.wizard_url) {

                            // window.location.href = idehweb_lwp.wizard_url;
                        }
                    }

                    function createTutorial() {
                        var wrap = $('.wrap');
                        $('.wrap .lwp_modal_overlay').removeClass('lwp-d-none');
                        $('.wrap .lwp_modal').removeClass('lwp-d-none');
                        wrap.prepend('<div class="lwp_modal_overlay"></div>')
                            .prepend('<div class="lwp_modal">' +
                                '<div class="lwp_modal_header">' +
                                '<div class="lwp_l"></div>' +
                                '<div class="lwp_r"><button class="lwp_close">x</button></div>' +
                                '</div>' +
                                '<div class="lwp_modal_body">' +
                                '<ul>' +
                                '<li>' + '<?php esc_html_e("1. create a page and name it login or register or what ever", 'login-with-phone-number') ?>' + '</li>' +
                                '<li>' + '<?php esc_html_e("2. copy this shortcode <code>[idehweb_lwp]</code> and paste in the page you created at step 1", 'login-with-phone-number') ?>' + '</li>' +
                                '<li>' + '<?php esc_html_e("3. now, that is your login page. check your login page with other device or browser that you are not logged in!", 'login-with-phone-number') ?>' +
                                '</li>' +
                                '<li>' +
                                '<?php esc_html_e("for more information visit: ", 'login-with-phone-number') ?>' + '<a target="_blank" href="https://idehweb.com/product/login-with-phone-number-in-wordpress/">Login with phone number</a>' +
                                '</li>' +
                                '</ul>' +
                                '</div>' +
                                '<div class="lwp_modal_footer">' +
                                '<button class="lwp_button"><?php esc_html_e("got it ", 'login-with-phone-number') ?></button>' +
                                '</div>' +
                                '</div>');

                    }
                });
            </script>
        </div>
        <?php
    }
    function localization_register()
    {
        register_setting('idehweb-lwp-localization', 'idehweb_lwp_settings_localization', array(
            'sanitize_callback' => array($this, 'settings_validate'),
        ));
    }

    function style_settings_page()
    {

        // Add nonce verification
        if (isset($_POST['submit']) && !empty($_POST['lwp_style_nonce_field'])) {
            if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['lwp_style_nonce_field'])), 'lwp_style_nonce')) {
                wp_die('Security check failed');
            }
        }


        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_phone_number'])) $options['idehweb_phone_number'] = '';
        if (!isset($options['idehweb_token'])) $options['idehweb_token'] = '';
        if (!isset($options['idehweb_online_support'])) $options['idehweb_online_support'] = '1';


        ?>
        <div class="wrap">
            <div id="icon-themes" class="icon32"></div>
            <h2><?php esc_html_e('Style settings', 'login-with-phone-number'); ?></h2>
            <?php
            if (isset($_GET['settings-updated'])) {
                $settings_updated = sanitize_text_field(wp_unslash($_GET['settings-updated']));

                if ($settings_updated) {
                    ?>
                    <div id="setting-error-settings_updated" class="updated settings-error">
                        <p><strong><?php esc_html_e('Settings saved.', 'login-with-phone-number'); ?></strong></p>
                    </div>
                    <?php
                }
            }
            ?>

            <form action="options.php" method="post" id="iuytfrdghj">
                <?php wp_nonce_field('lwp_style_nonce', 'lwp_style_nonce_field'); ?>
                <?php settings_fields('idehweb-lwp-styles'); ?>
                <?php do_settings_sections('idehweb-lwp-styles'); ?>

                <p class="submit">
                    <span id="wkdugchgwfchevg3r4r"></span>
                </p>
                <p class="submit">
                    <span id="oihdfvygehv"></span>
                </p>
                <p class="submit">

                    <input type="submit" class="button-primary"
                           value="<?php esc_html_e('Save Changes', 'login-with-phone-number'); ?>"/></p>

            </form>


        </div>
        <?php
    }


    function localization_settings_page()
    {
        // Add nonce verification
        if (isset($_POST['submit']) && !empty($_POST['lwp_localization_nonce_field'])) {
            if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['lwp_localization_nonce_field'])), 'lwp_localization_nonce')) {
                wp_die('Security check failed');
            }
        }


        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_phone_number'])) $options['idehweb_phone_number'] = '';
        if (!isset($options['idehweb_token'])) $options['idehweb_token'] = '';
        if (!isset($options['idehweb_online_support'])) $options['idehweb_online_support'] = '1';


        ?>
        <div class="wrap">
            <div id="icon-themes" class="icon32"></div>
            <h2><?php esc_html_e('Localization settings', 'login-with-phone-number'); ?></h2>
            <?php
            if (isset($_GET['settings-updated'])) {
                $settings_updated = sanitize_text_field(wp_unslash($_GET['settings-updated']));

                if ($settings_updated === 'true' || $settings_updated === '1') {
                    ?>
                    <div id="setting-error-settings_updated" class="updated settings-error">
                        <p><strong><?php esc_html_e('Settings saved.', 'login-with-phone-number'); ?></strong></p>
                    </div>
                    <?php
                }
            }
            ?>

            <form action="options.php" method="post" id="iuytfrdghj">
                <?php wp_nonce_field('lwp_localization_nonce', 'lwp_localization_nonce_field'); ?>

                <?php settings_fields('idehweb-lwp-localization'); ?>
                <?php do_settings_sections('idehweb-lwp-localization'); ?>

                <p class="submit">
                    <span id="wkdugchgwfchevg3r4r"></span>
                </p>
                <p class="submit">
                    <span id="oihdfvygehv"></span>
                </p>
                <p class="submit">

                    <input type="submit" class="button-primary"
                           value="<?php esc_html_e('Save Changes', 'login-with-phone-number'); ?>"/></p>

            </form>


        </div>
        <?php
    }


    function section_intro()
    {
//        echo '<p class="description"></p>';
    }

    function section_title()
    {
//        echo '<h2>' . __('Login With Phone Number', 'login-with-phone-number') . '</h2>';
    }

    function setting_idehweb_lwp_space()
    {
        echo '<div class="idehweb_lwp_mgt50"></div>';
    }

    function setting_idehweb_style_enable_custom_style()
    {
        $options = get_option('idehweb_lwp_settings_styles');
        if (!isset($options['idehweb_styles_status'])) $options['idehweb_styles_status'] = '0';
        else $options['idehweb_styles_status'] = sanitize_text_field($options['idehweb_styles_status']);

        echo '<input  type="hidden" name="idehweb_lwp_settings_styles[idehweb_styles_status]" value="0" />
		<label><input type="checkbox" id="idehweb_lwp_settings_idehweb_styles_status" name="idehweb_lwp_settings_styles[idehweb_styles_status]" value="1"' . (($options['idehweb_styles_status']) ? ' checked="checked"' : '') . ' />' . esc_html__('enable custom styles', 'login-with-phone-number') . '</label>';
        echo wp_kses_post($this->setting_idehweb_pro_label());
    }

    function setting_idehweb_style_button_background_color()
    {
        $options = get_option('idehweb_lwp_settings_styles');
        if (!isset($options['idehweb_styles_button_background'])) $options['idehweb_styles_button_background'] = '#009b9a';
        else $options['idehweb_styles_button_background'] = sanitize_text_field($options['idehweb_styles_button_background']);

        echo '<input type="color" name="idehweb_lwp_settings_styles[idehweb_styles_button_background]" class="regular-text" value="' . esc_attr($options['idehweb_styles_button_background']) . '" />
    <p class="description">' . esc_html__('button background color', 'login-with-phone-number') . '</p>';
    }

    function setting_idehweb_style_button_text_color()
    {
        $options = get_option('idehweb_lwp_settings_styles');
        if (!isset($options['idehweb_styles_button_text_color'])) $options['idehweb_styles_button_text_color'] = '#ffffff';
        else $options['idehweb_styles_button_text_color'] = sanitize_text_field($options['idehweb_styles_button_text_color']);

        echo '<input type="color" name="idehweb_lwp_settings_styles[idehweb_styles_button_text_color]" class="regular-text" value="' . esc_attr($options['idehweb_styles_button_text_color']) . '" />
		<p class="description">' . esc_html__('button text color', 'login-with-phone-number') . '</p>';
    }


    function setting_idehweb_store_number_with_country_code()
    {
        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_store_number_with_country_code'])) $options['idehweb_store_number_with_country_code'] = '1';
        echo '<input  type="hidden" name="idehweb_lwp_settings[idehweb_store_number_with_country_code]" value="0" />
		<label><input type="checkbox" name="idehweb_lwp_settings[idehweb_store_number_with_country_code]" value="1"' . (($options['idehweb_store_number_with_country_code']) ? ' checked="checked"' : '') . ' />' . esc_html__('Store numbers with country code?', 'login-with-phone-number') . '</label>';
        echo '<p>' . esc_html__('Only disable this if your site serves users from a single country. Make sure a default country is selected above.', 'login-with-phone-number') . '</p>';

    }


    function idehweb_online_support()
    {
        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_online_support'])) $options['idehweb_online_support'] = '1';

        echo '<input type="hidden" name="idehweb_lwp_settings[idehweb_online_support]" value="0" />
		<label><input type="checkbox" name="idehweb_lwp_settings[idehweb_online_support]" value="1"' . (($options['idehweb_online_support']) ? ' checked="checked"' : '') . ' />' . esc_html__('I want online support be active', 'login-with-phone-number') . '</label>';
        echo '<div></div>';

    }


    function idehweb_usage_tracking()
    {
        $options = get_option('idehweb_lwp_settings', []);

        // Default to enabled (optional; can be '0' for default off)
        if (!isset($options['idehweb_usage_tracking'])) {
            $options['idehweb_usage_tracking'] = '1';
        }

        ?>
        <input type="hidden" name="idehweb_lwp_settings[idehweb_usage_tracking]" value="0"/>
        <label>
            <input type="checkbox" name="idehweb_lwp_settings[idehweb_usage_tracking]" value="1"
                <?php checked($options['idehweb_usage_tracking'], '1'); ?> />
            <?php esc_html_e('Help improve this plugin by enabling anonymous usage tracking (Microsoft Clarity).', 'login-with-phone-number'); ?>
        </label>
        <p class="description"><?php esc_html_e('We only track usage on this plugin’s admin pages. No visitor or personal data is collected.', 'login-with-phone-number'); ?></p>
        <?php
    }


    function setting_idehweb_localization_disable_automatic_placeholder()
    {
        $options = get_option('idehweb_lwp_settings_localization');
        if (!isset($options['idehweb_localization_disable_placeholder'])) $options['idehweb_localization_disable_placeholder'] = '0';
        echo '<input  type="hidden" name="idehweb_lwp_settings_localization[idehweb_localization_disable_placeholder]" value="0" />
		<label><input type="checkbox" id="idehweb_lwp_settings_localization_disable_placeholder" name="idehweb_lwp_settings_localization[idehweb_localization_disable_placeholder]" value="1"' . (($options['idehweb_localization_disable_placeholder']) ? ' checked="checked"' : '') . ' />' . esc_html__('Turn off automatic placeholder based on country', 'login-with-phone-number') . '</label>';

    }


    function setting_idehweb_localization_enable_custom_localization()
    {
        $options = get_option('idehweb_lwp_settings_localization');
        if (!isset($options['idehweb_localization_status'])) $options['idehweb_localization_status'] = '0';
        echo '<input  type="hidden" name="idehweb_lwp_settings_localization[idehweb_localization_status]" value="0" />
		<label><input type="checkbox" id="idehweb_lwp_settings_localization_status" name="idehweb_lwp_settings_localization[idehweb_localization_status]" value="1"' . (($options['idehweb_localization_status']) ? ' checked="checked"' : '') . ' />' . esc_html__('enable localization', 'login-with-phone-number') . '</label>';

    }

    function setting_idehweb_localization_of_login_form()
    {
        $options = get_option('idehweb_lwp_settings_localization');
        if (!isset($options['idehweb_localization_title_of_login_form'])) $options['idehweb_localization_title_of_login_form'] = 'Login / register';
        else $options['idehweb_localization_title_of_login_form'] = sanitize_text_field($options['idehweb_localization_title_of_login_form']);


        echo '<input type="text" name="idehweb_lwp_settings_localization[idehweb_localization_title_of_login_form]" class="regular-text" value="' . esc_attr($options['idehweb_localization_title_of_login_form']) . '" />
		<p class="description">' . esc_html__('Login / register', 'login-with-phone-number') . '</p>';
    }


    function setting_idehweb_localization_of_login_form_email()
    {
        $options = get_option('idehweb_lwp_settings_localization');
        if (!isset($options['idehweb_localization_title_of_login_form_email'])) $options['idehweb_localization_title_of_login_form_email'] = 'Login / register';
        else $options['idehweb_localization_title_of_login_form_email'] = sanitize_text_field($options['idehweb_localization_title_of_login_form_email']);


        echo '<input type="text" name="idehweb_lwp_settings_localization[idehweb_localization_title_of_login_form_email]" class="regular-text" value="' . esc_attr($options['idehweb_localization_title_of_login_form_email']) . '" />
		<p class="description">' . esc_html__('Login / register', 'login-with-phone-number') . '</p>';
    }

    function setting_idehweb_localization_placeholder_of_phonenumber_field()
    {
        $options = get_option('idehweb_lwp_settings_localization');
        if (!isset($options['idehweb_localization_placeholder_of_phonenumber_field'])) $options['idehweb_localization_placeholder_of_phonenumber_field'] = '';
        else $options['idehweb_localization_placeholder_of_phonenumber_field'] = sanitize_text_field($options['idehweb_localization_placeholder_of_phonenumber_field']);

        echo '<input type="text" name="idehweb_lwp_settings_localization[idehweb_localization_placeholder_of_phonenumber_field]" class="regular-text" value="' . esc_attr($options['idehweb_localization_placeholder_of_phonenumber_field']) . '" />
		<p class="description">' . esc_html__('If empty, a valid example number for the selected country will be shown', 'login-with-phone-number') . '</p>';
    }

    function setting_idehweb_localization_firebase_option_title()
    {
        $options = get_option('idehweb_lwp_settings_localization');
        if (!isset($options['idehweb_localization_firebase_option_title'])) $options['idehweb_localization_firebase_option_title'] = '';
        else $options['idehweb_localization_firebase_option_title'] = sanitize_text_field($options['idehweb_localization_firebase_option_title']);

        echo '<input type="text" name="idehweb_lwp_settings_localization[idehweb_localization_firebase_option_title]" class="regular-text" value="' . esc_attr($options['idehweb_localization_firebase_option_title']) . '" />
		<p class="description">' . esc_html__('Show firebase title when use multiple gateway', 'login-with-phone-number') . '</p>';
    }

    function setting_idehweb_localization_custom_option_title()
    {
        $options = get_option('idehweb_lwp_settings_localization');
        if (!isset($options['idehweb_localization_custom_option_title'])) $options['idehweb_localization_custom_option_title'] = '';
        else $options['idehweb_localization_custom_option_title'] = sanitize_text_field($options['idehweb_localization_custom_option_title']);

        echo '<input type="text" name="idehweb_lwp_settings_localization[idehweb_localization_custom_option_title]" class="regular-text" value="' . esc_attr($options['idehweb_localization_custom_option_title']) . '" />
		<p class="description">' . esc_html__('Show firebase title when use multiple gateway', 'login-with-phone-number') . '</p>';
    }


    function setting_idehweb_pro_label()
    {
        if (!class_exists(LWP_PRO::class)) {
            return '<span class="pro-not-exist">' . esc_html__('PRO', 'login-with-phone-number') . '</span>';
        }
    }

}