<?php
/**
 * The template for displaying room content within loops
 *
 * This template can be overridden by copying it to yourtheme/opalhotel/loop-hotel/star.php.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

$hotel = opalhotel_get_hotel( get_the_ID() );

?>

<div class="opalhotel-star-wrapper">
	<?php printf( '%s', opalhotel_print_rating( $hotel->star, false ) ) ?>
</div>