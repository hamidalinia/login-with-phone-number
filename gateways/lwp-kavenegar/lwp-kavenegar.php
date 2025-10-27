<?php

class LWP_KavenegarSMS
{
    function __construct()
    {
        add_action('idehweb_custom_fields', array(&$this, 'admin_init'));
        add_filter('lwp_add_to_default_gateways', array(&$this, 'lwp_add_to_default_gateways'));
        add_action('lwp_send_sms_kavenegar', array(&$this, 'lwp_send_sms_kavenegar'), 10, 2);
    }

    function lwp_add_to_default_gateways($args = [])
    {
        if (!is_array($args)) {
            $args = [];
        }

        $exists = false;

        // Check if 'kavenegar' gateway already exists in the list
        foreach ($args as &$gateway) {
            if ($gateway['value'] === 'kavenegar') {
                $gateway['label'] = esc_html__("Kavenegar", 'login-with-phone-number');
                $gateway['isFree'] = true;
                $exists = true;
                break;
            }
        }

        // If 'kavenegar' is not in the list, add it
        if (!$exists) {
            $args[] = ["value" => "kavenegar","isFree" => true, "label" => esc_html__("kavenegar", 'login-with-phone-number')];
        }
        return $args;
    }

    function admin_init()
    {
        add_settings_field('idehweb_kavenegar_api_key', __('Enter Kavenegar API Key', 'login-with-phone-number'), array(&$this, 'setting_idehweb_api_key'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel lwp-gateways related_to_kavenegar']);
        add_settings_field('idehweb_kavenegar_template', __('Enter Kavenegar Template', 'login-with-phone-number'), array(&$this, 'setting_idehweb_template'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel lwp-gateways related_to_kavenegar']);
    }

    function lwp_send_sms_kavenegar($phone_number, $code)
    {
        $options = get_option('idehweb_lwp_settings');
        $api_key = isset($options['idehweb_kavenegar_api_key']) ? sanitize_text_field($options['idehweb_kavenegar_api_key']) : '';
        $template = isset($options['idehweb_kavenegar_template']) ? sanitize_text_field($options['idehweb_kavenegar_template']) : '';

        $url = "https://api.kavenegar.com/v1/{$api_key}/verify/lookup.json";

        $body = [
            'receptor' => $phone_number,
            'token' => $code,
            'template' => $template
        ];

        $response = wp_safe_remote_post($url, [
            'timeout' => 60,
            'redirection' => 1,
            'blocking' => true,
            'headers' => ['Content-Type' => 'application/x-www-form-urlencoded'],
            'body' => $body
        ]);
    }

    function setting_idehweb_api_key()
    {
        $options = get_option('idehweb_lwp_settings');
        $api_key = isset($options['idehweb_kavenegar_api_key']) ? esc_attr($options['idehweb_kavenegar_api_key']) : '';
        echo '<input type="text" name="idehweb_lwp_settings[idehweb_kavenegar_api_key]" class="regular-text" value="' . esc_attr($api_key) . '" /> ';
        echo '<p class="description">' . esc_html__('Enter Kavenegar API Key', 'login-with-phone-number') . '</p>';
    }

    function setting_idehweb_template()
    {
        $options = get_option('idehweb_lwp_settings');
        $template = isset($options['idehweb_kavenegar_template']) ? esc_attr($options['idehweb_kavenegar_template']) : '';
        echo '<input type="text" name="idehweb_lwp_settings[idehweb_kavenegar_template]" class="regular-text" value="' . esc_attr($template) . '" /> ';
        echo '<p class="description">' . esc_html__('Enter Kavenegar Template Name', 'login-with-phone-number') . '</p>';
        echo '<p style="color: green" class="description">' . esc_html__('**For the validation method from the Kavenegar service.', 'login-with-phone-number') . '</p>';
    }
}

global $lwp_kavenegar_sms;
$lwp_kavenegar_sms = new LWP_KavenegarSMS();