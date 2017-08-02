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

$average_rating = opalhotel_get_average_rating( get_the_ID() );

?>

<div class="opalhotel-rating-wrapper">
	<?php printf( '%s', opalhotel_print_rating( $average_rating ) ) ?>
</div>