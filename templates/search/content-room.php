<?php
/**
 * The template for displaying room content within loops
 *
 * This template can be overridden by copying it to yourtheme/opalhotel/search/loop/content-room.php.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

$action_url = add_query_arg( array(
			'add-to-cart'		=> 1
		), opalhotel_get_available_url() );

/* get packages */
$packages = $room->get_packages();
?>

<li class="opalhotel-available-item opalhotel-room clearfix">

	<form name="opalhotel-available-form" class="opalhotel-available-form" method="POST" action="<?php echo esc_url( $action_url ) ?>">
		<div class="inner-top clearfix">
			<!-- thumbnail -->
			<div class="room-info">
				<?php opalhotel_get_template( 'search/loop/thumbnail.php', array( 'room' => $room ) ); ?>
				<!-- description attributes -->
				<?php opalhotel_get_template( 'search/loop/details.php', array( 'room' => $room ) ); ?>
				<!-- description attributes -->
			</div>
			<div class="room-actions">
				<!-- price -->
				<?php opalhotel_get_template( 'search/loop/price.php', array( 'room' => $room ) ); ?>

				<!-- pricing -->
				<?php opalhotel_get_template( 'search/loop/pricing.php', array( 'room' => $room ) ); ?>
				<a href="#room-packages-<?php echo esc_attr( $room->id ); ?>" class="opalhotel-room-toggle-packages opalhotel-button btn btn-default btn-block">
					<span><?php esc_html_e( 'Show Rates', 'opal-hotel-room-booking' ); ?></span>
					<i class="fa"></i>
				</a>

				<div class="inner-bottom clearfix">
					<!-- extra optional -->
					<?php do_action( 'opalhotel_available_item', $room ); ?>
				</div>
			</div>
		</div>
		<?php opalhotel_get_template( 'search/loop/packages.php', array( 'room' => $room ) ); ?>
	</form>

</li>