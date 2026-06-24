<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

do_action( 'woocommerce_before_checkout_login_form' );
?>

<?php echo do_shortcode('[idehweb_lwp]'); ?>

<?php
do_action( 'woocommerce_after_checkout_login_form' );