<?php
/**
 * The template for displaying hotel content within single
 *
 * This template can be overridden by copying it to yourtheme/opalhotel/single-hotel/rooms.php.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

$hotel = opalhotel_get_hotel( get_the_ID() );

$rooms = $hotel->get_rooms_available( array(
		'posts_per_page'	=> -1,
	) );

?>

<div class="opalhotel-rooms-available">

	<?php
		/**
		 * Get Search Form
		 */
		opalhotel_get_template( 'single-hotel/form-check-room-available.php' );

	?>
	<div class="hotel-box rooms-available">

		<?php if ( $rooms->have_posts() ) : ?>

			<?php
				remove_action( 'opalhotel_archive_loop_item_list_description', 'opalhotel_loop_item_description', 5 );
				remove_action( 'opalhotel_room_available_actions', 'opalhotel_loop_item_room_available_pricing', 6 );
				remove_action( 'opalhotel_room_available_after', 'opalhotel_room_available_packages', 5 );
				add_action( 'opalhotel_after_available_room_info', 'opalhotel_room_available_optional', 5 );
				add_action( 'opalhotel_room_available_actions', 'opalhotel_loop_item_room_available_button', 99 );
			?>

				<h3 class="title"><?php _e( 'Rooms', 'opal-hotel-room-booking' ); ?></h3>
				<div class="content">
					<ul class="opalhotel-search-results opalhotel_main rooms">
						<?php while ( $rooms->have_posts() ) : $rooms->the_post(); ?>
							<?php opalhotel_get_template_part( 'content-room', 'form' ); ?>
						<?php endwhile; ?>
					</ul>
				</div>

			<?php
				add_action( 'opalhotel_archive_loop_item_list_description', 'opalhotel_loop_item_description', 5 );
				add_action( 'opalhotel_room_available_actions', 'opalhotel_loop_item_room_available_pricing', 6 );
				add_action( 'opalhotel_room_available_after', 'opalhotel_room_available_packages', 5 );
				remove_action( 'opalhotel_after_available_room_info', 'opalhotel_room_available_optional', 5 );
				remove_action( 'opalhotel_room_available_actions', 'opalhotel_loop_item_room_available_button', 99 );
			?>

		<?php wp_reset_postdata(); else: ?>

			<div class="content">
				<ul class="opalhotel-search-results opalhotel_main rooms">
					<li>
						<?php opalhotel_print_notice_message( __( 'No rooms available found.', 'opal-hotel-room-booking' ) ); ?>
					</li>
				</ul>
			</div>

		<?php endif; ?>

	</div>

</div>