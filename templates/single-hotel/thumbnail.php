<?php
/**
 * The template for displaying room content within loops
 *
 * This template can be overridden by copying it to yourtheme/opalhotel/single-room.php.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

global $post;

?>

<?php if ( has_post_thumbnail() ) : ?>
	<div class="preview">
		<?php echo get_the_post_thumbnail( $post->ID, 'full' ); ?>
	</div>
<?php endif; ?>