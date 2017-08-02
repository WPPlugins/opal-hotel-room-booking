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

$opalhotel_room = $room;
?>
<div class="room-content">
	<!-- title -->
	<?php opalhotel_get_template( 'search/loop/title.php', array( 'room' => $room ) ); ?>

	<div class="room-meta-info">
		<div class="inner">
			<ul class="opalhotel-room-meta">
				<?php if ( $opalhotel_room->adults ) : ?>
					<li class="meta-adults">
						<span class="meta-label"><?php esc_html_e( 'People', 'opal-hotel-room-booking' ); ?></span>
						<span class="meta-text"><?php printf( '%s', $opalhotel_room->adults ); ?></span>
					</li>
				<?php endif; ?>
				<?php if ( $opalhotel_room->room_size ) : ?>
					<li class="meta-size">
						<span class="meta-label"><?php esc_html_e( 'Room Size', 'opal-hotel-room-booking' ); ?></span>
						<span class="meta-text"><?php printf( '%s', $opalhotel_room->room_size ); ?></span>
					</li>
				<?php endif; ?>
				<?php if ( $opalhotel_room->bed ) : ?>
					<li class="meta-bed">
						<span class="meta-label"><?php esc_html_e( 'Bed', 'opal-hotel-room-booking' ); ?></span>
						<span class="meta-text"><?php printf( '%s', $opalhotel_room->bed ); ?></span>
					</li>
				<?php endif; ?>
				<?php if ( $opalhotel_room->view ) : ?>
					<li class="meta-view">
						<span class="meta-label"><?php esc_html_e( 'View', 'opal-hotel-room-booking' ); ?></span>
						<span class="meta-text"><?php printf( '%s', $opalhotel_room->view ); ?></span>
					</li>
				<?php endif; ?>
			</ul>
		</div>
	</div>

	<div class="room-except">
		<?php echo $room->data->post_excerpt;?>
	</div>
	
</div>