<?php
/**
 * The template for displaying hotel content within loops
 *
 * This template can be overridden by copying it to yourtheme/opalhotel/single-hotel-v3.php.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

get_header(); ?>

	<div class="opalhotel-wrapper">

		<div class="opalhotel-main-wrapper">

			<div class="grid-row">

				<?php

					/**
					 * Rating
					 */
					opalhotel_get_template( 'single-hotel/rating.php' );

					/**
					 * Title
					 */
					opalhotel_get_template( 'single-hotel/title.php' );

					/**
					 * Address
					 */
					opalhotel_get_template( 'single-hotel/address.php' );

				?>

				<div class="feature-full-screen">
					<?php
						/**
						 * Print Hotel Information
						 */
						add_action( 'opalhotel_thumbnail_attach_hotel_information', 'opalhotel_print_hotel_information_v2' );

						/**
						 * Hide Gallery
						 */
						add_filter( 'opalhotel_gallery_display_preview', 'opalhotel_preview_hide_gallery' );

						opalhotel_get_template( 'single-hotel/preview.php' );

						/**
						 * Hide Gallery
						 */
						remove_filter( 'opalhotel_gallery_display_preview', 'opalhotel_preview_hide_gallery' );

						/**
						 * Print Hotel Information
						 */
						remove_action( 'opalhotel_thumbnail_attach_hotel_information', 'opalhotel_print_hotel_information_v2' );
					?>
				</div>

				<div class="grid-column-8 pull-left">
					<?php while ( have_posts() ) : the_post(); ?>
						<?php opalhotel_get_template_part( 'content', 'single-hotel-tabs' ); ?>
					<?php endwhile; // end of the loop. ?>
				</div>

				<div class="grid-column-4 pull-left">
					<?php get_sidebar(); ?>
				</div>
			</div>

		</div>
	</div>

<?php get_footer(); ?>