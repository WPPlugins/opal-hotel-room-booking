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

global $post;
$order = OpalHotel_Order::instance( $post );
$payments = OpalHotel()->payment_gateways->get_payments();
?>

<div class="submitbox">
	<div id="minor-publishing">
		<div id="payments">
			<label for="_payment_method"><?php esc_html_e( 'Payment method', 'opal-hotel-room-booking' ); ?></label>
			<select name="_payment_method">
				<?php foreach ( $payments as $slug => $payment ) : ?>

					<option value="<?php echo esc_attr( $slug ) ?>"<?php selected( $order->payment_method, $slug ); ?>>
						<?php echo esc_html( $payment->title ); ?>
					</option>

				<?php endforeach; ?>
			</select>
		</div>

		<div id="status-action">
			<label for="opalhotel-status-action"><?php esc_html_e( 'Payment status', 'opal-hotel-room-booking' ); ?></label>
			<select name="_payment_status" class="opalhotel-status-action" id="opalhotel-status-action">
				<?php foreach ( opalhotel_get_order_statuses() as $slug => $status ) : ?>

					<option value="<?php echo esc_attr( $slug ) ?>"<?php selected( $post->post_status, $slug ); ?> data-desc="<?php echo esc_attr( opalhotel_get_order_status_description( $slug ) ); ?>"><?php echo esc_html( $status ); ?></option>

				<?php endforeach; ?>
			</select>
			<p class="opalhotel-status-desc"><?php printf( '%s', opalhotel_get_order_status_description( $post->post_status ) ); ?></p>
		</div>

		<div id="customer">
			<label for="_customer_id"><?php esc_html_e( 'Customer', 'opal-hotel-room-booking' ); ?></label>
			<select style="width: 100%" name="_customer_id" id="_customer_id" data-placeholder="<?php esc_attr_e( 'Enter user email', 'opal-hotel-room-booking' ); ?>">
				<?php if ( $order->customer_id ) : ?>
					<?php $user = get_user_by( 'id', $order->customer_id ); $user_email = $user->user_email; ?>
					<option value="<?php echo esc_attr( $order->customer_id ); ?>">
						<?php printf( '(#%s) %s', $order->customer_id, $user_email ) ?>
					</option>
				<?php endif; ?>
			</select>
		</div>
	</div>
	<div id="major-publishing-actions">
		<div id="delete-action">
			<?php if ( current_user_can( 'delete_post', $post->ID ) ) : ?>
				<a class="submitdelete deletion" href="<?php echo esc_attr( get_delete_post_link( $post->ID ) ) ?>"><?php esc_html_e( 'Move to Trash', 'opal-hotel-room-booking' ); ?></a>
			<?php endif; ?>
		</div>
		<div id="publishing-action">
			<button name="save" type="submit" class="button button-primary" id="publish">
				<?php printf( '%s', $post->post_status !== 'auto-draft' ? __( 'Update', 'opal-hotel-room-booking' ) : __( 'Save', 'opal-hotel-room-booking' ) ) ?>
			</button>
		</div>
	</div>

	<?php wp_nonce_field( 'opalhotel_save_data', 'opalhotel_meta_nonce' ); ?>
</div>
