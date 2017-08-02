<?php
/**
 * $Desc$
 *
 * @version    $Id$
 * @package    opalhotel
 * @author     Opal  Team <info@wpopal.com >
 * @copyright  Copyright (C) 2016 wpopal.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @website  http://www.wpopal.com
 * @support  http://www.wpopal.com/support/forum.html
 */
 
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

add_filter( 'body_class', function( $classes ){
	$classes[] = 'opalhotel-main-map';
	return $classes;
} );

get_header(); ?>

	<?php
		// print map
		opalhotel_print_map_hotel();

		/**
		 * opalhotel_before_main_content hook.
		 *
		 * @hooked opalhotel_output_content_wrapper - 10 (outputs opening divs for the content)
		 * @hooked opalhotel_breadcrumb - 20
		 */
		do_action( 'opalhotel_before_main_content' );
	?>

	<div class="opalhotel-wrapper opalhotel-hotels">

		<div class="opalhotel-main-wrapper">
			<?php

				opalhotel_get_template( 'search-hotels/form-search-horizontal.php' );

				/**
				 * opalhotel_archive_description hook.
				 *
				 */
				do_action( 'opalhotel_archive_description' );
			?>

			<?php
				/**
				 * Get hotels results template
				 */
				opalhotel_get_template( 'search-hotels/results.php', array( 'atts' => array(
						'columns'	=> opalhotel_loop_columns()
					) ) );
			?>

			<?php
				/**
				 * opalhotel_after_main_content hook.
				 *
				 * @hooked opalhotel_output_content_wrapper_end - 10 (outputs closing divs for the content)
				 */
				do_action( 'opalhotel_after_main_content' );
			?>
		</div>

	</div>
<?php //get_sidebar(); ?>

<?php get_footer(); ?>