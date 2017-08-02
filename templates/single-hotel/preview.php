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

$hotel = opalhotel_get_hotel( get_the_ID() );

?>

<div class="opalhotel-tabs hotel-preview">

	<ul class="tabs">
		<li class="map-preview active">
			<a href="#map-preview"><i class="fa fa-map" aria-hidden="true"></i></a>
		</li>
		<li class="image-preview">
			<a href="#image-preview"><i class="fa fa-file-image-o" aria-hidden="true"></i></a>
		</li>
		<li class="video-preview">
			<a href="#video-preview"><i class="fa fa-video-camera" aria-hidden="true"></i></a>
		</li>
	</ul>

	<div class="panel entry-content fade in" id="map-preview">
		<?php opalhotel_get_template( 'single-hotel/map.php' ); ?>
	</div>

	<div class="panel entry-content" id="image-preview">
		<?php opalhotel_get_template( 'single-hotel/header-gallery.php' ); ?>
	</div>

	<div class="panel entry-content" id="video-preview">
		<?php opalhotel_get_template( 'single-hotel/video.php' ); ?>
	</div>

</div>