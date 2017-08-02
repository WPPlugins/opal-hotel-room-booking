<?php
/**
 * The template for displaying room content within loops
 *
 * This template can be overridden by copying it to yourtheme/opalhotel/search/loop/optional.php.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}
global $post;
$room = opalhotel_get_room( get_the_ID() );
$max_adults = $room->get_max_adults_allowed();
$adult_options = array();
for ( $i = 1; $i <= $max_adults; $i++ ) {
	$adult_options[$i] = $i;
}

$extra = $room->get_extras_all_details();
$adult_option_attributes = isset( $extra['adult'] ) ? $extra['adult'] : array();
$child_option_attributes = isset( $extra['child'] ) ? $extra['child'] : array();

$max_child = $room->get_max_childrens_allowed();
$child_options = array();
for ( $i = 0; $i <= $max_child; $i++ ) {
	$child_options[$i] = $i;
}

$available = isset( $post->available ) && $post->available ? $post->available : 0;

$available_options = array();
for ( $i = 1; $i <= $available; $i++ ) {
	$available_options[$i] = $i;
}
?>

<div class="opalhotel-available-optional grid-row">

	<div class="grid-column-3">
		<?php
			opalhotel_print_dropdown( array(
					'label'			=> esc_html( 'Room(s)', 'opal-hotel-room-booking' ),
					'options'		=> $available_options,
					'selected'		=> isset( $_REQUEST['number_of_rooms'] ) ? absint( $_REQUEST['number_of_rooms'] ) : 1,
					'class'			=> array( 'number_of_rooms' )
				) );
		?>
	</div>

	<div class="grid-column-3">
		<?php
			opalhotel_print_dropdown( array(
					'label'			=> esc_html( 'Adult(s)', 'opal-hotel-room-booking' ),
					'options'		=> $adult_options,
					'option_attributes'	=> $adult_option_attributes,
					'selected'		=> isset( $_REQUEST['adult'] ) ? absint( $_REQUEST['adult'] ) : 1,
					'class'			=> array( 'people adult-select select-price' )
				) );
		?>
	</div>

	<div class="grid-column-3">
		<?php
			opalhotel_print_dropdown( array(
					'label'			=> esc_html( 'Children', 'opal-hotel-room-booking' ),
					'options'		=> $child_options,
					'option_attributes'	=> $child_option_attributes,
					'selected'		=> isset( $_REQUEST['child'] ) ? absint( $_REQUEST['child'] ) : 1,
					'class'			=> array( 'people child-select select-price' )
				) );
		?>
	</div>

</div>