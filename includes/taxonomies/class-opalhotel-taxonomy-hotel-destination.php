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

class OpalHotel_Hotel_Destination_Taxonomy extends OpalHotel_Abstract_Taxonomy {

	/* taxonomy name */
	public $taxonomy = null;

	/* taxonomy args */
	public $taxonomy_args = array();

	/* post types uses */
	public $post_types = array();

	/* contructor */
	public function __construct() {

		/* taxonomy name */
		$this->taxonomy = OPALHOTEL_TXM_HOTEL_DES;

		$this->post_types = array(
				OPALHOTEL_CPT_HOTEL
			);

		/* taxonomy args register */
		$this->taxonomy_args = array(
                'hierarchical'          => true,
                'label'                 => __( 'Destinations', 'opal-hotel-room-booking' ),
                'labels' => array(
                    'name'              => _x( 'Destinations', 'taxonomy general name', 'opal-hotel-room-booking' ),
                    'singular_name'     => _x( 'Destination', 'taxonomy singular name', 'opal-hotel-room-booking' ),
                    'menu_name'         => _x( 'Destinations', 'Destinations', 'opal-hotel-room-booking' ),
                    'search_items'      => __( 'Search Destinations', 'opal-hotel-room-booking' ),
                    'all_items'         => __( 'All Destinations', 'opal-hotel-room-booking' ),
                    'parent_item'       => __( 'Parent Destination', 'opal-hotel-room-booking' ),
                    'parent_item_colon' => __( 'Parent Destination:', 'opal-hotel-room-booking' ),
                    'edit_item'         => __( 'Edit Destination', 'opal-hotel-room-booking' ),
                    'update_item'       => __( 'Update Destination', 'opal-hotel-room-booking' ),
                    'add_new_item'      => __( 'Add New Destination', 'opal-hotel-room-booking' ),
                    'new_item_name'     => __( 'New Destination Name', 'opal-hotel-room-booking' )
                ),
                'public'                => true,
                'show_ui'               => true,
                'query_var'             => true,
                'rewrite'               => array( 'slug' => _x( 'hotel-destinations', 'URL slug', 'opal-hotel-room-booking' ) )
            );

		parent::__construct();
        // form fields
        add_action( OPALHOTEL_TXM_HOTEL_DES . '_add_form_fields', array( __CLASS__, 'create_form_field' ), 10, 1 );
        add_action( OPALHOTEL_TXM_HOTEL_DES . '_edit_form', array( __CLASS__, 'edit_form_field' ), 10, 2 );
        add_action( 'created_' . OPALHOTEL_TXM_HOTEL_DES, array( __CLASS__, 'updated_taxonomy' ), 10, 1 );
        add_action( 'edited_' . OPALHOTEL_TXM_HOTEL_DES, array( __CLASS__, 'updated_taxonomy' ), 10, 1 );
	}

    public static function create_form_field( $taxonomy ) {
        if ( $taxonomy !== OPALHOTEL_TXM_HOTEL_DES ) {
            return;
        }
        ?>

            <div class="form-field term-thumbnail">
                <label for="_thumbnail_id"><?php _e( 'Thumbnail', 'opal-hotel-room-booking' ); ?></label>
                <input type="hidden" name="_thumbnail_id" class="_thumbnail_id" value="" />
                <div class="media-thumbnail"></div>
                <a href="#" class="button add-media"><?php _e( 'Media Select', 'opal-hotel-room-booking' ); ?></a>
            </div>

        <?php
    }

    public static function edit_form_field( $term, $taxonomy ) {
        if ( $taxonomy !== OPALHOTEL_TXM_HOTEL_DES ) {
            return;
        }
        ?>
            <table class="form-table">
                <?php 
                    $thumbnail_id = get_term_meta( $term->term_id, '_thumbnail_id', true );
                    $image_url = wp_get_attachment_url( $thumbnail_id );
                ?>
                <tr class="form-field term-country-type">
                    <th scope="row"><label for="_thumbnail_id"><?php _e( 'Thumbnail', 'opal-hotel-room-booking' ); ?></label></th>
                    <td class="term-thumbnail">
                        <input type="hidden" name="_thumbnail_id" class="_thumbnail_id" value="<?php echo esc_attr( $thumbnail_id ) ?>" />
                        <div class="media-thumbnail">
                            <img src="<?php echo esc_url( $image_url ) ?>" width="100" height="100" />
                        </div>
                        <a href="#" class="button add-media"><?php _e( 'Media Select', 'opal-hotel-room-booking' ); ?></a>
                    </select>
                    </td>
                </tr>
            </table>

        <?php
    }

    public static function updated_taxonomy( $term_id ) {
        if ( ! empty( $_POST['_thumbnail_id'] ) ) {
            update_term_meta( $term_id, '_thumbnail_id', absint( $_POST['_thumbnail_id'] ) );
        }
    }

}

new OpalHotel_Hotel_Destination_Taxonomy();