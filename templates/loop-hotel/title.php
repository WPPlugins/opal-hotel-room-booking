<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

?>

<h4 class="hotel-title">
	<a href="<?php echo esc_attr( get_the_permalink() ) ?>">
		<?php the_title() ?>
	</a>
</h4>