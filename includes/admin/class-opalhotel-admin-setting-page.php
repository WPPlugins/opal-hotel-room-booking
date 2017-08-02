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

abstract class OpalHotel_Admin_Setting_Page {
	protected $id = null;

	protected $title = null;

	function __construct() {

		add_filter( 'opalhotel_admin_settings_tabs', array( $this, 'setting_tabs' ) );
		add_action( 'opalhotel_admin_settings_sections_' . $this->id, array( $this, 'setting_sections' ) );
		add_action( 'opalhotel_admin_settings_tab_' . $this->id, array( $this, 'output' ) );
	}

	/**
	 * get_settings field
	 * @return array settings fields
	 */
	public function get_settings() {
		return apply_filters( 'opalhotel_admin_setting_fields_' . $this->id, array() );
	}

	public function get_sections() {
		return apply_filters( 'opalhotel_admin_setting_sections_' . $this->id, array() );
	}

	// filter tab id
	public function setting_tabs( $tabs ) {
		$tabs[ $this->id ] = $this->title;
		return $tabs;
	}

	// output setting page
	public function output() {
		$settings = $this->get_settings();
		OpalHotel_Admin_Settings::render_fields( $settings );
	}

	// filter section in tab id
	public function setting_sections() {
		$sections = $this->get_sections();

		if ( count( $sections ) === 1 ) {
			return;
		}

		$current_section = $current_section = current( array_keys( $sections ) );

		if ( isset( $_REQUEST['section'] ) ) {
			$current_section = sanitize_text_field( $_REQUEST['section'] );
		}

		$html = array();

		$html[] = '<p style="clear: both"></p>';
		$html[] = '<ul class="opalhotel-section-tab subsubsub">';
		$sub = array();
		foreach( $sections as $id => $text ) {
			$sub[] = '<li>
						<a href="?page=opalhotel-settings&tab=' . $this->id . '&section=' . $id . '"'. ( $current_section == $id ? ' class="current"' : '' ) .'>'.esc_html( $text ).'</a>
					</li>';
		}
		$html[] = implode( '&nbsp;|&nbsp;', $sub );
		$html[] = '</ul><br />';

		echo implode( '', $html );
	}

	// save setting option
	public function save() {
		$settings = $this->get_settings();
		OpalHotel_Admin_Settings::save_fields( $settings );
	}
}