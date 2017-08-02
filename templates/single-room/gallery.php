<?php
/**
 * The template for displaying room content within single
 *
 * This template can be overridden by copying it to yourtheme/opalhotel/single-room/gallery.php.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

global $opalhotel_room;
if ( ! $opalhotel_room ) {
	$opalhotel_room = opalhotel_get_room( get_the_ID() );
}
?>

<?php if ( $opalhotel_room->gallery ) : ?>

	<div class="opalhotel-rom-gallery">

		<div class="opalhotel-room-single-gallery owl-carousel">
			<?php foreach ( $opalhotel_room->gallery as $gallery ) : ?>
				<div class="opalhotel-room-single-gallery-item">
					<div class="gallery-item"><?php echo $opalhotel_room->get_gallery_image_item( $gallery ); ?></div>
				</div>
			<?php endforeach; ?>
		</div>

		<a class="opalhotel-left carousel-control carousel-md" title="<?php esc_html_e( 'Previous', 'opal-hotel-room-booking' ); ?>" data-slide="prev" href="#">
			<span class="fa fa-angle-left"></span>
		</a>
		<a class="opalhotel-right carousel-control carousel-md" title="<?php esc_html_e( 'Next', 'opal-hotel-room-booking' ); ?>" data-slide="next" href="#">
			<span class="fa fa-angle-right"></span>
		</a>

		<div class="opalhotel-room-single-gallery-thumb owl-carousel">
			<?php foreach ( $opalhotel_room->gallery as $gallery ) : ?>
				<div class="opalhotel-room-single-thumb-item">
					<a href="#"><?php echo $opalhotel_room->get_gallery_thumb_item( $gallery ); ?></a>
				</div>
			<?php endforeach; ?>
		</div>

	</div>
<?php else : ?>
	<div class="preview"><?php the_post_thumbnail( 'full' ); ?></div>
<?php endif; ?>