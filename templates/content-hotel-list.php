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

?>

<div <?php post_class( 'opalhotel-hotel grid-column-12' ) ?>>
	<div class="hotel-list">
		<div class="media">
			<?php
				/**
				 * opalhotel_archive_loop_item_thumbnail hook.
				 * opalhotel_hotel_loop_item_thumbnail - 5
				 */
				do_action( 'opalhotel_hotel_loop_item_thumbnail' );
			?>
			<?php
				/**
				 * opalhotel_hotel_loop_item_labels hook.
				 * opalhotel_hotel_loop_item_labels - 5
				 */
				do_action( 'opalhotel_hotel_loop_item_labels' );
			?>
			<?php
				/**
				 * opalhotel_hotel_loop_item_rating hook.
				 * opalhotel_hotel_loop_item_rating - 5
				 */
				do_action( 'opalhotel_hotel_loop_item_rating' );
			?>
			<?php
				/**
				 * opalhotel_hotel_loop_item_actions hook.
				 * opalhotel_hotel_loop_item_actions - 5
				 */
				do_action( 'opalhotel_hotel_loop_item_actions' );
			?>
		</div>
		<div class="hotel-content">
			<div class="entry pull-left">
				<?php
					/**
					 * opalhotel_hotel_loop_item_title hook.
					 * opalhotel_hotel_loop_item_title - 5
					 */
					do_action( 'opalhotel_hotel_loop_item_title' );
				?>
				<?php
					/**
					 * opalhotel_hotel_loop_item_address hook.
					 * opalhotel_hotel_loop_item_address - 5
					 */
					do_action( 'opalhotel_hotel_loop_item_address' );
				?>
				<?php
					/**
					 * opalhotel_hotel_loop_item_includes hook.
					 * opalhotel_hotel_loop_item_includes - 5
					 */
					do_action( 'opalhotel_hotel_loop_item_includes' );
				?>
			</div>
			<div class="meta pull-left">
				<?php
					/**
					 * opalhotel_hotel_loop_item_price hook.
					 * opalhotel_hotel_loop_item_price - 5
					 */
					do_action( 'opalhotel_hotel_loop_item_price' );
					/**
					 * opalhotel_hotel_loop_item_book_button hook.
					 * opalhotel_hotel_loop_item_book_button - 5
					 */
					do_action( 'opalhotel_hotel_loop_item_book_button' );
				?>
			</div>
		</div>
	</div>
</div>