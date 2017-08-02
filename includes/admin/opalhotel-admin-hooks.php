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

/* enter title here placeholder custom post */
add_filter( 'enter_title_here', 'opalhotel_enter_title_here', 1, 2 );
if ( ! function_exists( 'opalhotel_enter_title_here' ) ) {

	/* enter title here custom post type */
	function opalhotel_enter_title_here( $text, $post ) {

		switch ( $post->post_type ) {
			case OPALHOTEL_CPT_ROOM:
				$text = __( 'Enter Room Name', 'opal-hotel-room-booking' );
				break;

			case OPALHOTEL_CPT_HOTEL:
				$text = __( 'Enter Room Name', 'opal-hotel-room-booking' );
				break;

			case OPALHOTEL_CPT_COUPON:
				$text = __( 'Enter Coupon Code', 'opal-hotel-room-booking' );
				break;

			case OPALHOTEL_CPT_PACKAGE:
				$text = __( 'Enter Package Name', 'opal-hotel-room-booking' );
				break;

			default:
				# code...
				break;
		}

		return $text;
	}
}
/* TEMPLATE JS */
add_action( 'admin_footer', 'opalhotel_template_admin_alert_underscore' );
if ( ! function_exists( 'opalhotel_template_admin_alert_underscore' ) ) {
	function opalhotel_template_admin_alert_underscore() {
		?>
		<script type="text/html" id="tmpl-opalhotel-confirm">
			<div class="opalhotel_backbone_modal_content">
				<form>
					<header>
						<h2>
							<# if ( data.message ) { #>
								{{{ data.message }}}
							<# } #>
						</h2>
						<a href="#" class="opalhotel_button_close"><i class="fa fa-times" aria-hidden="true"></i></a>
					</header>
					<footer class="center">

						<input type="hidden" name="action" value="{{ data.action }}">
						<input type="hidden" name="nonce" value="{{ data.nonce }}" />
						<input type="hidden" name="order_id" value="{{ data.order_id }}" />
						<input type="hidden" name="order_item_id" value="{{ data.order_item_id }}" />
						<button type="submit" class="opalhotel-button opalhotel-button-submit button button-primary"><?php esc_html_e( 'Yes', 'opal-hotel-room-booking' ); ?></button>
						<button type="reset" class="opalhotel-button-cancel opalhotel_button_close button"><?php esc_html_e( 'No', 'opal-hotel-room-booking' ) ?></button>

					</footer>
				</form>
			</div>
			<div class="opalhotel_backbone_modal_overflow"></div>
		</script>
		<?php
	}
}
add_action( 'admin_footer', 'opalhotel_template_order_item' );
if ( ! function_exists( 'opalhotel_template_order_item' ) ) {
	function opalhotel_template_order_item() {
		?>
		<script type="text/html" id="tmpl-opalhotel-template-order-item">
			<div class="opalhotel_backbone_modal_content">
				<form>
					<header>
						<h2>
							<# if ( data.message ) { #>
								<!-- {{{ data.message }}} -->
							<# } else { #>
								<?php esc_html_e( 'Add new item', 'opal-hotel-room-booking' ); ?>
							<# } #>
							<select style="width: 200px" name="room_id" data-placeholder="<?php esc_html_e( 'Enter room name', 'opal-hotel-room-booking' ); ?>">
								<# if ( data.room_id ) { #>
									<option value="{{ data.room_id }}" selected>{{ data.room_name }}</option>
								<# } #>
							</select>

							<!-- <select name="qty" class="qty"> -->
								<# if ( data.quantity ) { #>
									<select name="qty" class="qty">
										<option value="0"><?php esc_html_e( 'Quantity', 'opal-hotel-room-booking' ); ?></option>
										<# for ( var i = 1; i <= data.quantity; i++ ) { #>
											<# if ( data.qty == i ) { #>
												<option value="{{ i }}" selected>{{ i }}</option>
											<# } else { #>
												<option value="{{ i }}">{{ i }}</option>
											<# } #>
										<# } #>
									</select>
								<# } #>
							<!-- </select> -->

							<!-- <select name="adult"> -->
								<# if ( data.adults ) { #>
									<select name="adult">
										<option value="0"><?php esc_html_e( 'Adults', 'opal-hotel-room-booking' ); ?></option>
										<# for ( var i = data.adults; i > 0; i-- ) { #>
											<# if ( data.adult == i ) { #>
												<option value="{{ i }}" selected>{{ i }}</option>
											<# } else { #>
												<option value="{{ i }}">{{ i }}</option>
											<# } #>
										<# } #>
									</select>
								<# } #>
							<!-- </select> -->

							<!-- <select name="child"> -->
								<# if ( data.childs ) { #>
									<select name="child">
										<option value="0"><?php esc_html_e( 'Childs', 'opal-hotel-room-booking' ); ?></option>
										<# for ( var i = data.childs; i > 0; i-- ) { #>
											<# if ( data.child == i ) { #>
												<option value="{{ i }}" selected>{{ i }}</option>
											<# } else { #>
												<option value="{{ i }}">{{ i }}</option>
											<# } #>
										<# } #>
									</select>
								<# } #>
							<!-- </select> -->
						</h2>
						<a href="#" class="opalhotel_button_close"><i class="fa fa-times" aria-hidden="true"></i></a>
					</header>
					<div id="container">
						<div class="section opalhotel_datepick_wrap">
							<input type="text" name="arrival" class="opalhotel-has-datepicker" value="{{ data.arrival }}" placeholder="<?php esc_html_e( 'Arrival', 'opal-hotel-room-booking' ); ?>" />
							<input type="text" name="departure" class="opalhotel-has-datepicker" value="{{ data.departure }}" placeholder="<?php esc_html_e( 'Departure', 'opal-hotel-room-booking' ); ?>" />
							<button class="order_check_avaibility button"><?php esc_html_e( 'Check Avaibility', 'opal-hotel-room-booking' ); ?></button>
						</div>
						<div class="section packages">
							<# if ( data.packages ) { #>
								<h5><?php esc_html_e( 'Packages', 'opal-hotel-room-booking' ); ?></h5>
								<# for( var i = 0; i < Object.keys( data.packages ).length; i++ ) { #>
									<p>
										<# var package = data.packages[i] #>
										<input type="checkbox" name="packages[{{ package.id }}][checked]" {{{ package.checked }}} />
										<# if ( package.package ) { #>
											<input type="number" step="1" min="0" name="packages[{{ package.id }}][qty]" value="{{ package.qty }}"/>
										<# } else { #>
											<input type="hidden" name="packages[{{ package.id }}][qty]" value="{{ package.qty }}"/>
										<# } #>
										<strong>{{ package.name }}</strong>
									</p>
								<# } #>

							<# } #>
						</div>
					</div>
					<footer class="center">

						<input type="hidden" name="action" value="{{ data.action }}">
						<input type="hidden" name="nonce" value="{{ data.nonce }}" />
						<input type="hidden" name="order_id" value="{{ data.order_id }}" />
						<input type="hidden" name="order_item_id" value="{{ data.order_item_id }}" />
						<button type="submit" class="opalhotel-button opalhotel-button-submit button button-primary" disabled><?php esc_html_e( 'Yes', 'opal-hotel-room-booking' ); ?></button>
						<button type="reset" class="opalhotel-button-cancel opalhotel_button_close button"><?php esc_html_e( 'No', 'opal-hotel-room-booking' ) ?></button>

					</footer>
				</form>
			</div>
			<div class="opalhotel_backbone_modal_overflow"></div>
		</script>
	<?php
	}
}

/* i18n */
add_action( 'opalhotel_i18n', 'opalhotel_admin_i18n' );
if ( ! function_exists( 'opalhotel_admin_i18n' ) ) {
	function opalhotel_admin_i18n( $data ) {
		return array_merge( $data, array(
				'load_customer_by_email_nonce'		=> wp_create_nonce( 'customer-email' ),
				'load_customer_by_user_name_nonce'	=> wp_create_nonce( 'customer-user-name' ),
				'load_coupon_available_nonce'		=> wp_create_nonce( 'load-coupon-available' ),
				'load_room_by_name'					=> wp_create_nonce( 'load-room-by-name' ),
				'remove_order_item_nonce'			=> wp_create_nonce( 'remove-order-item' ),
				'coupon_code_empty'					=> __( 'Coupon code is empty.', 'opal-hotel-room-booking' ),
				'add_coupon_nonce'					=> wp_create_nonce( 'add-coupon-code' ),
				'remove_coupon_nonce'				=> wp_create_nonce( 'remove-coupon-code' ),
				'load_available_room_nonce'			=> wp_create_nonce( 'load-available-room-nonce' ),
				'add_order_item'					=> wp_create_nonce( 'add-order-item' ),
				'edit_order_item'					=> wp_create_nonce( 'edit-order-item' ),
				'arrival_invalid'					=> __( 'Arrival is invalid.', 'opal-hotel-room-booking' ),
				'departure_invalid'					=> __( 'Departure is invalid.', 'opal-hotel-room-booking' ),
				'remove_item_message'				=> __( 'Do you want remove this item?', 'opal-hotel-room-booking' ),
				'remove_coupon_message'				=> __( 'Do you want remove this coupon?', 'opal-hotel-room-booking' ),
				'add_line_item_message'				=> __( 'Add line item', 'opal-hotel-room-booking' ),
			) );
	}
}

add_action( 'save_post', 'opalhotel_admin_update_hotel_amenities' );
if ( ! function_exists( 'opalhotel_admin_update_hotel_amenities' ) ) {

	function opalhotel_admin_update_hotel_amenities( $post_id ) {
		if ( get_post_type( $post_id ) !== OPALHOTEL_CPT_HOTEL ) return;
		if ( empty( $_POST['_amenities'] ) ) {
			delete_post_meta( $post_id, '_amenity' );
		} else {
			delete_post_meta( $post_id, '_amenity' );
			foreach ( $_POST['_amenities'] as $id ) {
				add_post_meta( $post_id, '_amenity', $id );
			}
		}
	}

}

add_action( 'parse_query', 'opalhotel_admin_parse_query' );
if ( ! function_exists( 'opalhotel_admin_parse_query' ) ) {

	function opalhotel_admin_parse_query( $query ) {
		if ( ! is_admin() || empty( $_REQUEST['hotel_id'] ) || $query->get( 'post_type' ) !== OPALHOTEL_CPT_ROOM ) {
			return;
		}
		$query->set( 'meta_key', '_hotel' );
		$query->set( 'meta_value', absint( $_REQUEST['hotel_id'] ) );
		$query->set( 'meta_compare', '=' );
	}
}

















