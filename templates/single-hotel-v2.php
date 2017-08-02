<?php
/**
 * The template for displaying hotel content within loops
 *
 * This template can be overridden by copying it to yourtheme/opalhotel/single-hotel-v2.php.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

get_header(); ?>

	<div class="opalhotel-wrapper">
		<div class="feature-full-screen">
			<?php opalhotel_get_template( 'single-hotel/preview.php' ); ?>
		</div>

		<div class="opalhotel-main-wrapper">
			<div class="grid-row">
				<div class="grid-column-8 pull-left">
					<?php while ( have_posts() ) : the_post(); ?>
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

							/**
							 * tab - rooms
							 */
							opalhotel_get_template_part( 'content', 'single-hotel-tabs' );
						?>
					<?php endwhile; // end of the loop. ?>
				</div>

				<div class="grid-column-4 pull-left">
					<?php get_sidebar(); ?>
				</div>
			</div>
		</div>
	</div>

<?php get_footer(); ?>