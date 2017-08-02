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

get_header(); ?>

	<div class="opalhotel-wrapper opalhotel-hotels">

		<div class="opalhotel-main-wrapper">

			<?php

				$args = array(
						'post_type'			=> OPALHOTEL_CPT_HOTEL,
						'post_status'		=> 'publish',
						'posts_per_page'	=> get_option( 'posts_per_page' ),
						'paged'				=> max( get_query_var( 'paged' ), 1 )
					);

				$taxonomy = get_query_var( 'taxonomy' );
				$term = get_query_var( 'term' );
				if ( $taxonomy ) {
					$tax_query = array();
					$tax_query[] = array(
							'taxonomy'	=> $taxonomy,
							'field'		=> 'slug',
							'terms'		=> array( $term ),
							'operator'  => 'IN'
						);
					$args['tax_query'] = $tax_query;
				}

				$wp_query = new WP_Query( $args );
				/**
				 * opalhotel_before_main_content hook.
				 *
				 * @hooked opalhotel_output_content_wrapper - 10 (outputs opening divs for the content)
				 * @hooked opalhotel_breadcrumb - 20
				 */
				do_action( 'opalhotel_before_main_content', $wp_query );
			?>

				<?php
					/**
					 * opalhotel_archive_description hook.
					 *
					 */
					do_action( 'opalhotel_archive_description', $wp_query );
				?>

				<?php if ( $wp_query->have_posts() ) : ?>

					<?php
						/**
						 * opalhotel_before_hotel_loop hook.
						 *
						 * @hooked opalhotel_loop_sortable - 9
						 * @hooked opalhotel_display_modes - 10
						 */
						do_action( 'opalhotel_before_hotel_loop', $wp_query );
					?>

					<div class="opalhotel-main hotels <?php echo apply_filters( 'opalhotel_loop_grid_column_class', 'grid-row');?>">
						<?php while ( $wp_query->have_posts() ) : $wp_query->the_post(); ?>
							<?php opalhotel_get_template_part( 'content', 'hotel' ); ?>
						<?php endwhile; // end of the loop. ?>
					</div>

					<?php
						/**
						 * opalhotel_after_hotel_loop hook.
						 *
						 * @hooked opalhotel_archive_print_postcount - 4
						 * @hooked opalhotel_archive_pagination - 5
						 */
						do_action( 'opalhotel_after_hotel_loop', $wp_query );
					?>

					<?php wp_reset_postdata(); ?>

				<?php else : ?>

					<?php opalhotel_get_template( 'loop/no-hotel-found.php' ); ?>

				<?php endif; ?>

				<?php
					/**
					 * opalhotel_after_main_content hook.
					 *
					 * @hooked opalhotel_output_content_wrapper_end - 10 (outputs closing divs for the content)
					 */
					do_action( 'opalhotel_after_main_content', $wp_query );
				?>
		</div>

	</div>
<?php //get_sidebar(); ?>

<?php get_footer(); ?>