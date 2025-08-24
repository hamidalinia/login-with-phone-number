<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

trait Ajax_Handlers
{
    function idehweb_lwp_merge_old_woocommerce_users()
    {
        if (!isset($_GET['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['nonce'])), 'lwp_admin_nonce')) {
            wp_die('Busted!');
        }

        check_ajax_referer('lwp_admin_nonce', 'nonce');

        $users = get_users();

        foreach ($users as $user) {
            $user_id = $user->ID;
            $billing_phone = get_user_meta($user_id, 'billing_phone', true);
            $billing_phone = str_replace('+', '', $billing_phone);
            if (!empty($billing_phone)) {
                update_user_meta($user_id, 'phone_number', $billing_phone);
            }
        }

        wp_send_json_success('Phone numbers synced successfully.');

    }


    function lwp_ajax_login()
    {
        if (!isset($_GET['username']) || !isset($_GET['method'])) {
            wp_send_json_error('Missing required parameters');
        }

        $username = sanitize_text_field(wp_unslash($_GET['username']));
        $method = sanitize_text_field(wp_unslash($_GET['method']));
        $options = get_option('idehweb_lwp_settings');

        if (!isset($options['idehweb_store_number_with_country_code'])) $options['idehweb_store_number_with_country_code'] = '1';
        if (!isset($options['idehweb_country_codes_default'])) $options['idehweb_country_codes_default'] = '';

        if (!isset($_GET['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['nonce'])), 'lwp_login')) {
            wp_die('Busted!');
        }


        if (preg_replace('/^(\-){0,1}[0-9]+(\.[0-9]+){0,1}/', '', $username) == "") {
            $phone_number = ltrim($username, '0');
            $phone_number = substr($phone_number, 0, 15);
//echo $phone_number;
//die();
            if (strlen($phone_number) < 10) {
                wp_send_json([
                    'success' => false,
                    'phone_number' => $phone_number,
                    'message' => __('phone number is wrong!', 'login-with-phone-number')
                ]);

            }
            $phone_number_with_country_code = false;

            if ($options['idehweb_store_number_with_country_code'] != '1' && ($options['idehweb_country_codes_default'] != '')) {
                $country_code = $this->get_country_code_by_code($options['idehweb_country_codes_default']);
                $phone_number_with_country_code = $phone_number;
                $phone_number = preg_replace('/^' . preg_quote($country_code, '/') . '/', '', $phone_number);

            }
            $username_exists = $this->phone_number_exist($phone_number);
//            $registration = get_site_option('registration');
            if (!isset($options['idehweb_default_role'])) $options['idehweb_default_role'] = "";
//            echo $options['idehweb_default_role'];

            if (!isset($options['idehweb_user_registration'])) $options['idehweb_user_registration'] = '0';
            $registration = $options['idehweb_user_registration'];
            $is_multisite = is_multisite();
            if ($is_multisite) {
                if ($registration == '0' && !$username_exists) {
                    wp_send_json([
                        'success' => false,
                        'phone_number' => $username,
                        'registeration' => $registration,
                        'is_multisite' => $is_multisite,
                        'username_exists' => $username_exists,
                        'message' => __('users can not register!', 'login-with-phone-number')
                    ]);

                }
            } else {
                if (!$username_exists) {

                    if ($registration == '0') {
                        wp_send_json([
                            'success' => false,
                            'phone_number' => $username,
                            'registeration' => $registration,
                            'is_multisite' => $is_multisite,
                            'username_exists' => $username_exists,
                            'message' => __('users can not register!', 'login-with-phone-number')
                        ]);

                    }
                }
            }
            $userRegisteredNow = false;
            if (!$username_exists) {
                $info = array();
                $info['user_login'] = $this->generate_username($phone_number);
                $info['user_nicename'] = $info['nickname'] = $info['display_name'] = $this->generate_nickname();

                $info['user_url'] = isset($_GET['website']) ? sanitize_text_field(wp_unslash($_GET['website'])) : '';


                if ($options['idehweb_default_role'] && $options['idehweb_default_role'] !== "") {

                    $info['role'] = $options['idehweb_default_role'];
                }
                $user_register = wp_insert_user($info);
                if (is_wp_error($user_register)) {
                    $error = $user_register->get_error_codes();

                    if (in_array('empty_user_login', $error)) {
                        wp_send_json([
                            'success' => false,
                            'phone_number' => $phone_number,
                            'message' => $user_register->get_error_message('empty_user_login')
                        ]);

                    } elseif (in_array('existing_user_login', $error)) {
                        wp_send_json([
                            'success' => false,
                            'phone_number' => $phone_number,
                            'message' => __('This username is already registered.', 'login-with-phone-number')
                        ]);

                    } elseif (in_array('existing_user_email', $error)) {
                        wp_send_json([
                            'success' => false,
                            'phone_number' => $phone_number,
                            'message' => __('This email address is already registered.', 'login-with-phone-number')
                        ]);

                    }
                    wp_die();
                } else {
                    add_user_meta($user_register, 'phone_number', sanitize_user($phone_number));
                    update_user_meta($user_register, '_billing_phone', sanitize_user($phone_number));
                    update_user_meta($user_register, 'billing_phone', sanitize_user($phone_number));
//                    update_user_meta($user_register, '_shipping_phone', sanitize_user($phone_number));
//                    update_user_meta($user_register, 'shipping_phone', sanitize_user($phone_number));
                    $userRegisteredNow = true;
                    add_user_meta($user_register, 'userRegisteredNow', '1');

                    add_user_meta($user_register, 'updatedPass', 0);
                    $username_exists = $user_register;

                }


            }
            $showPass = false;
            $log = '';


//            $options = get_option('idehweb_lwp_settings');
            if (!isset($options['idehweb_password_login'])) $options['idehweb_password_login'] = '1';
            $options['idehweb_password_login'] = (bool)(int)$options['idehweb_password_login'];
            if (!$options['idehweb_password_login']) {
                $log = $this->lwp_generate_token($username_exists, $phone_number_with_country_code ? $phone_number_with_country_code : $phone_number, false, $method);

            } else {
                if (!$userRegisteredNow) {
                    $showPass = true;
                } else {
                    $log = $this->lwp_generate_token($username_exists, $phone_number_with_country_code ? $phone_number_with_country_code : $phone_number, false, $method);
                }
            }
            update_user_meta($username_exists, 'activation_code_timestamp', time());

            wp_clear_auth_cookie();
            wp_send_json([
                'success' => true,
                'ID' => $username_exists,
                'phone_number' => $phone_number,
                'showPass' => $showPass,
//                '$userRegisteredNow' => $userRegisteredNow,
//                '$userRegisteredNow1' => $options['idehweb_password_login'],
                'authWithPass' => (bool)(int)$options['idehweb_password_login'],
                'message' => __('Sms sent successfully!', 'login-with-phone-number'),
                'log' => $log
            ]);


        } else {
            wp_clear_auth_cookie();

            wp_send_json([
                'success' => false,
                'phone_number' => $username,
                'message' => __('phone number is wrong!', 'login-with-phone-number')
            ]);

        }
    }

    function lwp_enter_password_action()
    {

        if (!isset($_GET['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['nonce'])), 'lwp_login')) {
            wp_die('Busted!');
        }
// Check required fields
        if (!isset($_GET['password'])) {
            wp_send_json_error('Password is required');
        }

        if (!isset($_GET['email']) && !isset($_GET['ID'])) {
            wp_send_json_error('Email or ID is required');
        }

        $password = sanitize_text_field(wp_unslash($_GET['password']));
        $email = isset($_GET['email']) ? sanitize_email(wp_unslash($_GET['email'])) : '';
        $ID = isset($_GET['ID']) ? absint($_GET['ID']) : 0;


        if ($email != '') {
            $user = get_user_by('email', $email);

        }
        if ($ID != 0) {
            $user = get_user_by('ID', $ID);

        }
        $creds = array(
            'user_login' => $user->user_login,
            'user_password' => $password,
            'remember' => true
        );

        $user = wp_signon($creds, false);

        if (is_wp_error($user)) {
            wp_send_json([
                'success' => false,
                'ID' => $user->ID,
                'err' => $user->get_error_message(),
                'message' => __('Password is incorrect!', 'login-with-phone-number')
            ]);

        } else {

            wp_send_json([
                'success' => true,
                'ID' => $user->ID,
                'message' => __('Redirecting...', 'login-with-phone-number')
            ]);


        }
    }


    function lwp_update_password_action()
    {

        if (!is_user_logged_in()) {
            wp_die('user is not logged in!');
        }
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'lwp_login')) {
            wp_die('Busted!');
        }

//        if (!isset($_POST['role'])) $_POST['role'] = '';
//        $role = sanitize_text_field($_POST['role']);
//        if ($role == "") {
//            $role = null;
//        }
        if (!isset($_POST['email'])) $_POST['email'] = '';
        $email = sanitize_email(wp_unslash($_POST['email']));  // Unsplash before sanitization
        if ($email == "") {
            $email = null;
        }

        if (!isset($_POST['username'])) $_POST['username'] = '';
        $username = sanitize_text_field(wp_unslash($_POST['username']));  // Unsplash before sanitization
        if ($username == "") {
            $username = null;
        }

        if (!isset($_POST['nickname'])) $_POST['nickname'] = '';
        $nickname = sanitize_text_field(wp_unslash($_POST['nickname']));  // Unsplash before sanitization
        if ($nickname == "") {
            $nickname = null;
        }

        if (!isset($_POST['phone_number'])) $_POST['phone_number'] = '';
        $phone_number = sanitize_text_field(wp_unslash($_POST['phone_number']));  // Unsplash before sanitization
        if ($phone_number == "") {
            $phone_number = null;
        }


        if (isset($phone_number) && $phone_number != '' && !is_numeric($phone_number)) {
            wp_send_json([
                'success' => false,
                'phone_number' => $phone_number,
                'message' => __('Please enter correct phone number', 'login-with-phone-number')
            ]);
//            wp_die();
        }
        if (isset($email) && $email != '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            wp_send_json([
                'success' => false,
                'message' => __('Email is wrong!', 'login-with-phone-number')
            ]);
//            wp_die();
        }

        if (isset($phone_number) && !isset($email)) {
            $ID = $this->phone_number_exist($phone_number);
        }

        if (!isset($phone_number) && isset($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $ID = email_exists($email);
        }

        $current_user_id = get_current_user_id();

        $ID = $current_user_id;
        $user = get_user_by('ID', $ID);


//        $password = sanitize_text_field($_POST['password']);
        if (!isset($_POST['password']) || empty($_POST['password'])) {
            wp_send_json(['success' => false, 'message' => __('Password is required', 'login-with-phone-number')]);
        }

        $password = sanitize_text_field(wp_unslash($_POST['password']));
        if (strlen($password) < 6) {
            wp_send_json(['success' => false, 'message' => __('Password too short', 'login-with-phone-number')]);
        }

        $first_name = isset($_POST['first_name']) ? sanitize_text_field(wp_unslash($_POST['first_name'])) : '';
        $last_name = isset($_POST['last_name']) ? sanitize_text_field(wp_unslash($_POST['last_name'])) : '';
        if ($user) {
            $update_array = [
                'ID' => $user->ID,
                'user_pass' => $password
            ];

            if (!empty($first_name)) {
                $update_array['first_name'] = $first_name;

            }
            if (!empty($last_name)) {
                $update_array['last_name'] = $last_name;

            }
            if (!empty($nickname)) {
                $update_array['nickname'] = $nickname;
                $update_array['display_name'] = $nickname;
            }
            if ($username) {
                $update_array['user_login'] = sanitize_user($username, true);
            }
            wp_update_user($update_array);
            update_user_meta($user->ID, 'updatedPass', 1);
            update_user_meta($user->ID, 'userRegisteredNow', '0');
            wp_send_json([
                'success' => true,
                'message' => esc_html__('Password set successfully! redirecting...', 'login-with-phone-number')
            ]);

        } else {

            wp_send_json([
                'success' => false,
                'message' => esc_html__('User not found', 'login-with-phone-number')
            ]);


        }
    }


    function lwp_ajax_login_with_email()
    {

        if (!isset($_GET['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['nonce'])), 'lwp_login')) {
            wp_die('Busted!');
        }

        if (!isset($_GET['email']) || empty($_GET['email'])) {
            wp_send_json_error('Email is required');
        }

        $email = sanitize_email(wp_unslash($_GET['email']));
        $userRegisteredNow = false;



        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_default_role'])) $options['idehweb_default_role'] = "";

        if (!isset($options['idehweb_user_registration'])) $options['idehweb_user_registration'] = '0';
        $registration = $options['idehweb_user_registration'];


        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $email_exists = email_exists($email);
            if (!$email_exists) {
                if ($registration == '0') {
                    wp_send_json([
                        'success' => false,
                        'email' => $email,
                        'registeration' => $registration,
                        'email_exists' => $email_exists,
                        'message' => __('users can not register!', 'login-with-phone-number')
                    ]);

                }
                $info = array();
                $info['user_email'] = sanitize_user($email);
                $info['user_nicename'] = $info['nickname'] = $info['display_name'] = $this->generate_nickname();
                $info['user_url'] = isset($_GET['website']) ? sanitize_text_field(wp_unslash($_GET['website'])) : '';                $info['user_login'] = $this->generate_username($email);
                if ($options['idehweb_default_role'] && $options['idehweb_default_role'] !== "") {
                    $info['role'] = $options['idehweb_default_role'];
                }
                $user_register = wp_insert_user($info);
                if (is_wp_error($user_register)) {
                    $error = $user_register->get_error_codes();

                    wp_send_json([
                        'success' => false,
                        'email' => $email,
                        '$email_exists' => $email_exists,
                        '$error' => $error,
                        'message' => __('This email address is already registered.', 'login-with-phone-number')
                    ]);


                } else {
                    $userRegisteredNow = true;
                    add_user_meta($user_register, 'updatedPass', 0);
                    $email_exists = $user_register;
                }


            }
            $log = '';
            $showPass = false;
            if (!$userRegisteredNow) {
                $showPass = true;
            } else {
                $log = $this->lwp_generate_token($email_exists, $email, true);
            }
//            $options = get_option('idehweb_lwp_settings');
            if (!isset($options['idehweb_password_login'])) $options['idehweb_password_login'] = '1';
            $options['idehweb_password_login'] = (bool)(int)$options['idehweb_password_login'];
            if (!$options['idehweb_password_login']) {
                $log = $this->lwp_generate_token($email_exists, $email, true);


            }
            wp_send_json([
                'success' => true,
                'ID' => $email_exists,
                'log' => $log,
//                '$user' => $user,
                'showPass' => $showPass,
                'authWithPass' => (bool)(int)$options['idehweb_password_login'],

                'email' => $email,
                'message' => __('Email sent successfully!', 'login-with-phone-number')
            ]);


        } else {
            wp_send_json([
                'success' => false,
                'email' => $email,
                'message' => __('email is wrong!', 'login-with-phone-number')
            ]);

        }
    }


    function lwp_ajax_verify_with_email()

    {
        if (!isset($_GET['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['nonce'])), 'lwp_login')) {
            wp_die('Busted!');
        }

        if (!isset($_GET['email']) || empty($_GET['email'])) {
            wp_send_json_error('Email is required');
        }

        $email = sanitize_email(wp_unslash($_GET['email']));        $userRegisteredNow = false;
        $current_user = wp_get_current_user();
        $options = get_option('idehweb_lwp_settings');

//        if (!isset($options['idehweb_user_registration'])) $options['idehweb_user_registration'] = '1';
//        $registration = $options['idehweb_user_registration'];
//print_r($current_user);

        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $info = array();
            $info['user_email'] = sanitize_user($email);
            $user_data = wp_update_user(array('ID' => $current_user->ID, 'user_email' => $info['user_email']));

            if (is_wp_error($user_data)) {
                if ($user_data->errors['existing_user_email']) {
                    //set email for this user
                    update_user_meta($current_user->ID, 'temporary_email', $info['user_email']);
                    $log = $this->lwp_generate_token($current_user->ID, $email, true);
                    wp_send_json([
                        'success' => true,
                        'ID' => $current_user->ID,
                        'log' => $log,
                        'showPass' => false,
                        'authWithPass' => (bool)(int)$options['idehweb_password_login'],
                        'email' => $email,
                        'message' => __('Email sent successfully!', 'login-with-phone-number')
                    ]);

                }

            } else {
                // Success!
                echo 'User profile updated.';
            }

        } else {
            wp_send_json([
                'success' => false,
                'email' => $email,
                'message' => __('email is wrong!', 'login-with-phone-number')
            ]);

        }
    }

    function lwp_ajax_register()
    {

        if (!isset($_GET['nonce']) || empty($_GET['nonce'])) {
            wp_die('Nonce is required');
        }

        if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['nonce'])), 'lwp_login')) {
            wp_die('Busted!');
        }


        if (!isset($_GET['secod']) || empty($_GET['secod'])) {
            wp_send_json([
                'success' => false,
                'message' => __('activation code (secod) is required!', 'login-with-phone-number'),
            ]);
        }

        $secod = sanitize_text_field(wp_unslash($_GET['secod']));

        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_default_gateways'])) $options['idehweb_default_gateways'] = ['custom'];
        if (!isset($options['idehweb_use_custom_gateway'])) $options['idehweb_use_custom_gateway'] = '1';
        if (!isset($options['idehweb_store_number_with_country_code'])) $options['idehweb_store_number_with_country_code'] = '1';
        if (!isset($options['idehweb_country_codes_default'])) $options['idehweb_country_codes_default'] = '';

        if (isset($_GET['phone_number'])) {
            $phoneNumber = sanitize_text_field(wp_unslash($_GET['phone_number']));  // Unsplash before sanitization

            if (preg_replace('/^(\-){0,1}[0-9]+(\.[0-9]+){0,1}/', '', $phoneNumber) == "") {
                $phone_number = ltrim($phoneNumber, '0');
                $phone_number = substr($phone_number, 0, 15);

                if ($phone_number < 10) {
                    wp_send_json([
                        'success' => false,
                        'phone_number' => $phone_number,
                        'message' => __('phone number is wrong!', 'login-with-phone-number')
                    ]);
                }
            }
            if ($options['idehweb_store_number_with_country_code'] != '1' && ($options['idehweb_country_codes_default'] != '')) {
                $country_code = $this->get_country_code_by_code($options['idehweb_country_codes_default']);

                $phone_number = preg_replace('/^' . preg_quote($country_code, '/') . '/', '', $phone_number);
            }
            $username_exists = $this->phone_number_exist($phone_number);
        } else if (isset($_GET['email'])) {
            $email = sanitize_email(wp_unslash($_GET['email']));  // Unsplash before sanitization
            $username_exists = email_exists($email);

        } else {
            wp_send_json([
                'success' => false,
                'message' => __('phone number is wrong!', 'login-with-phone-number')
            ]);
        }
        if ($username_exists) {
            $activation_code = get_user_meta($username_exists, 'activation_code', true);
            $activation_code_timestamp = get_user_meta($username_exists, 'activation_code_timestamp', true);
            $now = time();


            if (!is_numeric($activation_code_timestamp)) {
                wp_send_json([
                    'success' => false,
                    'message' => __('activation code timestamp is missing or invalid.', 'login-with-phone-number')
                ]);
            }
            $passed = round(($now - $activation_code_timestamp) / 60, 2);


            if ($passed >= 10) {
//                update_user_meta($username_exists, 'activation_code', '');
//                update_user_meta($username_exists, 'activation_code_timestamp', '');

                wp_send_json([
                    'success' => false,
                    'phone_number' => $phone_number,
                    'time_passed' => $passed,
                    'message' => __('activation code is expired!', 'login-with-phone-number')
                ]);
            }
//die();
            $verification_support_functions = apply_filters('lwp_add_to_verification_support_functions', []);

            $theMethod = ''; //it should be method that has verification it self
            // Check if Firebase method is being used
            if (isset($_GET['method']) && $_GET['method'] == 'firebase') {
                if (!isset($_GET['verificationId']) || empty($_GET['verificationId'])) {
                    wp_send_json_error('Verification ID is required for Firebase method');
                }
                $verificationId = sanitize_text_field(wp_unslash($_GET['verificationId']));
            } else {
                $verificationId = '';
            }

            if ($options['idehweb_use_custom_gateway'] == '1'
                && in_array('firebase', $options['idehweb_default_gateways'])
                && isset($_GET['phone_number'])
                && isset($_GET['method'])
                && $_GET['method'] == 'firebase') {
                if (!isset($verificationId)) $verificationId = '';
                $response = $this->idehweb_lwp_activate_through_firebase($verificationId, $secod);
//                if ($response->error && $response->error->code == 400) {

                if (empty($response) || isset($response->error) || !isset($response->idToken)) {
                    wp_send_json([
                        'success' => false,
                        'phone_number' => $phone_number,
                        'firebase' => $response->error,
                        'message' => __('entered code is wrong!', 'login-with-phone-number')
                    ]);
                } else {
//                if($response=='true') {
                    $user = get_user_by('ID', $username_exists);
                    if (!is_wp_error($user)) {
//                        wp_clear_auth_cookie();
                        wp_set_current_user($user->ID); // Set the current user detail
                        wp_set_auth_cookie($user->ID, true); // Set auth details in cookie
                        update_user_meta($username_exists, 'activation_code', '');
                        if (!isset($options['idehweb_password_login'])) $options['idehweb_password_login'] = '1';
                        $options['idehweb_password_login'] = (bool)(int)$options['idehweb_password_login'];
                        $updatedPass = (bool)(int)get_user_meta($username_exists, 'updatedPass', true);


                        $options['idehweb_password_login'] = (bool)(int)$options['idehweb_password_login'];
                        $updatedPass = (bool)(int)get_user_meta($username_exists, 'updatedPass', true);
                        $userRegisteredNow = (bool)(int)get_user_meta($username_exists, 'userRegisteredNow', true);
                        $lwp_update_extra_fields = false;
                        if (class_exists(LWP_PRO::class)) {
                            $ROptions = get_option('idehweb_lwp_settings_registration_fields');
                            if (!isset($ROptions['idehweb_registration_fields'])) $ROptions['idehweb_registration_fields'] = [];
                            if ($ROptions['idehweb_registration_fields'][0]) {
                                $lwp_update_extra_fields = true;

                            }
                        }
//                        wp_send_json(array('success' => false, 'nonce' => wp_create_nonce('lwp_login'), 'loggedin' => true, 'message' => __('loading...', 'login-with-phone-number'), 'updatedPass' => $updatedPass, 'authWithPass' => true,'lwp_update_extra_fields'=>true));
                        if ($userRegisteredNow && $lwp_update_extra_fields) {
                            wp_send_json(array('success' => false, 'nonce' => wp_create_nonce('lwp_login'), 'loggedin' => true, 'message' => __('loading...', 'login-with-phone-number'), 'updatedPass' => $updatedPass, 'authWithPass' => $options['idehweb_password_login'], 'userRegisteredNow' => $userRegisteredNow, 'lwp_update_extra_fields' => $lwp_update_extra_fields));
                        }


//                        wp_send_json(array('success' => false, 'nonce' => wp_create_nonce('lwp_login'), 'firebase' => $response, 'loggedin' => true, 'message' => __('loading...', 'login-with-phone-number'), 'updatedPass' => false, 'authWithPass' => true));
                        wp_send_json(array('success' => true, 'nonce' => wp_create_nonce('lwp_login'), 'firebase' => $response, 'loggedin' => true, 'message' => __('loading...', 'login-with-phone-number'), 'updatedPass' => $updatedPass, 'authWithPass' => $options['idehweb_password_login']));

                    } else {
                        wp_send_json(array('success' => false, 'loggedin' => false, 'message' => __('wrong', 'login-with-phone-number')));

                    }

                }
            } else if ($options['idehweb_use_custom_gateway'] == '1'
                && in_array($_GET['method'], $options['idehweb_default_gateways'])
                && isset($_GET['phone_number'])
                && isset($_GET['method'])
                && in_array($_GET['method'], $verification_support_functions)) {
                if (!isset($verificationId)) $verificationId = '';
                $response = $this->idehweb_lwp_activate_through_firebase($verificationId, $secod);
//                if ($response->error && $response->error->code == 400) {

                if (empty($response) || isset($response->error) || !isset($response->idToken)) {
                    wp_send_json([
                        'success' => false,
                        'phone_number' => $phone_number,
                        'firebase' => $response->error,
                        'message' => __('entered code is wrong!', 'login-with-phone-number')
                    ]);
                } else {
//                if($response=='true') {
                    $user = get_user_by('ID', $username_exists);
                    if (!is_wp_error($user)) {
//                        wp_clear_auth_cookie();
                        wp_set_current_user($user->ID); // Set the current user detail
                        wp_set_auth_cookie($user->ID, true); // Set auth details in cookie
                        update_user_meta($username_exists, 'activation_code', '');
                        if (!isset($options['idehweb_password_login'])) $options['idehweb_password_login'] = '1';
                        $options['idehweb_password_login'] = (bool)(int)$options['idehweb_password_login'];
                        $updatedPass = (bool)(int)get_user_meta($username_exists, 'updatedPass', true);


                        $options['idehweb_password_login'] = (bool)(int)$options['idehweb_password_login'];
                        $updatedPass = (bool)(int)get_user_meta($username_exists, 'updatedPass', true);
                        $userRegisteredNow = (bool)(int)get_user_meta($username_exists, 'userRegisteredNow', true);
                        $lwp_update_extra_fields = false;
                        if (class_exists(LWP_PRO::class)) {
                            $ROptions = get_option('idehweb_lwp_settings_registration_fields');
                            if (!isset($ROptions['idehweb_registration_fields'])) $ROptions['idehweb_registration_fields'] = [];
                            if ($ROptions['idehweb_registration_fields'][0]) {
                                $lwp_update_extra_fields = true;

                            }
                        }
//                        wp_send_json(array('success' => false, 'nonce' => wp_create_nonce('lwp_login'), 'loggedin' => true, 'message' => __('loading...', 'login-with-phone-number'), 'updatedPass' => $updatedPass, 'authWithPass' => true,'lwp_update_extra_fields'=>true));
                        if ($userRegisteredNow && $lwp_update_extra_fields) {
                            wp_send_json(array('success' => false, 'nonce' => wp_create_nonce('lwp_login'), 'loggedin' => true, 'message' => __('loading...', 'login-with-phone-number'), 'updatedPass' => $updatedPass, 'authWithPass' => $options['idehweb_password_login'], 'userRegisteredNow' => $userRegisteredNow, 'lwp_update_extra_fields' => $lwp_update_extra_fields));
                        }


//                        wp_send_json(array('success' => false, 'nonce' => wp_create_nonce('lwp_login'), 'firebase' => $response, 'loggedin' => true, 'message' => __('loading...', 'login-with-phone-number'), 'updatedPass' => false, 'authWithPass' => true));
                        wp_send_json(array('success' => true, 'nonce' => wp_create_nonce('lwp_login'), 'firebase' => $response, 'loggedin' => true, 'message' => __('loading...', 'login-with-phone-number'), 'updatedPass' => $updatedPass, 'authWithPass' => $options['idehweb_password_login']));

                    } else {
                        wp_send_json(array('success' => false, 'loggedin' => false, 'message' => __('wrong', 'login-with-phone-number')));

                    }

                }
            } else {
                if (empty($activation_code)) {
                    wp_send_json([
                        'success' => false,
                        'message' => __('activation_code is empty!', 'login-with-phone-number')
                    ]);
                }
                if ($activation_code == $secod) {
                    // First get the user details
                    $user = get_user_by('ID', $username_exists);

                    if (!is_wp_error($user)) {
                        if (class_exists('LearnPress')) {
                            // Unsplash before using the cookie value
                            $guest_session_id = isset($_COOKIE['lp_session_guest']) ?
                                sanitize_text_field(wp_unslash($_COOKIE['lp_session_guest'])) : '';
                            $session = LearnPress::instance()->session;
                            $session->_customer_id = $guest_session_id;
                            $data_session_before_user_login = $session->get_session_by_customer_id($guest_session_id);
                        }

                        wp_set_current_user($user->ID);
                        if (class_exists('LearnPress')) {
                            $session->_customer_id = $user->ID;
                            foreach ($data_session_before_user_login as $key => $item) {
                                $session->set($key, maybe_unserialize($item));
                            }
                            $session->save_data();
                        }

                        wp_set_auth_cookie($user->ID, true); // Set auth details in cookie
                        update_user_meta($username_exists, 'activation_code', '');
                        if (!isset($options['idehweb_password_login'])) $options['idehweb_password_login'] = '1';
                        $options['idehweb_password_login'] = (bool)(int)$options['idehweb_password_login'];
                        $updatedPass = (bool)(int)get_user_meta($username_exists, 'updatedPass', true);
                        $userRegisteredNow = (bool)(int)get_user_meta($username_exists, 'userRegisteredNow', true);
                        $lwp_update_extra_fields = false;
                        if (class_exists(LWP_PRO::class)) {
                            $ROptions = get_option('idehweb_lwp_settings_registration_fields');
                            if (!isset($ROptions['idehweb_registration_fields'])) $ROptions['idehweb_registration_fields'] = [];
                            if ($ROptions['idehweb_registration_fields_status'] == '1' && $ROptions['idehweb_registration_fields'][0]) {
                                $lwp_update_extra_fields = true;

                            }
                        }
//                        wp_send_json(array('success' => false, 'nonce' => wp_create_nonce('lwp_login'), 'loggedin' => true, 'message' => __('loading...', 'login-with-phone-number'), 'updatedPass' => $updatedPass, 'authWithPass' => true,'lwp_update_extra_fields'=>true));
                        if ($userRegisteredNow && $lwp_update_extra_fields) {
                            wp_send_json(array('success' => false, 'nonce' => wp_create_nonce('lwp_login'), 'loggedin' => true, 'message' => __('loading...', 'login-with-phone-number'), 'updatedPass' => $updatedPass, 'authWithPass' => $options['idehweb_password_login'], 'userRegisteredNow' => $userRegisteredNow, 'lwp_update_extra_fields' => $lwp_update_extra_fields));
                        }
                        wp_send_json(array('success' => true, 'nonce' => wp_create_nonce('lwp_login'), 'loggedin' => true, 'message' => __('loading...', 'login-with-phone-number'), 'updatedPass' => $updatedPass, 'authWithPass' => $options['idehweb_password_login'], 'userRegisteredNow' => $userRegisteredNow, 'lwp_update_extra_fields' => $lwp_update_extra_fields));

                    } else {
                        wp_send_json(array('success' => false, 'loggedin' => false, 'message' => __('wrong', 'login-with-phone-number')));

                    }


                } else {
                    wp_send_json([
                        'success' => false,
                        'phone_number' => $phone_number,
                        'message' => __('entered code is wrong!', 'login-with-phone-number')
                    ]);

                }
            }
        } else {

            wp_send_json([
                'success' => false,
                'phone_number' => $phone_number,
                'message' => __('user does not exist!', 'login-with-phone-number')
            ]);

        }
    }


    function lwp_activate_email()
    {
        if (!isset($_GET['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['nonce'])), 'lwp_login')) {
            wp_die('Busted!');
        }

        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_default_gateways'])) $options['idehweb_default_gateways'] = ['custom'];
        if (!isset($options['idehweb_use_custom_gateway'])) $options['idehweb_use_custom_gateway'] = '1';
        $current_user = wp_get_current_user();


        if (is_wp_error($current_user) || 0 == $current_user->ID) {
            wp_send_json([
                'success' => false,
                'message' => __('user is not logged in!', 'login-with-phone-number')
            ]);
        }
        if (isset($_GET['email'])) {
            $email = sanitize_email(wp_unslash($_GET['email']));  // Unsplash before sanitization

        } else {
            wp_send_json([
                'success' => false,
                'message' => __('email is not entered!', 'login-with-phone-number')
            ]);
        }
        if ($current_user) {
            $temporary_email = get_user_meta($current_user->ID, 'temporary_email', true);
            $activation_code = get_user_meta($current_user->ID, 'activation_code', true);
            if (!isset($_GET['secod']) || empty($_GET['secod'])) {
                wp_send_json_error('activation code (secod) is required!');
            }

            $secod = sanitize_text_field(wp_unslash($_GET['secod']));
            if ($activation_code == $secod) {

                //remove this email from other user
                $this->remove_email_from_all_users($temporary_email);
                $user = wp_update_user([
                    'ID' => $current_user->ID,
                    'user_email' => $temporary_email
                ]);
                if (is_wp_error($user)) {
                    wp_send_json(array('success' => false, 'message' => __('There is problem with activating user.', 'login-with-phone-number'), 'updatedPass' => false, 'authWithPass' => false));
                }
                update_user_meta($current_user->ID, 'activation_code', '');
                if (!isset($options['idehweb_password_login'])) $options['idehweb_password_login'] = '1';
                $options['idehweb_password_login'] = (bool)(int)$options['idehweb_password_login'];
                $updatedPass = (bool)(int)get_user_meta($current_user->ID, 'updatedPass', true);

                wp_send_json(array('success' => true, 'loggedin' => true, 'message' => __('loading...', 'login-with-phone-number'), 'updatedPass' => $updatedPass, 'authWithPass' => $options['idehweb_password_login']));


            } else {
                wp_send_json([
                    'success' => false,
                    'email' => $email,
                    'user_id' => $current_user->ID,
                    'message' => __('Activation code is not correct!', 'login-with-phone-number')
                ]);

            }
        } else {

            wp_send_json([
                'success' => false,
                'email' => $email,
                'message' => __('user does not exist!', 'login-with-phone-number')
            ]);

        }
    }


    function lwp_forgot_password()
    {

        if (!isset($_GET['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['nonce'])), 'lwp_login')) {
            wp_die('Busted!');
        }

        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_store_number_with_country_code'])) $options['idehweb_store_number_with_country_code'] = '1';
        if (!isset($options['idehweb_country_codes_default'])) $options['idehweb_country_codes_default'] = '';

        $log = '';
        if (!isset($_GET['email'])) $_GET['email'] = '';
        $email = sanitize_email(wp_unslash($_GET['email']));

        if ($email == "") {
            $email = null;
        }


        if (!isset($_GET['method'])) $_GET['method'] = '';
        $method = sanitize_text_field(wp_unslash($_GET['method']));

        if (!isset($_GET['phone_number'])) $_GET['phone_number'] = '';
        $phone_number = sanitize_text_field(wp_unslash($_GET['phone_number']));

        if ($phone_number == "") {
            $phone_number = null;
        }

        if (isset($phone_number) && $phone_number != '' && !is_numeric($phone_number)) {
            wp_send_json([
                'success' => false,
                'phone_number' => $phone_number,
                'message' => __('Please enter correct phone number', 'login-with-phone-number')
            ]);
        }
        $phone_number_with_country_code = false;
        if ($options['idehweb_store_number_with_country_code'] != '1' && ($options['idehweb_country_codes_default'] != '')) {
            $country_code = $this->get_country_code_by_code($options['idehweb_country_codes_default']);
            $phone_number_with_country_code = $phone_number;
            $phone_number = preg_replace('/^' . preg_quote($country_code, '/') . '/', '', $phone_number);
        }
        if (isset($email) && $email != '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            wp_send_json([
                'success' => false,
                'message' => __('Email is wrong!', 'login-with-phone-number')
            ]);
        }
        if (isset($phone_number) && !isset($email)) {
            $ID = $this->phone_number_exist($phone_number);
        }

        if (!isset($phone_number) && isset($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $ID = email_exists($email);
        }
        if (!is_numeric($ID)) {
            wp_send_json([
                'success' => false,
                'message' => __('Please enter correct user ID', 'login-with-phone-number')
            ]);
        }
        $user = get_user_by('ID', $ID);

        if (is_wp_error($user)) {
            wp_send_json([
                'success' => false,
                'message' => __('User not found!', 'login-with-phone-number')
            ]);
        }
        if ($email != '' && $ID) {
            $log = $this->lwp_generate_token($ID, $email, true);

        }
        if ($phone_number != '' && $ID != '') {
            $log = $this->lwp_generate_token($ID, $phone_number_with_country_code ? $phone_number_with_country_code : $phone_number, false, $method);

//
        }
        update_user_meta($ID, 'updatedPass', '0');

        wp_send_json([
            'success' => true,
            'ID' => $ID,
            'log' => $log,
            'message' => __('Update password', 'login-with-phone-number')
        ]);
    }


    function lwp_verify_domain()
    {

        wp_send_json([
            'success' => true
        ]);
    }

    function lwp_set_countries()
    {
        // Verify nonce first before processing any data
        if (!isset($_POST['nonce']) || empty($_POST['nonce'])) {
            wp_send_json([
                'success' => false,
                'message' => __('Nonce is required.', 'login-with-phone-number')
            ], 403);
        }

        $nonce = sanitize_text_field(wp_unslash($_POST['nonce']));

        if (!wp_verify_nonce($nonce, 'lwp_admin_nonce')) {
            wp_send_json([
                'success' => false,
                'message' => __('Invalid nonce. Please refresh the page and try again.', 'login-with-phone-number')
            ], 403);
        }

// Check if required fields are set and not empty
        if (!isset($_POST['selected_countries']) || empty($_POST['selected_countries'])) {
            wp_send_json([
                'success' => false,
                'message' => __('No countries selected.', 'login-with-phone-number')
            ], 400);
        }

        if (!isset($_POST['selected_gateways']) || empty($_POST['selected_gateways'])) {
            wp_send_json([
                'success' => false,
                'message' => __('No gateways selected.', 'login-with-phone-number')
            ], 400);
        }

// Validate that we're working with arrays
        if (!is_array($_POST['selected_countries'])) {
            wp_send_json([
                'success' => false,
                'message' => __('Invalid countries data format.', 'login-with-phone-number')
            ], 400);
        }

        if (!is_array($_POST['selected_gateways'])) {
            wp_send_json([
                'success' => false,
                'message' => __('Invalid gateways data format.', 'login-with-phone-number')
            ], 400);
        }

// Sanitize the arrays
        $selected_countries = array_map('sanitize_text_field', wp_unslash($_POST['selected_countries']));
        $selected_gateways = array_map('sanitize_text_field', wp_unslash($_POST['selected_gateways']));


// Fetch existing settings
        $options = get_option('idehweb_lwp_settings', []);
        if (!is_array($options)) {
            $options = [];
        }

// Update options safely
        $options['idehweb_country_codes'] = $selected_countries;
        $options['idehweb_default_gateways'] = $selected_gateways;


        update_option('idehweb_lwp_settings', $options);

//        error_log("Saved Countries: " . print_r($selected_countries, true));
//        error_log("Saved Gateways: " . print_r($selected_gateways, true));
        // Send JSON response
        wp_send_json([
            'success' => true,
            'data' => [
                'selected_countries' => $selected_countries,
                'selected_gateways' => $selected_gateways
            ],
            'message' => __('Countries and gateways saved successfully.', 'login-with-phone-number')
        ]);

    }

    function phone_number_exist($phone_number)
    {
        $args = array(
            'meta_query' => array(
                array(
                    'key' => 'phone_number',
                    'value' => $phone_number,
                    'compare' => '='
                )
            )
        );

        $member_arr = get_users($args);
        if ($member_arr && $member_arr[0])
            return $member_arr[0]->ID;
        else
            return 0;

    }


    function generate_username($defU = '')
    {
        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_default_username'])) $options['idehweb_default_username'] = 'user';
        if (!isset($options['idehweb_use_phone_number_for_username'])) $options['idehweb_use_phone_number_for_username'] = '0';
        if ($options['idehweb_use_phone_number_for_username'] == '0') {
            $ulogin = $options['idehweb_default_username'];

        } else {
            $ulogin = $defU;
        }

        // make user_login unique so WP will not return error
        $check = username_exists($ulogin);
        if (!empty($check)) {
            $suffix = 2;
            while (!empty($check)) {
                $alt_ulogin = $ulogin . '-' . $suffix;
                $check = username_exists($alt_ulogin);
                $suffix++;
            }
            $ulogin = $alt_ulogin;
        }

        return $ulogin;
    }

    function generate_nickname()
    {
        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_default_nickname'])) $options['idehweb_default_nickname'] = 'user';


        return $options['idehweb_default_nickname'];
    }

    function remove_email_from_all_users($email)
    {
        $username_exists = email_exists($email);
        if ($username_exists) {
            wp_update_user(
                [
                    'ID' => $username_exists,
                    'user_email' => ''
                ]
            );
        }
    }

    function idehweb_lwp_activate_through_firebase($sessionInfo, $code)
    {
        $options = get_option('idehweb_lwp_settings');

//        if (!isset($options['idehweb_firebase_api'])) $options['idehweb_firebase_api'] = '';
        if (empty($options['idehweb_firebase_api'])) {
            return (object)[
                'error' => 'Firebase API key is missing. Please configure it in the plugin settings.'
            ];
        }
        $response = wp_safe_remote_post("https://www.googleapis.com/identitytoolkit/v3/relyingparty/verifyPhoneNumber?key=" . $options['idehweb_firebase_api'], [
            'timeout' => 60,
            'redirection' => 4,
            'blocking' => true,
            'headers' => array('Content-Type' => 'application/json'),
            'body' => wp_json_encode([
                'code' => $code,
                'sessionInfo' => $sessionInfo
            ])
        ]);


        // Check if the request was successful
        if (is_wp_error($response)) {
            return (object)[
                'error' => 'Error connecting to Firebase: ' . $response->get_error_message()
            ];
        }


        $body = wp_remote_retrieve_body($response);
        $response_data = json_decode($body);

        return $response_data;
    }

}