<?php
/**
 * The template for displaying room content within single
 *
 * This template can be overridden by copying it to yourtheme/opalhotel/single-room/room-details.php.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

do_action( 'opalhotel_before_single_room_details' );
?>
<div class="room-content">
	<?php

	
		/**
		 * opalhotel_single_room_attribute - 5
		 * opalhotel_single_room_description - 10
		 * opalhotel_single_room_pricing_plan - 15
		 *
		 **/
		do_action( 'opalhotel_content_single_room_details' );
	?>
</div>
<?php do_action( 'opalhotel_after_single_room_details' ); ?>