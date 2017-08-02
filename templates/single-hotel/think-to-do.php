<?php
/**
 * The template for displaying hotel content within single
 *
 * This template can be overridden by copying it to yourtheme/opalhotel/single-hotel/title.php.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

$hotel = opalhotel_get_hotel( get_the_ID() );

$sections = $hotel->think_to_do_section;
?>

<div class="hotel-box">
	<h3 class="title"><?php esc_html_e( 'Things To Do', 'opal-hotel-room-booking' ); ?></h3>
	<div class="content">

		<p class="description"><?php echo esc_html( $hotel->think_to_do_description ) ?></p>

		<?php if ( $sections ) : ?>

			<ul class="sections">
				<?php foreach ( $sections as $section ) : ?>

					<li>
						<?php if ( ! empty( $section['_think_to_do_image'] ) ) : ?>
							<div class="image">
								<img src="<?php echo esc_url( $section['_think_to_do_image'] ) ?>" />
							</div>
						<?php endif; ?>
						<div class="content">
							<?php if ( ! empty( $section['_think_to_do_title'] ) ) : ?>
								<h5 class="sub-title"><?php echo esc_html( $section['_think_to_do_title'] ) ?></h5>
							<?php endif; ?>
							<?php if ( ! empty( $section['_think_to_do_short_description'] ) ) : ?>
								<p class="description"><?php echo esc_html( $section['_think_to_do_short_description'] ) ?></p>
							<?php endif; ?>
						</div>
					</li>

				<?php endforeach; ?>
			</ul>

		<?php endif; ?>
	</div>
</div>