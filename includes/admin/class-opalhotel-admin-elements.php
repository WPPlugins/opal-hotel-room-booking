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

class OpalHotel_Admin_Elements {

    private $icon_data;

    public function __construct(){

        $icons = new OpalHotel_Font_Awesome();

        $this->icon_data = $icons->getIcons();
    }

    public function label( $args = array() ) {
        $args = wp_parse_args( $args, array(
            'name'              => '',
            'id'                => '',
            'description'       => '',
            'default'           => '',
            'icon_data'         => $this->icon_data,
            'icon'              => '',
            'icon_class'        => '',
        ) );
        extract( $args );
        ?>
            <div class="panel-group">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a class="toggle-panel">
                                <?php esc_html_e('Label', 'opal-hotel-room-booking') ?> : <?php echo esc_html( $name ) ?></a>
                            <a href="#" class="remove-custom-field-item">x</a>
                        </h4>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="form-group-field">
                        <label class="control-label label-field"><?php esc_html_e('Title', 'opal-hotel-room-booking') ?></label>
                        <div class="content-field">
                            <input type="text" name="name[]" class="form-control etf-in" placeholder="<?php esc_html_e( 'Please Enter Title', 'opal-hotel-room-booking' ); ?>" value="<?php echo esc_attr( $name ); ?>">
                        </div>
                    </div>
                </div>
                <input type="hidden" name="type[]" value="label" />
            </div>
        <?php
    }

    public function text( $args = array() ) {
        $default = array(
            'name'              => '',
            'id'                => '',
            'description'       => '',
            'default'           => '',
            'icon_data'         => $this->icon_data,
            'icon'              => '',
            'icon_class'        => '',
        );

        $args = array_merge( $default, $args );

        extract($args);

        ?>
        <div class="panel-group" >
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a class="toggle-panel">
                            <?php esc_html_e('TextField', 'opal-hotel-room-booking') ?> : <?php echo esc_html( $name ) ?></a>
                        <a href="#" class="remove-custom-field-item">x</a>
                    </h4>

                </div>
                <div class="panel-body" style="display: none">
                    <div class="form-group-field">
                        <label class="control-label label-field"><?php esc_html_e('Metakey', 'opal-hotel-room-booking') ?></label>
                        <div class="content-field">
                            <input type="text" name="id[]" class="form-control etf-in" placeholder="<?php esc_html_e( 'Please Enter Meta Key', 'opal-hotel-room-booking' ); ?>" value="<?php echo esc_attr( $id ) ?>">
                           <p> <i><?php esc_html_e( 'Please enter word not contain blank, special characters. This field is used for search able, it should be lowercase or _ for example: your_key_here' ); ?></i> </p>
                        </div>
                    </div>
                    <div class="form-group-field">
                        <label class="control-label label-field"><?php esc_html_e('Title', 'opal-hotel-room-booking') ?></label>
                        <div class="content-field">
                            <input type="text" name="name[]" class="form-control etf-in" placeholder="<?php esc_html_e( 'Please Enter Title', 'opal-hotel-room-booking' ); ?>" value="<?php echo esc_attr( $name); ?>">
                        </div>
                    </div>
                    <div class="form-group-field">
                        <label class="control-label label-field"><?php esc_html_e('Description', 'opal-hotel-room-booking') ?></label>
                        <div class="content-field">
                            <textarea name="description[]" class="etf-textarea" placeholder="<?php esc_html_e( 'Please Enter Description', 'opal-hotel-room-booking' ); ?>"><?php echo esc_textarea( $description ); ?></textarea>
                        </div>
                    </div>
                    <div class="form-group-field">
                        <label class="control-label label-field"><?php esc_html_e('Default', 'opal-hotel-room-booking') ?></label>
                        <div class="content-field">
                            <input type="text" name="default[]" class="form-control etf-in" placeholder="<?php esc_html_e( 'Please Enter Default', 'opal-hotel-room-booking' ); ?>" value="<?php echo esc_attr( $default ) ?>">
                        </div>
                    </div>

                    <div class="form-group-field">
                        <label class="control-label label-field"><?php esc_html_e('Icon Class', 'opal-hotel-room-booking') ?></label>
                        <div class="content-field">
                            <input type="text" name="icon_class[]" class="form-control etf-in" placeholder="<?php esc_html_e( 'Please Enter Icon Class', 'opal-hotel-room-booking' ); ?>" value="<?php echo esc_attr( $icon_class ); ?>">
                        </div>
                    </div>

                    <div class="form-group-field">
                        <label class="control-label label-field"><?php esc_html_e('Icon', 'opal-hotel-room-booking') ?></label>
                        <div class="content-field">

                            <select class="fa-icon-picker" name="icon[]">
                                <option value=""></option>
                                <?php
                                    foreach ($icon_data as $icon_item) { ?>
                                        <option <?php echo ( $icon == "fa " . $icon_item["class"]) ? 'selected="selected"' : ""; ?> value="fa <?php echo $icon_item['class'] ?>"><?php echo $icon_item['class'] ?></option>
                                        <?php
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <input type="hidden" name="type[]" value="text" />
        </div>
        <?php
    }

    public function textarea( $args = array() ){

        $default = array(
            'name'           => '',
            'id'             => '',
            'description'    => '',
            'default'  => '',
            'icon_data'     => $this->icon_data,
            'icon'           => '',
            'icon_class'    => '',
        );

        $args = array_merge( $default, $args );

        extract($args);

        ?>
        <div class="panel-group" >
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a class="toggle-panel"><?php esc_html_e('TextArea', 'opal-hotel-room-booking') ?> : <?php echo esc_html( $name ) ?></a>
                        <a href="#" class="remove-custom-field-item">x</a>
                    </h4>

                </div>
                <div class="panel-body" style="display: none">
                    <div class="form-group-field">
                        <label class="control-label label-field"><?php esc_html_e('Metakey', 'opal-hotel-room-booking') ?></label>
                        <div class="content-field">
                            <input type="text" name="id[]" class="form-control etf-in" placeholder="<?php esc_html_e('Please Enter Meta Key', 'opal-hotel-room-booking') ?>" value="<?php echo esc_attr( $id ) ?>">
                            <p> <i><?php esc_html_e( 'Please enter word not contain blank, special characters. This field is used for search able, it should be lowercase or _ for example: your_key_here' ); ?></i> </p>
                        </div>
                    </div>
                    <div class="form-group-field">
                        <label class="control-label label-field"><?php esc_html_e('Title', 'opal-hotel-room-booking') ?></label>
                        <div class="content-field">
                            <input type="text" name="name[]" class="form-control etf-in" placeholder="<?php esc_html_e('Please Enter Title', 'opal-hotel-room-booking') ?>" value="<?php echo esc_attr( $name ) ?>">
                        </div>
                    </div>
                    <div class="form-group-field">
                        <label class="control-label label-field"><?php esc_html_e('Description', 'opal-hotel-room-booking') ?></label>
                        <div class="content-field">
                            <textarea name="description[]" class="etf-textarea" placeholder="<?php esc_html_e('Please Enter Description', 'opal-hotel-room-booking') ?>"><?php echo esc_textarea( $description ); ?></textarea>
                        </div>
                    </div>
                    <div class="form-group-field">
                        <label class="control-label label-field"><?php esc_html_e('Default', 'opal-hotel-room-booking') ?></label>
                        <div class="content-field">
                            <input type="text" name="default[]" class="form-control etf-in" placeholder="<?php esc_html_e('Please Enter Default', 'opal-hotel-room-booking') ?>" value="<?php echo esc_attr( $default ) ?>">
                        </div>
                    </div>
                    <div class="form-group-field">
                        <label class="control-label label-field"><?php esc_html_e('Icon Class', 'opal-hotel-room-booking') ?></label>
                        <div class="content-field">
                            <input type="text" name="icon_class[]" class="form-control etf-in" placeholder="<?php esc_html_e( 'Please Enter Icon Class', 'opal-hotel-room-booking' ); ?>" value="<?php echo esc_attr( $icon_class ); ?>">
                        </div>
                    </div>
                    <div class="form-group-field">
                        <label class="control-label label-field"><?php esc_html_e('Icon', 'opal-hotel-room-booking') ?></label>
                        <div class="content-field">
                            <select class="fa-icon-picker" name="icon[]">
                                <option value=""></option>
                                <?php
                                foreach ($icon_data as $icon_item) { ?>
                                    <option <?php echo ($icon == "fa " . $icon_item["class"]) ? 'selected="selected"' : ""; ?> value="fa <?php echo $icon_item['class'] ?>"><?php printf( '%s', $icon_item['class'] ) ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <input type="hidden" name="type[]" value="textarea" />
        </div>
        <?php
    }


    public function text_date( $args = array() ){

        $default = array(
            'name'           => '',
            'id'             => '',
            'description'    => '',
            'default'  => '',
            'icon_data'     => $this->icon_data,
            'icon'           => '',
            'icon_class'    => '',
            'date_format' => 'l jS \of F Y'
        );

        $args = array_merge( $default, $args );

        extract($args);

        ?>
        <div class="panel-group" >
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a class="toggle-panel">
                            <?php esc_html_e('TextDate', 'opal-hotel-room-booking') ?> : <?php  echo $name ?></a>
                        <a href="#" class="remove-custom-field-item">x</a>
                    </h4>

                </div>
                <div class="panel-body" style="display: none">
                    <div class="form-group-field">
                        <label class="control-label label-field"><?php esc_html_e('Metakey', 'opal-hotel-room-booking') ?></label>
                        <div class="content-field">
                            <input type="text" name="id[]" class="form-control etf-in" placeholder="<?php esc_html_e('Please Enter Meta Key', 'opal-hotel-room-booking') ?>" value="<?php echo esc_attr( $id ); ?>">
                            <p> <i><?php esc_html_e( 'Please enter word not contain blank, special characters. This field is used for search able, it should be lowercase or _ for example: your_key_here' ); ?></i> </p>
                        </div>
                    </div>
                    <div class="form-group-field">
                        <label class="control-label label-field"><?php esc_html_e('Title', 'opal-hotel-room-booking') ?></label>
                        <div class="content-field">
                            <input type="text" name="name[]" class="form-control etf-in" placeholder="<?php esc_html_e('Please Enter Title', 'opal-hotel-room-booking') ?>" value="<?php echo esc_attr( $name ); ?>">
                        </div>
                    </div>
                    <div class="form-group-field">
                        <label class="control-label label-field"><?php esc_html_e('Description', 'opal-hotel-room-booking') ?></label>
                        <div class="content-field">
                            <textarea name="description[]" class="etf-textarea" placeholder="<?php esc_html_e('Please Enter Description', 'opal-hotel-room-booking') ?>"><?php echo esc_textarea( $description ); ?></textarea>
                        </div>
                    </div>
                    <div class="form-group-field">
                        <label class="control-label label-field"><?php esc_html_e('Default', 'opal-hotel-room-booking') ?></label>
                        <div class="content-field">
                            <input type="text" name="default[]" class="form-control etf-in" placeholder="<?php esc_html_e('Please Enter Default', 'opal-hotel-room-booking') ?>" value="<?php echo esc_attr( $default ) ?>" />
                        </div>
                    </div>
                    <div class="form-group-field">
                        <label class="control-label label-field"><?php esc_html_e('Icon Class', 'opal-hotel-room-booking') ?></label>
                        <div class="content-field">
                            <input type="text" name="icon_class[]" class="form-control etf-in" placeholder="<?php esc_html_e( 'Please Enter Icon Class', 'opal-hotel-room-booking' ); ?>" value="<?php echo esc_attr( $icon_class ); ?>">
                        </div>
                    </div>
                    <div class="form-group-field">
                        <label class="control-label label-field"><?php esc_html_e('Icon', 'opal-hotel-room-booking') ?></label>
                        <div class="content-field">

                            <select class="fa-icon-picker" name="icon[]">
                                <option value=""></option>
                                <?php
                                foreach ($icon_data as $icon_item) { ?>
                                    <option <?php echo ($icon == "fa " . $icon_item["class"]) ? 'selected="selected"' : ""; ?> value="fa <?php echo $icon_item['class'] ?>"><?php printf( '%s', $icon_item['class'] ) ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <input type="hidden" name="type[]" value="text_date" />
        </div>
        <?php
    }


    public function select_option( $args = array() ){

        $default = array(
            'index' => 0,
            'checked_default' => '',
            'option_index' => 0
        );

        $args = array_merge( $default, $args );

        extract($args);

        ?>
        <div class="row option-row">
            <div class="label-wrap">
                <div>
                    <strong><?php esc_html_e('Label', 'opal-hotel-room-booking') ?></strong>
                </div>
                <div class="option-row-val"><input type="text" name="opal_custom_select_options_label[<?php echo $index ?>][]" class="opalopalhotel-options-label form-control" value="" /></div>
            </div>

            <div class="value-wrap">
                <div>
                    <strong><?php esc_html_e('Value', 'opal-hotel-room-booking') ?></strong>
                </div>
                <div class="option-row-val"><input type="text" name="opal_custom_select_options_value[<?php echo $index ?>][]" class="opalopalhotel-options-value form-control" value="" /></div>
            </div>
            <div class="col-lg-3 col-md-3 default-wrap">
                <div>
                    <strong><?php esc_html_e('Default', 'opal-hotel-room-booking') ?></strong>
                </div>
                <div class="option-row-val"><input type="radio" class="opalopalhotel-options-default" <?php echo $checked_default ?> name="opal_custom_select_options_default[<?php echo esc_attr( $option_index ) ?>]" class="opalopalhotel-options-default" value="<?php echo esc_attr( $option_index ) ?>"></div>
            </div>
            <div class="col-lg-1 col-md-1 remove-wrap">
                <a href="#" class="opalopalhotel-remove-option">x</a>
            </div>
        </div>
        <?php
    }


    public function select( $args = array() ){

        $default = array(
            'name'           => '',
            'id'             => '',
            'description'    => '',
            'default'  => '',
            'icon_data'     => $this->icon_data,
            'icon'           => '',
            'icon_class'    => '',
            'i' => 0,
            'options' => array()
        );

        $args = array_merge( $default, $args );

        extract($args);
        
        ?>
        <div class="panel-group select-container" >
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a class="toggle-panel">
                            <?php esc_html_e('Select', 'opal-hotel-room-booking') ?> : <?php echo esc_html( $name ) ?></a>
                        <a href="#" class="remove-custom-field-item">x</a>
                    </h4>

                </div>
                <div class="panel-body" style="display: none">
                    <div class="form-group-field">
                        <label class="control-label label-field"><?php esc_html_e('Metakey', 'opal-hotel-room-booking') ?></label>
                        <div class="content-field">
                            <input type="text" name="id[]" class="form-control etf-in" placeholder="<?php esc_html_e('Please Enter Meta Key', 'opal-hotel-room-booking') ?>" value="<?php echo $id ?>">
                           <p> <i><?php esc_html_e( 'Please enter word not contain blank, special characters. This field is used for search able, it should be lowercase or _ for example: your_key_here' ); ?></i> </p>
                        </div>
                    </div>
                    <div class="form-group-field">
                        <label class="control-label label-field"><?php esc_html_e('Title', 'opal-hotel-room-booking') ?></label>
                        <div class="content-field">
                            <input type="text" name="name[]" class="form-control etf-in" placeholder="<?php esc_html_e('Please Enter Title', 'opal-hotel-room-booking') ?>" value="<?php echo $name; ?>">
                        </div>
                    </div>
                    <div class="form-group-field">
                        <label class="control-label label-field"><?php esc_html_e('Description', 'opal-hotel-room-booking') ?></label>
                        <div class="content-field">
                            <textarea name="description[]" class="etf-textarea" placeholder="<?php esc_html_e('Please Enter Description', 'opal-hotel-room-booking') ?>"><?php echo $description; ?></textarea>
                        </div>
                    </div>
                    <div class="form-group-field">
                        <label class="control-label label-field"><?php esc_html_e('Options', 'opal-hotel-room-booking') ?></label>
                        <div class="content-field options-container">

                            <?php
                            $index = 0;
                            foreach( $options as $option_item_key => $option_item ) {
                                $checked = false;
                                if($option_item_key == $default){
                                    $checked = true;
                                }

                                ?>
                                <div class="row option-row">
                                    <div class="label-wrap">
                                        <div>
                                            <strong><?php esc_html_e('Label', 'opal-hotel-room-booking') ?></strong>
                                        </div>
                                        <div class="option-row-val">
                                            <input type="text" name="opal_custom_select_options_label[<?php echo $i ?>][]" class="opalopalhotel-options-label form-control" value="<?php echo $option_item ?>" />
                                        </div>

                                    </div>
                                    <div class="value-wrap">
                                        <div>
                                            <strong><?php esc_html_e('Value', 'opal-hotel-room-booking') ?></strong>
                                        </div>
                                        <div class="option-row-val">
                                            <input type="text" name="opal_custom_select_options_value[<?php echo $i ?>][]" class="opalopalhotel-options-value form-control" value="<?php echo $option_item_key; ?>" />
                                        </div>

                                    </div>
                                    <div class="col-lg-3 col-md-3 default-wrap">
                                        <div>
                                            <strong><?php esc_html_e('Default', 'opal-hotel-room-booking') ?></strong>
                                        </div>
                                        <div class="option-row-val">
                                            <input type="radio" class="opalopalhotel-options-default" name="opal_custom_select_options_default[<?php echo $i ?>]" <?php echo $checked ? 'checked' : ''; ?> value="<?php echo $index ?>">
                                        </div>

                                    </div>
                                    <div class="col-lg-1 col-md-1 remove-wrap">
                                        <a href="#" class="opalopalhotel-remove-option">x</a>
                                    </div>
                                </div>

                                <?php
                                $index++;
                            }
                            ?>

                            <a href="#" class="btn btn-info add-new-options"><?php esc_html_e('Add New Item', 'opal-hotel-room-booking') ?></a>
                        </div>
                    </div>

                    <div class="form-group-field">
                        <label class="control-label label-field"><?php esc_html_e('Icon Class', 'opal-hotel-room-booking') ?></label>
                        <div class="content-field">
                            <input type="text" name="icon_class[]" class="form-control etf-in" placeholder="<?php esc_html_e( 'Please Enter Icon Class', 'opal-hotel-room-booking' ); ?>" value="<?php echo esc_html( $icon_class ) ?>">
                        </div>
                    </div>
                    <div class="form-group-field">
                        <label class="control-label label-field"><?php esc_html_e('Icon', 'opal-hotel-room-booking') ?></label>
                        <div class="content-field">
                            <select class="fa-icon-picker" name="icon[]">
                                <option value=""></option>
                                <?php
                                foreach ($icon_data as $icon_item) { ?>
                                    <option <?php echo ($icon == "fa " . $icon_item["class"]) ? 'selected="selected"' : ""; ?> value="fa <?php echo $icon_item['class'] ?>"><?php echo $icon_item['class'] ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                </div>
            </div>
            <input type="hidden" name="type[]" value="select" />
            <input type="hidden" name="select_id[]" class="opalopalhotel-select-index" value="<?php echo $i; ?>" />
        </div>
        <?php
    }


    public function checkbox( $args = array() ){

        $default = array(
            'name'           => '',
            'id'             => '',
            'description'    => '',
            'default'  => '',
            'icon_data'     => $this->icon_data,
            'icon'           => '',
            'icon_class'    => '',
        );

        $args = array_merge( $default, $args );

        extract($args);

        ?>
        <div class="panel-group" >
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a class="toggle-panel"><?php esc_html_e('Checkbox', 'opal-hotel-room-booking') ?> : <?php echo esc_html( $name ) ?></a>
                        <a href="#" class="remove-custom-field-item">x</a>
                    </h4>

                </div>
                <div class="panel-body" style="display: none">
                    <div class="form-group-field">
                        <label class="control-label label-field"><?php esc_html_e('Metakey', 'opal-hotel-room-booking') ?></label>
                        <div class="content-field">
                            <input type="text" name="id[]" class="form-control etf-in" placeholder="<?php esc_html_e('Please Enter Meta Key', 'opal-hotel-room-booking') ?>" value="<?php echo $id ?>">
                            <p> <i><?php esc_html_e( 'Please enter word not contain blank, special characters. This field is used for search able, it should be lowercase or _ for example: your_key_here' ); ?></i> </p>
                        </div>
                    </div>
                    <div class="form-group-field">
                        <label class="control-label label-field"><?php esc_html_e('Title', 'opal-hotel-room-booking') ?></label>
                        <div class="content-field">
                            <input type="text" name="name[]" class="form-control etf-in" placeholder="<?php esc_html_e('Please Enter Title', 'opal-hotel-room-booking') ?>" value="<?php echo $name; ?>">
                        </div>
                    </div>
                    <div class="form-group-field">
                        <label class="control-label label-field"><?php esc_html_e('Description', 'opal-hotel-room-booking') ?></label>
                        <div class="content-field">
                            <textarea name="description[]" class="etf-textarea" placeholder="<?php esc_html_e('Please Enter Description', 'opal-hotel-room-booking') ?>"><?php echo esc_textarea( $description ); ?></textarea>
                        </div>
                    </div>
                    <div class="form-group-field">
                        <label class="control-label label-field"><?php esc_html_e('Check by default', 'opal-hotel-room-booking') ?></label>
                        <div class="content-field">
                            <input type="checkbox" name="default[]" class="form-control etf-in" <?php echo $default ? "checked" : ""; ?> placeholder="<?php esc_html_e('Please Enter Default', 'opal-hotel-room-booking') ?>" value="1">
                        </div>
                    </div>
                    <div class="form-group-field">
                        <label class="control-label label-field"><?php esc_html_e('Icon Class', 'opal-hotel-room-booking') ?></label>
                        <div class="content-field">
                            <input type="text" name="icon_class[]" class="form-control etf-in" placeholder="<?php esc_html_e( 'Please Enter Icon Class', 'opal-hotel-room-booking' ); ?>" value="<?php echo esc_html( $icon_class ) ?>">
                        </div>
                    </div>
                    <div class="form-group-field">
                        <label class="control-label label-field"><?php esc_html_e('Icon', 'opal-hotel-room-booking') ?></label>
                        <div class="content-field">
                            <select class="fa-icon-picker" name="icon[]">
                                <option value=""></option>
                                <?php
                                    foreach ( $icon_data as $icon_item ) { ?>
                                        <option <?php echo ($icon == "fa " . $icon_item["class"]) ? 'selected="selected"' : ""; ?> value="fa <?php echo $icon_item['class'] ?>"><?php echo $icon_item['class'] ?></option>
                                        <?php
                                    }
                                ?>
                            </select>

                        </div>
                    </div>
                </div>
            </div>
            <input type="hidden" name="type[]" value="checkbox" />
        </div>
        <?php
    }


}