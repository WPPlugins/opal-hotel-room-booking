<?php
/**
 * The template for displaying room content within loops
 *
 * This template can be overridden by copying it to yourtheme/opalhotel/content-room-form.php.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

$room = opalhotel_get_room( get_the_ID() );
$arrival = isset( $_REQUEST['arrival_datetime'] ) ? sanitize_text_field( $_REQUEST['arrival_datetime'] ) : current_time( 'mysql' );

?>

<li <?php post_class( 'opalhotel-available-item opalhotel-room opalhotel_room clearfix' ); ?>>
	<form method="POST" name="opalhotel-available-form" class="opalhotel-available-form">
		<div class="inner-top">
			<div class="room-info">
				<?php
					/**
					 * opalhotel_archive_loop_item_thumbnail hook.
					 * opalhotel_loop_item_thumbnail - 5
					 */
					do_action( 'opalhotel_archive_loop_item_thumbnail' );
				?>
				<div class="room-content">
					<?php
						/**
						 * opalhotel_archive_loop_item_title hook.
						 * opalhotel_loop_item_title - 5
						 */
						do_action( 'opalhotel_archive_loop_item_title' );

						/**
						 * opalhotel_loop_item_details hook
						 * opalhotel_loop_item_details - 5
						 */
						do_action( 'opalhotel_loop_item_details' );

						/**
						 * opalhotel_archive_loop_item_list_description hook
						 *
						 * opalhotel_archive_loop_item_list_description - 5
						 */
						do_action( 'opalhotel_archive_loop_item_list_description' );
					?>
					<a href="javascript:void(0)" id="<?php echo esc_attr( uniqid() ) ?>" class="opalhotel-modal-button" data-id="opalhotel-modal-<?php echo esc_attr( get_the_ID() ) ?>" data-title="<?php echo esc_attr( get_the_title() ) ?>"><?php esc_html_e( 'View More', 'opal-hotel-room-booking' ); ?></a>
				</div>

				<?php
					do_action( 'opalhotel_after_available_room_info' );
				?>
			</div>
			<div class="room-actions">
				<?php if( $room->has_discounts( $arrival ) ) : ?>
					<span class="room-label room-label-discount"><?php esc_html_e( 'Discount', 'opal-hotel-room-booking'); ?></span>
				<?php endif; ?>

				<div class="inner">
					<?php
						/**
						 * opalhotel_room_available_actions hook
						 * 
						 * opalhotel_loop_item_room_available_price - 5
						 * opalhotel_loop_item_room_available_pricing - 6
						 */
						do_action( 'opalhotel_room_available_actions' );
					?>
				</div>
			</div>

			<?php
				/**
				 * opalhotel_after_archive_loop_item hook.
				 */
				do_action( 'opalhotel_after_archive_loop_item' );
			?>
		</div>

		<?php
			/**
			 * opalhotel_room_available_after hook
			 * 
			 * opalhotel_room_available_after - 5
			 */
			do_action( 'opalhotel_room_available_after' );
		?>
	</form>
</li>