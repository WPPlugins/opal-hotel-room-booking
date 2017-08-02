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

$hotels = opalhotel_get_hotels_available();
$columns = isset( $atts['columns'] ) ? absint( $atts['columns'] ) : null;

?>

<div class="opalhotel-main hotels opalhotel-hotel-available" data-atts="<?php echo esc_attr( maybe_serialize( $atts ) ) ?>">
	<?php if ( $hotels->have_posts() ) : ?>

		<?php
			/**
			 * opalhotel_before_hotel_loop hook.
			 *
			 * @hooked opalhotel_result_count - 20
			 * @hooked opalhotel_catalog_ordering - 30
			 */
			do_action( 'opalhotel_before_hotel_loop' );

			if ( $columns ) {
				global $opalhotel_loop;
				$opalhotel_loop['columns'] = $columns;
			}
		?>

		<div class="<?php echo apply_filters( 'opalhotel_loop_grid_column_class', 'grid-row' ); ?>">
			<?php while ( $hotels->have_posts() ) : $hotels->the_post(); ?>
				<?php opalhotel_get_template_part( 'content-hotel', opalhotel_loop_display_mode() ) ?>
			<?php endwhile; ?>
		</div>

		<?php
			if ( $columns ) {
				$opalhotel_loop['columns'] = null;
			}
			/**
			 * opalhotel_after_hotel_loop hook.
			 *
			 * @hooked opalhotel_archive_pagination - 5
			 */
			do_action( 'opalhotel_after_hotel_loop', $hotels );
		?>

		<?php wp_reset_postdata(); ?>

	<?php else: ?>

		<?php opalhotel_get_template( 'loop-hotel/no-hotel-found.php' ); ?>

	<?php endif; ?>
</div>