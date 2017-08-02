<?php
/**
 * The template for displaying room content within loops
 *
 * This template can be overridden by copying it to yourtheme/opalhotel/loop-hotel/descriptions.php.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

$hotel = opalhotel_get_hotel( get_the_ID() );

if ( ! $hotel->address ) return;

?>

<span class="address">
	<i class="fa fa-map-marker" aria-hidden="true"></i>
	<?php printf( '%s', $hotel->address ) ?>
</span>
