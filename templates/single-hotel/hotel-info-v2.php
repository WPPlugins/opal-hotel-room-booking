<?php
/**
 * The template for displaying hotel content within single
 *
 * This template can be overridden by copying it to yourtheme/opalhotel/single-hotel/hotel-info.php.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

global $post;
if ( ! $post || $post->post_type !== OPALHOTEL_CPT_HOTEL || $post->post_status !== 'publish' ) {
	return;
}
$hotel = opalhotel_get_hotel( $post->ID );

$room_id = $hotel->get_the_most_cheap_room();

if ( ! $room_id ) {
	return;
}

$room = opalhotel_get_room( $room_id );

?>

<div class="opalhotel-hotel-info hotel-info-v2">

	<header class="price">
		<span class="price-value"><?php printf( __( '%s', 'opal-hotel-room-booking' ), opalhotel_format_price( $room->base_price() ) ) ?></span>
		<span class="price-title"><?php esc_html_e( 'From', 'opal-hotel-room-booking' ); ?></span> / <span class="price-unit"><?php esc_html_e( ' per night', 'opal-hotel-room-booking' ) ?></span>
	</header>
	<?php if ( $hotel->image ) : ?>
		<div class="hotel-feature-image">
			<img src="<?php echo esc_url( $hotel->image ) ?>" width="100" height="100" />
		</div>
	<?php endif; ?>
	<div class="meta">
		<ul>
			<li>
				<?php $types = opalhotel_hotel_types(); ?>
				<span class="label"><?php esc_html_e( 'Type', 'opal-hotel-room-booking' ); ?></span>
				<span class="value"><?php echo esc_html( ! empty( $types[$hotel->type] ) ? $types[$hotel->type] : '' ) ?></span>
			</li>
			<li>
				<span class="label"><?php esc_html_e( 'Star', 'opal-hotel-room-booking' ); ?></span>
				<span class="value"><?php printf( translate_nooped_plural( _n_noop( '%s star', '%s stars' ), $hotel->star, 'opal-hotel-room-booking' ), $hotel->star ) ?></span>
			</li>
			<li>
				<span class="label"><?php esc_html_e( 'Location', 'opal-hotel-room-booking' ); ?></span>
				<span class="value"><?php echo esc_html( $hotel->address ) ?></span>
			</li>
			<li>
				<span class="label"><?php esc_html_e( 'Phone', 'opal-hotel-room-booking' ); ?></span>
				<span class="value"><?php echo esc_html( $hotel->phone ) ?></span>
			</li>
			<li>
				<span class="label"><?php esc_html_e( 'Checkin', 'opal-hotel-room-booking' ); ?></span>
				<span class="value"><?php echo esc_html( $hotel->checkin_time ) ?></span>
			</li>
			<li>
				<span class="label"><?php esc_html_e( 'Checkout', 'opal-hotel-room-booking' ); ?></span>
				<span class="value"><?php echo esc_html( $hotel->checkout_time ) ?></span>
			</li>
		</ul>
	</div>
	<footer class="action">
		<div class="pull-left">
			<a href="<?php echo esc_url( wp_login_url() ) ?>" data-id="<?php echo esc_attr( $post->ID ) ?>" data-nonce="<?php echo wp_create_nonce( 'opalhotel-toggle-nonce' ); ?>" class="opalhotel-pre-add-wishlist <?php echo esc_attr( is_user_logged_in() ? 'opalhotel-toggle-favorite' : 'opalhotel-need-login' ) ?>">
				<?php if ( ! is_user_logged_in() || ! opalhotel_is_favorited( $post->ID ) ) : ?>
					<i class="fa fa-heart-o"></i>
				<?php else: ?>
					<i class="fa fa-heart"></i>
				<?php endif; ?>
				<?php if ( ! is_user_logged_in() ) : ?>
					<?php esc_html_e( 'Login to add to wishlist', 'opal-hotel-room-booking' ); ?>
				<?php else: ?>
					<?php esc_html_e( 'Add to wishlist', 'opal-hotel-room-booking' ); ?>
				<?php endif; ?>
			</a>
		</div>
		<div class="pull-right">
			<a href="javascript:void(0)" class="opalhotel-share" id="<?php echo esc_attr( uniqid() ) ?>" data-id="<?php echo esc_attr( 'opalhotel-share-' . $room_id ) ?>" data-title="<?php esc_attr_e( 'Let\'s share', 'opal-hotel-room-booking' ) ?>"><i class="fa fa-share-alt" aria-hidden="true"></i></a>
			<a href="javascript:void(0)" class="opalhotel-print-page"><i class="fa fa-print" aria-hidden="true"></i></a>
		</div>
	</footer>

	<div class="opalhotel-share-content" id="<?php echo esc_attr( 'opalhotel-share-' . $room_id ) ?>">
		<ul class="opalhotel-single-share">
			<li>
				<a href="<?php echo esc_attr( get_the_permalink() ) ?>" class="opalhotel-fb-share"><i class="fa fa-facebook-official" aria-hidden="true"></i></a>
			</li>
			<li>
				<a href="<?php echo esc_attr( get_the_permalink() ) ?>" class="opalhotel-google-share"><i class="fa fa-google" aria-hidden="true"></i></a>
			</li>
			<li>
				<a href="<?php echo esc_attr( get_the_permalink() ) ?>" class="opalhotel-twitter-share" data-text="<?php echo esc_attr( get_the_title() ) ?>" data-image="<?php echo esc_attr( get_the_post_thumbnail_url() ) ?>"><i class="fa fa-twitter" aria-hidden="true"></i></a>
			</li>
			<li>
				<a href="<?php echo esc_attr( get_the_permalink() ) ?>" class="opalhotel-pinterest-share" data-text="<?php echo esc_attr( get_the_title() ) ?>" data-image="<?php echo esc_attr( get_the_post_thumbnail_url() ) ?>"><i class="fa fa-pinterest-p" aria-hidden="true"></i></a>
			</li>
		</ul>
	</div>
</div>
