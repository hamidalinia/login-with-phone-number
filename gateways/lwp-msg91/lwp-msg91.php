<?php
class lwp_msg91
{
    function __construct()
    {
        add_action('idehweb_custom_fields', [$this, 'admin_init']);
        add_filter('lwp_add_to_default_gateways', [$this, 'lwp_add_to_default_gateways']);
        add_action('lwp_send_sms_msg91', [$this, 'lwp_send_sms_msg91'], 10, 2);
    }

    function lwp_add_to_default_gateways($args = [])
    {
        if (!is_array($args)) {
            $args = [];
        }

        $exists = false;

        // Check if 'msg91' gateway already exists in the list
        foreach ($args as &$gateway) {
            if ($gateway['value'] === 'msg91') {
                $gateway['label'] = esc_html__("msg91", 'login-with-phone-number'); // Update label
                $gateway['isFree'] = true;
                $exists = true;
                break;
            }
        }

        // If 'msg91' is not in the list, add it
        if (!$exists) {
            $args[] = ["value" => "msg91","isFree" => true, "label" => esc_html__("msg91", 'login-with-phone-number')];
        }
        return $args;
    }

    function admin_init()
    {
        add_settings_field('idehweb_msg91_authkey', __('Enter MSG91 Authkey', 'login-with-phone-number'), array(&$this, 'setting_msg91_authkey'), 'idehweb-lwp', 'idehweb-lwp',['label_for' => '', 'class' => 'ilwplabel lwp-gateways related_to_msg91']);
        add_settings_field('idehweb_msg91_sender', __('Enter MSG91 Sender', 'login-with-phone-number'), array(&$this, 'setting_msg91_sender'), 'idehweb-lwp', 'idehweb-lwp',['label_for' => '', 'class' => 'ilwplabel lwp-gateways related_to_msg91']);
        add_settings_field('idehweb_msg91_country', __('Enter Country Code', 'login-with-phone-number'), array(&$this, 'setting_msg91_country'), 'idehweb-lwp', 'idehweb-lwp',['label_for' => '', 'class' => 'ilwplabel lwp-gateways related_to_msg91']);
        add_settings_field('idehweb_msg91_route', __('Enter Route', 'login-with-phone-number'), array(&$this, 'setting_msg91_route'), 'idehweb-lwp', 'idehweb-lwp',['label_for' => '', 'class' => 'ilwplabel lwp-gateways related_to_msg91']);
        add_settings_field('idehweb_msg91_unicode', __('Enable Unicode', 'login-with-phone-number'), array(&$this, 'setting_msg91_unicode'), 'idehweb-lwp', 'idehweb-lwp',['label_for' => '', 'class' => 'ilwplabel lwp-gateways related_to_msg91']);
    }

    function lwp_send_sms_msg91($phone_number, $message)
    {
        $options = get_option('idehweb_lwp_settings');
        $authkey = isset($options['idehweb_msg91_authkey']) ? sanitize_text_field($options['idehweb_msg91_authkey']) : '';
        $sender = isset($options['idehweb_msg91_sender']) ? sanitize_text_field($options['idehweb_msg91_sender']) : '';
        $country = isset($options['idehweb_msg91_country']) ? sanitize_text_field($options['idehweb_msg91_country']) : '91'; // Default to India
        $route = isset($options['idehweb_msg91_route']) ? sanitize_text_field($options['idehweb_msg91_route']) : '4'; // Default to transactional route
        $unicode = isset($options['idehweb_msg91_unicode']) ? $options['idehweb_msg91_unicode'] : false; // Default to false

        $to = $phone_number;

        if (empty($authkey) || empty($sender) || empty($to)) {
            // Removed error_log for production
            return;
        }

        // Ensure HTTPS protocol is used
        $url = "https://api.msg91.com/api/v2/sendsms";

        $response = wp_safe_remote_post($url, [
            'timeout' => 30,
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'body' => json_encode([
                'authkey' => $authkey,
                'mobiles' => $to,
                'message' => $message,
                'sender' => $sender,
                'route' => $route,
                'country' => $country,
                'unicode' => $unicode ? 'true' : 'false', // Ensure boolean value is properly handled
            ]),
        ]);

        if (is_wp_error($response)) {
            // Removed error_log for production
            return;
        }

        $response_code = wp_remote_retrieve_response_code($response);
        if ($response_code !== 200) {
            // Removed error_log for production
        }
    }

    function setting_msg91_authkey()
    {
        $options = get_option('idehweb_lwp_settings');
        $authkey = isset($options['idehweb_msg91_authkey']) ? esc_attr($options['idehweb_msg91_authkey']) : '';
        echo '<input type="text" name="idehweb_lwp_settings[idehweb_msg91_authkey]" class="regular-text" value="' . esc_attr($authkey) . '" />';
        echo '<p class="description">' . esc_html__('Enter the MSG91 Authkey.', 'login-with-phone-number') . '</p>';
    }

    function setting_msg91_sender()
    {
        $options = get_option('idehweb_lwp_settings');
        $sender = isset($options['idehweb_msg91_sender']) ? esc_attr($options['idehweb_msg91_sender']) : '';
        echo '<input type="text" name="idehweb_lwp_settings[idehweb_msg91_sender]" class="regular-text" value="' . esc_attr($sender) . '" />';
        echo '<p class="description">' . esc_html__('Enter the sender ID for MSG91 messages.', 'login-with-phone-number') . '</p>';
    }

    function setting_msg91_country()
    {
        $options = get_option('idehweb_lwp_settings');
        $country = isset($options['idehweb_msg91_country']) ? esc_attr($options['idehweb_msg91_country']) : '91';
        echo '<input type="text" name="idehweb_lwp_settings[idehweb_msg91_country]" class="regular-text" value="' . esc_attr($country) . '" />';
        echo '<p class="description">' . esc_html__('Enter the country code for the recipient phone number.', 'login-with-phone-number') . '</p>';
    }

    function setting_msg91_route()
    {
        $options = get_option('idehweb_lwp_settings');
        $route = isset($options['idehweb_msg91_route']) ? esc_attr($options['idehweb_msg91_route']) : '4'; // Default route for transactional
        echo '<input type="text" name="idehweb_lwp_settings[idehweb_msg91_route]" class="regular-text" value="' . esc_attr($route) . '" />';
        echo '<p class="description">' . esc_html__('Enter the message route (e.g., 1 for Promotional, 4 for Transactional).', 'login-with-phone-number') . '</p>';
    }

    function setting_msg91_unicode()
    {
        $options = get_option('idehweb_lwp_settings');
        $unicode = isset($options['idehweb_msg91_unicode']) ? $options['idehweb_msg91_unicode'] : false;
        echo '<input type="checkbox" name="idehweb_lwp_settings[idehweb_msg91_unicode]" value="1" ' . checked($unicode, true, false) . ' />';
        echo '<p class="description">' . esc_html__('Enable Unicode for the message (e.g., for non-Latin characters).', 'login-with-phone-number') . '</p>';
    }
}

global $lwp_msg91;
$lwp_msg91 = new lwp_msg91();