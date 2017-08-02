<?php
/**
 * The template for displaying room content within loops
 *
 * This template can be overridden by copying it to yourtheme/opalhotel/archive-room.php.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

get_header(); ?>

	<div class="opalhotel-wrapper opalhotel-rooms">

		<?php
			/**
			 * opalhotel_before_main_content hook.
			 *
			 * @hooked opalhotel_output_content_wrapper - 10 (outputs opening divs for the content)
			 * @hooked opalhotel_breadcrumb - 20
			 */
			do_action( 'opalhotel_before_main_content' );
		?>

		<div class="opalhotel-main-wrapper">

			<?php
				/**
				 * opalhotel_archive_description hook.
				 *
				 */
				do_action( 'opalhotel_archive_description' );
			?>

			<?php if ( have_posts() ) : ?>

				<?php
					/**
					 * opalhotel_before_room_loop hook.
					 *
					 * @hooked opalhotel_result_count - 20
					 * @hooked opalhotel_catalog_ordering - 30
					 */
					do_action( 'opalhotel_before_room_loop' );
				?>

				<div class="opalhotel-main rooms <?php echo apply_filters( 'opalhote_loop_wrap_class', 'grid-row');?>">
					<?php while ( have_posts() ) : the_post(); ?>
						<?php opalhotel_get_template_part( 'content-room', apply_filters('opalhotel_room_display_mode','') ); ?>
					<?php endwhile; // end of the loop. ?>
				</div>

				<?php
					/**
					 * opalhotel_after_room_loop hook.
					 *
					 * @hooked opalhotel_archive_print_postcount - 4
					 * @hooked opalhotel_archive_pagination - 5
					 */
					do_action( 'opalhotel_after_room_loop' );
				?>

				<?php wp_reset_postdata(); ?>

			<?php else : ?>

				<?php opalhotel_get_template( 'loop/no-room-found.php' ); ?>

			<?php endif; ?>
		</div>

		<?php
			/**
			 * opalhotel_after_main_content hook.
			 *
			 * @hooked opalhotel_output_content_wrapper_end - 10 (outputs closing divs for the content)
			 */
			do_action( 'opalhotel_after_main_content' );
		?>

	</div>
<?php //get_sidebar(); ?>

<?php get_footer(); ?>