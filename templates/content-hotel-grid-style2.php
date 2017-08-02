<?php
/**
 * The template for displaying room hotel within loops
 *
 * This template can be overridden by copying it to yourtheme/opalhotel/content-hotel-grid-style2.php.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

$class = opalhotel_get_loop_class();

?>

<div <?php post_class( $class ); ?>>
	<div class="hotel-grid">
		<div class="hotel-top-wrap clearfix">
			<?php
				/**
				 * opalhotel_archive_loop_item_thumbnail hook.
				 * opalhotel_hotel_loop_item_thumbnail - 5
				 */
				do_action( 'opalhotel_hotel_loop_item_thumbnail' );
			?>
		</div>

		<div class="hotel-content">

			<?php

				/**
				 * Print Rating
				 */
				do_action( 'opalhotel_hotel_loop_item_rating' );

			?>

			<div class="hotel-wrap-title">
				<?php

					/**
					 * opalhotel_before_archive_loop_item_title hook.
					 * opalhotel_hotel_loop_item_title - 5
					 */
					do_action( 'opalhotel_hotel_loop_item_title' );
					/**
					 * Print Hotel Address
					 */
					do_action( 'opalhotel_hotel_loop_item_address' );

					/**
					 * Print The Most Cheap price of Room
					 */
					do_action( 'opalhotel_hotel_loop_item_price' );
				?>
			</div>
			<footer>
				<?php

					/**
					 * Print Hotel Include
					 */
					do_action( 'opalhotel_hotel_loop_item_includes' );

					/**
					 * Print Actions
					 */
					do_action( 'opalhotel_hotel_loop_item_actions' );
				?>
			</footer>
		</div>

	</div>
</div>