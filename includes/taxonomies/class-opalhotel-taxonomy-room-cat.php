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

class OpalHotel_Room_Cat_Taxnomy extends OpalHotel_Abstract_Taxonomy {

	/* taxonomy name */
	public $taxonomy = null;

	/* taxonomy args */
	public $taxonomy_args = array();

	/* post types uses */
	public $post_types = array();

	/* contructor */
	public function __construct() {

		/* taxonomy name */
		$this->taxonomy = OPALHOTEL_TXM_ROOM_CAT;

		$this->post_types = array(
				OPALHOTEL_CPT_ROOM
			);

		/* taxonomy args register */
		$this->taxonomy_args = array(
                'hierarchical'          => true,
                'label'                 => __( 'Room Categories', 'opal-hotel-room-booking' ),
                'labels' => array(
                    'name'              => _x( 'Room Categories', 'taxonomy general name', 'opal-hotel-room-booking' ),
                    'singular_name'     => _x( 'Room Category', 'taxonomy singular name', 'opal-hotel-room-booking' ),
                    'menu_name'         => _x( 'Categories', 'Room Categories', 'opal-hotel-room-booking' ),
                    'search_items'      => __( 'Search Room Categories', 'opal-hotel-room-booking' ),
                    'all_items'         => __( 'All Room Categories', 'opal-hotel-room-booking' ),
                    'parent_item'       => __( 'Parent Room Category', 'opal-hotel-room-booking' ),
                    'parent_item_colon' => __( 'Parent Room Category:', 'opal-hotel-room-booking' ),
                    'edit_item'         => __( 'Edit Room Category', 'opal-hotel-room-booking' ),
                    'update_item'       => __( 'Update Room Category', 'opal-hotel-room-booking' ),
                    'add_new_item'      => __( 'Add New Room Category', 'opal-hotel-room-booking' ),
                    'new_item_name'     => __( 'New Room Category Name', 'opal-hotel-room-booking' )
                ),
                'public'                => true,
                'show_ui'               => true,
                'query_var'             => true,
                'rewrite'               => array( 'slug' => _x( 'room-cat', 'URL slug', 'opal-hotel-room-booking' ) )
            );

		parent::__construct();
	}

}

new OpalHotel_Room_Cat_Taxnomy();