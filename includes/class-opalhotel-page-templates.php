<?php
/**
 * $Desc$
 *
 * @version    $Id$
 * @package    opal-hotel-room-booking
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

if ( ! class_exists( 'OpalHotel_Page_Templates' ) ) :

	class OpalHotel_Page_Templates {

		/**
		 * A reference to an instance of this class.
		 */
		private static $instance;

		/**
		 * The array of templates that this plugin tracks.
		 */
		public $templates;

		/**
		 * Returns an instance of this class.
		 */
		public static function instance() {

			if ( null == self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;

		}

		/**
		 * Initializes the plugin by setting filters and administration functions.
		 */
		private function __construct() {

			// Add a filter to the attributes metabox to inject template into the cache.
			if ( version_compare( floatval( get_bloginfo( 'version' ) ), '4.7', '<' ) ) {

				// 4.6 and older
				add_filter( 'page_attributes_dropdown_pages_args', array( $this, 'register_project_templates' ) );

			} else {

				// Add a filter to the wp 4.7 version attributes metabox
				add_filter( 'theme_page_templates', array( $this, 'add_new_template' ) );

			}

			// Add a filter to the save post to inject out template into the page cache
			add_filter( 'wp_insert_post_data', array( $this, 'register_project_templates' ) );

			// Add a filter to the template include to determine if the page has our
			// template assigned and return it's path
			add_filter( 'template_include', array( $this, 'template_include') );

			// Add your templates to this array.
			$this->templates = apply_filters( 'opal-hotel-room-booking_page_templates', array(
				'page-templates/hotels-map.php' 				=> __( 'Hotels Map', 'opal-hotel-room-booking' ),
				'page-templates/hotels-map-sidebar-left.php' 	=> __( 'Hotels Map Sidebar Left', 'opal-hotel-room-booking' ),
				'page-templates/hotels-map-sidebar-right.php' 	=> __( 'Hotels Map Sidebar Right', 'opal-hotel-room-booking' )
			) );

		}

		/**
		 * Adds our template to the page dropdown for v4.7+
		 *
		 */
		public function add_new_template( $posts_templates ) {
			$posts_templates = array_merge( $posts_templates, $this->templates );
			return $posts_templates;
		}

		/**
		 * Adds our template to the pages cache in order to trick WordPress
		 * into thinking the template file exists where it doens't really exist.
		 */
		public function register_project_templates( $atts ) {

			// Create the key used for the themes cache
			$cache_key = 'page_templates-' . md5( get_theme_root() . '/' . get_stylesheet() );

			// Retrieve the cache list.
			// If it doesn't exist, or it's empty prepare an array
			$templates = wp_get_theme()->get_page_templates();
			if ( empty( $templates ) ) {
				$templates = array();
			}

			// New cache, therefore remove the old one
			wp_cache_delete( $cache_key , 'themes');

			// Now add our template to the list of templates by merging our templates
			// with the existing templates array from the cache.
			$templates = array_merge( $templates, $this->templates );

			// Add the modified cache to allow WordPress to pick it up for listing
			// available templates
			wp_cache_add( $cache_key, $templates, 'themes', 1800 );

			return $atts;

		}

		/**
		 * Checks if the template is assigned to the page
		 */
		public function template_include( $template ) {

			// Get global post
			global $post;

			// Return template if post is empty
			if ( ! $post ) {
				return $template;
			}

			$page_template = get_post_meta( $post->ID, '_wp_page_template', true );

			// Return default template if we don't have a custom one defined
			if ( ! isset( $this->templates[ $page_template ] ) ) {
				return $template;
			}

			// Look within passed path within the theme - this is priority.
			$template = locate_template(
				array(
					OpalHotel()->template_path() . $page_template,
					$page_template
				)
			);

			if ( ! $template ) {
				$template = OpalHotel()->plugin_path() . '/templates/' . $page_template;
			}

			// Return template
			return $template;

		}

	}

endif;