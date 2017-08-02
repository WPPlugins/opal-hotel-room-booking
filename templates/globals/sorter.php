<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}
global $wp;
$current_url = add_query_arg( $wp->query_string, '', home_url( $wp->request ) );

$options = apply_filters( 'opalhotel_hotel_sortable_filter', array(
		'0' => __( 'Default filter', 'opal-hotel-room-booking' ),
		'1'	=> __( 'Price Ascending' ),
		'2'	=> __( 'Price Descending' ),
		'3' => __( 'Rating Ascending' ),
		'4'	=> __( 'Rating Descending' )
	) );
if ( ! $options ) return;
$selected = isset( $_REQUEST['sortable'] ) ? absint( $_REQUEST['sortable'] ) : 0;

?>

<select name="sortable" class="opalhotel-select">
	<?php foreach ( $options as $value => $text ) : ?>
		<option value="<?php echo esc_attr( $value ) ?>" data-sortable="<?php echo esc_attr( $value ) ?>" <?php selected( $selected, $value ) ?>><?php echo esc_html( $text ) ?></option>
	<?php endforeach; ?>
</select>