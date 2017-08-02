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

?>

<p class="opalhotel_field_group">
    <label for="_icon"><?php esc_html_e( 'Icon', 'opal-hotel-room-booking') ?></label>
    <select class="fa-icon-picker" name="_icon" id="_icon">
        <option value=""></option>
        <?php foreach ( $icon_data as $icon_item ) : ?>
                <option <?php echo ( $icon == 'fa ' . $icon_item['class'] ) ? 'selected="selected"' : ""; ?> value="fa <?php echo esc_attr( $icon_item['class'] ) ?>"><?php echo esc_html($icon_item['class']) ?></option>
        <?php endforeach; ?>
    </select>
</p>

<p class="opalhotel_field_group">
    <label for="_icon_color"><?php esc_html_e( 'Color', 'opal-hotel-room-booking') ?></label>
    <input type="text" name="_icon_color" id="_icon_color" value="<?php echo esc_attr( $icon_color ) ?>" />
</p>

<?php wp_nonce_field( 'opalhotel_save_data', 'opalhotel_meta_nonce' ); ?>

<script type="text/javascript">
    (function($){
        $(document).ready(function(){
            $('#_icon_color').wpColorPicker();
        });
    })(jQuery);
</script>
<style type="text/css">
    #normal-sortables{
        min-height: 0 !important;
    }
</style>