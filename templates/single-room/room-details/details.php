<?php
/**
 * The template for displaying room content within single
 *
 * This template can be overridden by copying it to yourtheme/opalhotel/single-room/room-details/details.php.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}
global $opalhotel_room;

?>
<div class="room-meta-info">
	<h4><?php esc_html_e( 'Amenities', 'opal-hotel-room-booking' ); ?></h4>
	<div class="inner">
		<ul class="opalhotel-room-meta">
			<?php if ( $opalhotel_room->bed ) : ?>
				<li class="meta-bed">
					<span class="meta-label"><?php esc_html_e( 'Bed', 'opal-hotel-room-booking' ); ?></span>
					<span class="meta-text"><?php printf( '%s', $opalhotel_room->bed ); ?></span>
				</li>
			<?php endif; ?>

			<?php if ( $opalhotel_room->adults ) : ?>
				<li class="meta-adults">
					<span class="meta-label"><?php esc_html_e( 'People', 'opal-hotel-room-booking' ); ?></span>
					<span class="meta-text"><?php printf( '%s', $opalhotel_room->adults ); ?></span>
				</li>
			<?php endif; ?>

			<?php if ( $opalhotel_room->room_size ) : ?>
				<li class="meta-size">
					<span class="meta-label"><?php esc_html_e( 'Room Size', 'opal-hotel-room-booking' ); ?></span>
					<span class="meta-text"><?php printf( '%s', $opalhotel_room->room_size ); ?></span>
				</li>
			<?php endif; ?>

			<?php if ( $opalhotel_room->view ) : ?>
				<li class="meta-view">
					<span class="meta-label"><?php esc_html_e( 'View', 'opal-hotel-room-booking' ); ?></span>
					<span class="meta-text"><?php printf( '%s', $opalhotel_room->view ); ?></span>
				</li>
			<?php endif; ?>
			<?php
				$amenities = opalhoteL_get_room_amenities();
				foreach( $amenities as $key => $amenity ): 
					if( isset($opalhotel_room->amenities[$key]) && !empty($opalhotel_room->amenities[$key]) ):
					?>
					<li class="meta-<?php echo $key; ?>">
						<span class="meta-label"><?php echo $amenities[$key]['label']; ?></span>
						<span class="meta-text"><?php printf( '%s', $opalhotel_room->amenities[$key] ); ?></span>
					</li>
					<?php endif; ?>
			<?php endforeach; ?>	
		</ul>
	</div>
</div>
<?php 
	$labels   = (array) $opalhotel_room->extra_amenities_label;
	$contents = (array) $opalhotel_room->extra_amenities_content;
?>
<?php if( $labels ): ?>
	<div class="room-extra-amenities ">
		<div class="grid-row">
			<?php foreach($labels as $key => $value ): ?>
				<div class="grid-column grid-column-6"><div class="amenity-item"><i class="fa fa-check text-black"></i> 	<span><?php echo $value; ?></span>: <strong><?php echo (isset($contents[$key])?$contents[$key]:""); ?></strong></div></div>
			<?php endforeach; ?>
		</div>
	</div>
<?php endif; ?>