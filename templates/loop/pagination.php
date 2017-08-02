<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

if ( $query->max_num_pages <= 1 ) { return ; }

?>

<nav class="opalhotel-pagination">
	<?php
		echo paginate_links( apply_filters( 'opalhotel_pagination_args', array(
			'base'         => esc_url_raw( str_replace( 999999999, '%#%', get_pagenum_link( 999999999, false ) ) ),
			'format'       => '',
			'add_args'     => false,
			'current'      => max( 1, get_query_var( 'paged' ) ),
			'total'        => $query->max_num_pages,
			'prev_text'    => __( '&larr; Previous', 'opal-hotel-room-booking' ),
			'next_text'    => __( 'Next &rarr;', 'opal-hotel-room-booking' ),
			'type'         => 'list',
			'end_size'     => 3,
			'mid_size'     => 3
		) ) );
	?>
</nav>
