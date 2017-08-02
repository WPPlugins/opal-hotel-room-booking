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

?>
<div class="opl-bootstrap">
    <div class="opalhotel-creator-custom-fields">
        <div class="content-fields form-horizontal">

            <?php
                $elements = new OpalHotel_Admin_Elements();

                if( $custom_fields ){
                    $index_select = 0;
                    foreach ( $custom_fields as $custom_field ) {
                        switch($custom_field['type']){
                            case 'label':
                                $elements->label( $custom_field );
                                break;
                            case 'text':
                                $elements->text( $custom_field );
                                break;
                            case 'text_date' :
                                $elements->text_date( $custom_field );
                                break;
                            case 'textarea':
                                $elements->textarea( $custom_field );
                                break;
                            case 'select':
                                $custom_field['i'] = $index_select;
                                $elements->select( $custom_field );
                                $index_select++;
                                break;
                            case 'checkbox':
                                $elements->checkbox( $custom_field );
                                break;
                            default:
                        }
                    }
                }
            ?>

        </div>

        <div class="control-button">
            <a href="#" data-type="label" class="create-et-field-btn button button-primary"><?php esc_html_e('Label', 'opal-hotel-room-booking') ?></a>
        </div>
    </div>
</div>
