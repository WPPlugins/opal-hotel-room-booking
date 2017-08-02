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

<div class="opalhotel-catalog-thumbnail">
	<div class="room-thumbnail">
		<a href="<?php echo esc_attr( wp_get_attachment_url( get_post_thumbnail_id( $room->id ) ) ); ?>" class="opalhotel-lightbox" rel="opalhotel-fancybox-<?php echo esc_attr( $room->id ) ?>">
			<?php echo opalhotel_room_get_catalog_thumbnail( $room->id ) ?>
		</a>
	</div>
	<?php opalhotel_get_template( 'search/loop/gallery.php', array( 'room' => $room ) ); ?>
</div>