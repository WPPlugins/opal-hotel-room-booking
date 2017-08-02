<?php
/**
 * The template for displaying room content within loops
 *
 * This template can be overridden by copying it to yourtheme/opalhotel/checkout/payment-method.php.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

$payments = OpalHotel()->payment_gateways->get_payments();

$available = false;
?>

<h3 class="opalhotel-section-title"><?php esc_html_e( 'Payment Method', 'opal-hotel-room-booking' ); ?></h3>

<?php if ( $payments ) : ?>

	<ul class="opalhotel_payment_gateways">

		<?php foreach ( $payments as $id => $payment ) : ?>

			<?php if ( $payment->is_enabled() ) : ?>

				<li>
					<label for="<?php echo esc_attr( $id ) ?>">
						<input type="radio" name="payment_method" value="<?php echo esc_attr( $id ) ?>" id="<?php echo esc_attr( $id ) ?>"<?php echo ( $available === false ) ? ' checked' : '' ?>/>
						<?php printf( '%s', $payment->title ) ?>
					</label>
					<div class="description" <?php echo ( $available === false ) ? '' : ' style="display:none"' ?>>
						<p><?php printf( '%s', $payment->description ) ?></p>
						<?php if ( $form = $payment->form() ) : ?>
							<div class="payment-gateway-fields">
								<?php printf( '%s', $form ) ?>
							</div>
						<?php endif; ?>
					</div>
				</li>

			<?php $available = true; endif; ?>

		<?php endforeach; ?>

	</ul>

<?php endif; ?>

<?php if ( ! $available ) : ?>

	<?php echo __( '<p>Sorry. We have no payment available for you. Please contact administartor to get more details.</p>', 'opal-hotel-room-booking' ) ?>

<?php endif; ?>