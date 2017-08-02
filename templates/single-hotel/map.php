<?php
/**
 * The template for displaying hotel content within single
 *
 * This template can be overridden by copying it to yourtheme/opalhotel/single-hotel/title.php.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

global $post;
$map = get_post_meta( $post->ID, '_map', true );

$id = 'opalhotel-hotel-map-' . uniqid();
?>
<div class="section map" id="<?php echo esc_attr( $id ) ?>" style="width: 100%; min-height: 300px"></div>

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