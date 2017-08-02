<?php
/**
 * Email Header
 *
 * This template can be overridden by copying it to yourtheme/opalhotel/emails/order-details.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

do_action( 'opalhotel_before_email_order_details', $order );

?>

<h3><?php esc_html_e( 'Reservation Details', 'opal-hotel-room-booking' ); ?></h3>

<div class="opalhotel_order_details">

	<?php foreach ( $order->get_rooms() as $room ) : ?>
		<?php $room = OpalHotel_Order_Item::instance( $room->order_item_id ); ?>
		<div class="opalhotel-order-item-details">

			<div class="opalhotel-order-item-room-title">
				<?php printf( '%s(x%s)', esc_html( $room->order_item_name ), $room->qty ); ?>
				<label class="opalhotel-review-price">
					<?php printf( '%s', opalhotel_format_price( $room->subtotal, $order->payment_currency ) ) ?>
				</label>
			</div>
			<div class="opalhotel_order_item_room_info">
				<span class="adult"><?php printf( __( 'Adult: %d' ), $room->adult ) ?></span>
				<span class="children"><?php printf( __( 'Children: %d' ), $room->child ) ?></span>
			</div>
			<?php if ( $packages = $order->get_room_packages( $room->order_item_id ) ) : ?>
				<div class="opalhotel_reservation_packages">
					<?php foreach ( $packages as $package ) : ?>
						<?php $package = OpalHotel_Order_Item::instance( $package->order_item_id ); ?>
						<div class="opalhotel-reservation-available_package-item">
							<label class="opalhotel_package_title"><?php printf( '%s(x%s)', $package->order_item_name, $package->qty ) ?></label>
							<label class="opalhotel-review-price">
								<?php printf( '%s', opalhotel_format_price( $package->subtotal, $order->payment_currency ) ) ?>
							</label>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
			<div class="opalhotel_order_item_subtotal">
				<label><?php esc_html_e( 'Subtotal', 'opal-hotel-room-booking' ) ?></label>
				<label class="opalhotel-review-price">
					<?php printf( '%s', opalhotel_format_price( $order->get_room_subtotal( $room->order_item_id ), $order->payment_currency ) ) ?>
				</label>
			</div>

		</div>

	<?php endforeach; ?>

	<div class="opalhotel-order-item-details">
		<!-- subtotal -->
		<div class="opalhotel-review-subtotal">
			<label><?php esc_html_e( 'Subtotal', 'opal-hotel-room-booking' ) ?></label>
			<label class="opalhotel-review-price">
				<?php printf( '%s', opalhotel_format_price( $order->get_subtotal(), $order->payment_currency ) ) ?>
			</label>
		</div>

		<?php if ( $order->coupon ) : ?>

			<!-- Coupon -->
			<div class="opalhotel-review-subtotal">
				<label><?php printf( __( 'Coupon: %s', 'opal-hotel-room-booking' ), $order->coupons['code'] ) ?></label>
				<label class="opalhotel-review-price">
					<?php printf( '-%s', opalhotel_format_price( $order->coupon_discount, $order->payment_currency ) ) ?>
				</label>
			</div>

		<?php endif; ?>

		<?php if ( opalhotel_tax_enable() ) : ?>
			<!-- tax -->
			<div class="opalhotel-review-subtotal">
				<label><?php esc_html_e( 'Tax', 'opal-hotel-room-booking' ) ?></label>
				<label class="opalhotel-review-price">
					<?php printf( '%s', opalhotel_format_price( $order->get_tax_total(), $order->payment_currency ) ) ?>
				</label>
			</div>
		<?php endif; ?>
		<!-- total -->
		<div class="opalhotel-review-total">
			<label><?php printf( __( 'Total%s', 'opal-hotel-room-booking' ), ( opalhotel_tax_enable() && opalhotel_tax_enable_cart() ? __( '<small>(Included Tax)</small>', 'opal-hotel-room-booking' ) : '' ) ) ?></label>
			<label class="opalhotel-review-price">
				<?php printf( '%s', opalhotel_format_price( $order->get_total(), $order->payment_currency ) ) ?>
			</label>
		</div>
	</div>

</div>

<?php do_action( 'opalhotel_before_email_order_details', $order ); ?>