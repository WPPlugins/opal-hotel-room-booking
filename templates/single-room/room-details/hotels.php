<?php
/**
 * The template for displaying room content within single
 *
 * This template can be overridden by copying it to yourtheme/opalhotel/single-room/room-details/pricing-plans.php.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

global $opalhotel_room;
if ( ! $opalhotel_room ) {
	$opalhotel_room = opalhotel_get_room( get_the_ID() );
}
$hotels = $opalhotel_room->get_hotels();

if( $hotels && ! is_wp_error( $hotels ) ): ?>
	<div class="room-box">
		<div class="room-optional-packages">
			<h4><?php esc_html_e( 'Hotel Availability', 'opal-hotel-room-booking' ); ?></h4>
			<div class="inner">
				<div class="opalhotel-room-packages">
					<?php foreach ( $hotels as $hotel ) : setup_postdata( $hotel ); ?>
						<div class="package-item <?php if( $hotel->post_excerpt ) : ?> has-content<?php endif; ?>">
							<?php echo get_the_post_thumbnail( $hotel->ID ); ?>
							<div class="package-content">
								<!-- each package -->
								<h4><?php echo esc_html( $hotel->post_title ); ?></h4>
								<!-- end each package -->
							</div>	
							<?php if( $hotel->post_excerpt ) : ?>
							<div class="package-description">
								<?php echo wp_strip_all_tags($hotel->post_excerpt); ?>
							</div>
							<?php endif ; ?>
						</div>
				<?php endforeach; wp_reset_postdata(); ?>
				</div>
			</div>
		</div>
	</div>
<?php endif;