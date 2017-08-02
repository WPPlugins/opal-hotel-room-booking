<?php
/**
 * The template for displaying hotel content within single
 *
 * This template can be overridden by copying it to yourtheme/opalhotel/single-hotel/hotel-info.php.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

global $post;
if ( ! $post || $post->post_type !== OPALHOTEL_CPT_HOTEL || $post->post_status !== 'publish' ) {
	return;
}

$map = get_post_meta( $post->ID, '_map', true );
$phone = get_post_meta( $post->ID, '_phone', true );
$website = get_post_meta( $post->ID, '_website', true );
$address = get_post_meta( $post->ID, '_address', true );

$id = 'opalhotel-hotel-map-' . uniqid();
?>

<div class="opalhotel-hotel-info">

	<?php if ( ! empty( $map ) ) : ?>
		<div class="section map" id="<?php echo esc_attr( $id ) ?>" style="width: 500px; height: 300px"></div>
	<?php endif; ?>
	<div class="section address">
		<h4 class="title"><?php esc_html_e( 'Hotel Address', 'opal-hotel-room-booking' ); ?></h4>
		<p><?php echo esc_html( $address ); ?></p>
	</div>
	<?php if ( $phone || $website ) : ?>
		<div class="section meta">
			<ul>
				<?php if ( $phone ) : ?>
					<li>
						<label class="label"><?php esc_html_e( 'Phone', 'opal-hotel-room-booking' ); ?></label>
						<span class="value"><?php echo esc_html( $phone ); ?></span>
					</li>
				<?php endif; ?>
				<?php if ( $website ) : ?>
					<li>
						<label class="label"><?php esc_html_e( 'Website', 'opal-hotel-room-booking' ); ?></label>
						<span class="value"><?php echo esc_html( $website ); ?></span>
					</li>
				<?php endif; ?>
			</ul>
		</div>
	<?php endif; ?>

</div>

<script type="text/javascript">

	(function($){

		$(document).ready(function(){
			var zoom = <?php echo ! empty( $map['zoom'] ) ? absint( $map['zoom'] ) : 10 ?>;
			var propertyLocation = new google.maps.LatLng( <?php echo esc_js( $map['latitude'] ) ?>, <?php echo esc_js( $map['longitude'] ) ?> );
	        var propertyMapOptions = {
	            center: propertyLocation,
	            zoom: zoom,
	            mapTypeId: google.maps.MapTypeId.ROADMAP,
	            scrollwheel: false
	        };
	        var map = new google.maps.Map( document.getElementById( '<?php echo esc_attr( $id ) ?>' ), propertyMapOptions );
	        var marker = new google.maps.Marker({
	          	position: propertyLocation,
	          	map: map,
	          	title: '<?php echo esc_js( ! empty( $map['address'] ) ? $map['address'] : '' ) ?>'
	        });
		});

	})(jQuery);

</script>