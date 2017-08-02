<?php
/**
 * The template for displaying hotel content within single
 *
 * This template can be overridden by copying it to yourtheme/opalhotel/loop/rating.php.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

$reviews_count = opalhotel_get_review_count( get_the_ID() );
$average_rating = opalhotel_get_average_rating( get_the_ID() );
?>

<div class="opalhotel-rating-wrapper">
	<?php
		printf( '%s (%s)', opalhotel_print_rating( $average_rating ), sprintf( _n( '%d reiew', '%d reiews', $reviews_count, 'opal-hotel-room-booking' ), $reviews_count ) );
	?>
</div>