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

// if not existed class OpalHotel_Autoload
if ( ! class_exists( 'OpalHotel_Autoload' ) ) {


	class OpalHotel_Autoload {

		/* includes path */
		protected $path = null;

		public function __construct() {

			$this->path = OPALHOTEL_INC_PATH;

			if ( function_exists( "__autoload" ) ) {
	            spl_autoload_register( "__autoload" );
	        }

	        spl_autoload_register( array( $this, 'autoload' ) );

		}

		/* generate file name from class name */
		protected function get_file_name( $classname = null ) {

			if ( ! $classname ) {
				return;
			}

			return 'class-' . str_replace( '_', '-', strtolower( $classname ) ) . '.php' ;

		}

		/* include single file */
		protected function _include( $file ) {

			if ( file_exists( $file ) && is_readable( $file ) ) {
				require_once $file;
			}

		}

		/* autoload process */
		public function autoload( $classname ) {

			// generate file name from class name
			$file = $this->get_file_name( $classname );
			$path = $this->path;

			// acstract
			if ( strpos( $classname, 'OpalHotel_Abstract_' ) === 0 ) {
				$file = 'abstracts/' . $file;
			}

			// widgets
			if ( strpos( $classname, 'OpalHotel_Widget_' ) === 0 ) {
				$file = 'widgets/' . $file;
			}

			// shortcodes
			if ( strpos( $classname, 'OpalHotel_Shortcode_' ) === 0 ) {
				$file = 'shortcodes/' . $file;
			}

			// metaboxes
			if ( strpos( $classname, 'OpalHotel_MetaBox_' ) === 0 ) {
				$file = 'admin/metaboxes/' . $file;
			}

			// gateways
			if ( strpos( $classname, 'OpalHotel_Gateway' ) === 0 ) {
				$gateway = strtolower( substr( $classname, strlen( 'OpalHotel_Gateway_' ) ) );
				$file = 'gateways/' . $gateway . '/' . $file;
			}

			// include file
			$this->_include( $path . '/' . $file );

		}

	}

}

new OpalHotel_Autoload();