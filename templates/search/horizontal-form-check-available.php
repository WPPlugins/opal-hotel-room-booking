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

$current_page_id = isset( $_REQUEST['current_page_id'] ) ? absint( $_REQUEST['current_page_id'] ) : ( ( $post && $post->ID ) ? $post->ID : 0 );

?>

<div class="opalhotel_form_section">

	<form action="<?php echo esc_attr( opalhotel_get_reservation_url() ) ?>" method="GET" name="opalhotel_check_availability" class="opalhotel_check_availability opalhotel_datepick_wrap">
		<div class="horizontal-form clearfix">
			<header class="heading-form">
				<h3><?php esc_html_e( 'Book', 'opal-hotel-room-booking' ) ?> <span><?php esc_html_e( 'A Room', 'opal-hotel-room-booking' ) ?></span></h3>
			</header>
			<div class="form-content">
				<!-- before hook -->
				<?php do_action( 'opalhotel_before_reservation_form' ) ?>
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
								'placeholder'	=> false,
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
									'placeholder'	=> false,
									// 'none'	=> __( 'Children', 'opal-hotel-room-booking' ),
									'class'	=> 'opalhotel_children',
									'selected'	=> $child
								) ) );
							?>
						</div>
					</div>
					<!-- end max child -->

				<?php endif; ?>

				<?php if ( opalhotel_get_option( 'search_enable_room_type', 1 ) ) : ?>

					<div class="opalhotel-form-field room-type-input">
						<label class="opalhotel-form-lable"><?php esc_html_e( 'Room Type', 'opal-hotel-room-booking' ); ?></label>
						<div class="opalhotel-form-field-group">
							<?php
								printf( '%s', opalhotel_select_room_types(array(
										'name'			=> 'room_type',
										'placeholder'	=> __( 'Select Room Type', 'opal-hotel-room-booking' ),
										'selected'		=> $room_type_selected
									)) );
							?>
						</div>
					</div>

				<?php endif; ?>

				<!-- before hook -->
				<?php do_action( 'opalhotel_after_reservation_form' ) ?>
				<footer class="opalhotel-form-field button-wrap">
					<input type="hidden" name="step" value="2" />
					<input type="hidden" name="current_page_id" value="<?php echo esc_attr( $current_page_id ) ?>" />
					<button type="submit" class="opalhotel-button-submit button button-theme button-block"><?php esc_html_e( 'Check Availability', 'opal-hotel-room-booking' ); ?></button>
				</footer>
			</div>
		</div>
	</form>

</div>