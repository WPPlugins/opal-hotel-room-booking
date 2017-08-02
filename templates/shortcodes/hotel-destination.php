<?php
/**
 * $Desc$
 *
 * @version    1.1.7
 * @package    opalhotel
 * @author     Opal  Team <info@wpopal.com >
 * @copyright  Copyright (C) 2016 wpopal.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @website  http://www.wpopal.com
 * @support  http://www.wpopal.com/support/forum.html
 */
 
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

$terms = get_terms( array(
    'taxonomy' 		=> OPALHOTEL_TXM_HOTEL_DES,
    'hide_empty' 	=> false,
) );

if ( ! $terms ) {
	return;
}

?>

<div class="opalhotel-owl-carousel destination" data-items="1" data-carousels="true" data-pagination="true">
	<?php foreach ( $terms as $term ) : ?>
		<?php
			$thumb_id = get_term_meta( $term->term_id, '_thumbnail_id', true );
			if ( ! $thumb_id ) continue;
			$hotels = opalhotel_get_posts_destination( $term->term_id );
			$the_most_cheap_room_id = opalhotel_get_the_most_cheap_room( $hotels );
			if ( ! $the_most_cheap_room_id ) continue;
			$opalhotel_room = opalhotel_get_room( $the_most_cheap_room_id );
		?>
		<div class="item">
			<div class="information">
				<div class="title">
					<?php echo esc_html( get_the_title( $opalhotel_room->id ) ) ?>
					<?php printf( translate_nooped_plural( _n_noop('%s hotel', '%s hotels'), count( $hotels ), 'opal-hotel-room-booking' ), count( $hotels ) ); ?>
					<div class="opalhotel-price">
						<span class="price-value"><?php printf( __( '%s', 'opal-hotel-room-booking' ), opalhotel_format_price( $opalhotel_room->base_price() ) ) ?></span>
						<span class="price-title"><?php esc_html_e( 'From', 'opal-hotel-room-booking' ); ?></span> / <span class="price-unit"><?php esc_html_e( ' per night', 'opal-hotel-room-booking' ) ?></span>
					</div>
				</div>
			</div>
			<div class="thumbnail">
				<?php $attach = wp_get_attachment_image_src( $thumb_id, $image_size ); ?>
				<?php if ( isset( $attach[0] ) ) : ?>
					<img src="<?php echo esc_url( $attach[0] ) ?>" />
				<?php endif; ?>
			</div>
		</div>
	<?php endforeach; ?>
</div>