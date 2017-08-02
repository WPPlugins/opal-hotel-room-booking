<?php
/**
 * The template for displaying room content within loops
 *
 * This template can be overridden by copying it to yourtheme/opalhotel/content-room.php.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

?>
<div id="opalhotel-modal-<?php echo esc_attr( get_the_ID() ) ?>" class="opalhotel-modal" style="display: none">
  	<div class="row">
		<div class="col-xs-12 col-md-6 col-sm-6">
			<?php
				// print galler
				do_action( 'opalhotel_template_single_gallery' );

				remove_action( 'opalhotel_print_room_hotels', 'opalhotel_print_room_hotels', 5 );
				remove_action( 'opalhotel_print_room_packages_discounts', 'opalhotel_print_room_packages_discounts', 5 );
				// pricing plan
				do_action( 'opalhotel_single_room_pricing_plan' );

				add_action( 'opalhotel_print_room_hotels', 'opalhotel_print_room_hotels', 5 );
				add_action( 'opalhotel_print_room_packages_discounts', 'opalhotel_print_room_packages_discounts', 5 );
			?>
		</div>
		<div class="col-xs-12 col-md-6 col-sm-6">
			<?php
				// print package and discounts
				do_action( 'opalhotel_print_room_packages_discounts' );
			?>
		</div>
	</div>
</div>
