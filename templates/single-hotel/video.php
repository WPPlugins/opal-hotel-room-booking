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

<div class="opalhotel-feature-video" id="<?php echo esc_attr( uniqid() ) ?>" data-loop="yes" data-url="<?php echo esc_url( $hotel->video ) ?>" data-mute="yes" data-autoplay="no" data-control="yes"></div>