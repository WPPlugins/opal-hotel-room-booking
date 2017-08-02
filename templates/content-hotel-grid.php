<?php
/**
 * The template for displaying room hotel within loops
 *
 * This template can be overridden by copying it to yourtheme/opalhotel/content-hotel.php.
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

				/**
				 * Print Rating
				 */
				do_action( 'opalhotel_hotel_loop_item_rating' );

				/**
				 * Print Actions
				 */
				do_action( 'opalhotel_hotel_loop_item_actions' );
			?>
		</div>

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
			 * Print Hotel Include
			 */
			do_action( 'opalhotel_hotel_loop_item_includes' );
		?>
		<footer>
			<?php

				/**
				 * Print The Most Cheap price of Room
				 */
				do_action( 'opalhotel_hotel_loop_item_price' );

				/**
				 * Print Book Now button hook
				 */
				do_action( 'opalhotel_hotel_loop_item_book_button' );
			?>
		</footer>

		<?php
			/**
			 * opalhotel_after_archive_loop_item hook.
			 */
			do_action( 'opalhotel_after_archive_loop_item' );
		?>
	</div>
</div>