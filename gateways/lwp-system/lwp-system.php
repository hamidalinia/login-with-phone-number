<?php

class lwp_system
{
    function __construct()
    {
        add_action('idehweb_custom_fields', array(&$this, 'admin_init'));
        add_filter('lwp_add_to_default_gateways', array(&$this, 'lwp_add_to_default_gateways'));
        add_action('lwp_send_sms_system', array(&$this, 'lwp_send_sms_system'), 10, 2);
    }

    function lwp_add_to_default_gateways($args = [])
    {
        if (!is_array($args)) {
            $args = [];
        }
        array_push($args, ["value" => "system", "label" => __("System default", 'login-with-phone-number')]);
        return $args;
    }

    function admin_init()
    {
        add_settings_field('idehweb_system_packages', __('System packages', 'login-with-phone-number'), array(&$this, 'setting_idehweb_packages'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel lwp-gateways related_to_system']);
        add_settings_field('idehweb_system_credit', __('System otp credit', 'login-with-phone-number'), array(&$this, 'setting_idehweb_credit'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel lwp-gateways related_to_system']);
        add_settings_field('idehweb_system_message', __('Enter Message', 'login-with-phone-number'), array(&$this, 'setting_idehweb_message'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel lwp-gateways related_to_system']);
        add_settings_field('idehweb_system_api_key', __('Enter system API Key', 'login-with-phone-number'), array(&$this, 'setting_idehweb_api_key'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel lwp-gateways related_to_system']);

        register_setting('idehweb-lwp', 'idehweb_system_packages');
        register_setting('idehweb-lwp', 'idehweb_system_credit');
//        register_setting('idehweb-lwp', 'idehweb_system_message');
        register_setting('idehweb-lwp', 'idehweb_system_api_key');


    }


    function setting_idehweb_packages()
    {
        $options = get_option('idehweb_lwp_settings');
        $setting_idehweb_packages = isset($options['setting_idehweb_packages']) ? esc_attr($options['setting_idehweb_packages']) : '';
        $twilio_sms_price = 0.15;

        echo esc_html__(
            'You can use our default system for OTP SMS by purchasing credits. Currently, we support sending OTPs only via WhatsApp. Let us know if you need help getting started!',
            'login-with-phone-number'
        );

        // Define your credit packages including a smaller starter package
        $packages = [
            [
                'credits'     => 80,
                'price'       => 7,
                'description' => __('Starter package for trying our WhatsApp OTP service.', 'login-with-phone-number'),
                'buy_url'     => 'https://idehweb.com/product/80-whatsapp-otp-credits/',
            ],
            [
                'credits'     => 400,
                'price'       => 12,
                'description' => __('Perfect for small businesses needing occasional OTPs.', 'login-with-phone-number'),
                'buy_url'     => 'https://idehweb.com/product/400-whatsapp-otp-credits/',
            ],
            [
                'credits'     => 2000,
                'price'       => 42,
                'description' => __('Ideal for larger businesses with higher OTP traffic.', 'login-with-phone-number'),
                'buy_url'     => 'https://idehweb.com/product/2000-whatsapp-otp-credits/',
            ],
        ];

        echo '<div style="display: flex; gap: 20px; margin-top: 20px;">';

        foreach ($packages as $package) {
            // Calculate savings per package
            $whatsapp_unit_price = $package['price'] / $package['credits'];
            $savings_per_credit = $twilio_sms_price - $whatsapp_unit_price;
            $total_savings = $savings_per_credit * $package['credits'];
            $total_savings = $total_savings > 0 ? $total_savings : 0;

            echo '<div style="flex: 1; background: #f0f8ff; border: 1px solid #b3d4fc; padding: 20px; border-radius: 6px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">';

            // WhatsApp Icon
            $whatsapp_icon_url = plugin_dir_url(__FILE__) . '../../images/whatsapp.png';
            echo '<img src="' . esc_url($whatsapp_icon_url) . '" alt="WhatsApp" style="width: 40px; height: 40px; margin-right: 10px; vertical-align: middle;">';

            echo '<h3 style="margin-top: 0;">' . esc_html($package['credits']) . ' credits</h3>';
            echo '<p style="font-size: 18px; font-weight: bold; margin: 5px 0;">$' . esc_html(number_format($package['price'], 2)) . '</p>';
            echo '<p>' . esc_html($package['description']) . '</p>';

            // Expiration info
            echo '<p style="font-style: italic; font-size: 13px; color: #555;">' . esc_html__('Credits valid for 30 days from purchase.', 'login-with-phone-number') . '</p>';

            // Savings message
            if ($total_savings > 0) {
                echo '<p style="color: #1a7f37; font-weight: bold; margin-top: 10px;">';
                echo sprintf(
                    esc_html__('Save up to $%s compared to SMS!', 'login-with-phone-number'),
                    esc_html(number_format($total_savings, 2))
                );
                echo '</p>';
            }

            echo '<a href="' . esc_url($package['buy_url']) . '" class="button button-primary" style="margin-top: 10px;" target="_blank">' . esc_html__('Buy Now', 'login-with-phone-number') . '</a>';

            echo '</div>';
        }

        echo '</div>';

        echo '<p style="margin-top: 15px;">' . esc_html__(
                'After purchase, you can send OTPs via WhatsApp immediately.',
                'login-with-phone-number'
            ) . '</p>';
    }


    function lwp_send_sms_system($phone_number, $code)
    {
        $options = get_option('idehweb_lwp_settings');
        $apiKey = isset($options['idehweb_system_api_key']) ? sanitize_text_field($options['idehweb_system_api_key']) : '';
        $message = isset($options['idehweb_system_message']) ? sanitize_text_field($options['idehweb_system_message']) : '';

        $message = str_replace('${code}', sanitize_text_field($code), $message);

        $url = 'https://messaging.idehweb.com/wp-json/wpotp/v1/store';
        $params = [
            "token" => $apiKey,
            "phone_number" => $phone_number,
            "message_text" => $message
        ];

        $response = wp_safe_remote_post($url, [
            'timeout' => 60,
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded'
            ],
            'body' => http_build_query($params)
        ]);

        $body = wp_remote_retrieve_body($response);
        // Optionally handle the response
    }


    function setting_idehweb_api_key()
    {
        $options = get_option('idehweb_lwp_settings');
        $apiKey = isset($options['idehweb_system_api_key']) ? esc_attr($options['idehweb_system_api_key']) : '';
        echo '<input type="text" name="idehweb_lwp_settings[idehweb_system_api_key]" class="regular-text" value="' . $apiKey . '" /> ';
    }
    /**
     * Fetch credit and expiration from remote API.
     * Returns array on success: ['credit' => float, 'credit_expiration' => string]
     * Returns WP_Error on failure.
     */
    function idehweb_fetch_credit() {
        $options = get_option('idehweb_lwp_settings');
        $apiKey = isset($options['idehweb_system_api_key']) ? sanitize_text_field($options['idehweb_system_api_key']) : '';

        if (empty($apiKey)) {
            return new WP_Error('no_api_key', __('API Key not set.', 'login-with-phone-number'));
        }

        $url = 'https://messaging.idehweb.com/wp-json/wpotp/v1/credit?token=' . urlencode($apiKey);

        $response = wp_safe_remote_get($url, [
            'timeout' => 30,
            'headers' => [
                'Accept' => 'application/json',
            ],
        ]);

        if (is_wp_error($response)) {
            return new WP_Error('api_connection_error', sprintf(__('Error connecting to API: %s', 'login-with-phone-number'), $response->get_error_message()));
        }

        $body = wp_remote_retrieve_body($response);
        // For debugging (disable in production)
        // error_log('API raw response: ' . $body);

        $data = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return new WP_Error('json_error', sprintf(__('JSON decode error: %s', 'login-with-phone-number'), json_last_error_msg()));
        }

        if (!is_array($data)) {
            return new WP_Error('unexpected_format', __('Unexpected response format.', 'login-with-phone-number'));
        }

        if (isset($data['message'])) {
            return new WP_Error('api_error', sprintf(__('API error: %s', 'login-with-phone-number'), esc_html($data['message'])));
        }

        if (isset($data['credit']) && isset($data['credit_expiration'])) {
            return [
                'credit' => floatval($data['credit']),
                'credit_expiration' => sanitize_text_field($data['credit_expiration']),
            ];
        }

        return new WP_Error('unknown_error', __('Unknown error occurred.', 'login-with-phone-number'));
    }

    /**
     * Output credit info in settings page.
     * Use safely inside admin page callback.
     */
    function setting_idehweb_credit() {
        $credit_data = $this->idehweb_fetch_credit();

        if (is_wp_error($credit_data)) {
            echo '<p style="color:red;">' . esc_html($credit_data->get_error_message()) . '</p>';
            return;
        }

        printf(
            '<p><strong>%1$s:</strong> %2$s<br /><strong>%3$s:</strong> %4$s</p>',
            esc_html__('Current Credit', 'login-with-phone-number'),
            esc_html($credit_data['credit']),
            esc_html__('Credit Expiration', 'login-with-phone-number'),
            esc_html($credit_data['credit_expiration'])
        );
    }


//    function setting_idehweb_credit()
//    {
//        $options = get_option('idehweb_lwp_settings');
//        $credit = isset($options['idehweb_system_credit']) ? esc_attr($options['idehweb_system_credit']) : '';
//        echo '0';
//
//    }

    function setting_idehweb_message()
    {
        $options = get_option('idehweb_lwp_settings');
        $message = isset($options['idehweb_system_message']) ? sanitize_textarea_field($options['idehweb_system_message']) : '';
        echo '<textarea name="idehweb_lwp_settings[idehweb_system_message]" class="regular-text">' . esc_textarea($message) . '</textarea>';
        echo '<p class="description">' . __('Enter message, use ${code} for the verification code', 'login-with-phone-number') . '</p>';
    }
}

global $lwp_system;
$lwp_system = new lwp_system();

