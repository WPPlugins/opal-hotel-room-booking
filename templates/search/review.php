<?php
/**
 * $Desc$
 *
 * @version    $Id$
 * @package    opalhotel
 * @author     Opal  Team <info@wpopal.com >
 * @copyright  Copyright (C) 2016 wpopal.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @website  http://www.wpopal.com
 * @support  http://www.wpopal.com/support/forum.html
 */
 
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
 
?>
<div class="opalhotel-reservation-available-review">
<?php if ( $rooms = OpalHotel()->cart->get_rooms() ) : ?>

	<h3 class="widget-title hide"><span><span><?php esc_html_e( 'Booking Cart' ,'opal-hotel-room-booking' );?></span></span></h3>
	<?php foreach ( $rooms as $cart_item_id => $room ) : ?>

		<?php $data = $room['data']; ?>

		<div class="opalhotel-available-review-item">

			<div class="opalhotel-reservation-available-room-title">
				<?php printf( '%s(x%s)', esc_html( $data->get_title() ), $room['qty'] ); ?>
				<label class="opalhotel-review-price">
					<?php printf( '%s', opalhotel_format_price( OpalHotel()->cart->get_cart_item_subtotal( $cart_item_id ) ) ) ?>
					<a href="#" class="cart_remove_item" data-id="<?php echo esc_attr( $cart_item_id ) ?>">
						<i class="fa fa-times" aria-hidden="true"></i>
					</a>
				</label>
			</div>
			<div class="room-meta">
				<span class="adult"><?php printf( __( 'Adult: %d', 'opal-hotel-room-booking' ), $data->adult ) ?></span>
				<span class="children"><?php printf( __( 'Children: %d', 'opal-hotel-room-booking' ), $data->child ) ?></span>
				<?php if ( ! empty( $room['hotel'] ) ) : ?>
					<span class="hotel" style="display: block; clear: both"><?php printf( __( 'Hotel: %s', 'opal-hotel-room-booking' ), get_the_title( $room['hotel'] ) ) ?></span>
				<?php endif; ?>
			</div>
			<?php if ( $packages = OpalHotel()->cart->get_packages( $cart_item_id ) ) : ?>
				<div class="opalhotel_reservation_packages">
					<?php foreach ( $packages as $package_cart_id => $package ) : ?>
						<div class="opalhotel-reservation-available_package-item">
							<label class="opalhotel_package_title"><?php printf( '%s(x%s)', $package['data']->post_title, $package['qty'] ) ?></label>
							<label class="opalhotel-review-price">
								<?php printf( '%s', opalhotel_format_price( OpalHotel()->cart->get_cart_item_subtotal( $package_cart_id ) ) ) ?>
								<a href="#" class="cart_remove_item" data-id="<?php echo esc_attr( $package_cart_id ) ?>">
									<i class="fa fa-times" aria-hidden="true"></i>
								</a>
							</label>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
			<div class="opalhotel-reservation-subtotal">
				<label><?php esc_html_e( 'Subtotal', 'opal-hotel-room-booking' ) ?></label>
				<label class="opalhotel-review-price">
					<?php printf( '%s', opalhotel_format_price( OpalHotel()->cart->get_room_subtotal( $cart_item_id ) ) ) ?>
				</label>
			</div>

		</div>

	<?php endforeach; ?>

	<div class="opalhotel-available-review-item">
		<!-- subtotal -->
		<div class="opalhotel-review-subtotal">
			<label><?php esc_html_e( 'Subtotal', 'opal-hotel-room-booking' ) ?></label>
			<label class="opalhotel-review-price">
				<?php printf( '%s', opalhotel_format_price( OpalHotel()->cart->get_cart_subtotal_display() ) ) ?>
			</label>
		</div>

		<?php if ( OpalHotel()->cart->discount_total ) : ?>

			<?php foreach ( OpalHotel()->cart->coupon_discounts as $code => $amount ) : ?>

				<!-- Coupon -->
				<div class="opalhotel-review-subtotal">
					<label><?php printf( __( 'Coupon: %s (<a href="#" class="remove_coupon" data-code="%s">Remove</a>)', 'opal-hotel-room-booking' ), $code, $code ) ?></label>
					<label class="opalhotel-review-price">
						<?php printf( '-%s', opalhotel_format_price( $amount ) ) ?>
					</label>
				</div>

			<?php endforeach; ?>

		<?php endif; ?>

		<?php if ( opalhotel_tax_enable() && ! opalhotel_tax_enable_cart() ) : ?>
			<!-- tax -->
			<div class="opalhotel-review-subtotal">
				<label><?php esc_html_e( 'Tax', 'opal-hotel-room-booking' ) ?></label>
				<label class="opalhotel-review-price">
					<?php printf( '%s', opalhotel_format_price( OpalHotel()->cart->get_tax_total() ) ) ?>
				</label>
			</div>
		<?php endif; ?>
		<!-- total -->
		<div class="opalhotel-review-total">
			<label><?php printf( __( 'Total%s', 'opal-hotel-room-booking' ), ( opalhotel_tax_enable() && opalhotel_tax_enable_cart() ? __( '<small>(Included Tax)</small>', 'opal-hotel-room-booking' ) : '' ) ) ?></label>
			<label class="opalhotel-review-price">
				<?php printf( '%s', opalhotel_format_price( OpalHotel()->cart->get_cart_total() ) ) ?>
			</label>
		</div>
	</div>

<?php endif; ?>

</div>
