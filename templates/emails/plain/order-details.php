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

============================= <?php esc_html_e( 'Reservation Details', 'opal-hotel-room-booking' ); ?> =============================

<table>
	<thead>
		<tr>
			<th><?php esc_html_e( 'Room', 'opal-hotel-room-booking' ); ?></th>
			<th><?php esc_html_e( 'Data', 'opal-hotel-room-booking' ); ?></th>
			<th><?php esc_html_e( 'Package', 'opal-hotel-room-booking' ); ?></th>
			<th><?php esc_html_e( 'Price', 'opal-hotel-room-booking' ); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ( $order->get_rooms() as $room ) : ?>
			<?php $room = OpalHotel_Order_Item::instance( $room->order_item_id ); ?>
			<tr>

				<td>
					<?php printf( '%s(x%s)', esc_html( $room->order_item_name ), $room->qty ); ?>
					(<?php printf( '%s', opalhotel_format_price( $room->subtotal, $order->payment_currency ) ) ?>)
				</td>
				<td>
					<?php printf( __( 'Adult: %d' ), $room->adults ) . "\n\n" ?>
					<?php printf( __( 'Children: %d' ), $room->childrens ) ?>
				</td>
				<?php if ( $packages = $order->get_room_packages( $room->order_item_id ) ) : ?>
					<td>
						<?php foreach ( $packages as $package ) : ?>
							<?php $package = OpalHotel_Order_Item::instance( $package->order_item_id ); ?>
							<?php printf( '%s(x%s)', $package->order_item_name, $package->qty ) ?>
							(<?php printf( '%s', opalhotel_format_price( $package->subtotal, $order->payment_currency ) ) ?>)
						<?php endforeach; ?>
					</td>
				<?php endif; ?>
				<td>
					<?php printf( '%s', opalhotel_format_price( $order->get_room_subtotal( $room->order_item_id ), $order->payment_currency ) ) ?>
				</td>

			</tr>

		<?php endforeach; ?>
		<tfoot>
			<tr>
				<td>
					<?php esc_html_e( 'Subtotal', 'opal-hotel-room-booking' ) ?>
				</td>
				<td>
					<?php printf( '%s', opalhotel_format_price( $order->subtotal, $order->payment_currency ) ) ?>
				</td>
			</tr>
			<?php if ( $order->coupons ) : ?>

				<!-- Coupon -->
				<tr>
					<td>
						<?php printf( __( 'Coupon: %s', 'opal-hotel-room-booking' ), $order->coupons['code'] ) ?>
					</td>
					<td>
						<?php printf( '-%s', opalhotel_format_price( $order->coupon_discount, $order->payment_currency ) ) ?>
					</td>
				</tr>

			<?php endif; ?>

			<?php if ( opalhotel_tax_enable() ) : ?>
				<!-- tax -->
				<tr>
					<td><?php esc_html_e( 'Tax', 'opal-hotel-room-booking' ) ?></td>
					<td>
						<?php printf( '%s', opalhotel_format_price( $order->tax_total, $order->payment_currency ) ) ?>
					</td>
				</tr>
			<?php endif; ?>
			<!-- total -->
			<tr>
				<td><?php printf( __( 'Total%s', 'opal-hotel-room-booking' ), ( opalhotel_tax_enable() && opalhotel_tax_enable_cart() ? __( '<small>(Included Tax)</small>', 'opal-hotel-room-booking' ) : '' ) ) ?></td>
				<td class="opalhotel-review-price">
					<?php printf( '%s', opalhotel_format_price( $order->total, $order->payment_currency ) ) ?>
				</td>
			</tr>
		</tfoot>
	</tbody>
</table>


<?php do_action( 'opalhotel_before_email_order_details', $order ); ?>