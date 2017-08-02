<?php
/**
 * @Author: brainos
 * @Date:   2016-04-24 20:12:41
 * @Last Modified by:   someone
 * @Last Modified time: 2016-05-02 10:47:29
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

$order = OpalHotel_Order::instance( $post );

?>
<table class="opalhotel_order_items" cellpadding="0" cellspacing="0">
	<thead>
		<tr>
			<th class="item"><?php esc_html_e( 'Items', 'opal-hotel-room-booking' ); ?></th>
			<th class="arrival_departure"><?php esc_html_e( 'Arrival - Departure', 'opal-hotel-room-booking' ); ?></th>
			<th class="night"><?php esc_html_e( 'Night', 'opal-hotel-room-booking' ); ?></th>
			<th class="qty"><?php esc_html_e( 'Qty', 'opal-hotel-room-booking' ); ?></th>
			<th class="total"><?php esc_html_e( 'Total', 'opal-hotel-room-booking' ); ?></th>
			<th class="action"><?php esc_html_e( 'Actions', 'opal-hotel-room-booking' ); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php if ( $rooms = $order->get_rooms() ) : ?>
			<?php foreach ( $rooms as $room ) : ?>
				<?php $order_item = OpalHotel_Order_Item::instance( $room->order_item_id ); ?>
				<tr>
					<td class="item">
						<?php printf( '<a href="%s">%s</a>', get_edit_post_link( $order_item->product_id ), $room->order_item_name ) ?>
						<?php if ( $order_item->hotel ) : $hotel = get_post( $order_item->hotel ); ?>
							<?php printf( '(%s <a href="%s" target="_blank">%s</a>)', __( 'Hotel', 'opal-hotel-room-booking' ), get_permalink( $order_item->hotel ), $hotel->post_title ) ?>
						<?php endif; ?>
					</td>
					<td class="arrival_departure"><?php printf( '%s - %s', opalhotel_format_date( $order_item->arrival ), opalhotel_format_date( $order_item->departure ) ); ?></td>
					<td class="night"><?php printf( '%s', opalhotel_count_nights( $order_item->arrival, $order_item->departure ) ) ?></td>
					<td class="qty"><?php printf( '%s', $order_item->qty ); ?></td>
					<td class="total"><?php printf( '%s', opalhotel_format_price( $order_item->subtotal, $order->payment_currency ) ); ?></td>
					<td class="action">
						<a href="#" class="opalhotel_tiptip edit_order_item" data-tip="<?php esc_html_e( 'Edit', 'opal-hotel-room-booking' ); ?>" data-id="<?php echo esc_attr( $room->order_item_id ); ?>" data-order-id="<?php echo esc_attr( $order->id ); ?>">
							<i class="fa fa-pencil"></i>
						</a>
						<a href="#" class="opalhotel_tiptip remove_order_item" data-tip="<?php esc_html_e( 'Remove', 'opal-hotel-room-booking' ); ?>" data-id="<?php echo esc_attr( $room->order_item_id ); ?>" data-order-id="<?php echo esc_attr( $order->id ); ?>">
							<i class="fa fa-times-circle"></i>
						</a>
					</td>
				</tr>
				<?php if ( $packages = $order->get_room_packages( $room->order_item_id ) ) : ?>
					<?php foreach ( $packages as $package ) : ?>
						<?php $order_item = OpalHotel_Order_Item::instance( $package->order_item_id ); ?>
						<tr>
							<td class="item center" colspan="3">
								<?php printf( '<a href="%s">%s</a>', get_edit_post_link( $order_item->product_id ), $package->order_item_name ) ?>
							</td>
							<td class="qty"><?php printf( '%s', $order_item->qty ); ?></td>
							<td class="total"><?php printf( '%s', opalhotel_format_price( $order_item->subtotal, $order->payment_currency ) ); ?></td>
							<td class="action">
								<!-- <a href="#" class="opalhotel_tiptip edit_order_item" data-tip="<?php esc_html_e( 'Edit', 'opal-hotel-room-booking' ); ?>" data-id="<?php echo esc_attr( $package->order_item_id ); ?>" data-order-id="<?php echo esc_attr( $order->id ); ?>">
									<i class="fa fa-pencil"></i>
								</a> -->
								<a href="#" class="opalhotel_tiptip remove_order_item" data-tip="<?php esc_html_e( 'Remove', 'opal-hotel-room-booking' ); ?>" data-id="<?php echo esc_attr( $package->order_item_id ); ?>" data-order-id="<?php echo esc_attr( $order->id ); ?>">
									<i class="fa fa-times-circle"></i>
								</a>
							</td>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
			<?php endforeach; ?>
		<?php endif; ?>
	</tbody>
	<tfoot>
		<tr class="add_line_item_tr">
			<td colspan="6">
				<a href="#" class="button add_line_item"><?php esc_html_e( 'Add Line Item', 'opal-hotel-room-booking' ); ?></a>
			</td>
		</tr>
		<tr>
			<td colspan="5"><?php esc_html_e( 'Subtotal', 'opal-hotel-room-booking' ); ?></td>
			<td><?php printf( '%s', opalhotel_format_price( $order->get_subtotal() ) ) ?></td>
		</tr>
		<?php if ( $order->coupon ) : ?>
			<tr>
				<td colspan="5">
					<?php printf( __( 'Coupon(%s)', 'opal-hotel-room-booking' ), $order->coupon['code'] ); ?>
					(<a href="#" class="remove_coupon" data-order-id="<?php echo esc_attr( $order->id ); ?>"><?php esc_html_e( 'Remove Coupon','opal-hotel-room-booking' ); ?></a>)
				</td>
				<td>
					<?php printf( '-%s', opalhotel_format_price( $order->coupon_discount, $order->payment_currency ) ) ?>
				</td>
			</tr>
		<?php else : ?>
			<tr>
				<td colspan="5">
					<select style="min-width: 150px;" name="_coupon" id="_coupon_code" data-placeholder="<?php esc_html_e( 'Enter Coupon Code', 'opal-hotel-room-booking' ) ?>" data-order-id="<?php echo esc_attr( $order->id ); ?>"></select>
				</td>
				<td>
					<a href="#" class="button add_coupon" data-order-id="<?php echo esc_attr( $order->id ); ?>"><?php esc_html_e( 'Add Coupon', 'opal-hotel-room-booking' ); ?></a>
				</td>
			</tr>
		<?php endif; ?>
		<?php if ( opalhotel_tax_enable() ) : ?>
			<!-- tax -->
			<tr class="opalhotel-review-subtotal">
				<td colspan="5"><?php esc_html_e( 'Tax', 'opal-hotel-room-booking' ) ?></td>
				<td>
					<?php printf( '%s', opalhotel_format_price( $order->get_tax_total(), $order->payment_currency ) ) ?>
				</td>
			</tr>
		<?php endif; ?>
		<tr>
			<td colspan="5">
				<?php printf( __( 'Total%s', 'opal-hotel-room-booking' ), ( opalhotel_tax_enable() && opalhotel_tax_enable_cart() ? __( '<small>(Included Tax)</small>', 'opal-hotel-room-booking' ) : '' ) ) ?>
			</td>
			<td>
				<?php printf( '%s', opalhotel_format_price( $order->get_total(), $order->payment_currency ) ) ?>
			</td>
		</tr>
	</tfoot>
</table>