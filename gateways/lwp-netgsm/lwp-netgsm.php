<?php
/**
 * LWP Netgsm Gateway Class
 *
 * @package LoginWithPhoneNumber
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class lwp_netgsm
 *
 * Netgsm SMS Gateway integration for Login With Phone Number plugin
 */
class lwp_netgsm
{
    /**
     * Class constructor to set up hooks and filters.
     */
    public function __construct()
    {
        // Add a new section to the LWP settings page
        add_action('idehweb_custom_fields', array($this, 'admin_init'));

        // Add 'netgsm' to the list of available gateways in LWP
        add_filter('lwp_add_to_default_gateways', array($this, 'lwp_add_to_default_gateways'));
        add_filter('lwp_add_to_verification_support_functions', array($this, 'lwp_add_to_verification_support_functions'));

        // Hook into the LWP action to send SMS via Netgsm
        add_action('lwp_send_sms_netgsm', array($this, 'lwp_send_sms_netgsm'), 10, 2);
    }

    /**
     * Adds Netgsm to the list of gateways in the LWP plugin.
     *
     * @param array $args The existing list of gateways.
     * @return array The updated list of gateways.
     */
    public function lwp_add_to_default_gateways($args = array())
    {
        if (!is_array($args)) {
            $args = array();
        }

        $exists = false;

        // Check if 'netgsm' gateway already exists in the list
        foreach ($args as &$gateway) {
            if ($gateway['value'] === 'netgsm') {
                $gateway['isFree'] = true;
                $gateway['label'] = __('Netgsm', 'login-with-phone-number');
                $exists = true;
                break;
            }
        }

        // If 'netgsm' is not in the list, add it
        if (!$exists) {
            $args[] = array(
                'value' => 'netgsm',
                'isFree' => true,
                'label' => __('Netgsm', 'login-with-phone-number')
            );
        }
        return $args;
    }

    /**
     * Adds Netgsm to the list of gateways that support verification function.
     *
     * @param array $args The existing list of gateways.
     * @return array The updated list of gateways.
     */
    public function lwp_add_to_verification_support_functions($args = array())
    {
        if (!is_array($args)) {
            $args = array();
        }

        $exists = false;

        // Check if 'netgsm' gateway already exists in the list
        foreach ($args as &$gateway) {
            if ($gateway === 'netgsm') {
                $exists = true;
                break;
            }
        }

        // If 'netgsm' is not in the list, add it
        if (!$exists) {
            $args[] = 'netgsm';
        }
        return $args;
    }

    /**
     * Registers the Netgsm settings fields in the WordPress admin.
     */
    public function admin_init()
    {
        // Add the settings fields for Netgsm's credentials (username/password for Basic Auth)
        add_settings_field(
            'idehweb_netgsm_username',
            __('Enter Netgsm Username', 'login-with-phone-number'),
            array($this, 'setting_idehweb_username'),
            'idehweb-lwp',
            'idehweb-lwp',
            array(
                'label_for' => 'lwp_netgsm_username',
                'class' => 'ilwplabel lwp-gateways related_to_netgsm'
            )
        );

        add_settings_field(
            'idehweb_netgsm_password',
            __('Enter Netgsm Password', 'login-with-phone-number'),
            array($this, 'setting_idehweb_password'),
            'idehweb-lwp',
            'idehweb-lwp',
            array(
                'label_for' => 'lwp_netgsm_password',
                'class' => 'ilwplabel lwp-gateways related_to_netgsm'
            )
        );

        add_settings_field(
            'idehweb_netgsm_from',
            __('Enter Sender Name (msgheader)', 'login-with-phone-number'),
            array($this, 'setting_idehweb_from'),
            'idehweb-lwp',
            'idehweb-lwp',
            array(
                'label_for' => 'lwp_netgsm_from',
                'class' => 'ilwplabel lwp-gateways related_to_netgsm'
            )
        );

        add_settings_field(
            'idehweb_netgsm_message',
            __('Enter Message Template (use ${code} for OTP code)', 'login-with-phone-number'),
            array($this, 'setting_idehweb_message'),
            'idehweb-lwp',
            'idehweb-lwp',
            array(
                'label_for' => 'lwp_netgsm_message',
                'class' => 'ilwplabel lwp-gateways related_to_netgsm'
            )
        );

        add_settings_field(
            'idehweb_netgsm_encoding',
            __('Message Encoding', 'login-with-phone-number'),
            array($this, 'setting_idehweb_encoding'),
            'idehweb-lwp',
            'idehweb-lwp',
            array(
                'label_for' => 'lwp_netgsm_encoding',
                'class' => 'ilwplabel lwp-gateways related_to_netgsm'
            )
        );
    }

    /**
     * Sends a verification code using Netgsm's REST API v2 with Basic Authentication.
     *
     * @param string $phone_number The phone number to send the SMS to.
     * @param string $code The verification code.
     * @return bool|array The response from Netgsm or false on failure.
     */
    public function lwp_send_sms_netgsm($phone_number, $code)
    {
        // Retrieve Netgsm credentials from the plugin's options
        $options = get_option('idehweb_lwp_settings');

        // Ensure the required settings exist
        if (
            !isset($options['lwp_netgsm_username']) ||
            empty($options['lwp_netgsm_username']) ||
            !isset($options['lwp_netgsm_password']) ||
            empty($options['lwp_netgsm_password']) ||
            !isset($options['lwp_netgsm_from']) ||
            empty($options['lwp_netgsm_from'])
        ) {
            return false;
        }

        $username = sanitize_text_field($options['lwp_netgsm_username']);
        $password = sanitize_text_field($options['lwp_netgsm_password']);
        $from = sanitize_text_field($options['lwp_netgsm_from']);
        $encoding = isset($options['lwp_netgsm_encoding']) ? sanitize_text_field($options['lwp_netgsm_encoding']) : 'TR';

        // Get message template or use default
        $message = isset($options['lwp_netgsm_message']) && !empty($options['lwp_netgsm_message'])
            ? sanitize_text_field($options['lwp_netgsm_message'])
            : 'Your verification code: ${code}';

        // Replace the placeholder with the actual code
        $message = str_replace('${code}', sanitize_text_field($code), $message);

        // Format phone number (remove any non-digit characters)
        $phone_number = preg_replace('/[^0-9]/', '', $phone_number);

        // Netgsm REST API v2 endpoint
        $url = 'https://api.netgsm.com.tr/sms/rest/v2/otp';

        // Prepare data as per the provided example
        $data = array(
            'usercode' => $username,
            'msgheader' => $from,
            'messages' => array(
                array(
                    'msg' => $message,
                    'no' => $phone_number
                )
            ),
            'encoding' => $encoding,
            'iysfilter' => '',
            'partnercode' => ''
        );

        // Make POST request with JSON and Basic Auth
        $response = wp_safe_remote_post(
            $url,
            array(
                'timeout' => 60,
                'headers' => array(
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Basic ' . base64_encode($username . ':' . $password)
                ),
                'body' => wp_json_encode($data)
            )
        );

        // Check for errors
        if (is_wp_error($response)) {
            return false;
        }

        $body = wp_remote_retrieve_body($response);
        $response_code = wp_remote_retrieve_response_code($response);

        // Parse JSON response
        $result = json_decode($body, true);

        // Check response
        if ($response_code === 200) {
            // Check if code is "00" (success) as per documentation
            if (isset($result['code']) && $result['code'] === '00') {
                return $result;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Displays the input field for the Netgsm Username.
     */
    public function setting_idehweb_username()
    {
        $options = get_option('idehweb_lwp_settings');
        $value = isset($options['lwp_netgsm_username']) ? esc_attr($options['lwp_netgsm_username']) : '';
        ?>
        <input type="text" name="idehweb_lwp_settings[lwp_netgsm_username]" id="lwp_netgsm_username" class="regular-text" value="<?php echo esc_attr($value); ?>" />
        <p class="description"><?php esc_html_e('Enter your Netgsm Username', 'login-with-phone-number'); ?></p>
        <?php
    }

    /**
     * Displays the input field for the Netgsm Password.
     */
    public function setting_idehweb_password()
    {
        $options = get_option('idehweb_lwp_settings');
        $value = isset($options['lwp_netgsm_password']) ? esc_attr($options['lwp_netgsm_password']) : '';
        ?>
        <input type="password" name="idehweb_lwp_settings[lwp_netgsm_password]" id="lwp_netgsm_password" class="regular-text" value="<?php echo esc_attr($value); ?>" />
        <p class="description"><?php esc_html_e('Enter your Netgsm Password', 'login-with-phone-number'); ?></p>
        <?php
    }

    /**
     * Displays the input field for the Netgsm Sender Name (msgheader).
     */
    public function setting_idehweb_from()
    {
        $options = get_option('idehweb_lwp_settings');
        $value = isset($options['lwp_netgsm_from']) ? esc_attr($options['lwp_netgsm_from']) : '';
        ?>
        <input type="text" name="idehweb_lwp_settings[lwp_netgsm_from]" id="lwp_netgsm_from" class="regular-text" value="<?php echo esc_attr($value); ?>" />
        <p class="description"><?php esc_html_e('Enter your Sender Name/Header (msgheader) approved by Netgsm', 'login-with-phone-number'); ?></p>
        <?php
    }

    /**
     * Displays the input field for the Netgsm Message Template.
     */
    public function setting_idehweb_message()
    {
        $options = get_option('idehweb_lwp_settings');
        $value = isset($options['lwp_netgsm_message']) ? esc_attr($options['lwp_netgsm_message']) : 'Your verification code: ${code}';
        ?>
        <input type="text" name="idehweb_lwp_settings[lwp_netgsm_message]" id="lwp_netgsm_message" class="regular-text" value="<?php echo esc_attr($value); ?>" />
        <p class="description"><?php esc_html_e('Enter your message template. Use ${code} as placeholder for the OTP code.', 'login-with-phone-number'); ?></p>
        <?php
    }

    /**
     * Displays the dropdown for message encoding.
     */
    public function setting_idehweb_encoding()
    {
        $options = get_option('idehweb_lwp_settings');
        $value = isset($options['lwp_netgsm_encoding']) ? esc_attr($options['lwp_netgsm_encoding']) : 'TR';
        ?>
        <select name="idehweb_lwp_settings[lwp_netgsm_encoding]" id="lwp_netgsm_encoding">
            <option value="TR" <?php selected($value, 'TR'); ?>><?php esc_html_e('TR (Türkçe)', 'login-with-phone-number'); ?></option>
            <option value="EN" <?php selected($value, 'EN'); ?>><?php esc_html_e('EN (İngilizce)', 'login-with-phone-number'); ?></option>
            <option value="UTF-8" <?php selected($value, 'UTF-8'); ?>><?php esc_html_e('UTF-8', 'login-with-phone-number'); ?></option>
        </select>
        <p class="description"><?php esc_html_e('Select message encoding type', 'login-with-phone-number'); ?></p>
        <?php
    }
}

// Instantiate the class to start the plugin
global $lwp_netgsm;
$lwp_netgsm = new lwp_netgsm();