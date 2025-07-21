#WooCommerce OTP Login With Phone Number, OTP Verification
Contributors: glboy
Requires at least: 3.0.1
Tested up to: 6.8
Stable tag: 1.8.47
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Tags: woocommerce, sms, phone, otp, login


OTP login with phone, SMS, or WhatsApp. WooCommerce OTP verification for WordPress & WooCommerce using custom gateways. GDPR-compliant. Login with otp


## Description

Enable OTP login for WordPress and WooCommerce using phone numbers, SMS, or WhatsApp. Secure passwordless login with custom gateways and GDPR compliance.

Login/register with phone number in WordPress can happen with this plugin. Your customer can authenticate with their mobile number via OTP.

Added country flags to login with phone number form

You can add almost every SMS gateways (if you have) yourself for free, or you can ask us to develop your sms gateway with paying extra.

you can change style and appearance of forms

You can use Firebase, textlocal and other SMS gateways

For checking docs and getting more help please visit:
[Login with phone number in WordPress documentations](https://idehweb.com/product/login-with-phone-number-in-wordpress/ "login with phone number in WordPress")

* Login with phone number in WordPress

* Login with OTP WordPress

* Login with mobile number WordPress

* Login/Register with E-mail

* Wordpress Login Form

* Woocommerce Registration Form

* Woocommerce Login With Phone Number

* Wordpress OTP Login

* Woocommerce Registration With Phone Number

* Add Phone Number to Wordpress Registration

* Simple Use

* Support of International SMS Delivery

* Activating Users by Phone Number

* Password Recovery Form

* Page Authentication in Order to Visit Pages

* Login and Registration with Phone Number

* Redirect Users to Specific URLs After Logging in or Registering

You can use your custom gateway. you can also use other ready sms gateways from idehweb.com.

Supported gateways for now:

* Firebase
* Twilio
* Netgsm
* Alibabacloud
* Messagebird
* Kavenegar
* Trustsignal
* Msg91
* taqnyat
* 2factor
* Textlocal
* BlueSoft
* IQSMS
* Ippanel
* Whatsapp
* Ultramessage
* Telegram
* Sms.ir
* MelliPayamak
* KavehNegar
* Farazsms


You can now choose to store phone numbers with or without the country code.

[youtube https://www.youtube.com/watch?v=0B0sE9JMzCE]

##Installation

1. download plugin from wordpress directory
1. Upload the ‘login-with-phone-number’ folder to the /wp-content/plugins/ directory
1. Activate it through the ‘Plugins’ menu in WordPress
1. use  [idehweb_lwp] shortcode in your posts and pages where you need user to be logged in
1. use  [idehweb_lwp_metas nicename="false" username="false" phone_number="true" email="false"] where you want to show logged in users metas. for example you can use this shortcode in user's profile page. you can show phone number, email, username and nicename.
1. for sending otp sms, you need credit. you can buy credit inside plugin and use our default gateway, or you can use your custom gateways. some gateways have been added.


== Frequently Asked Questions ==

= Does this plugin work with WooCommerce? =

Yes, the plugin is fully compatible with WooCommerce. You can use phone number login and registration on WooCommerce account pages and checkout forms.

= What countries are supported for phone number login? =

The plugin supports international phone numbers. You can configure default country codes and formatting options in the plugin settings.

= Is OTP (One-Time Password) login supported? =

Yes. Users can log in using an OTP sent to their phone number. You can choose between SMS gateways to handle OTP delivery.

= Which SMS gateways are supported? =

The plugin supports multiple SMS gateways, including local (Iranian) and international providers. You can select your preferred gateway from the settings panel.

= Can I customize the login form design? =

Yes, the plugin provides shortcodes and hooks for developers. You can fully customize the form layout using CSS or integrate it into your theme templates. Also you can purchase Pro version for customizing easier.

= Can this plugin work with existing users? =

Yes. The plugin can be synchronized with existing WordPress users. If a phone number is already stored (e.g., in user meta), the plugin can match and allow login without requiring re-registration.

= Can I allow login with both phone number and email? =

Yes, you can enable dual login. The plugin lets users log in using either their phone number or email address — whichever is more convenient.

= Does the plugin support Google SSO (Single Sign-On)? =

Yes. You can enable Google SSO alongside phone login. This allows users to log in quickly with their Google account, providing a seamless and flexible login experience.

= Does this plugin replace the default WordPress login? =

You can either use this plugin alongside the default login or redirect users to the phone-based login form exclusively. This behavior is configurable in the settings.

= Is the plugin translation-ready? =

Yes, the plugin is fully translation-ready and compatible with `WPML`, `Polylang`, and other popular translation plugins. It also includes Persian (`fa_IR`) translations out of the box.

= How can I report security bugs? =

You can report security bugs through the Patchstack Vulnerability Disclosure Program. The Patchstack team help validate, triage and handle any security vulnerabilities. [Report a security vulnerability.](https://patchstack.com/database/vdp/login-with-phone-number)


##Changelog

###1.8.46
* Added option to customize the text displayed after login (instead of the form)
* Added `logout` className support
* Added `UserName` and `IsLoggedIn` to the `wp_localize_script` object


###1.8.44
* Added conditional check to ensure the .lwp_login_overlay is appended only if it does not already exist.
* Enhanced the overlay display functionality for improved user experience when the sticky option is enabled.

###1.8.43
* New Feature: Added an option to show the login/register form on all pages, except the "My Account" page in WooCommerce.
* Admin can enable this option via the settings page.
* The form will be displayed in the footer of all pages, excluding the WooCommerce account pages (my-account).

###1.8.42
* Added a notice to encourage users to activate an SMS gateway for enabling login via phone number.
* The notice informs users to configure the SMS gateway and highlights WhatsApp OTP as a more efficient, cost-effective solution.
* Provides a direct link to the gateway settings page for easy configuration.

###1.8.41
* System Default Gateway Upgrade! See your credit, track expiry, and send OTPs over WhatsApp with ease!

###1.8.39
* Updated prices for the system default SMS gateway. You can enable it under the General tab by selecting System default in the "SMS Default Gateway" field.

###1.8.37
* solve problem of "lwp_forgot_password"

###1.8.36
* Added Netgsm Turkey SMS gateway

###1.8.26
* Added system default sms gateway (Whatsapp OTP gateway)

###1.8.25
* Added Option to store phone numbers without the country code. By default, phone numbers are stored with the country code.

###1.8.24
* update lwp_update_password_action function
* update extra fields with method firebase

###1.8.23
* update algorithm of setting extra fields and registration

###1.8.21
* add usage tracking code
* enable button to disable support chat and tracking code
* only show chat on admin page

###1.8.15
* solve msg91 for indian users

###1.8.14
* add firebase label

###1.8.13
* remove conflicting for iti__flags

###1.8.12
* remove ads

###1.8.11
* update readme and docs
* remove wizard
* optimize flags styles
* add some other sms gateways
* sync old woocommerce users billing_phone with $billing_phone
