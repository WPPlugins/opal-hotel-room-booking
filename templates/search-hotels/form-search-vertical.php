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

$location = isset( $_REQUEST['location'] ) ? sanitize_text_field( $_REQUEST['location'] ) : '';
$arrival = isset( $_REQUEST['arrival_datetime'] ) ? sanitize_text_field( $_REQUEST['arrival_datetime'] ) : date( 'Y-m-d', current_time( 'timestamp' ) );
$departure = isset( $_REQUEST['departure_datetime'] ) ? sanitize_text_field( $_REQUEST['departure_datetime'] ) : date( 'Y-m-d', current_time( 'timestamp' ) + DAY_IN_SECONDS );

$adult = isset( $_REQUEST['adult'] ) ? absint( $_REQUEST['adult'] ) : 0;
$child = isset( $_REQUEST['child'] ) ? absint( $_REQUEST['child'] ) : 0;
$number_of_rooms = isset( $_REQUEST['number_of_rooms'] ) ? absint( $_REQUEST['number_of_rooms'] ) : 0;

$room_type = ! isset( $room_type ) && isset( $_REQUEST['room_type'] ) ? $_REQUEST['room_type'] : 0;
$room_type_selected = isset( $_REQUEST['room_type'] ) ? sanitize_text_field( $_REQUEST['room_type'] ) : '';

$current_page_id = isset( $_REQUEST['current_page_id'] ) ? absint( $_REQUEST['current_page_id'] ) : ( ( $post && $post->ID ) ? $post->ID : 0 );

?>

<div class="opalhotel_form_section form-hotel-availabel vertical">

	<form action="<?php echo esc_attr( opalhotel_get_hotel_available_url() ) ?>" method="GET" name="opalhotel_check_hotel_available" class="opalhotel_check_hotel_available opalhotel_datepick_wrap">

		<!-- Search Text Field -->
		<div class="opalhotel-form-field">
			<label class="opalhotel-form-lable"><?php esc_html_e( 'Location', 'opal-hotel-room-booking' ); ?></label>
			<div class="opalhotel-form-field-group">
				<input name="location" placeholder="<?php esc_attr_e( 'Enter your keyword', 'opal-hotel-room-booking' ); ?>" value="<?php echo esc_attr( $location ) ?>"/>
				<?php if ( isset( $_REQUEST['hotel_id'] ) && $_REQUEST['hotel_id'] ) : ?>
					<input type="hidden" name="hotel_id" value="<?php echo esc_attr( $_REQUEST['hotel_id'] ) ?>" />
				<?php elseif( isset( $_REQUEST['room_id'] ) && $_REQUEST['room_id'] ) : ?>
					<input type="hidden" name="room_id" value="<?php echo esc_attr( $_REQUEST['room_id'] ) ?>" />
				<?php endif; ?>
			</div>
		</div>
		<!-- end Search Text Field -->

		<!-- arrival date -->
		<div class="opalhotel-form-field">
			<label class="opalhotel-form-lable"><?php esc_html_e( 'Check in', 'opal-hotel-room-booking' ); ?></label>
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
		<div class="opalhotel-form-field">
			<label class="opalhotel-form-lable"><?php esc_html_e( 'Check out', 'opal-hotel-room-booking' ); ?></label>
			<div class="opalhotel-form-field-group">
				<i class="fa fa-calendar-o" aria-hidden="true"></i>
				<input class="opalhotel-departure-date opalhotel-has-datepicker" name="departure" placeholder="<?php esc_attr_e( 'Departure Date', 'opal-hotel-room-booking' ); ?>" data-start="opalhotel_arrival_date" value="<?php echo esc_attr( opalhotel_format_date( strtotime( $departure ) ) ) ?>"/>
				<?php if ( isset( $departure ) && $departure ) : ?>
					<input type="hidden" name="departure_datetime" value="<?php echo esc_attr( $departure ) ?>" />
				<?php endif; ?>
			</div>
		</div>
		<!-- end departure date -->

		<!-- max adult -->
		<div class="opalhotel-form-field half">
			<label class="opalhotel-form-lable"><?php esc_html_e( 'Adults', 'opal-hotel-room-booking' ); ?></label>
			<?php $max_adult = opalhotel_get_max_adults(); ?>
			<div class="opalhotel-form-field-group">
				<?php
					printf( '%s', opalhotel_select_number( array(
						'name'	=> 'adult',
						'class'	=> 'adult',
						'min'	=> 1,
						'max'	=> $max_adult,
						'class'	=> 'opalhotel_adult',
						'selected'	=> $adult
					) ) );
				?>
			</div>
		</div>
		<!-- end max adult -->

		<!-- rooms number -->
		<div class="opalhotel-form-field half">
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
		<!-- rooms number -->

		<footer>
			<?php wp_nonce_field( 'opalhotel-search-hotel', 'nonce' ); ?>
			<input type="hidden" name="action" value="opalhotel_search_hotel" />
			<input type="hidden" name="current_page_id" value="<?php echo esc_attr( $current_page_id ) ?>" />
			<button type="submit" class="opalhotel-button-submit button button-theme">
				<i class="fa fa-search" aria-hidden="true"></i>
				<?php esc_html_e( 'Check Availability', 'opal-hotel-room-booking' ); ?>
			</button>
		</footer>

	</form>

</div>