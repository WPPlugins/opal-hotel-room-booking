<?php
/**
 * The template for displaying room content within loops
 *
 * This template can be overridden by copying it to yourtheme/opalhotel/loop-hotel/descriptions.php.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

global $post;
$hotel = opalhotel_get_hotel( $post->ID );
$galleries = $hotel->gallery;
$video = $hotel->video;
$uniqid = 'prettyPhoto-' . $post->ID;
?>

<ul class="opalhotel-hotel-actions">
	<li>
		<a href="<?php echo esc_url( wp_login_url() ) ?>" data-id="<?php echo esc_attr( $post->ID ) ?>" data-nonce="<?php echo wp_create_nonce( 'opalhotel-toggle-nonce' ); ?>" class="opalhotel-pre-add-wishlist <?php echo esc_attr( is_user_logged_in() ? 'opalhotel-toggle-favorite' : 'opalhotel-need-login' ) ?>">
			<?php if ( ! is_user_logged_in() || ! opalhotel_is_favorited( $post->ID ) ) : ?>
				<i class="fa fa-heart-o"></i>
			<?php else: ?>
				<i class="fa fa-heart"></i>
			<?php endif; ?>
		</a>
	</li>
	<?php if ( $galleries ) : ?>
	<li>
		<a href="<?php echo esc_url( get_the_post_thumbnail_url( $post->ID, 'full' ) ) ?>" class="gallery view-gallery" data-id="<?php echo esc_attr( get_the_ID() ) ?>" <?php printf( '%s', $galleries ? 'usemap=#' . $uniqid : '' ) ?>></a>
		<map name="<?php echo esc_attr( $uniqid ) ?>">
			<?php foreach ( $galleries as $id ) : $attach = wp_get_attachment_image_src( $id, 'full' ); ?>
				<area shape="rect" coords="6,11,72,73" href="<?php echo esc_url( isset( $attach[0] ) ? $attach[0] : ''  ) ?>" rel="prettyPhoto[<?php echo esc_attr( $uniqid ) ?>]">
			<?php endforeach; ?>
		</map>
	</li>
	<?php endif; ?>
	<?php if ( $video ) : ?>
		<li>
			<a href="<?php echo esc_url( $video ) ?>" class="video view-video" rel="prettyPhoto" data-id="<?php echo esc_attr( get_the_ID() ) ?>"></a>
		</li>
	<?php endif; ?>
</ul>