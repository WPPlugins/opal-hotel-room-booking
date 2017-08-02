<?php
/**
 * The template for displaying room content within loops
 *
 * This template can be overridden by copying it to yourtheme/opalhotel/search/form-results.php.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

/* print notices */
opalhotel_print_notices();

$arrival = isset( $_REQUEST['arrival_datetime'] ) ? date( 'Y-m-d', strtotime( $_REQUEST['arrival_datetime'] ) ) : current_time( 'mysql' );
$departure = isset( $_REQUEST['departure_datetime'] ) ? date( 'Y-m-d', strtotime( $_REQUEST['departure_datetime'] ) ) : date( 'Y-m-d', current_time( 'timestamp' ) + DAY_IN_SECONDS );
$adult = isset( $_REQUEST['adult'] ) ? absint( $_REQUEST['adult'] ) : false;
$child = isset( $_REQUEST['child'] ) ? absint( $_REQUEST['child'] ) : false;
$room_type = true;

// results
$args = array(
		'arrival'		=> $arrival,
		'departure'		=> $departure,
		'adult'			=> $adult,
		'child'			=> $child,
		'room_type'		=> $room_type
	);
if ( is_singular( OPALHOTEL_CPT_HOTEL ) ) {
	$args['hotel_id'] = get_the_ID();
}

$rooms = opalhotel_get_room_available( $args );

$paged = max( get_query_var( 'paged' ), 1 );
$posts_per_page = get_option( 'posts_per_page', 1 );
$total = ceil( count( $rooms ) / $posts_per_page );

$rooms = array_slice( $rooms, ( $paged - 1 ) * $posts_per_page, $posts_per_page );

?>

<div class="opalhotel-wrapper">
	<ul class="opalhotel-search-results opalhotel_main rooms">
		<?php foreach ( $rooms as $room ) : $args['room'] = $room; ?>
			<?php opalhotel_get_template( 'search/content-room.php', array( 'room' => $room ) ); ?>
		<?php unset( $args['room'] ); endforeach; ?>
	</ul>

	<?php if( $total > 1 ): ?>
		<nav class="opalhotel-pagination">
			<ul class="opalhotel-pagination-available page-numbers">
				<?php
					if ( $total > 1 && $paged > 1 ) {
						echo '<li><a href="#" class="prev page-numbers opalhotel-page" data-page="' . esc_attr( $paged - 1 ) . '" data-arrival="' . esc_attr( $arrival ) . '" data-step="2" data-departure="' . esc_attr( $departure ) . '">' . __( '&larr; Previous', 'opal-hotel-room-booking' ) . '</a></li>';
					}

					for ( $i = 1; $i <= $total; $i++ ) {
						if ( $i == $paged ) {
							echo '<li><span class="page-numbers current">' . $i . '</span></li>';
						} else {
							echo '<li><a href="#" class="page-numbers opalhotel-page" data-step="2" data-page="' . esc_attr( $i ) . '" data-arrival="' . esc_attr( $arrival ) . '" data-departure="' . esc_attr( $departure ) . '">' . $i . '</a></li>';
						}
					}

					if ( $paged < $total && $total > 1 ) {
						echo '<li><a href="#" class="next page-numbers opalhotel-page" data-step="2" data-page="' . esc_attr( $paged + 1 ) . '" data-arrival="' . esc_attr( $arrival ) . '" data-departure="' . esc_attr( $departure ) . '">' . __( 'Next &rarr;', 'opal-hotel-room-booking' ) . '</a></li>';
					}
				?>
			</ul>
		</nav>
	<?php endif ?>
</div>