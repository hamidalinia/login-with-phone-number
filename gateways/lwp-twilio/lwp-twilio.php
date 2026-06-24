<?php
require __DIR__ . '/Twilio/autoload.php';

use Twilio\Rest\Client;

class lwp_twilio
{
    /**
     * Class constructor to set up hooks and filters.
     */
    public function __construct()
    {
        // Add a new section to the LWP settings page
        add_action('idehweb_custom_fields', array($this, 'admin_init'));

        // Add 'twilio' to the list of available gateways in LWP
        add_filter('lwp_add_to_default_gateways', array($this, 'lwp_add_to_default_gateways'));
        add_filter('lwp_add_to_verification_support_functions', array($this, 'lwp_add_to_verification_support_functions'));

        // Hook into the LWP action to send SMS via Twilio
        // Note: The LWP core plugin expects two arguments, so we define 10, 2
        add_action('lwp_send_sms_twilio', array($this, 'lwp_send_sms_twilio'), 10, 2);
        add_action('lwp_verify_twilio', array($this, 'lwp_verify_twilio'), 10, 2);
    }

    /**
     * Adds Twilio to the list of gateways in the LWP plugin.
     *
     * @param array $args The existing list of gateways.
     * @return array The updated list of gateways.
     */
    public function lwp_add_to_default_gateways($args = [])
    {
        if (!is_array($args)) {
            $args = [];
        }

        $exists = false;

        // Check if 'twilio' gateway already exists in the list
        foreach ($args as &$gateway) {
            if ($gateway['value'] === 'twilio') {
                $gateway['isFree'] = true;
                $gateway['label'] = __("Twilio", 'lwp-twilio'); // Update label
                $exists = true;
                break;
            }
        }

        // If 'twilio' is not in the list, add it
        if (!$exists) {
            $args[] = ["value" => "twilio","isFree" => true,  "label" => __("Twilio", 'lwp-twilio')];
        }
        return $args;
    }

    /**
     * Adds Twilio to the list of gateways that support verification function.
     *
     * @param array $args The existing list of gateways.
     * @return array The updated list of gateways.
     */
    public function lwp_add_to_verification_support_functions($args = [])
    {
        if (!is_array($args)) {
            $args = [];
        }

        $exists = false;

        // Check if 'twilio' gateway already exists in the list
        foreach ($args as &$gateway) {
            if ($gateway === 'twilio') {
                $exists = true;
                break;
            }
        }

        // If 'twilio' is not in the list, add it
        if (!$exists) {
            $args[] = "twilio";
        }
        return $args;
    }

    /**
     * Registers the Twilio settings fields in the WordPress admin.
     */
    public function admin_init()
    {
//        add_settings_field('idehweb_twilio_username', __('Enter TWILIO ACCOUNT SID', 'lwp-twilio'), array(&$this, 'setting_idehweb_username'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel  lwp-gateways related_to_twilio']);
//        add_settings_field('idehweb_twilio_password', __('Enter TWILIO AUTH TOKEN', 'lwp-twilio'), array(&$this, 'setting_idehweb_password'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel  lwp-gateways related_to_twilio']);
//        add_settings_field('idehweb_twilio_from', __('Enter from number', 'lwp-twilio'), array(&$this, 'setting_idehweb_from'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel  lwp-gateways related_to_twilio']);
//        add_settings_field('idehweb_twilio_message', __('Enter text (use ${code} for OTP code)', 'lwp-twilio'), array(&$this, 'setting_idehweb_message'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel  lwp-gateways related_to_twilio']);

        // Add the settings fields for Twilio's credentials
        add_settings_field(
            'idehweb_twilio_account_sid',
            __('Enter TWILIO ACCOUNT SID', 'lwp-twilio'),
            array($this, 'setting_idehweb_account_sid'),
            'idehweb-lwp',
            'idehweb-lwp',
            ['label_for' => 'lwp_twilio_account_sid', 'class' => 'ilwplabel lwp-gateways related_to_twilio']
        );
        add_settings_field(
            'idehweb_twilio_auth_token',
            __('Enter TWILIO AUTH TOKEN', 'lwp-twilio'),
            array($this, 'setting_idehweb_auth_token'),
            'idehweb-lwp',
            'idehweb-lwp',
            ['label_for' => 'lwp_twilio_auth_token', 'class' => 'ilwplabel lwp-gateways related_to_twilio']
        );
        add_settings_field(
            'idehweb_twilio_verify_service_sid',
            __('Enter TWILIO VERIFY SERVICE SID', 'lwp-twilio'),
            array($this, 'setting_idehweb_verify_service_sid'),
            'idehweb-lwp',
            'idehweb-lwp',
            ['label_for' => 'lwp_twilio_verify_service_sid', 'class' => 'ilwplabel lwp-gateways related_to_twilio']
        );
    }

    /**
     * An empty method for the section introduction, as required by the LWP settings page structure.
     */
    public function section_intro()
    {
        // No content needed here.
    }

    /**
     * A placeholder function to validate settings.
     *
     * @param array $input The settings input to validate.
     * @return array The validated input.
     */
    public function settings_validate($input)
    {
        return $input;
    }

    /**
     * Sends a verification code using Twilio's Verify API.
     *
     * @param string $phone_number The phone number to send the SMS to.
     * @param string $code The verification code (this is ignored as Twilio handles it).
     * @return bool|string The status of the verification or false on failure.
     */
    public function lwp_send_sms_twilio($phone_number, $code)
    {
        // Retrieve Twilio credentials from your plugin's options
        $options = get_option('idehweb_lwp_settings');

        // Ensure the required settings exist to prevent errors
        if (
            !isset($options['lwp_twilio_account_sid']) ||
            !isset($options['lwp_twilio_auth_token']) ||
            !isset($options['lwp_twilio_verify_service_sid'])
        ) {
            // Log an error or return false if settings are missing
            error_log('LWP Twilio Plugin: Missing Twilio API credentials in settings.');
            return false;
        }

        $account_sid = $options['lwp_twilio_account_sid'];
        $auth_token = $options['lwp_twilio_auth_token'];
        $verify_service_sid = $options['lwp_twilio_verify_service_sid'];

        // Ensure the phone number is in E.164 format (e.g., +1234567890)
        // The LWP core plugin might pass it with or without the leading '+'
        $phone_number = ltrim($phone_number, '+');
//        $e164_phone_number = '+' . $phone_number;
        $e164_phone_number = '+' . $phone_number;
//echo '$verify_service_sid'.$verify_service_sid.'<br>';
//echo '$account_sid'.$account_sid.'<br>';
//echo '$auth_token'.$auth_token.'<br>';
//echo '$e164_phone_number'.$e164_phone_number.'<br>';
        try {
            // Initialize the Twilio client
            $twilio = new Client($account_sid, $auth_token);
//echo $code;
            // Send the verification code using the Verify Service
            $verification = $twilio->verify->v2->services($verify_service_sid)
                ->verifications
                ->create($e164_phone_number, "sms",  ["customCode" => $code]);

            // Return the verification status or object on success
//            print_r($verification->status);

        } catch (Exception $e) {
            // Log the error for debugging
//            print_r('Twilio SMS sending failed: ' . $e->getMessage());
//            return false;
        }
    }

    /**
     * Displays the input field for the Twilio Account SID.
     */
    public function setting_idehweb_account_sid()
    {
        $options = get_option('idehweb_lwp_settings');
        $value = isset($options['lwp_twilio_account_sid']) ? esc_attr($options['lwp_twilio_account_sid']) : '';
        echo '<input type="text" name="idehweb_lwp_settings[lwp_twilio_account_sid]" id="lwp_twilio_account_sid" class="regular-text" value="' . $value . '" />';
        echo '<p class="description">' . __('Enter your Twilio Account SID', 'lwp-twilio') . '</p>';
    }

    /**
     * Displays the input field for the Twilio Auth Token.
     */
    public function setting_idehweb_auth_token()
    {
        $options = get_option('idehweb_lwp_settings');
        $value = isset($options['lwp_twilio_auth_token']) ? esc_attr($options['lwp_twilio_auth_token']) : '';
        echo '<input type="text" name="idehweb_lwp_settings[lwp_twilio_auth_token]" id="lwp_twilio_auth_token" class="regular-text" value="' . $value . '" />';
        echo '<p class="description">' . __('Enter your Twilio Auth Token', 'lwp-twilio') . '</p>';
    }

    /**
     * Displays the input field for the Twilio Verify Service SID.
     */
    public function setting_idehweb_verify_service_sid()
    {
        $options = get_option('idehweb_lwp_settings');
        $value = isset($options['lwp_twilio_verify_service_sid']) ? esc_attr($options['lwp_twilio_verify_service_sid']) : '';
        echo '<input type="text" name="idehweb_lwp_settings[lwp_twilio_verify_service_sid]" id="lwp_twilio_verify_service_sid" class="regular-text" value="' . $value . '" />';
        echo '<p class="description">' . __('Enter your Twilio Verify Service SID', 'lwp-twilio') . '</p>';
    }
}

// Instantiate the class to start the plugin
global $lwp_twilio;
$lwp_twilio = new lwp_twilio();



