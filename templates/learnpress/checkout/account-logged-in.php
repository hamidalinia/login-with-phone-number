<?php
/**
 * Template for displaying logged in form.
 *
 * This template can be overridden by copying it to yourtheme/learnpress/checkout/form-logged-in.php.
 *
 * @author   ThimPress
 * @package  Learnpress/Templates
 * @version  4.0.0
 */

defined( 'ABSPATH' ) || exit();

if ( ! is_user_logged_in() ) {
	return;
}

global $user_identity;
?>

<div id="checkout-account-logged-in" class="lp-checkout-block left">
    <p>
        <?php
        /* translators: 1: User profile URL, 2: User display name */
        printf(
        /* translators: 1: User profile URL, 2: User display name */
            esc_html__( 'Logged in as %2$s.', 'login-with-phone-number' ),
            esc_url( get_edit_user_link() ),
            esc_html( $user_identity )
        );
        ?>

        <a href="<?php echo esc_url( wp_logout_url( get_permalink() ) ); ?>" title="<?php esc_attr_e( 'Log out of this account', 'login-with-phone-number' ); ?>">
            <?php esc_html_e( 'Log out &raquo;', 'login-with-phone-number' ); ?>
        </a>
    </p>
</div>
