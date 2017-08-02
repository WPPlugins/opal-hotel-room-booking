<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

global $wp;
$current_url = add_query_arg( $wp->query_string, '', home_url( $wp->request ) );
$mode = opalhotel_loop_display_mode( 'grid' );

if ( isset($_COOKIE['opalhotel_display']) && $_COOKIE['opalhotel_display'] == 'list' ) {
	$mode = $_COOKIE['opalhotel_display'];
}

?>

<form action="<?php //echo esc_url( $current_url ) ?>" class="display-mode" method="GET">
	<button title="<?php esc_attr_e('Grid','opal-hotel-room-booking') ?>" class="btn<?php echo esc_attr( $mode == 'grid' ? ' active' : '' ) ?>" value="grid" name="display" type="submit">
		<i class="fa fa-th"></i>
		<?php esc_html_e('Grid','opal-hotel-room-booking') ?>
	</button>
	<button title="<?php esc_attr_e('List','opal-hotel-room-booking') ?>" class="btn<?php echo esc_attr( $mode == 'list' ? ' active' : '' ) ?>" value="list" name="display" type="submit">
		<i class="fa fa-th-list"></i>
		<?php esc_html_e('List','opal-hotel-room-booking') ?>
	</button>
</form>