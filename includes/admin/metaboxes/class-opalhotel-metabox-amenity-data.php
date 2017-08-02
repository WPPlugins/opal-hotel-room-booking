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

class OpalHotel_MetaBox_Amenity_Data {

	/* render */
	public static function render( $post ) {
		global $post;
        $icons = new OpalHotel_Font_Awesome();

        $args = array(
            'icon'          => get_post_meta( $post->ID, '_icon', true ),
            'icon_color'    => get_post_meta( $post->ID, '_icon_color', true ),
            'icon_data'     => $icons->getIcons()
        );

        extract( $args );
        require_once OPALHOTEL_PATH . '/includes/admin/metaboxes/views/amenity-data.php';
	}

	/* save post meta*/
	public static function save( $post_id, $post ) {
		if ( $post->post_type !== OPALHOTEL_CPT_ANT || empty( $_POST ) ) {
			return;
		}
		update_post_meta( $post_id, '_icon', ! empty( $_POST['_icon'] ) ? sanitize_text_field( $_POST['_icon'] ) : '' );
		update_post_meta( $post_id, '_icon_color', ! empty( $_POST['_icon_color'] ) ? sanitize_text_field( $_POST['_icon_color'] ) : '' );
	}

}
