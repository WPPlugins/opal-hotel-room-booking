<?php
/**
 * The template for displaying room content within loops
 *
 * This template can be overridden by copying it to yourtheme/opalhotel/loop/descriptions.php.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

global $opalhotel_room;

$hotels = $opalhotel_room->get_hotels();
if ( empty( $hotels ) ) return;

?>
<ul class="opalhotel-room-hotels">
	<?php foreach( $hotels as $hotel ) : ?>

		<li>
			<a href="<?php echo get_term_link( $hotel->term_id, OPALHOTEL_CPT_HOTEL ); ?>"><?php echo esc_html( $hotel->name ); ?></a>
		</li>

	<?php endforeach; ?>
</ul>