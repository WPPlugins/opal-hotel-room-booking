<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

?>
<h3 class="room-title">
	<a href="<?php echo esc_attr( get_the_permalink() ) ?>">
		<?php the_title() ?>
	</a>
</h3>