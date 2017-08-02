<?php
/**
 * The template for displaying room content within single
 *
 * This template can be overridden by copying it to yourtheme/opalhotel/single-room/room-details/pricing-plans.php.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

global $opalhotel_room;
if ( ! $opalhotel_room ) {
	$opalhotel_room = opalhotel_get_room( get_the_ID() );
}

global $wp_locale;

	// print hotels of room
	do_action( 'opalhotel_print_room_hotels' );

	// print package and discounts
	do_action( 'opalhotel_print_room_packages_discounts' );
?>

<div class="room-box">

	<?php $pricing_plans = opalhotel_get_current_week_pricing( get_the_ID() ); ?>
	<?php if ( $pricing_plans ) : ?>
		<h4><?php esc_html_e( 'Pricing Plans', 'opal-hotel-room-booking' ); ?></h4>
		<div class="grid-row">
		<div class="room-pricing-plans grid-column grid-column-6">
				<h5><?php esc_html_e( 'This Week' , 'opal-hotel-room-booking' ); ?></h5>
				<?php foreach ( $pricing_plans as $day => $price ) : ?>
					<?php $day = date_i18n( 'l', $day ); ?>

					<div class="pricing-day">
						<span class="day_name meta-label"><?php printf( '%s', $wp_locale->weekday_abbrev[$day] ); ?></span>	
						<span class="day_price meta-text pull-right"><?php printf( '%s', opalhotel_format_price( $price ) ); ?></span>
					</div>
				<?php endforeach; ?>
		</div>
	<?php endif; ?>
	<?php $pricing_plans = opalhotel_get_next_week_pricing( get_the_ID() );?>
	<?php if ( $pricing_plans ) : ?>

		<div class="room-pricing-plans grid-column grid-column-6">
				<h5><?php esc_html_e( 'Next Week' , 'opal-hotel-room-booking' ); ?></h5>
				<?php foreach ( $pricing_plans as $day => $price ) : ?>
					<?php $day = date_i18n( 'l', $day ); ?>

					<div class="pricing-day">
						<span class="day_name meta-label"><?php printf( '%s', $wp_locale->weekday_abbrev[$day] ); ?></span>	
						<span class="day_price meta-text pull-right"><?php printf( '%s', opalhotel_format_price( $price ) ); ?></span>
					</div>
				<?php endforeach; ?>
		</div>
	<?php endif; ?>
	</div>
</div>