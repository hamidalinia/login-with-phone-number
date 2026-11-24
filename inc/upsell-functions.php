<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Free vs PRO comparison card, comparison page and a dismissible upgrade notice.
 * Nothing in the free plugin is locked: PRO-only items are only described here.
 */
trait Upsell_Functions
{
    function lwp_pro_url($medium = 'compare-card')
    {
        return add_query_arg(array(
            'utm_source'   => 'lwp-plugin',
            'utm_medium'   => $medium,
            'utm_campaign' => 'plugin-install',
        ), 'https://idehweb.com/product/login-with-phone-number-in-wordpress/');
    }

    function lwp_compare_rows()
    {
        // [label, free, pro]  — true = included, false = not included, string = short note
        return array(
            array(__('Phone number & email OTP login', 'login-with-phone-number'), true, true),
            array(__('Free gateways: Firebase, kwtSMS, Netgsm, Webruno, Custom API', 'login-with-phone-number'), true, true),
            array(__('WooCommerce, shortcode & Gutenberg block', 'login-with-phone-number'), true, true),
            array(__('Resend cooldown & OTP attempt limits', 'login-with-phone-number'), true, true),
            array(__('Premium gateways: Twilio, MSG91, WhatsApp, Vonage, SMS.ir, MelliPayamak, FarazSMS…', 'login-with-phone-number'), false, true),
            array(__('Style panel: logo, colors, background, buttons', 'login-with-phone-number'), __('Custom CSS', 'login-with-phone-number'), true),
            array(__('Custom registration fields & default user role', 'login-with-phone-number'), false, true),
            array(__('Priority support', 'login-with-phone-number'), false, true),
        );
    }

    function lwp_compare_soon()
    {
        return array(
            __('CAPTCHA before sending SMS (Turnstile, hCaptcha, reCAPTCHA)', 'login-with-phone-number'),
            __('SMS usage reports with CSV export', 'login-with-phone-number'),
            __('Telegram & email alerts for suspicious activity', 'login-with-phone-number'),
            __('Automatic fallback: WhatsApp or voice call', 'login-with-phone-number'),
        );
    }

    function lwp_compare_cell($v)
    {
        if ($v === true) {
            return '<span class="lwp-cmp-yes" aria-label="' . esc_attr__('Included', 'login-with-phone-number') . '">&#10003;</span>';
        }
        if ($v === false) {
            return '<span class="lwp-cmp-no" aria-label="' . esc_attr__('Not included', 'login-with-phone-number') . '">&mdash;</span>';
        }
        return '<span class="lwp-cmp-note">' . esc_html($v) . '</span>';
    }

    function lwp_render_compare_card($context = 'sidebar')
    {
        if (class_exists(LWP_PRO::class)) {
            return;
        }
        $medium = $context === 'page' ? 'compare-page' : 'compare-card';
        ?>
        <div class="lwp-compare-card lwp-compare-<?php echo esc_attr($context); ?>">
            <h3><?php esc_html_e('Free vs PRO', 'login-with-phone-number'); ?></h3>
            <table class="lwp-compare-table">
                <thead>
                <tr>
                    <th></th>
                    <th><?php esc_html_e('Free', 'login-with-phone-number'); ?></th>
                    <th class="lwp-cmp-pro-col"><?php esc_html_e('PRO', 'login-with-phone-number'); ?></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($this->lwp_compare_rows() as $row) { ?>
                    <tr>
                        <td><?php echo esc_html($row[0]); ?></td>
                        <td><?php echo wp_kses($this->lwp_compare_cell($row[1]), array('span' => array('class' => array(), 'aria-label' => array()))); ?></td>
                        <td class="lwp-cmp-pro-col"><?php echo wp_kses($this->lwp_compare_cell($row[2]), array('span' => array('class' => array(), 'aria-label' => array()))); ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            <div class="lwp-compare-soon">
                <strong><?php esc_html_e('Coming soon to PRO', 'login-with-phone-number'); ?></strong>
                <ul>
                    <?php foreach ($this->lwp_compare_soon() as $item) { ?>
                        <li><?php echo esc_html($item); ?></li>
                    <?php } ?>
                </ul>
            </div>
            <a class="button button-primary lwp-compare-cta" href="<?php echo esc_url($this->lwp_pro_url($medium)); ?>" target="_blank" rel="noopener">
                <?php esc_html_e('Upgrade to PRO', 'login-with-phone-number'); ?>
            </a>
        </div>
        <?php
    }

    function lwp_compare_page()
    {
        if (!current_user_can('manage_options')) {
            return;
        }
        echo '<div class="wrap"><h1>' . esc_html__('Free vs PRO', 'login-with-phone-number') . '</h1>';
        if (class_exists(LWP_PRO::class)) {
            echo '<p>' . esc_html__('PRO is active. Thank you for supporting the plugin!', 'login-with-phone-number') . '</p>';
        } else {
            $this->lwp_render_compare_card('page');
        }
        echo '</div>';
    }

    function lwp_register_compare_page()
    {
        if (class_exists(LWP_PRO::class)) {
            return;
        }
        $hook = add_submenu_page('idehweb-lwp', __('Free vs PRO', 'login-with-phone-number'), __('Free vs PRO', 'login-with-phone-number'), 'manage_options', 'idehweb-lwp-compare', array(&$this, 'lwp_compare_page'));
        add_action('admin_print_styles-' . $hook, array(&$this, 'admin_custom_css'));
    }

    function lwp_maybe_hide_upsell()
    {
        if (!isset($_GET['lwp_hide_upsell']) || !current_user_can('manage_options')) {
            return;
        }
        $nonce = isset($_GET['_wpnonce']) ? sanitize_text_field(wp_unslash($_GET['_wpnonce'])) : '';
        if (!wp_verify_nonce($nonce, 'lwp_hide_upsell')) {
            return;
        }
        update_user_meta(get_current_user_id(), 'lwp_upsell_hidden_until', time() + 30 * DAY_IN_SECONDS);
        wp_safe_redirect(remove_query_arg(array('lwp_hide_upsell', '_wpnonce')));
        exit;
    }

    function lwp_upgrade_notice()
    {
        if (!current_user_can('manage_options') || class_exists(LWP_PRO::class)) {
            return;
        }
        // Only on this plugin's own settings pages, never site-wide.
        $page = isset($_GET['page']) ? sanitize_key(wp_unslash($_GET['page'])) : '';
        if (strpos($page, 'idehweb-lwp') !== 0 || $page === 'idehweb-lwp-compare') {
            return;
        }
        if ((int) get_user_meta(get_current_user_id(), 'lwp_upsell_hidden_until', true) > time()) {
            return;
        }
        $hide    = wp_nonce_url(add_query_arg('lwp_hide_upsell', '1'), 'lwp_hide_upsell');
        $compare = admin_url('admin.php?page=idehweb-lwp-compare');
        ?>
        <div class="notice notice-info lwp-upgrade-notice">
            <p>
                <?php esc_html_e('Want a branded login form, custom registration fields and premium SMS gateways? See what PRO adds.', 'login-with-phone-number'); ?>
                <a class="button button-primary" href="<?php echo esc_url($compare); ?>"><?php esc_html_e('Compare Free vs PRO', 'login-with-phone-number'); ?></a>
                <a href="<?php echo esc_url($hide); ?>"><?php esc_html_e('Hide for 30 days', 'login-with-phone-number'); ?></a>
            </p>
        </div>
        <?php
    }
}
