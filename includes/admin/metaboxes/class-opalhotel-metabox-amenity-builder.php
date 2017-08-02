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

class OpalHotel_MetaBox_Amenity_Builder {

	/* render */
	public static function render( $post ) {
        $custom_fields = get_post_meta( $post->ID, '_amenities_fields', true );
        require_once OPALHOTEL_PATH . '/includes/admin/metaboxes/views/html-custom-fields.php';
	}

	/* save post meta*/
	public static function save( $post_id, $post ) {
		if ( $post->post_type !== OPALHOTEL_CPT_ANT || empty( $_POST ) ) {
			return;
		}
		$custom_fields = array();
        if( isset( $_POST['type'] ) ) {

            $case_select = 0;
            $case_common = 0;

            foreach( $_POST['type'] as $key => $value ){
                switch ( $value ){
                    case 'label':
                        $custom_fields[$key] = array(
                            'type'          => $value,
                            'name'          => sanitize_text_field( $_POST['name'][$key] )
                        );
                        break;
                    case 'text':
                    case 'textarea':
                    case 'checkbox':
                    case 'text_date':
                        $custom_fields[$key] = array(
                            'type'          => $value,
                            'id'            => sanitize_title( $_POST['id'][$key] ),
                            'name'          => sanitize_text_field( $_POST['name'][$key] ),
                            'description'   => sanitize_text_field( $_POST['description'][$key] ),
                            'icon'          => sanitize_text_field( $_POST['icon'][$key] ),
                            'icon_class'    => sanitize_text_field( $_POST['icon_class'][$key] )
                        );

                        $default_value = isset( $_POST['default'][$case_common] ) ? $_POST['default'][$case_common] : '';
                        $custom_fields[$key]['default'] = $default_value;
                        $case_common++;
                        break;
                    case 'select':
                        if ( isset($_POST['opal_custom_select_options_default'][$case_select]) ){
                            $def_value_index = (int)$_POST['opal_custom_select_options_default'][$case_select];
                        } else {
                            $def_value_index = 0;
                        }

                        if( $_POST['opal_custom_select_options_value'][$case_select] ){
                            $option_values = $_POST['opal_custom_select_options_value'][$case_select];
                            if($option_values){
                                foreach ($option_values as $k => $v) {
                                    $option_values[$k] = sanitize_text_field($v);
                                }
                            }
                        } else {
                            $option_values = array();
                        }

                        if ( $_POST['opal_custom_select_options_label'][$case_select] ) {
                            $option_labels = $_POST['opal_custom_select_options_label'][$case_select];
                            if($option_labels){
                                foreach ($option_labels as $k => $v) {
                                    $option_labels[$k] = sanitize_text_field($v);
                                }
                            }
                        } else {
                            $option_labels = array();
                        }

                        $custom_fields[$key] = array(
                            'type' => 'select',
                            'id' => sanitize_title($_POST['id'][$key]),
                            'name' => sanitize_text_field($_POST['name'][$key]),
                            'description' => sanitize_text_field($_POST['description'][$key]),
                            'icon' => sanitize_text_field($_POST['icon'][$key]),
                            'icon_class' => sanitize_text_field($_POST['icon_class'][$key]),
                            'options' => array_combine( $option_values, $option_labels),
                            'default'=> sanitize_text_field( $option_values[$def_value_index]),
                            'default_value_index'=>$def_value_index
                        );

                        $case_select++;
                        break;
                }
            }
        }

        if ( $custom_fields ) {
            $old_custom_fields = get_post_meta( $post_id, '_amenities_fields', true );
            update_post_meta( $post_id, '_amenities_fields', $custom_fields );

            do_action('opalhotel_update_custom_fields', $post_id, $old_custom_fields, $custom_fields);
        } else {
            delete_post_meta( $post_id, '_amenities_fields' );
        }
	}

}
