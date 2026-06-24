<?php
class lwp_kwtsms
{
    function __construct()
    {
        add_action('idehweb_custom_fields', [$this, 'admin_init']);
        add_filter('lwp_add_to_default_gateways', [$this, 'lwp_add_to_default_gateways']);
        add_action('lwp_send_sms_kwtsms', [$this, 'lwp_send_sms_kwtsms'], 10, 2);
    }

    function lwp_add_to_default_gateways($args = [])
    {
        if (!is_array($args)) {
            $args = [];
        }

        $exists = false;

        // Check if 'kwtsms' gateway already exists in the list
        foreach ($args as &$gateway) {
            if ($gateway['value'] === 'kwtsms') {
                $gateway['label'] = esc_html__("kwtSMS", 'login-with-phone-number'); // Update label
                $gateway['isFree'] = true;
                $exists = true;
                break;
            }
        }

        // If 'kwtsms' is not in the list, add it
        if (!$exists) {
            $args[] = ["value" => "kwtsms","isFree" => true, "label" => esc_html__("kwtSMS", 'login-with-phone-number')];
        }
        return $args;
    }

    function admin_init()
    {
        add_settings_field('idehweb_kwtsms_username', __('Enter kwtSMS API Username', 'login-with-phone-number'), array(&$this, 'setting_kwtsms_username'), 'idehweb-lwp', 'idehweb-lwp',['label_for' => '', 'class' => 'ilwplabel lwp-gateways related_to_kwtsms']);
        add_settings_field('idehweb_kwtsms_password', __('Enter kwtSMS API Password', 'login-with-phone-number'), array(&$this, 'setting_kwtsms_password'), 'idehweb-lwp', 'idehweb-lwp',['label_for' => '', 'class' => 'ilwplabel lwp-gateways related_to_kwtsms']);
        add_settings_field('idehweb_kwtsms_sender', __('Enter kwtSMS SenderID', 'login-with-phone-number'), array(&$this, 'setting_kwtsms_sender'), 'idehweb-lwp', 'idehweb-lwp',['label_for' => '', 'class' => 'ilwplabel lwp-gateways related_to_kwtsms']);
    }

    function lwp_send_sms_kwtsms($phone_number, $message)
    {
        $options = get_option('idehweb_lwp_settings');
        $username = isset($options['idehweb_kwtsms_username']) ? sanitize_text_field($options['idehweb_kwtsms_username']) : '';
        $password = isset($options['idehweb_kwtsms_password']) ? sanitize_text_field($options['idehweb_kwtsms_password']) : '';
        $sender = isset($options['idehweb_kwtsms_sender']) ? sanitize_text_field($options['idehweb_kwtsms_sender']) : '';

	// extend the OTP message:
	if(strlen($message)<=6)
		$message = 'Your OTP is: '.$message;

        // Clean phone number - remove + and leading zeros if any
        $to = preg_replace('/^(\+|00)/', '', $phone_number);

        if (empty($username) || empty($password) || empty($sender) || empty($to)) {
            return;
        }

        $url = "https://www.kwtsms.com/API/send/";

        $response = wp_safe_remote_post($url, [
            'timeout' => 30,
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            'body' => [
                'username' => $username,
                'password' => $password,
                'mobile' => $to,
                'sender' => $sender,
                'message' => $message,
                'test' => '0',
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
	//$result = json_decode($response_body, true);
	$result = explode(':',$response_body);

        if (isset($result[0]) && $result[0] == 'OK') {
            return true;
        } else {
            return false;
        }
    }

    function setting_kwtsms_username()
    {
        $options = get_option('idehweb_lwp_settings');
        $username = isset($options['idehweb_kwtsms_username']) ? esc_attr($options['idehweb_kwtsms_username']) : '';
        echo '<input type="text" name="idehweb_lwp_settings[idehweb_kwtsms_username]" class="regular-text" value="' . esc_attr($username) . '" />';
        echo '<p class="description">' . esc_html__('Enter the kwtSMS API username.', 'login-with-phone-number') . '</p>';
    }

    function setting_kwtsms_password()
    {
        $options = get_option('idehweb_lwp_settings');
        $password = isset($options['idehweb_kwtsms_password']) ? esc_attr($options['idehweb_kwtsms_password']) : '';
        echo '<input type="password" name="idehweb_lwp_settings[idehweb_kwtsms_password]" class="regular-text" value="' . esc_attr($password) . '" />';
        echo '<p class="description">' . esc_html__('Enter the kwtSMS API password.', 'login-with-phone-number') . '</p>';
    }

    function setting_kwtsms_sender()
    {
        $options = get_option('idehweb_lwp_settings');
        $sender = isset($options['idehweb_kwtsms_sender']) ? esc_attr($options['idehweb_kwtsms_sender']) : '';
        echo '<input type="text" name="idehweb_lwp_settings[idehweb_kwtsms_sender]" class="regular-text" value="' . esc_attr($sender) . '" />';
        echo '<p class="description">' . esc_html__('Enter the sender ID for kwtSMS messages.', 'login-with-phone-number') . '</p>';
    }
}

global $lwp_kwtsms;
$lwp_kwtsms = new lwp_kwtsms();
