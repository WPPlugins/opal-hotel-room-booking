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

class OpalHotel_MetaBox_Coupon_Data {

	/* render */
	public static function render( $post ) {
		$coupon = OpalHotel_Coupon::instance( $post );
		/* filter */
		$tab_panels = apply_filters( 'opalhotel_coupon_data_tab', array(
				'general' => array(
					'label'  => __( 'General', 'opal-hotel-room-booking' ),
					'target' 	=> 'general_coupon_data',
					'icon'		=> 'fa fa-cog'
				),
				'restriction' => array(
					'label'  	=> __( 'Usage Restriction', 'opal-hotel-room-booking' ),
					'target' 	=> 'restriction_conpon_data',
					'icon'		=> 'fa fa-adjust'
				),
				'limit' => array(
					'label'  	=> __( 'Usage Limit', 'opal-hotel-room-booking' ),
					'target' 	=> 'limit_coupon_data',
					'icon'		=> 'fa fa-ban'
				)
		) );

		?>
		<div id="opalhotel_coupon_data_container" class="opalhotel_metabox_data_container">
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

				<?php do_action( 'opalhotel_coupon_data_before_tab_panel', $key, $tab ); ?>

				<?php if ( $key === 'general' ) : ?>

					<div id="general_coupon_data" class="opalhotel_room_data_panel active">

						<!-- discount type -->
						<div class="opalhotel_field_group">
							<label for="discount_type"><?php esc_html_e( 'Discount type', 'opal-hotel-room-booking' ); ?></label>
							<?php $types = opalhotel_coupon_discount_type_label(); ?>
							<select name="_coupon_type" id="discount_type">
								<?php foreach ( $types as $type => $text ) : ?>
									<option value="<?php echo esc_attr( $type ) ?>"<?php selected( $coupon->coupon_type, $type ); ?>><?php echo esc_html( $text ) ?></option>
								<?php endforeach; ?>
							</select>
							<span class="opalhotel_tiptip" data-tip="<?php esc_attr_e( 'Set type of discount coupon.', 'opal-hotel-room-booking' ); ?>"><i class="fa fa-question-circle-o"></i></span>
							<?php do_action( 'opalhotel_copon_data_discount_type' ); ?>
						</div>

						<!-- discount amount -->
						<div class="opalhotel_field_group">
							<label for="discount_amount"><?php esc_html_e( 'Amount', 'opal-hotel-room-booking' ); ?></label>
							<input type="number" step="any" min="0" name="_coupon_amount" id="discount_amount" value="<?php echo esc_attr( floatval( $coupon->coupon_amount ) ) ?>" />
							<span class="opalhotel_tiptip" data-tip="<?php esc_attr_e( 'Set discount value coupon.', 'opal-hotel-room-booking' ); ?>"><i class="fa fa-question-circle-o"></i></span>
							<?php do_action( 'opalhotel_room_data_coupon_amount' ); ?>
						</div>

						<!-- discount expiry -->
						<div class="opalhotel_field_group">
							<label for="_discount_expire"><?php esc_html_e( 'Expiry', 'opal-hotel-room-booking' ); ?></label>
							<input type="text" name="_coupon_expire" class="opalhotel-has-datepicker" id="_discount_expire" value="<?php echo esc_attr( opalhotel_format_date( $coupon->coupon_expire_timestamp ) ) ?>" />
							<span class="opalhotel_tiptip" data-tip="<?php esc_attr_e( 'Set discount expiry coupon.', 'opal-hotel-room-booking' ); ?>"><i class="fa fa-question-circle-o"></i></span>
							<?php do_action( 'opalhotel_room_data_coupon_expiry' ); ?>
						</div>
					</div>

				<?php elseif ( $key === 'restriction' ) : ?>

					<div id="restriction_conpon_data" class="opalhotel_room_data_panel">

						<!-- discount minimum -->
						<div class="opalhotel_field_group">
							<label for="discount_minimum_spend"><?php esc_html_e( 'Minimum spend', 'opal-hotel-room-booking' ); ?></label>
							<input type="number" step="any" min="0" name="_coupon_minimum_spend" id="discount_minimum_spend" placeholder="<?php esc_attr_e( 'No minimum', 'opal-hotel-room-booking' ); ?>" value="<?php echo esc_attr( $coupon->coupon_minimum_spend ) ?>" />
							<span class="opalhotel_tiptip" data-tip="<?php esc_attr_e( 'Set minimum spend coupon.', 'opal-hotel-room-booking' ); ?>"><i class="fa fa-question-circle-o"></i></span>
							<?php do_action( 'opalhotel_room_data_coupon_expiry' ); ?>
						</div>

						<!-- discount maximum -->
						<div class="opalhotel_field_group">
							<label for="discount_maximum_spend"><?php esc_html_e( 'Maximum spend', 'opal-hotel-room-booking' ); ?></label>
							<input type="number" step="any" min="0" name="_coupon_maximum_spend" id="discount_maximum_spend" placeholder="<?php esc_attr_e( 'No maximum', 'opal-hotel-room-booking' ); ?>" value="<?php echo esc_attr( $coupon->coupon_maximum_spend ) ?>" />
							<span class="opalhotel_tiptip" data-tip="<?php esc_attr_e( 'Set maximum spend coupon.', 'opal-hotel-room-booking' ); ?>"><i class="fa fa-question-circle-o"></i></span>
							<?php do_action( 'opalhotel_room_data_coupon_spend' ); ?>
						</div>

					</div>

				<?php elseif ( $key === 'limit' ) : ?>
					<!-- limit tab content -->
					<div id="limit_coupon_data" class="opalhotel_room_data_panel">

						<!-- discount maximum -->
						<div class="opalhotel_field_group">
							<label for="useage_time_limit"><?php esc_html_e( 'Useage time limit', 'opal-hotel-room-booking' ); ?></label>
							<input type="number" step="any" min="0" name="_coupon_usage_time_limit" id="useage_time" placeholder="<?php esc_attr_e( 'No limit', 'opal-hotel-room-booking' ); ?>" value="<?php echo esc_attr( $coupon->coupon_usage_time_limit ) ?>" />
							<span class="opalhotel_tiptip" data-tip="<?php esc_attr_e( 'Set maximum time use this coupon.', 'opal-hotel-room-booking' ); ?>"><i class="fa fa-question-circle-o"></i></span>
							<?php do_action( 'opalhotel_room_data_coupon_useage_tim' ); ?>
						</div>

						<!-- discount maximum -->
						<div class="opalhotel_field_group">
							<label for="useage_time"><?php esc_html_e( 'Useaged time', 'opal-hotel-room-booking' ); ?></label>
							<label><?php echo esc_html( absint( opalhotel_coupon_useaged( $coupon->id ) ) ); ?></label>
							<span class="opalhotel_tiptip" data-tip="<?php esc_attr_e( 'How many time usaged coupon.', 'opal-hotel-room-booking' ); ?>"><i class="fa fa-question-circle-o"></i></span>
							<?php do_action( 'opalhotel_room_data_coupon_useage_tim' ); ?>
						</div>

					</div>

				<?php else : ?>

					<?php do_action( 'opalhotel_coupon_data_tab_panel', $key, $tab ); ?>

				<?php endif; ?>

				<?php do_action( 'opalhotel_coupon_data_after_tab_panel', $key, $tab ); ?>

			<?php endforeach; ?>
		</div>

		<?php
	}

	/* save post meta*/
	public static function save( $post_id, $post ) {
		if ( $post->post_type !== 'opalhotel_coupon' || empty( $_POST ) ) {
			return;
		}

		/* each save meta has prefix _coupon */
		foreach ( $_POST as $name => $value ) {
			if ( strpos( $name, '_coupon' ) === 0 ) {
				update_post_meta( $post_id, sanitize_key( $name ), $value );
				if ( $name === '_coupon_expire_datetime' ) {
					update_post_meta( $post_id, '_coupon_expire_timestamp', strtotime( $value ) );
				}
				do_action( 'opalhotel_coupon_update_post_meta', $name, $value, $post_id );
			}
		}

	}

}