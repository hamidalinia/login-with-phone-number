<?php

if (!defined('ABSPATH')) {
    exit;
}

class lwp_webruno
{
    function __construct()
    {
        add_action('idehweb_custom_fields', [$this, 'admin_init']);
        add_filter('lwp_add_to_default_gateways', [$this, 'lwp_add_to_default_gateways']);
        add_action('lwp_send_sms_webruno', [$this, 'lwp_send_sms_webruno'], 10, 2);
    }

    function lwp_add_to_default_gateways($args = [])
    {
        if (!is_array($args)) {
            $args = [];
        }

        $exists = false;

        // Check if 'webruno' gateway already exists in the list
        foreach ($args as &$gateway) {
            if ($gateway['value'] === 'webruno') {
                $gateway['label'] = esc_html__("Webruno", 'login-with-phone-number'); // Update label
                $gateway['isFree'] = true;
                $exists = true;
                break;
            }
        }

        // If 'webruno' is not in the list, add it
        if (!$exists) {
            $args[] = ["value" => "webruno","isFree" => true, "label" => esc_html__("Webruno", 'login-with-phone-number')];
        }
        return $args;
    }

    function admin_init()
    {
        add_settings_field('idehweb_webruno_username', __('Enter Webruno Username', 'login-with-phone-number'), array(&$this, 'setting_webruno_username'), 'idehweb-lwp', 'idehweb-lwp',['label_for' => '', 'class' => 'ilwplabel lwp-gateways related_to_webruno']);
        add_settings_field('idehweb_webruno_password', __('Enter Webruno Password', 'login-with-phone-number'), array(&$this, 'setting_webruno_password'), 'idehweb-lwp', 'idehweb-lwp',['label_for' => '', 'class' => 'ilwplabel lwp-gateways related_to_webruno']);
        add_settings_field('idehweb_webruno_sender', __('Enter Webruno Sender', 'login-with-phone-number'), array(&$this, 'setting_webruno_sender'), 'idehweb-lwp', 'idehweb-lwp',['label_for' => '', 'class' => 'ilwplabel lwp-gateways related_to_webruno']);
    }

    function lwp_send_sms_webruno($phone_number, $message)
    {
        $options = get_option('idehweb_lwp_settings');
        $username = isset($options['idehweb_webruno_username']) ? sanitize_text_field($options['idehweb_webruno_username']) : '';
        $password = isset($options['idehweb_webruno_password']) ? sanitize_text_field($options['idehweb_webruno_password']) : '';
        $sender = isset($options['idehweb_webruno_sender']) ? sanitize_text_field($options['idehweb_webruno_sender']) : '';

        // Clean phone number - remove + and leading zeros if any
        $to = preg_replace('/^(\+|00)/', '', $phone_number);

        // Remove leading zero from Iranian numbers and add country code
        if (substr($to, 0, 1) === '0' && strlen($to) === 11) {
            $to = '98' . substr($to, 1);
        }

        if (empty($username) || empty($password) || empty($sender) || empty($to)) {
            return;
        }

        $url = "https://rest.payamak-panel.com/api/SendSMS/SendSMS";

        $response = wp_safe_remote_post($url, [
            'timeout' => 30,
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            'body' => [
                'username' => $username,
                'password' => $password,
                'to' => $to,
                'from' => $sender,
                'text' => $message,
                'isflash' => 'false',
            ],
        ]);

        if (is_wp_error($response)) {
            return;
        }

        $response_code = wp_remote_retrieve_response_code($response);
        if ($response_code !== 200) {
            return;
        }

        $response_body = wp_remote_retrieve_body($response);
        $result = json_decode($response_body, true);

        if (isset($result['Value']) && is_numeric($result['Value']) && $result['Value'] > 1000) {
            return true;
        } else {
            return false;
        }
    }

    function setting_webruno_username()
    {
        $options = get_option('idehweb_lwp_settings');
        $username = isset($options['idehweb_webruno_username']) ? esc_attr($options['idehweb_webruno_username']) : '';
        echo '<input type="text" name="idehweb_lwp_settings[idehweb_webruno_username]" class="regular-text" value="' . esc_attr($username) . '" />';
        echo '<p class="description">' . esc_html__('Enter the Webruno username.', 'login-with-phone-number') . '</p>';
    }

    function setting_webruno_password()
    {
        $options = get_option('idehweb_lwp_settings');
        $password = isset($options['idehweb_webruno_password']) ? esc_attr($options['idehweb_webruno_password']) : '';
        echo '<input type="password" name="idehweb_lwp_settings[idehweb_webruno_password]" class="regular-text" value="' . esc_attr($password) . '" />';
        echo '<p class="description">' . esc_html__('Enter the Webruno password.', 'login-with-phone-number') . '</p>';
    }

    function setting_webruno_sender()
    {
        $options = get_option('idehweb_lwp_settings');
        $sender = isset($options['idehweb_webruno_sender']) ? esc_attr($options['idehweb_webruno_sender']) : '';
        echo '<input type="text" name="idehweb_lwp_settings[idehweb_webruno_sender]" class="regular-text" value="' . esc_attr($sender) . '" />';
        echo '<p class="description">' . esc_html__('Enter the sender ID for Webruno messages.', 'login-with-phone-number') . '</p>';
    }
}

global $lwp_webruno;
$lwp_webruno = new lwp_webruno();