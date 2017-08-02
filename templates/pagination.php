<?php
/**
 * $Desc$
 *
 * @version    $Id$
 * @package    $package$
 * @author     Opal Team <info@wpopal.com >
 * @copyright  Copyright (C) 2014 wpopal.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @website  http://www.wpopal.com
 * @support  http://www.wpopal.com/support/forum.html
 */

defined( 'ABSPATH' ) || exit();

if ( ! isset( $query ) || ! $query ) {
	global $wp_query;
	$query = $wp_query;
}

$current = get_query_var( 'paged' );
if ( isset( $_REQUEST['paged'] ) ) {
	$current = absint( $_REQUEST['paged'] );
}
$current = max( $current, 1 );

if ( ! isset( $ajax ) || ! $ajax ) {
	$ajax = false;
}

if ( ! isset( $args ) || ! $args ) {
	$args = array();
}

if ( ! isset( $atts ) || ! $atts ) {
	$atts = array();
}

if ( $query->max_num_pages <= 1 ) { return; }

$url = esc_url_raw( str_replace( 999999999, '%#%', get_pagenum_link( 999999999, false ) ) );

?>

<nav class="opalhotel-pagination">
	<?php if ( ! $ajax ) : ?>
		<?php
			echo opalhotel_paginate_links( apply_filters( 'opalhotel_pagination_args', array(
				'base'         => $url,
				'format'       => '',
				'add_args'     => false,
				'current'      => $current,
				'total'        => $query->max_num_pages,
				'prev_text'    => __( '&larr; Previous', 'opal-hotel-room-booking' ),
				'next_text'    => __( 'Next &rarr;', 'opal-hotel-room-booking' ),
				'type'         => 'list',
				'end_size'     => 3,
				'mid_size'     => 3
			) ) );
		?>
	<?php else: ?>
		<form action="<?php echo esc_url( $url ) ?>" class="opalhotel-ajax-load-more">
			<input type="hidden" name="paged" value="<?php echo esc_attr( $current + 1 ) ?>" />
			<input type="hidden" name="per_page" value="<?php echo esc_attr( $query->get('posts_per_page') ) ?>" />
			<input type="hidden" name="args" value="<?php echo esc_attr( maybe_serialize( $args ) ); ?>" />
			<input type="hidden" name="atts" value="<?php echo esc_attr( maybe_serialize( $atts ) ); ?>" />
			<input type="hidden" name="action" value="opalhotel_load_more_ajax" />
			<?php wp_nonce_field( 'opalhotel-ajax-load-more', 'opalhotel-load-more-nonce' ); ?>
			<button type="submit" class="submit btn button btn-primary opalhotel-button-submit">
				<?php esc_html_e( 'Show more', 'opal-hotel-room-booking' ); ?>
			</button>
		</form>
	<?php endif; ?>
</nav>
