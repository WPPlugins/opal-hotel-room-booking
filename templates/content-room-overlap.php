<?php
/**
 * The template for displaying room content within loops
 *
 * This template can be overridden by copying it to yourtheme/opalhotel/content-room-overlap.php.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}
global $page_link;
global $opalhotel_room;
$arrival = isset( $_REQUEST['arrival_datetime'] ) ? sanitize_text_field( $_REQUEST['arrival_datetime'] ) : current_time( 'mysql' );
?>

<div <?php post_class(); ?>>
	<div class="room-overlap zoom-2">
		<?php if($opalhotel_room->has_discounts( $arrival ) ) : ?>
			<span class="room-label room-label-discount"><?php esc_html_e( 'Discount', 'opal-hotel-room-booking'); ?></span>
		<?php endif; ?>
		<?php
			/**
			 * opalhotel_archive_loop_item_thumbnail hook.
			 * opalhotel_loop_item_thumbnail - 5
			 */
			do_action( 'opalhotel_archive_loop_item_thumbnail' );

		?>
		<div class="room-content-wrapper">

			<?php
				/**
				 * opalhotel_before_archive_loop_item_title hook.
				 * opalhotel_loop_item_title - 5
				 */
				do_action( 'opalhotel_archive_loop_item_title' );
			?>

			<?php

				/**
				 * opalhotel_archive_loop_item_title hook.
				 *
				 * @hooked opalhotel_loop_item_description - 5
				 */
				do_action( 'opalhotel_archive_loop_item_list_description' );
			?>
		</div>

		<?php
			/**
			 * opalhotel_before_archive_loop_item_title hook.
			 * opalhotel_loop_item_title - 5
			 */
			do_action( 'opalhotel_archive_loop_item_price' );
		?>

		<!-- check room -->
			<?php if ( isset($page_link) && !empty($page_link) ): ?>
			<a class ="link-reservation button button-theme" href="<?php echo esc_url( get_permalink( get_page_by_path( $page_link ) ) ); ?>" title="<?php esc_html_e('Reservation page', 'opal-hotel-room-booking'); ?>">
				<?php esc_html_e( 'Check availability', 'opal-hotel-room-booking'); ?>
			</a>
			<?php endif; ?>

		<?php
			/**
			 * opalhotel_after_archive_loop_item hook.
			 */
			do_action( 'opalhotel_after_archive_loop_item' );
		?>
	</div>
</div>