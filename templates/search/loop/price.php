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
$qty = ! empty( $_REQUEST['number_of_rooms'] ) ? absint( $_REQUEST['number_of_rooms'] ) : 1 ;

$adult = isset( $_REQUEST['adult'] ) ? absint( $_REQUEST['adult'] ) : 1;
$child = isset( $_REQUEST['child'] ) ? absint( $_REQUEST['child'] ) : 0;
$night = opalhotel_count_nights( strtotime( $arrival ), strtotime( $departure ) );

$room = opalhotel_get_room( get_the_ID() );
$base_price = $room->base_price() * $night * $qty;
$price = $room->get_price( array( 'arrival' => $arrival, 'departure' => $departure, 'adult' => $adult, 'child' => $child, 'qty' => $qty ) );

?>

<div class="inner">
	<div class="opalhotel-room-price">

		<!-- has sale -->
		<?php if ( $base_price > $price ) : ?>
			<del>
				<span class="price-value base-price" data-price="<?php echo esc_attr( $base_price / $qty ) ?>">
				<?php printf( __( '%s', 'opal-hotel-room-booking' ), opalhotel_format_price( $base_price ) ) ?>
				</span>
			</del>
		<?php endif; ?>

		<ins>
			<span class="price-value" data-price="<?php echo esc_attr( $price / $qty ) ?>">
				<?php printf( __( '%s', 'opal-hotel-room-booking' ), opalhotel_format_price( $room->get_price_display( $price ) ) ) ?>
			</span>
		</ins>
		<span class="price-title"><?php esc_html_e( 'From', 'opal-hotel-room-booking' ); ?></span> / <span class="price-unit"><?php echo ( $night == 1 ) ? esc_html( ' per night', 'opal-hotel-room-booking' ) : sprintf( '%d %s', $night, esc_html( 'nights', 'opal-hotel-room-booking' ) ) ?></span>
	</div>
</div>
