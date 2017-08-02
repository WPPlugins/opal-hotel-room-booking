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


class OpalHotel_MetaBox_Package_Data {

	/* render */
	public static function render( $post ) {
		$package = OpalHotel_Package::instance( $post );
		/* filter */
		$tab_panels = apply_filters( 'opalhotel_package_data_tab', array(
				'general' => array(
					'label'  => __( 'General', 'opal-hotel-room-booking' ),
					'target' 	=> 'general_package_data',
					'icon'		=> 'fa fa-cog'
				),
				'description' => array(
					'label'  	=> __( 'Description', 'opal-hotel-room-booking' ),
					'target' 	=> 'description_package_data',
					'icon'		=> 'fa fa-file-text-o'
				)
		) );

		?>
		<div id="opalhotel_package_data_container" class="opalhotel_metabox_data_container">
		<?php wp_nonce_field( 'opalhotel_save_data', 'opalhotel_meta_nonce' ); ?>
			<ul class="opalhotel_metabox_data_tabs">
				<?php $i = 0; foreach ( $tab_panels as $key => $tab ) : ?>

					<li class="opalhotel_tab <?php echo esc_attr( $key ) ?>">
						<a href="#<?php echo esc_attr( $tab['target'] ) ?>" class="<?php echo $i === 0 ? ' active' : '' ?>">
							<?php if ( isset( $tab['icon'] ) ) : ?>
								<i class="<?php echo esc_attr( $tab['icon'] ) ?>"></i>
							<?php endif; ?>
							<?php echo esc_html( $tab['label'] ); ?>
						</a>
					</li>

				<?php $i++; endforeach; ?>
			</ul>

			<?php foreach ( $tab_panels as $key => $tab ) : ?>

				<?php do_action( 'opalhotel_room_data_before_tab_panel', $key, $tab ); ?>

				<?php if ( $key === 'general' ) : ?>

					<div id="general_package_data" class="opalhotel_room_data_panel active">

						<!-- package amount -->
						<div class="opalhotel_field_group">
							<label for="package_amount"><?php printf( __( 'Package amount (%s)', 'opal-hotel-room-booking' ), opalhotel_get_currency_symbol() ); ?></label>
							<input type="number" step="any" min="0" name="_package_amount" id="package_amount" placeholder="<?php esc_attr_e( 'Free', 'opal-hotel-room-booking' ); ?>" value="<?php echo esc_attr( floatval( $package->package_amount ) ) ?>" />
							<span class="opalhotel_tiptip" data-tip="<?php esc_attr_e( 'Set amount of this package.', 'opal-hotel-room-booking' ); ?>"><i class="fa fa-question-circle-o"></i></span>
							<?php do_action( 'opalhotel_package_amount' ); ?>
						</div>

						<!-- package type -->
						<div class="opalhotel_field_group">
							<label for="package_type"><?php esc_html_e( 'Package type', 'opal-hotel-room-booking' ); ?></label>
							<?php $package_types = opalhotel_package_types(); ?>
							<select name="_package_type" id="package_type">
								<?php foreach ( $package_types as $type => $label ) : ?>
									<option value="<?php echo esc_attr( $type ) ?>"<?php selected( $package->package_type, $type ); ?>><?php printf( '%s', $label ); ?></option>
								<?php endforeach; ?>
							</select>
							<span class="opalhotel_tiptip" data-tip="<?php esc_attr_e( 'Set type of package. It will change base price of room.', 'opal-hotel-room-booking' ); ?>"><i class="fa fa-question-circle-o"></i></span>
							<?php do_action( 'opalhotel_package_type' ); ?>
						</div>
					</div>

				<?php elseif ( $key === 'description' ) : ?>

					<div id="description_package_data" class="opalhotel_room_data_panel">

						<!-- wp editor description -->
						<div class="opalhotel_field_group">
							<?php wp_editor( $package->post_content, 'content' ); ?>
						</div>
					</div>

				<?php else : ?>

					<?php do_action( 'opalhotel_package_data_tab_panel', $key, $tab ); ?>

				<?php endif; ?>

			<?php endforeach; ?>
		</div>

		<?php
	}

	/* save post meta*/
	public static function save( $post_id, $post ) {
		if ( $post->post_type !== 'opalhotel_package' || empty( $_POST ) ) {
			return;
		}

		/* each save meta has prefix _package */
		foreach ( $_POST as $name => $value ) {
			if ( strpos( $name, '_package' ) === 0 ) {
				update_post_meta( $post_id, sanitize_key( $name ), $value );
				do_action( 'opalhotel_package_update_post_meta', $name, $value, $post_id );
			}
		}

	}

}