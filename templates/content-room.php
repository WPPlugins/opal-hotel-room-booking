<?php
/**
 * The template for displaying room content within loops
 *
 * This template can be overridden by copying it to yourtheme/opalhotel/content-room.php.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

opalhotel_get_template_part( 'content-room', 'grid' );