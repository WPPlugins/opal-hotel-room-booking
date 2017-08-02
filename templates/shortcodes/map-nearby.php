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

$id = uniqid();

$query = opalhotel_get_hotels_query();

?>

<div class="grid-row">

	<!-- Hotel Items -->
	<div class="grid-column-5">
		<?php  ?>
	</div>

	<!-- Google Maps -->
	<div class="grid-column-7">
		<?php
			opalhotel_print_map_hotel( array(
					'width'		=> $width,
					'height'	=> $height,
					'center'	=> $center,
					'zoom'		=> $zoom
				) );
		?>
	</div>

</div>