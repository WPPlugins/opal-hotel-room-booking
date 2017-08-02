<?php
/**
 * @Author: brainos
 * @Date:   2016-04-24 20:12:41
 * @Last Modified by:   someone
 * @Last Modified time: 2016-05-02 10:47:29
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

class OpalHotel_MetaBox_Room_Gallery {

	/* render */
	public static function render( $post ) {
		?>
		<div id="opalhotel_room_images_container">
			<ul class="opalhotel_room_images opalhotel_sortable_container">
				<?php
					$attachments = get_post_meta( $post->ID, '_gallery', true );

					$update_meta = false;

					if ( ! empty( $attachments ) ) {
						foreach ( $attachments as $attachment_id ) {
							$attachment = wp_get_attachment_image( $attachment_id, 'thumbnail' );

							// if attachment is empty skip
							if ( empty( $attachment ) ) {
								$update_meta = true;

								continue;
							}

							echo '<li class="image" data-attachment_id="' . esc_attr( $attachment_id ) . '">
								' . $attachment . '
								<ul class="actions">
									<li>
										<a href="#" class="delete tips" data-tip="' . esc_attr__( 'Delete image', 'opal-hotel-room-booking' ) . '"><i class="fa fa-times" aria-hidden="true"></i></a>
									</li>
								</ul>
								<input type="hidden" name="_gallery[]" value="' . esc_attr( $attachment_id ) . '" />
							</li>';
						}
					}
				?>
			</ul>

		</div>
		<p class="opalhotel_add_room_images hide-if-no-js">
			<a class="opalhotel_add_gallery" href="#" data-tip="<?php esc_attr_e( 'Click to select and insert more gallery image.', 'opal-hotel-room-booking' ); ?>">
				<?php wp_nonce_field( 'opalhotel_save_data', 'opalhotel_meta_nonce' ); ?>
				<?php esc_html_e( 'Add room gallery images', 'opal-hotel-room-booking' ); ?>
			</a>
		</p>
		<?php
	}

	/* save post meta*/
	public static function save( $post_id, $post ) {
		if ( ! isset( $_POST['_gallery'] ) ) {
			/* delete post meta */
			delete_post_meta( $post_id, '_gallery' );
		} else {
			update_post_meta( $post_id, '_gallery', $_POST['_gallery'] );
		}

	}

}