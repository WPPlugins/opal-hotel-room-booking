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

global $post;
/* print notices when apply coupon */
opalhotel_print_notices();

?>

<?php if( OpalHotel()->cart->is_empty ) : ?>
	<?php
		/* cart is empty */
		opalhotel_get_template( 'checkout/empty.php' );
	?>
<?php else: ?>

	<!-- customer info -->
	<form action="<?php ?>" method="POST" name="opalhotel-checkout-form" class="opalhotel-checkout-form">

		<?php do_action( 'opalhotel_before_checkout' ); ?>

		<div class="opalhotel_reservation_checkout">

			<div class="row">
				<div class="col-md-8 col-xs-12">
					<div class="opalhotel-customer-details opalhotel-form-section-group">
						<?php do_action( 'opalhotel_checkout_customer_form' ) ?>
					</div>
					<div class="term-conditional">
						<label for="term_conditional">
							<input type="checkbox" name="term_conditional" id="term_conditional" />
							<?php echo esc_html( get_the_title( opalhotel_get_page_id( 'terms' ) ) ) ?> 
								<a href="<?php echo esc_url( opalhotel_get_term_url() ) ?>" target="_blank">
									<?php esc_html_e( 'View Detail', 'opal-hotel-room-booking' );?>	
								</a>
						</label>
					</div>
				</div>

				<div class="col-md-4 col-xs-12">
					<div class="opalhotel-checkout-review opalhotel-form-section-group">
						<?php do_action( 'opalhotel_checkout_review' ) ?>
						<div class="opalhotel-payment-methods opalhotel-form-section-group">
							<?php do_action( 'opalhotel_checkout_payment_method' ) ?>
						</div>
					</div>
				</div>
			</div>

			<div class="opalhotel-form-section-group footer">

				<?php if ( get_option( 'opalhotel_terms_require' ) && opalhotel_get_page_id( 'terms' ) ) : ?>

					<div class="opalhotel-form-footer"></div>

				<?php endif; ?>

				<div class="opalhotel-form-footer">
					<input type="hidden" name="action" value="opalhotel_process_checkout" />
					<?php wp_nonce_field( 'opalhotel-checkout', 'opalhotel_checkout_nonce' ); ?>
					<input type="hidden" name="page" value="<?php echo $post ? esc_attr( $post->ID ) : '' ?>">
					<button type="submit" class="opalhotel-button-submit button button-primary-inverse  pull-right"><?php esc_html_e( 'Reservation', 'opal-hotel-room-booking' ); ?></button>
				</div>

			</div>
		</div>

		<?php do_action( 'opalhotel_after_checkout' ); ?>

	</form>

<?php endif; ?>