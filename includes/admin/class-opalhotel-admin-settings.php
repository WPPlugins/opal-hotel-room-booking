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

class OpalHotel_Admin_Settings {

	public function __construct() {

		// update options
		add_action( 'admin_init', array( $this, 'update_options' ) );
	}

	/* store our option in setting page */
	public function update_options() {

		if ( empty( $_POST ) || ! isset( $_POST[ 'option_page' ] ) || $_POST[ 'option_page' ] !== 'opalhotel_' ) {
			return;
		}

		foreach ( $_POST as $name => $value ) {

			// valid option name
			if ( stripos( $name, 'opalhotel_' ) === 0 ) {
				update_option( $name, $value );

				// allow hook to save action
				do_action( 'opalhotel_updated_option_' . $name, $value );
				do_action( 'opalhotel_updated_option', $name, $value );
			}
		}
		// wp_redirect( admin_url( 'admin.php?page=opalhotel-settings' ) );
	}

	/* get page settings */
	public static function get_settings_pages() {

		OpalHotel::instance()->_include( 'admin/class-opalhotel-admin-setting-page.php' );
		$tabs = array();

		// use OpalHotel::instance() return null active hook
		$tabs[] = include 'settings/class-opalhotel-admin-setting-general.php';
		$tabs[] = include 'settings/class-opalhotel-admin-setting-hotel.php';
		$tabs[] = include 'settings/class-opalhotel-admin-setting-room.php';
		$tabs[] = include 'settings/class-opalhotel-admin-setting-tax.php';
		$tabs[] = include 'settings/class-opalhotel-admin-setting-checkout.php';
		$tabs[] = include 'settings/class-opalhotel-admin-setting-email.php';
		$tabs[] = include 'settings/class-opalhotel-admin-setting-comments.php';
		// $tabs[] = include 'settings/class-opalhotel-admin-setting-woocommerce.php';

		return apply_filters( 'opalhotel_admin_setting_pages', $tabs );
	}

	// render field
	public static function render_fields( $fields = array() ) {
		if ( empty( $fields ) ) {
			return;
		}
		foreach ( $fields as $k => $field ) {
			$field = wp_parse_args( $field, array(
					'id'			=> '',
					'class'			=> '',
					'title'			=> '',
					'desc'			=> '',
					'default'		=> '',
					'type'			=> '',
					'placeholder'	=> '',
					'options'		=> '',
					'atts'			=> array()
				));

			$custom_attr = '';
			if ( ! empty( $field['atts'] ) ) {
				foreach ( $field['atts'] as $k => $val ) {
					$custom_attr .= $k . '="' . $val .'"';
				}
			}
			switch ( $field['type'] ) {
				case 'section_start':
						?>
							<?php if ( isset( $field['title'] ) ) : ?>
								<h3><?php echo esc_html( $field['title'] ); ?></h3>
								<?php if ( isset( $field['desc'] ) && $field['desc'] ) : ?>
									<p class="description"><?php echo esc_html( $field['desc'] ) ?></p>
								<?php endif; ?>
								<table class="form-table">
							<?php endif; ?>
						<?php
					break;

				case 'section_end':
						?>
							<?php do_action( 'opalhotel_setting_field_' . $field['id'] . '_end' ); ?>
							</table>
							<?php do_action( 'opalhotel_setting_field_' . $field['id'] . '_after' ); ?>
						<?php
					break;

				case 'select':
				case 'multiselect':
					$selected = get_option( $field['id'], isset( $field['default'] ) ? $field['default'] : array() );
						?>
							<tr valign="top"<?php echo isset( $field['trclass'] ) ? ' class="'.implode( '', $field['trclass'] ).'"' : '' ?>>
								<th scope="row">
									<?php if ( isset( $field['title'] ) ) : ?>
										<label for="<?php echo isset( $field['id'] ) ? esc_attr( $field['id'] ) : '' ?>">
											<?php echo esc_html( $field['title'] ) ?>
										</label>
									<?php endif; ?>
								</th>
								<td class="opalhotel-field opalhotel-field-<?php echo esc_attr( $field['type'] ) ?>">
									<?php if ( isset( $field['options'] ) ) : ?>
										<select name="<?php echo isset( $field['id'] ) ? esc_attr( $field['id'] ) : '' ?><?php echo $field['type'] === 'multiselect' ? '[]' : '' ?>"
											id="<?php echo isset( $field['id'] ) ? esc_attr( $field['id'] ) : '' ?>"
											<?php echo ( $field['type'] === 'multiple' ) ? 'multiple="multiple"' : '' ?>
										>
											<?php foreach ( $field['options'] as $val => $text ) : ?>
												<option value="<?php echo esc_attr( $val ) ?>"
													<?php echo ( is_array( $selected ) && in_array( $val, $selected ) ) || $selected == $val ? ' selected' : '' ?>
												>
													<?php echo esc_html( $text ) ?>
												</option>
											<?php endforeach; ?>
										</select>
									<?php endif; ?>
									<?php if ( isset( $field['desc'] ) && $field['desc'] ) : ?>
										<p class="description"><?php printf( '%s', $field['desc'] ) ?></p>
									<?php endif; ?>
								</td>
							</tr>
						<?php
					break;

				case 'text':
				case 'number':
				case 'email':
				case 'password':
					$value = get_option( $field['id'] );
					if ( $value === false && isset( $field['default'] ) ) {
						$value = $field['default'];
					}
						?>
							<tr valign="top"<?php echo isset( $field['trclass'] ) ? ' class="'.implode( '', $field['trclass'] ).'"' : '' ?>>
								<th scope="row">
									<?php if ( isset( $field['title'] ) ) : ?>
										<label for="<?php echo isset( $field['id'] ) ? esc_attr( $field['id'] ) : '' ?>">
											<?php echo esc_html( $field['title'] ) ?>
										</label>
									<?php endif; ?>
								</th>
								<td class="opalhotel-field opalhotel-field-<?php echo esc_attr( $field['type'] ) ?>">
									<input
										type="<?php echo esc_attr( $field['type'] ) ?>"
										name="<?php echo esc_attr( $field['id'] ) ?>"
										value="<?php echo esc_attr( $value ) ?>"
										class="regular-text"
										placeholder="<?php echo esc_attr( $field['placeholder'] ) ?>"
										<?php if ( $field['type'] === 'number' ) : ?>

											<?php echo isset( $field['min'] ) && is_numeric( $field['min'] ) ? ' min="'.esc_attr( $field['min'] ).'"' : '' ?>
											<?php echo isset( $field['max'] ) && is_numeric( $field['max'] ) ? ' max="'.esc_attr( $field['max'] ).'"' : '' ?>
											<?php echo isset( $field['step'] ) && $field['step'] ? ' step="'.esc_attr( $field['step'] ).'"' : '' ?>

										<?php endif; ?>
									/>
									<?php if ( isset( $field['desc'] ) && $field['desc'] ) : ?>
										<p class="description"><?php printf( '%s', $field['desc'] ) ?></p>
									<?php endif; ?>
								</td>
							</tr>
						<?php
					break;

				case 'checkbox':
						$val = get_option( $field['id'] );
						if ( $val === false && isset( $field['default'] ) ) {
							$val = $field['default'];
						}
						?>
							<tr valign="top"<?php echo isset( $field['trclass'] ) ? ' class="'.implode( '', $field['trclass'] ).'"' : '' ?>>
								<th scope="row">
									<?php if ( isset( $field['title'] ) ) : ?>
										<label for="<?php echo isset( $field['id'] ) ? esc_attr( $field['id'] ) : '' ?>">
											<?php echo esc_html( $field['title'] ) ?>
										</label>
									<?php endif; ?>
								</th>
								<td class="opalhotel-field opalhotel-field-<?php echo esc_attr( $field['type'] ) ?>">
									<input type="hidden" name="<?php echo isset( $field['id'] ) ? esc_attr( $field['id'] ) : '' ?>" value="0"/>
									<input type="checkbox" name="<?php echo isset( $field['id'] ) ? esc_attr( $field['id'] ) : '' ?>" id="<?php echo isset( $field['id'] ) ? esc_attr( $field['id'] ) : '' ?>" value="1" <?php echo $custom_attr ?><?php checked( $val, 1 ); ?>/>
									<?php if ( isset( $field['sub_desc'] ) && $field['sub_desc'] ) : ?>
										<?php echo esc_html( $field['sub_desc'] ); ?>
									<?php endif; ?>
									<?php if ( isset( $field['desc'] ) && $field['desc'] ) : ?>
										<p class="description"><?php printf( '%s', $field['desc'] ) ?></p>
									<?php endif; ?>
								</td>
							</tr>
						<?php
					break;

				case 'radio':
						$selected = get_option( $field['id'], isset( $field['default'] ) ? $field['default'] : '' );
						?>
							<tr valign="top">
								<th scope="row">
									<?php if ( isset( $field['title'] ) ) : ?>
										<label for="<?php echo isset( $field['id'] ) ? esc_attr( $field['id'] ) : '' ?>">
											<?php echo esc_html( $field['title'] ) ?>
										</label>
									<?php endif; ?>
								</th>
								<td class="opalhotel-field opalhotel-field-<?php echo esc_attr( $field['type'] ) ?>">
									<?php if ( isset( $field['options'] ) ) : ?>
										<?php foreach ( $field['options'] as $val => $text ) : ?>

											<label>
												<input type="radio" name="<?php echo isset( $field['id'] ) ? esc_attr( $field['id'] ) : '' ?>"<?php selected( $selected, $val ); ?>/>
												<?php echo esc_html( $text ) ?>
											</label>

										<?php endforeach; ?>
									<?php endif; ?>
									<?php if ( isset( $field['desc'] ) && $field['desc'] ) : ?>
										<p class="description"><?php printf( '%s', $field['desc'] ) ?></p>
									<?php endif; ?>
								</td>
							</tr>
						<?php
					break;

				case 'image_size':
						$width = get_option( $field['id'] . '_width', isset( $field['default']['width'] ) ? $field['default']['width'] : 270 );
						$height = get_option( $field['id'] . '_height', isset( $field['default']['height'] ) ? $field['default']['height'] : 270 );
						?>
							<tr valign="top">
								<th scope="row">
									<?php if ( isset( $field['title'] ) ) : ?>
										<label for="<?php echo isset( $field['id'] ) ? esc_attr( $field['id'] ) : '' ?>">
											<?php echo esc_html( $field['title'] ) ?>
										</label>
									<?php endif; ?>
								</th>
								<td class="opalhotel-field opalhotel-field-<?php echo esc_attr( $field['type'] ) ?>">
									<?php if ( isset( $field['id'] ) && isset( $field['options'] ) ) : ?>

										<?php if ( isset( $field['options']['width'] ) ) : ?>
												<input
													type="number"
													name="<?php echo esc_attr( $field['id'] ) ?>_width"
													value="<?php echo esc_attr( $width ) ?>"
													min="0"
												/> x
										<?php endif; ?>
										<?php if ( isset( $field['options']['height'] ) ) : ?>
												<input
													type="number"
													name="<?php echo esc_attr( $field['id'] ) ?>_height"
													value="<?php echo esc_attr( $height ) ?>"
													min="0"
												/> px
										<?php endif; ?>
									<?php endif; ?>
									<?php if ( isset( $field['desc'] ) && $field['desc'] ) : ?>
										<p class="description"><?php printf( '%s', $field['desc'] ) ?></p>
									<?php endif; ?>
								</td>
							</tr>
						<?php
					break;

				case 'textarea':
					$content = get_option( $field['id'] );
						?>
							<tr valign="top">
								<th scope="row">
									<?php if ( isset( $field['title'] ) ) : ?>
										<label for="<?php echo isset( $field['id'] ) ? esc_attr( $field['id'] ) : '' ?>">
											<?php echo esc_html( $field['title'] ) ?>
										</label>
									<?php endif; ?>
								</th>
								<td class="opalhotel-field opalhotel-field-<?php echo esc_attr( $field['type'] ) ?>">
									<?php if ( isset( $field['id'] ) ) : ?>
										<?php wp_editor( $content, $field['id'], isset( $field['options'] ) ? $field['options'] : array() ); ?>
									<?php endif; ?>
									<?php if ( isset( $field['desc'] ) && $field['desc'] ) : ?>
										<p class="description"><?php printf( '%s', $field['desc'] ) ?></p>
									<?php endif; ?>
								</td>
							</tr>
						<?php
					break;

				case 'select_page':
					$selected = get_option( $field['id'], 0 );
					$page_id = opalhotel_get_page_id( str_replace( 'opalhotel_', '', str_replace( '_page_id', '', $field['id'] ) ) );
						?>
							<tr valign="top">
								<th scope="row">
									<?php if ( isset( $field['title'] ) ) : ?>
										<label for="<?php echo isset( $field['id'] ) ? esc_attr( $field['id'] ) : '' ?>">
											<?php echo esc_html( $field['title'] ) ?>
										</label>
									<?php endif; ?>
									<?php if ( isset( $field['desc'] ) && $field['desc'] ) : ?>
										<span class="opalhotel_tiptip" data-tip="<?php echo esc_attr( $field['desc'] ) ?>"><i class="fa fa-question-circle-o"></i></span>
									<?php endif; ?>
								</th>
								<td class="opalhotel-field opalhotel-field-<?php echo esc_attr( $field['type'] ) ?>">
									<?php if ( isset( $field['id'] ) ) : ?>
										<?php
											wp_dropdown_pages(
							                    array(
							                        'show_option_none'  => __( '---Select page---', 'opal-hotel-room-booking' ),
							                        'option_none_value' => 0,
							                        'name'      => $field['id'],
							                        'selected'  => $selected
							                    )
							                );
										?>
										<?php if ( $page_id ) : ?>
											<a href="<?php echo esc_url( get_edit_post_link( $page_id ) ); ?>"><?php esc_html_e( 'Edit page', 'opal-hotel-room-booking' ); ?></a>
											<a href="<?php echo esc_url( get_permalink( $page_id ) ); ?>"><?php esc_html_e( 'View page', 'opal-hotel-room-booking' ); ?></a>
										<?php endif; ?>
									<?php endif; ?>
									<?php if ( isset( $field['desc'] ) && $field['desc'] ) : ?>
										<p class="description"><?php printf( '%s', $field['desc'] ) ?></p>
									<?php endif; ?>
								</td>
							</tr>
						<?php
					break;

				default:
					do_action( 'opalhotel_setting_field_' . $field['id'], $field );
					break;
			}
		}
	}

	// save field settings
	public static function save_fields( $options = array() ) {
		if ( empty( $options ) ) {
			return;
		}
	}

	// output page settings
	public static function output() {

		// retrive options
		self::get_settings_pages();

		$tabs = opalhotel_admin_settings_tabs();
		$selected_tab = ! empty( $_REQUEST['tab'] ) ? sanitize_text_field( $_REQUEST['tab'] ) : '';
		$section_tab = ! empty( $_REQUEST['section'] ) ? sanitize_text_field( $_REQUEST['section'] ) : '';

		if( ! array_key_exists( $selected_tab, $tabs ) ){
		    $tab_keys = array_keys( $tabs );
		    $selected_tab = reset( $tab_keys );
		}

		?>
			<div class="wrap">

			    <h2 class="nav-tab-wrapper">
			        <?php if( $tabs ) :
				        foreach( $tabs as $slug => $title) { ?>
				            <a class="nav-tab<?php echo sprintf( '%s', $selected_tab == $slug ? ' nav-tab-active' : '' ); ?>" href="?page=opalhotel-settings&tab=<?php echo esc_attr( $slug ); ?>">
				            	<?php echo esc_html( $title ); ?>
				            </a>
			        <?php } endif; ?>
			    </h2>

			    <?php opalhotel_print_admin_notices(); ?>

			    <form method="post" action="options.php" enctype="multipart/form-data" name="opalhotel-admin-settings-form">
			    	<?php settings_fields( OPALHOTEL_SETTING_GROUP_NAME ); ?>
			        <?php do_action( 'opalhotel_admin_settings_tab_before', $selected_tab, $section_tab ); ?>

			        <!-- setting_sections subtab -->
			        <?php do_action( 'opalhotel_admin_settings_sections_' . $selected_tab, $section_tab ); ?>

			        <!-- main setting -->
			        <?php do_action( 'opalhotel_admin_settings_tab_' . $selected_tab, $section_tab ); ?>

			        <!-- nonce -->
			        <?php wp_nonce_field( 'opalhotel_admin_settings_tab_' . $selected_tab, 'opalhotel_admin_settings_tab_' . $selected_tab . '_field' ); ?>
			        <?php do_action( 'opalhotel_admin_settings_tab_after', $selected_tab, $section_tab ); ?>

			        <div class="clearfix"></div>
			        <p class="clearfix">
			            <?php submit_button( __( 'Save Changes', 'opal-hotel-room-booking' ) ); ?>
			        </p>

			    </form>
			</div>
		<?php
	}

}

new OpalHotel_Admin_Settings();
