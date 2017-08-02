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
$class = opalhotel_get_loop_class();
$arrival = isset( $_REQUEST['arrival_datetime'] ) ? sanitize_text_field( $_REQUEST['arrival_datetime'] ) : current_time( 'mysql' );
$has_discount = $opalhotel_room->has_discounts( $arrival );

?>

<div <?php post_class( $class ); ?>>
	<div class="room-grid">
		<div class="room-top-wrap">
			<?php if( $has_discount ) : ?>
				<span class="room-label room-label-discount"><?php esc_html_e( 'Discount', 'opal-hotel-room-booking'); ?></span>
			<?php endif; ?>
			<?php
				/**
				 * opalhotel_archive_loop_item_thumbnail hook.
				 * opalhotel_loop_item_thumbnail - 5
				 */
				do_action( 'opalhotel_archive_loop_item_thumbnail' );
			?>

			<a href="javascript:void(0)" id="<?php echo esc_attr( uniqid() ) ?>" class="opalhotel-modal-button" data-id="opalhotel-modal-<?php echo esc_attr( get_the_ID() ) ?>" data-title="<?php echo esc_attr( get_the_title() ) ?>"><i class="fa fa-search" aria-hidden="true"></i></a>
		</div>
		<div class="room-main-wrap">
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
				do_action( 'opalhotel_archive_loop_item_detail' );
			?>
		</div>
		<footer>
			<?php

				/**
				 * opalhotel_archive_loop_room_footer - hook
				 *
				 * opalhotel_loop_room_rating - 5
				 * opalhotel_loop_room_price - 6
				 */
				do_action( 'opalhotel_archive_loop_room_footer' );
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