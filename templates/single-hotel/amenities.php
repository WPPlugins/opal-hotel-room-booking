<?php
/**
 * The template for displaying hotel content within single
 *
 * This template can be overridden by copying it to yourtheme/opalhotel/single-hotel/amenities.php.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

global $post;
$hotel = opalhotel_get_hotel( $post->ID );
?>

<div class="hotel-box">
	<h3 class="title"><?php esc_html_e( 'Amenities', 'opal-hotel-room-booking' ); ?></h3>
	<div class="content">
		<?php if ( $hotel->amenities ) : ?>

			<ul class="amenities">
				<?php foreach ( $hotel->amenities as $id ) : ?>
					<?php
						$icon = get_post_meta( $id, '_icon', true );
						$color = get_post_meta( $id, '_icon_color', true );
						$amenities = get_post_meta( $id, '_amenities_fields', true );
					?>
					<li>
						<?php if ( $icon ) : ?>
							<i class="<?php echo esc_attr( $icon ) ?>"<?php printf( '%s', $color ? ' style="color: '.esc_attr( $color ).'"' : '' ) ?>></i>
						<?php endif; ?>
						<?php echo esc_html( get_the_title( $id ) ) ?>
						<?php if ( $amenities ) : ?>
							<ul>
								<?php foreach ( $amenities as $amenity ) : ?>
									<li><?php echo esc_html( ! empty( $amenity['name'] ) ? $amenity['name'] : '' ) ?></li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>

		<?php endif; ?>
	</div>
</div>
