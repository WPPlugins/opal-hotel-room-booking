<?php
/**
 * $Desc$
 *
 * @version    1.1.7
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

$query = opalhotel_get_hotels_available();
extract( $atts );
?>

<div class="opalhotel-main-map" data-nonce="<?php echo esc_attr( wp_create_nonce( 'opalhotel-main-map-nonce' ) ) ?>">

	<?php if ( $map ) : ?>
		<?php opalhotel_print_map_hotel( array( 'places' => opalhotel_map_hotels_data( $query ), 'height' => $height ) ); ?>
	<?php endif; ?>

	<div class="grid-row">

		<div class="grid-column-3">
			<?php
				/**
				 * search form
				 */
				opalhotel_get_template( 'search-hotels/form-search-vertical.php' );

				/**
				 * search form
				 */
				opalhotel_get_template( 'search-hotels/filter-hotel-available.php' );
			?>
		</div>

		<div class="grid-column-9">
			<?php OpalHotel_Shortcodes::hotel_available_results( $atts ); ?>
		</div>

	</div>

</div>