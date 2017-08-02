<?php
/**
 * The template for displaying hotel content within loops
 *
 * This template can be overridden by copying it to yourtheme/opalhotel/single-hotel-v2.php.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

get_header(); ?>

	<div class="opalhotel-wrapper">

		<div class="feature-full-screen">
			<?php
				$map = get_post_meta( get_the_ID(), '_map', true );
				opalhotel_print_map_hotel( array(
					'places'	=> array(
						array(
							'id'			=> get_the_ID(),
							'title'			=> get_the_title(),
							'permalink'		=> get_the_permalink(),
							'thumbnail' 	=> get_the_post_thumbnail_url( get_the_ID() ),
							'lat'			=> isset( $map['latitude'] ) ? floatval( $map['latitude'] ) : 0,
							'lng'			=> isset( $map['longitude'] ) ? floatval( $map['longitude'] ) : 0,
							'address'		=> isset( $map['address'] ) ? esc_html( $map['address'] ) : '',
							'content'		=> get_the_content()
						)
					),
					'zoom'		=> isset( $map['zoom'] ) ? absint( $map['zoom'] ) : 12
				) );
			?>
		</div>

		<div class="opalhotel-main-wrapper">
			<div class="grid-row">
				<div class="grid-column-8 pull-left">
					<?php while ( have_posts() ) : the_post(); ?>
						<?php

							/**
							 * Rating
							 */
							opalhotel_get_template( 'single-hotel/rating.php' );

							/**
							 * Title
							 */
							opalhotel_get_template( 'single-hotel/title.php' );

							/**
							 * Address
							 */
							opalhotel_get_template( 'single-hotel/address.php' );

							?>
								<div class="feature-full-screen">
									<?php opalhotel_get_template( 'single-hotel/gallery.php' ); ?>
								</div>
							<?php

							/**
							 * tab - rooms
							 */
							opalhotel_get_template_part( 'content', 'single-hotel-tabs' );
						?>
					<?php endwhile; // end of the loop. ?>
				</div>

				<div class="grid-column-4 pull-left">
					<?php get_sidebar(); ?>
				</div>
			</div>
		</div>
	</div>

<?php get_footer(); ?>