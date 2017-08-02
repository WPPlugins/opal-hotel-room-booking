<?php
/**
 * $Desc$
 *
 * @version    1.1.7
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

$arrival = isset( $_REQUEST['arrival_datetime'] ) ? sanitize_text_field( $_REQUEST['arrival_datetime'] ) : date( 'Y-m-d', current_time( 'timestamp' ) );
$departure = isset( $_REQUEST['departure_datetime'] ) ? sanitize_text_field( $_REQUEST['departure_datetime'] ) : date( 'Y-m-d', current_time( 'timestamp' ) + DAY_IN_SECONDS );

?>

<?php wp_nonce_field( 'opalhotel_add_to_cart', 'opalhotel-add_to-cart' ); ?>
<input type="hidden" name="arrival" value="<?php echo esc_attr( $arrival ) ?>" />
<input type="hidden" name="departure" value="<?php echo esc_attr( $departure ) ?>" />
<input type="hidden" name="id" value="<?php echo esc_attr( get_the_ID() ) ?>" />
<input type="hidden" name="action" value="opalhotel_add_to_cart" />
<input type="hidden" name="is_hotel" value="<?php echo esc_attr( is_singular( OPALHOTEL_CPT_HOTEL ) || ( isset( $_REQUEST['hotel_id'] ) && $_REQUEST['hotel_id'] ) ? 1 : 0 ) ?>" />
<button type="subbmit" class="button opalhotel-button-submit button-primary-inverse button-block"><?php esc_html_e( 'Book This Room', 'opal-hotel-room-booking' ); ?></button>