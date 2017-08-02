<?php
/**
 * The template for displaying room content within single
 *
 * This template can be overridden by copying it to yourtheme/opalhotel/single-room/related.php.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

$related = opalhotel_get_related_room( get_the_ID() );

if ( $related->have_posts() ) : ?>
	<div class="room-related">
		<h3><span><?php esc_html_e( 'Other Room', 'opal-hotel-room-booking' ); ?></span></h3>

		<div class="<?php echo apply_filters( 'opalhote_loop_wrap_class' ,'grid-row');?>">
			<?php while ( $related->have_posts() ) : $related->the_post(); ?>
					<?php opalhotel_get_template_part( 'content', 'room' ); ?>
			<?php endwhile; ?>
		</div>

	</div>
<?php endif; ?>