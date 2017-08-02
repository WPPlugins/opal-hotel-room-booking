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

$terms = wp_get_post_terms( get_the_ID(), OPALHOTEL_TXM_HOTEL_INC );

if ( is_wp_error( $terms ) || ! $terms ) return;

$includes = array();
foreach ( $terms as $term ) {
	$includes[] = $term->name;
}
?>

<span class="includes">
	<i class="fa fa-check" aria-hidden="true"></i>
	<?php printf( '%s', implode( ', ', $includes ) ); ?>
</span>
