<?php
/**
 * The template for displaying room content within loops
 *
 * This template can be overridden by copying it to yourtheme/opalhotel/notices/error.php.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

if ( ! $messages ) {
	return;
}
?>

<div class="opalhotel-error-messages">

	<?php foreach ( $messages as $messagge ) : ?>

		<div class="opalhotel-notice-error"><?php echo wp_kses_post( $messagge ) ?></div>

	<?php endforeach; ?>

</div>
