<?php
/**
 * Show messages
 *
 * This template can be overridden by copying it to yourtheme/opalhotel/notices/success.php.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! $messages ){
	return;
}

?>

<?php foreach ( $messages as $message ) : ?>

	<div class="opalhotel-notice-success"><?php echo wp_kses_post( $message ); ?></div>

<?php endforeach; ?>
