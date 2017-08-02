<?php
/**
 * The template for displaying room content within loops
 *
 * This template can be overridden by copying it to yourtheme/opalhotel/checkout/checkout.php.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

?>

<?php if ( empty( OpalHotel()->cart->coupons ) ) : ?>

	<div class="opalhotel-form-group coupon_section opalhotel-form-group">
		<label><?php esc_html_e( 'Have you got a coupon code?', 'opal-hotel-room-booking' ); ?></label>
		<input type="text" name="opalhotel_coupon_code" id="opalhotel_coupon_code" placeholder="<?php esc_attr_e( 'Coupon Code', 'opal-hotel-room-booking' ); ?>" />
		<button class="opalhotel-button-submit" id="opalhotel_apply_coupon"><?php esc_html_e( 'Apply Coupon', 'opal-hotel-room-booking' ); ?></button>
	</div>
<?php endif; ?>
