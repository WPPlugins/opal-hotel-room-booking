<?php
/**
 * The template for displaying hotel content within loops
 *
 * This template can be overridden by copying it to yourtheme/opalhotel/single-hotel.php.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

get_header(); ?>

	<div class="opalhotel-wrapper">

		<div class="opalhotel-main-wrapper">

			<?php
				/**
				 * opalhotel_before_main_hotel_content hook.
				 * 
				 */
				do_action( 'opalhotel_before_main_hotel_content' );
			?>

			<div class="grid-row">

				<div class="grid-column-8 pull-left">

					<?php while ( have_posts() ) : the_post(); ?>

						<?php opalhotel_get_template_part( 'content', 'single-hotel' ); ?>

					<?php endwhile; // end of the loop. ?>

				</div>

				<div class="grid-column-4 pull-left">
					<?php

						/**
						 * opalhotel_sidebar hook.
						 *
						 * @hooked opalhotel_get_sidebar - 10
						 */
						do_action( 'opalhotel_sidebar' );
					?>
				</div>

			</div>

			<?php
				/**
				 * opalhotel_after_main_hotel_content hook.
				 *
				 */
				do_action( 'opalhotel_after_main_hotel_content' );
			?>

		</div>
	</div>

<?php get_footer(); ?>