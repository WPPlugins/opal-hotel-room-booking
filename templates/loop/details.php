<?php global $opalhotel_room; ?>

<div class="room-meta-info">
	<div class="meta-content">
		<ul class="opalhotel-room-meta">
			<?php if ( $opalhotel_room->bed ) : ?>
				<li class="meta-bed">
					<span class="meta-label"><?php esc_html_e( 'Bed', 'opal-hotel-room-booking' ); ?></span>
					<span class="meta-text"><?php printf( '%s', $opalhotel_room->bed ); ?></span>
				</li>
			<?php endif; ?>

			<?php if ( $opalhotel_room->adults ) : ?>
				<li class="meta-adults">
					<span class="meta-label"><?php esc_html_e( 'People', 'opal-hotel-room-booking' ); ?></span>
					<span class="meta-text"><?php printf( '%s', $opalhotel_room->adults ); ?></span>
				</li>
			<?php endif; ?>

			<?php if ( $opalhotel_room->view ) : ?>
				<li class="meta-view">
					<span class="meta-label"><?php esc_html_e( 'View', 'opal-hotel-room-booking' ); ?></span>
					<span class="meta-text"><?php printf( '%s', $opalhotel_room->view ); ?></span>
				</li>
			<?php endif; ?>

			<?php if ( $opalhotel_room->room_size ) : ?>
				<li class="meta-size">
					<span class="meta-label"><?php esc_html_e( 'Room Size', 'opal-hotel-room-booking' ); ?></span>
					<span class="meta-text"><?php printf( '%s', $opalhotel_room->room_size ); ?></span>
				</li>
			<?php endif; ?>
		</ul>
	</div>
</div>