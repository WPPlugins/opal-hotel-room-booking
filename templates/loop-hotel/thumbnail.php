<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

?>

<div class="opalhotel-catalog-thumbnail hotel-thumbnail zoom-2">
	<?php if ( has_post_thumbnail() ) : ?>
		<a href="<?php echo esc_attr( get_the_permalink() ); ?>">
			<?php the_post_thumbnail( 'hotel_catalog' ); ?>
		</a>
	<?php endif; ?>
</div>