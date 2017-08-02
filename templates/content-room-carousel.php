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
global $opalhotel_room;
$arrival = isset( $_REQUEST['arrival_datetime'] ) ? sanitize_text_field( $_REQUEST['arrival_datetime'] ) : current_time( 'mysql' );
?>

<div <?php post_class(); ?>>
	<div class="room-carousel-item">
		<div class="opalhotel-thumbnail-wrapper">
			<?php
				/**
				 * opalhotel_archive_loop_item_thumbnail hook.
				 * opalhotel_loop_item_thumbnail - 5
				 */
				do_action( 'opalhotel_archive_loop_item_thumbnail' );

			?>
			<?php if( $opalhotel_room->has_discounts( $arrival ) ) : ?>
				<span class="room-label room-label-discount"><?php esc_html_e( 'Discount', 'opal-hotel-room-booking'); ?></span>
			<?php endif; ?>
		</div>

		<div class="room-content-wrapper clearfix">

			<div class="room-price-wrap pull-left">
				<?php
					/**
					 * opalhotel_before_archive_loop_item_title hook.
					 * opalhotel_loop_item_title - 5
					 */
					do_action( 'opalhotel_archive_loop_item_price' );
				?>
			</div>

			<div class="room-content-inner">
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
					remove_action( 'opalhotel_archive_loop_item_list_description', 'opalhotel_loop_item_description', 5 );
					do_action( 'opalhotel_archive_loop_item_list_description' );
				?>
				<footer>
					<a class="text-primary" href="<?php echo esc_attr( get_the_permalink() ) ?>"><?php esc_html_e( 'Check detail', 'paradise' ); ?> <i class="fa fa-arrow-circle-o-right"></i></a>
				</footer>
			</div>

		</div>

		<?php
			/**
			 * opalhotel_after_archive_loop_item hook.
			 */
			do_action( 'opalhotel_after_archive_loop_item' );
		?>
	</div>
</div>