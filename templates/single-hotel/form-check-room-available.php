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

$arrival = isset( $_REQUEST['arrival_datetime'] ) ? sanitize_text_field( $_REQUEST['arrival_datetime'] ) : date( 'Y-m-d', current_time( 'timestamp' ) );
$departure = isset( $_REQUEST['departure_datetime'] ) ? sanitize_text_field( $_REQUEST['departure_datetime'] ) : date( 'Y-m-d', current_time( 'timestamp' ) + DAY_IN_SECONDS );

$adult = isset( $_REQUEST['adult'] ) ? absint( $_REQUEST['adult'] ) : 0;
$child = isset( $_REQUEST['child'] ) ? absint( $_REQUEST['child'] ) : 0;

$room_type = ! isset( $room_type ) && isset( $_REQUEST['room_type'] ) ? $_REQUEST['room_type'] : 0;
$room_type_selected = isset( $_REQUEST['room_type'] ) ? sanitize_text_field( $_REQUEST['room_type'] ) : '';

$hotel_id = $post && $post->ID ? $post->ID : 0;
$number_of_rooms = isset( $_REQUEST['number_of_rooms'] ) ? absint( $_REQUEST['number_of_rooms'] ) : 0;

?>

<div class="opalhotel_form_section">

	<form action="<?php echo esc_attr( opalhotel_get_reservation_url() ) ?>" method="GET" name="opalhotel_load_rooms_of_hotel" class="opalhotel_load_rooms_of_hotel single-hotel opalhotel_datepick_wrap">
		<div class="opalhotel horizontal-form clearfix">
			<div class="form-content">
				<!-- arrival date -->
				<div class="opalhotel-form-field checkin-input">
					<label class="opalhotel-form-lable"><?php esc_html_e( 'Check In', 'opal-hotel-room-booking' ); ?></label>
					<div class="opalhotel-form-field-group">
						<i class="fa fa-calendar-o" aria-hidden="true"></i>
						<input class="opalhotel_arrival_date opalhotel-has-datepicker" name="arrival" placeholder="<?php esc_attr_e( 'Arrival Date', 'opal-hotel-room-booking' ); ?>" data-end="opalhotel-departure-date" value="<?php echo esc_attr( opalhotel_format_date( strtotime( $arrival ) ) ) ?>"/>
						<?php if ( isset( $arrival ) && $arrival ) : ?>
							<input type="hidden" name="arrival_datetime" value="<?php echo esc_attr( $arrival ) ?>" />
						<?php endif; ?>
					</div>
				</div>
				<!-- end arrival date -->

				<!-- departure date -->
				<div class="opalhotel-form-field checkout-input">
					<label class="opalhotel-form-lable"><?php esc_html_e( 'Check Out', 'opal-hotel-room-booking' ); ?></label>
					<div class="opalhotel-form-field-group">
						<i class="fa fa-calendar-o" aria-hidden="true"></i>
						<input class="opalhotel-departure-date opalhotel-has-datepicker" name="departure" placeholder="<?php esc_attr_e( 'Departure Date', 'opal-hotel-room-booking' ); ?>" data-start="opalhotel_arrival_date" value="<?php echo esc_attr( opalhotel_format_date( strtotime( $departure ) ) ) ?>"/>
						<?php if ( isset( $departure ) && $departure ) : ?>
							<input type="hidden" name="departure_datetime" value="<?php echo esc_attr( $departure ) ?>" />
						<?php endif; ?>
					</div>
				</div>
				<!-- end departure date -->

				<div class="opalhotel-form-field room-type-input">
					<label class="opalhotel-form-lable"><?php esc_html_e( 'Rooms', 'opal-hotel-room-booking' ); ?></label>
					<?php $max_rooms = opalhotel_get_max_rooms_number(); ?>
					<div class="opalhotel-form-field-group">
						<?php
							printf( '%s', opalhotel_select_number( array(
								'name'	=> 'number_of_rooms',
								'class'	=> 'adult',
								'min'	=> 1,
								'max'	=> $max_rooms,
								'class'	=> 'opalhotel_rooms',
								'selected'	=> $number_of_rooms
							) ) );
						?>
					</div>
				</div>

				<!-- max adult -->
				<div class="opalhotel-form-field adults-input">
					<label class="opalhotel-form-lable"><?php esc_html_e( 'Adults', 'opal-hotel-room-booking' ); ?></label>
					<?php $max_adult = opalhotel_get_max_adults(); ?>
					<div class="opalhotel-form-field-group">
						<?php
							printf( '%s', opalhotel_select_number( array(
								'name'	=> 'adult',
								'class'	=> 'adult',
								'min'	=> 1,
								'max'	=> $max_adult,
								// 'none'	=> __( 'Adult', 'opal-hotel-room-booking' ),
								'class'	=> 'opalhotel_adult',
								'selected'	=> $adult
							) ) );
						?>
					</div>
				</div>
				<!-- end max adult -->

				<?php if ( opalhotel_get_option( 'search_enable_max_child', 1 ) ) : ?>

					<!-- max child -->
					<div class="opalhotel-form-field children-input">
						<label class="opalhotel-form-lable"><?php esc_html_e( 'Children', 'opal-hotel-room-booking' ); ?></label>
						<?php $max_child = opalhotel_get_max_childs();?>
						<div class="opalhotel-form-field-group">
							<?php
								printf( '%s', opalhotel_select_number( array(
									'name'	=> 'child',
									'class'	=> 'child',
									'min'	=> 0,
									'max'	=> $max_child,
									// 'none'	=> __( 'Children', 'opal-hotel-room-booking' ),
									'class'	=> 'opalhotel_children',
									'selected'	=> $child
								) ) );
							?>
						</div>
					</div>
					<!-- end max child -->

				<?php endif; ?>

				<!-- before hook -->
				<div class="opalhotel-form-field button-wrap">
					<input type="hidden" name="hotel_id" value="<?php echo esc_attr( $hotel_id ) ?>" />
					<input type="hidden" name="action" value="opalhotel_load_rooms_hotel" />
					<?php wp_nonce_field( 'opalhotel-rooms-avaialble-of-hotel', 'opalhotel-load-rooms-nonce' ); ?>
					<button type="submit" class="opalhotel-button-submit button button-theme button-block"><?php esc_html_e( 'Check Availability', 'opal-hotel-room-booking' ); ?></button>
				</div>
			</div>
		</div>
	</form>

</div>