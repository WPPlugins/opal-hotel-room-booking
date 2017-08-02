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

$term = get_term_by( 'slug', $destination_id, OPALHOTEL_TXM_HOTEL_DES );

if ( is_wp_error( $term ) ) return;

$thumb_id = get_term_meta( $term->term_id, '_thumbnail_id', true );

if ( ! $thumb_id ) return;

$hotels = opalhotel_get_posts_destination( $term->term_id );
$the_most_cheap_room_id = opalhotel_get_the_most_cheap_room( $hotels );
if ( ! $the_most_cheap_room_id ) return;
$opalhotel_room = opalhotel_get_room( $the_most_cheap_room_id );

?>

<div class="opalhotel-single-destination">
	<div class="thumbnail">
		<?php $attach = wp_get_attachment_image_src( $thumb_id, $image_size ); ?>
		<?php if ( isset( $attach[0] ) ) : ?>
			<img src="<?php echo esc_url( $attach[0] ) ?>" />
		<?php endif; ?>
		<div class="information">
			<div class="title">
				<a href="<?php echo esc_url( get_term_link( $term->term_id ) ) ?>">
					<?php echo esc_html( $term->name ) ?>
				</a>

				<div class="meta">
					<span class="hotel-count">
						<?php printf( translate_nooped_plural( _n_noop('%s hotel', '%s hotels'), count( $hotels ), 'opal-hotel-room-booking' ), count( $hotels ) ); ?> -
					</span>
					<div class="opalhotel-price">
						<span class="price-value"><?php printf( __( '%s', 'opal-hotel-room-booking' ), opalhotel_format_price( $opalhotel_room->base_price() ) ) ?></span>
						<span class="price-title"><?php esc_html_e( 'From', 'opal-hotel-room-booking' ); ?></span> / <span class="price-unit"><?php esc_html_e( ' per night', 'opal-hotel-room-booking' ) ?></span>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>