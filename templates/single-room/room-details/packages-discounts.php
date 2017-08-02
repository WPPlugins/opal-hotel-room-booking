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

$packages = $opalhotel_room->get_packages();
$discounts = $opalhotel_room->get_discounts_prices_info();

if ( $packages || $discounts ) : ?>
	<div class="room-box">
		<?php if( $packages ): ?>
			<div class="room-optional-packages">
				<h4><?php esc_html_e( 'Optional Packages', 'opal-hotel-room-booking' ); ?></h4>
				<div class="inner">
					<div class="opalhotel-room-packages">
						<?php foreach ( $packages as $k => $package ) :   ?>

							<div class="package-item <?php if( $package->post_content ) : ?> has-content<?php endif; ?>">
								<?php echo get_the_post_thumbnail( $package->id); ?>
								<div class="package-content">
									<!-- each package -->
									<h4><?php echo esc_html( $package->post_title ); ?></h4>

									<!-- price -->
									<div class="opalhotel-package-price">
										<span class="price-value">
											<?php printf( __( '%s', 'opal-hotel-room-booking' ), opalhotel_format_price( $package->get_price_display( $package->get_price() ) ) ) ?>
										</span>
										<span class="price-unit"><?php printf( ' / %s', opalhotel_get_package_label( $package->id ) ) ?></span>
									</div>
									<!-- end each package -->
								</div>
								<?php if( $package->post_content ) : ?>
								<div class="package-description">
									<?php echo apply_filters( 'the_content', $package->post_content ); ?>
								</div>
								<?php endif ; ?>
							</div>
					<?php endforeach; ?>
					</div>
				</div>
			</div>
		<?php endif; ?>

		<?php if( $discounts ): ?>
			<div class="room-discounts-info">
				<div class="alert alert-success">
					<h4><?php esc_html_e('Discount','opal-hotel-room-booking'); ?></h4>
					<div class="content">
						<ul>
							<?php foreach( $discounts as $discount ): if ( ! isset( $discount['unit'] ) || ! $discount['unit'] ) continue; ?>

								<li> <i class="fa fa-check" aria-hidden="true"></i>

								  	<?php if ( $discount['type'] === 'before' ) : ?>

										<?php printf( __( 'Book before %s', 'opal-hotel-room-booking' ), opalhotel_days_display( $discount['unit'] ), $discount['amount'] ) ?>

									<?php elseif ( $discount['type'] === 'live' ) : ?>

										<?php printf( __( 'Stay %s', 'opal-hotel-room-booking' ), opalhotel_days_display( $discount['unit'] ) ) ?>

									<?php endif; ?>

									<?php if ( $discount['sale_type'] === 'subtract' ) : ?>
										<?php printf( __( ' - <span>%s/room</span>', 'opal-hotel-room-booking' ), opalhotel_format_price( $discount['amount'] ) ) ?>
									<?php else : ?>
										<?php printf( __( ' - <span>%s%s/room</span>', 'opal-hotel-room-booking' ), $discount['amount'], '%' ) ?>
									<?php endif; ?>
								</li>

							<?php endforeach; ?>
						</ul>
					</div>
				</div>
			</div>
		<?php endif;  ?>

		<?php if ( $prices = $opalhotel_room->get_extras_all_details() ) : ?>
			<div class="opalhotel-price-day">
				<div class="alert alert-danger">
					<h4><?php esc_html_e( 'Extra Price', 'opal-hotel-room-booking' ); ?></h4>
					<div class="content">
						<ul>
							<?php if ( isset( $prices['adult'] ) ) : ?>
								<?php foreach ( $prices['adult'] as $adult => $price ) : ?>
									<li>
										<i class="fa fa-check" aria-hidden="true"></i>
										<?php printf( translate_nooped_plural( _n_noop( '%1$s adult + <span>%2$s/room</span>', '%1$s adults + <span>%2$s/room</span>' ), 'opal-hotel-room-booking' ), $adult, opalhotel_format_price( $price ) ); ?>
									</li>
								<?php endforeach; ?>
							<?php endif; ?>
							<?php if ( isset( $prices['child'] ) ) : ?>
								<?php foreach ( $prices['child'] as $child => $price ) : ?>
									<li>
										<i class="fa fa-check" aria-hidden="true"></i>
										<?php printf( translate_nooped_plural( _n_noop( '%1$s child + <span>%2$s</span>', '%1$s child + <span>%2$s/room</span>' ), 'opal-hotel-room-booking' ), $child, opalhotel_format_price( $price ) ); ?>
									</li>
								<?php endforeach; ?>
							<?php endif; ?>
						</ul>
					</div>
				</div>
			</div>
		<?php endif;  ?>
	</div>
<?php endif; ?>