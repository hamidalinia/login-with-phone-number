=== OTP Login With Phone Number, OTP Verification ===
Contributors: glboy
Requires at least: 5.9
Tested up to: 7.0
Stable tag: 1.8.69
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Tags: otp login, phone number login, sms verification, otp verification, passwordless login

Passwordless OTP login for WordPress. Let users login or register with phone number via SMS or Firebase. No password needed. Compatible with WooCommerce.

== Description ==

**OTP Login With Phone Number** lets your users login and register using their mobile phone number — no password required. Send a One-Time Password (OTP) via SMS or Firebase and authenticate instantly.

Works seamlessly with WordPress and is fully compatible with WooCommerce login, registration, and checkout pages. Supports 20+ SMS gateways and lets you connect any custom SMS provider for free.

[youtube https://www.youtube.com/watch?v=0B0sE9JMzCE]

---

### 🔑 KEY FEATURES (FREE)

* **Phone number login & registration** — replace or extend the default WordPress login
* **OTP via SMS or Firebase** — free Firebase integration included
* **Compatible with WooCommerce** — works on My Account, checkout, and registration pages
* **Compatible with LearnPress** — OTP login on course checkout pages
* **Email login** — let users login with email + OTP (no password)
* **Country flags & auto country code detection**
* **Passwordless login** — frictionless UX, higher conversion rates
* **Redirect after login/register** to any URL
* **Page protection** — restrict pages to logged-in users only
* **Password recovery** via phone number OTP
* **Existing user sync** — match phone numbers already stored in user meta (e.g. WooCommerce billing phone)
* **Store phone with or without country code**
* **Custom gateway** — connect any SMS provider yourself via URL + JSON config
* **GDPR-compliant**
* **Translation-ready** — includes Persian (fa_IR), compatible with WPML & Polylang
* **Multisite support**
* **Shortcodes** for embedding login form anywhere
* **Custom CSS** support

---

### 📱 FREE SMS GATEWAYS

* **Firebase** — free OTP via Google Firebase (recommended for international sites)
* **Twilio** — international SMS gateway, free to configure
* **Netgsm** — Turkey SMS gateway
* **Kavenegar** — popular Iranian SMS gateway
* **DrPayamak** — Iranian SMS gateway
* **Custom API** — connect any SMS gateway using your own URL, headers, and body config

### 📱 PRO SMS GATEWAYS

* WhatsApp via UltraMessage
* Telegram
* MSG91 (India)
* Alibabacloud
* MessageBird
* Trustsignal
* Taqnyat (Arabic)
* 2Factor
* Textlocal
* Vonage
* SMS.ir
* MelliPayamak
* FarazSMS

---

### 🔌 COMPATIBLE WITH

* WooCommerce login, registration & checkout
* LearnPress course checkout
* Woodmart Theme sidebar login
* Elementor (via shortcode)
* WPForms (via shortcode)
* Contact Form 7 (via shortcode)
* WPBakery, Divi, Gutenberg (via shortcode)
* WPML & Polylang (translation-ready)

---

### ⚡ USE CASES

* eCommerce stores — reduce cart abandonment with frictionless phone login
* WooCommerce shops — phone-verified checkout without passwords
* Membership sites — verified user registration via OTP
* LMS platforms — secure student login for online courses
* Booking sites — quick login without password
* Any site wanting to reduce fake registrations and improve security

---

### 🚀 PRO VERSION

Unlock advanced features with the [Pro version](https://idehweb.com/product/login-with-phone-number-in-wordpress/):

* 15+ additional SMS gateways (Twilio, WhatsApp, Telegram, MSG91, and more)
* Advanced form builder & UI customization
* Custom registration fields
* Default user role assignment
* Custom gateway development support
* Priority support

---

### 📄 SHORTCODES

`[idehweb_lwp]` — embed the login/register form anywhere

`[idehweb_lwp_metas phone_number="true" email="true"]` — show logged-in user's phone/email

`[idehweb_lwp_verify_email]` — email verification form

---

### 📚 Documentation & Support

* [Full Documentation](https://idehweb.com/product/login-with-phone-number-in-wordpress/)
* [GitHub Repository](https://github.com/hamidalinia/login-with-phone-number)
* [Report Security Bug](https://patchstack.com/database/vdp/login-with-phone-number)

---

== Installation ==

1. Install the plugin from the WordPress plugin directory or upload the zip file
2. Activate through the 'Plugins' menu in WordPress
3. Go to **Login Settings** in your WordPress admin
4. Choose your SMS gateway and configure it
5. Use `[idehweb_lwp]` shortcode on any page or let the plugin replace the default login form

== Frequently Asked Questions ==

= Does this plugin work with WooCommerce? =
Yes. The plugin is fully compatible with WooCommerce. It replaces or extends the login and registration forms on the My Account page, checkout page, and registration forms — all without passwords.

= Which SMS gateways are free? =
Firebase, Kavenegar, DrPayamak, and Custom API are all free. Firebase is recommended for international sites. You can also connect any SMS provider yourself using the Custom API option.

= Is Firebase free to use? =
Yes. Firebase OTP is free within Google's usage limits and is the recommended gateway for international sites.

= Can users login with both phone number and email? =
Yes. You can enable dual login — phone number OTP or email OTP — from the settings.

= Does this replace the default WordPress login page? =
You can configure it to replace or work alongside the default login. Fully configurable.

= Can I use this on existing users? =
Yes. The plugin syncs with existing WordPress users. If a phone number is stored in user meta (e.g. WooCommerce billing_phone), it will match automatically. You can also run a bulk sync from the settings page.

= Does it support Google SSO? =
Yes. Pro version supports Google SSO alongside phone number login.

= Is the plugin translation-ready? =
Yes. Compatible with WPML, Polylang, and standard .po/.mo translation files. Includes Persian (fa_IR) translation out of the box.

= Does it work on multisite? =
Yes. Multisite is supported.

= Can I customize the login form appearance? =
Yes. Custom CSS is supported in the free version. Pro version includes a full style panel — colors, fonts, logo, background, button styles.

= Which page builders are supported? =
Use the `[idehweb_lwp]` shortcode inside Elementor, WPBakery, Divi, Gutenberg, or any page builder that supports shortcodes.

= Can I add extra fields to the registration form? =
Yes, with the Pro version you can add custom registration fields and collect additional user data on signup.

= Does it work with LearnPress? =
Yes. The plugin is compatible with LearnPress and replaces the login/checkout form for course purchases.

= How can I connect my own SMS gateway? =
Use the Custom API option — enter your gateway URL, request method (GET/POST), headers, and body in JSON format. Use `${code}` as a placeholder for the OTP code.

= How can I report security bugs? =
Through the Patchstack Vulnerability Disclosure Program: [Report a vulnerability](https://patchstack.com/database/vdp/login-with-phone-number)

== Screenshots ==

1. Login form with phone number and country flag selector
2. OTP verification screen
3. Admin settings — general configuration
4. Gateway settings panel
5. Compatible with WooCommerce checkout

== Changelog ==

= 1.8.65 =
* Enable show form on checkout page

= 1.8.63 =
* Maintenance and stability improvements

= 1.8.61 =
* Fixed Firebase vulnerability reported by Wordfence

= 1.8.59 =
* Added: DrPayamak Iranian SMS gateway (free)
* MSG91 moved to Pro
* Optimized country selector on mobile

= 1.8.58 =
* Added: Kavenegar SMS gateway (free)

= 1.8.57 =
* Added: MSG91 gateway

= 1.8.52 =
* Fixed security vulnerabilities — nonce verification on all forms
* Added input validation and sanitization
* Fixed AJAX authentication with proper cookie handling
* Added external services disclosure

= 1.8.50 =
* Added GPLv2 license declaration
* Refactored main plugin into modular files
* Fixed fatal error on sites without WooCommerce
* Enhanced PHP 8+ compatibility
* Improved admin settings UI
* Added test SMS feature before saving gateway settings
* Fixed multisite settings saving issue

= 1.8.43 =
* Added option to show login form on all pages (except WooCommerce My Account)

= 1.8.36 =
* Added Netgsm Turkey SMS gateway

= 1.8.26 =
* Added WhatsApp OTP as default system gateway

= 1.8.25 =
* Added option to store phone numbers without country code

== Upgrade Notice ==

= 1.8.63 =
Recommended update for all users.

== External Services ==

This plugin uses the following external services:

**Firebase Authentication** (optional — only when Firebase gateway is selected)
- Verifies phone numbers via OTP
- Data sent: phone number, IP address
- [Terms](https://firebase.google.com/terms) | [Privacy](https://firebase.google.com/support/privacy)

**Crisp Chat** (optional, can be disabled in Settings > Installation)
- Live chat support inside the plugin admin panel
- Data sent: chat messages, name, email, IP address
- [Terms](https://crisp.chat/en/terms/) | [Privacy](https://crisp.chat/en/privacy/)

**Microsoft Clarity** (optional, can be disabled in Settings > Installation)
- Anonymous usage analytics on the plugin admin pages only. No visitor or frontend data collected.
- [Terms](https://clarity.microsoft.com/terms) | [Privacy](https://privacy.microsoft.com/en-us/privacystatement)