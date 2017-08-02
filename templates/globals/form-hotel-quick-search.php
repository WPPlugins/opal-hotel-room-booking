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

?>

<form class="opalhotel-quick-search-form grid-row" action="" method="POST">

	<div class="grid-column-4">
		<input type="text" name="keyword" class="opalhotel-input-text" value="<?php echo esc_attr( isset( $_REQUEST['keyword'] ) ? sanitize_text_field( $_REQUEST['keyword'] ) : '' ) ?>" placeholder="<?php esc_attr_e( 'Keywords', 'opal-hotel-room-booking' ); ?>" />
	</div>
	<div class="grid-column-4">
		<input type="text" name="location" class="opalhotel-input-text" value="<?php echo esc_attr( isset( $_REQUEST['location'] ) ? sanitize_text_field( $_REQUEST['location'] ) : '' ) ?>" placeholder="<?php esc_attr_e( 'Enter a location', 'opal-hotel-room-booking' ); ?>" />
		<span class="location-icon">
			<i class="fa fa-gear"></i>
		</span>
	</div>
	<div class="grid-column-3">
		<?php
			wp_dropdown_categories( array(
					'taxonomy' 				=> 'opalhotel_hotel_cat',
					'show_option_none'   	=> esc_html( 'Select category', 'opal-hotel-room-booking' ),
				) )
		?>
	</div>
	<div class="grid-column-1">
		<button class="btn btn-primary btn-submit" type="submit"><i class="fa fa-search"></i></button>
	</div>

</form>