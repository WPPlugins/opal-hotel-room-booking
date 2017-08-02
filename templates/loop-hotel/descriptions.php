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

?>

<div class="hotel-description">
	<?php the_excerpt(); ?>
</div>
