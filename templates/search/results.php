<?php
/**
 * The template for displaying room content within loops
 *
 * This template can be overridden by copying it to yourtheme/opalhotel/search/results.php.
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
$room_type = ! empty( $_REQUEST['room_type'] ) ? absint( $_REQUEST['room_type'] ) : 0;
$qty = isset( $_REQUEST['number_of_rooms'] ) ? absint( $_REQUEST['number_of_rooms'] ) : 1;

$paged = max( 1, get_query_var( 'paged' ) );
if ( isset( $_REQUEST['paged'] ) ) {
	$paged = absint( $_REQUEST['paged'] );
}
// join filter get rooms
add_filter( 'posts_fields', 'opalhotel_join_search_rooms_available_fields' );
add_filter( 'posts_join', 'opalhotel_join_search_rooms_available_filter' );
add_filter( 'posts_where', 'opalhotel_join_search_rooms_available_where' );

$args  = array(
		'post_type'			=> OPALHOTEL_CPT_ROOM,
		'posts_per_page'	=> get_option( 'posts_per_page', 10 ),
		'post_status'		=> 'publish',
		'paged'				=> $paged
	);
if ( $room_type ) {
	$args['tax_query'] = array(
			array(
					'taxonomy'	=> OPALHOTEL_TXM_ROOM_CAT,
					'field'		=> 'term_id',
					'terms'		=> $room_type
				)
		);
}
$query = new WP_Query( $args );
// echo $query->request;
remove_filter( 'posts_where', 'opalhotel_join_search_rooms_available_where' );
remove_filter( 'posts_join', 'opalhotel_join_search_rooms_available_filter' );
remove_filter( 'posts_fields', 'opalhotel_join_search_rooms_available_fields' );
$total = $query->max_num_pages;

if ( $query->have_posts() ) : ?>

	<div class="opalhotel-wrapper">
		<ul class="opalhotel-search-results opalhotel_main rooms">
			<?php
				while ( $query->have_posts() ) {
					$query->the_post();
					opalhotel_get_template_part( 'content-room', 'form' );
				}
			?>
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
			<p class="count-results">
				<?php
					$per_page = $query->get( 'posts_per_page' );
					$total    = $query->found_posts;
					$first    = ( $per_page * $paged ) - $per_page + 1;
					$last     = min( $total, $query->get( 'posts_per_page' ) * $paged );

					if ( $total <= $per_page || -1 === $per_page ) {
						printf( _n( 'Showing the single room', 'Showing all %d rooms', $total, 'opal-hotel-room-booking' ), $total );
					} else {
						printf( _nx( 'Showing the single rooms', 'Showing %1$d&ndash;%2$d of %3$d rooms', $total, '%1$d = first, %2$d = last, %3$d = total', 'opal-hotel-room-booking' ), $first, $last, $total );
					}
				?>
			</p>
			<p style="clear: both;"></p>
		<?php endif ?>
	</div>

<?php wp_reset_postdata(); else: ?>

	<?php opalhotel_get_template( 'loop/no-room-found.php' ); ?>

<?php endif;
