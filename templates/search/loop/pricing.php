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

$arrival = ! empty( $_REQUEST['arrival_datetime'] ) ? sanitize_text_field( $_REQUEST['arrival_datetime'] ) : date( 'Y-m-d' );
$departure = ! empty( $_REQUEST['departure_datetime'] ) ? sanitize_text_field( $_REQUEST['departure_datetime'] ) : date( 'Y-m-d', current_time( 'timestamp' ) + DAY_IN_SECONDS );

$adult = isset( $_REQUEST['adult'] ) ? absint( $_REQUEST['adult'] ) : 1;
$child = isset( $_REQUEST['child'] ) ? absint( $_REQUEST['child'] ) : 0;
$room = opalhotel_get_room( get_the_ID() );

$qty = ! empty( $_REQUEST['number_of_rooms'] ) ? absint( $_REQUEST['number_of_rooms'] ) : 1 ;
$night = opalhotel_count_nights( strtotime( $arrival ), strtotime( $departure ) );
$base_price = $room->base_price() * $night;
?>
<a href="#opalhotel-modal-pricing-<?php echo esc_attr( $room->id ); ?>" class="opalhotel-view-price opalhotel-fancybox"><?php esc_html_e( 'Price Details', 'opal-hotel-room-booking' ); ?></a>
<div class="opalhotel-modal-pricing" style="display:none" id="opalhotel-modal-pricing-<?php echo esc_attr( $room->id ); ?>">

	<div class="opalhotel-pricing-details">

		<div class="opalhotel-pricing-content">
			<div class="opalhtel-price-day">
				<div class="content">
					<?php if ( $prices = $room->get_pricing( $arrival, $departure ) ) : ?>
						<ul>
							<?php foreach ( $prices as $timestamp => $price ) : ?>
								<li>
									<?php printf( '%s -', opalhotel_format_date( $timestamp ) ); ?>
									<span><?php printf( '%s', opalhotel_format_price( $price ) ) ?></span>
								</li>
							<?php endforeach; ?>
							<?php $price = $room->get_price( array( 'arrival' => $arrival, 'departure' => $departure, 'adult' => $adult, 'child' => $child, 'qty' => $qty ) ); ?>
							<?php if ( $price < $base_price ) : ?>
								<li>
									<?php printf( '%s - <span>%s</span>', __( 'Discount', 'opal-hotel-room-booking' ), opalhotel_format_price( $base_price - $price ) ) ?>
								</li>
							<?php endif; ?>
						</ul>
					<?php endif; ?>
				</div>
			</div>
			<?php $prices = $room->get_extras_details(); if ( $prices && ( $room->need_calculate_adult_price( $adult ) || $room->need_calculate_child_price( $child ) ) ) : ?>
				<div class="opalhtel-price-day">
					<h5><?php esc_html_e( 'Extra Price', 'opal-hotel-room-booking' ); ?></h5>
					<div class="content">
						<ul>
							<?php if ( ! empty( $prices['adult'] ) ) : ?>
								<?php foreach ( $prices['adult'] as $adult => $price ) : ?>
									<li>
										<?php printf( translate_nooped_plural( _n_noop( '%1$s adult + <span>%2$s/room</span>', '%1$s adults + <span>%2$s/room</span>' ), 'opal-hotel-room-booking' ), $adult, opalhotel_format_price( $price ) ); ?>
									</li>
								<?php endforeach; ?>
							<?php endif; ?>
							<?php if ( ! empty( $prices['child'] ) ) : ?>
								<?php foreach ( $prices['child'] as $child => $price ) : ?>
									<li>
										<?php printf( translate_nooped_plural( _n_noop( '%1$s child + <span>%2$s</span>', '%1$s child + <span>%2$s/room</span>' ), 'opal-hotel-room-booking' ), $child, opalhotel_format_price( $price ) ); ?>
									</li>
								<?php endforeach; ?>
							<?php endif; ?>
						</ul>
					</div>
				</div>
			<?php endif; ?>

			<?php if ( $discount = $room->get_discounts_details() ) : ?>
				<div class="opalhtel-price-day">
					<h5><?php esc_html_e( 'Discount Price', 'opal-hotel-room-booking' ); ?></h5>
					<div class="content">
						<ul>
							<li>
								<?php if ( $discount['type'] === 'before' ) : ?>
									<?php printf( __( 'Book before %s', 'opal-hotel-room-booking' ), opalhotel_days_display( $discount['unit'] ), $discount['amount'] ) ?>
								<?php elseif ( $discount['type'] === 'live' ) : ?>
									<?php printf( __( 'Stay %s', 'opal-hotel-room-booking' ), opalhotel_days_display( $discount['unit'] ) ) ?>
								<?php endif; ?>

								<?php if ( $discount['sale_type'] === 'subtract' ) : ?>
									<?php printf( __( ' - %s/room', 'opal-hotel-room-booking' ), opalhotel_format_price( $discount['amount'] ) ) ?>
								<?php else : ?>
									<?php printf( __( ' - %s%s/room', 'opal-hotel-room-booking' ), $discount['amount'], '%' ) ?>
								<?php endif; ?>
							</li>
						</ul>
					</div>
				</div>
			<?php endif; ?>

		</div>
	</div>
	<div class="opalhotel-modal-close">
		<i class="fa fa-close"></i>
	</div>

</div>
<a href="#room-packages-<?php echo esc_attr( $room->id ); ?>" class="opalhotel-room-toggle-packages opalhotel-button btn btn-default btn-block">
	<span><?php esc_html_e( 'Show Rates', 'opal-hotel-room-booking' ); ?></span>
	<i class="fa"></i>
</a>