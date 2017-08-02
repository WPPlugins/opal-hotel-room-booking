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
/**
 * Get User IP
 *
 * Returns the IP address of the current visitor
 *
 * @since 1.0
 * @return string $ip User's IP address
 */
function opalhotel_get_ip() {

    $ip = '127.0.0.1';

    if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
        //check ip from share internet
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
        //to check ip is pass from proxy
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } elseif ( ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }

    return apply_filters( 'opalhotel_get_ip', $ip );
}

function opalhoteL_get_room_amenities() {

	$amenities = array(
		'wifi'		=> array( 'label' => __( 'Wifi' ,'opal-hotel-room-booking' ) , 'icon' => 'fa fa-wifi'),
		'cable_tv'	=> array( 'label' => __( 'Cable TV' ,'opal-hotel-room-booking' ) , 'icon' => 'fa fa-wifi'), 
		'iron'		=> array( 'label' => __( 'Iron' ,'opal-hotel-room-booking' ) , 'icon' => 'fa fa-wifi'),
		'bearkfast'	=> array( 'label' => __( 'Breakfast' ,'opal-hotel-room-booking' ) , 'icon' => 'fa fa-wifi'),
		'pickup'	=> array( 'label' => __( 'Airport Pickup' ,'opal-hotel-room-booking' ) , 'icon' => 'fa fa-car'),
		'freeslippers'	=> array( 'label' => __( 'Free Slippers' ,'opal-hotel-room-booking' ) , 'icon' => 'fa fa-car'),
		'petsallowed'	=> array( 'label' => __( 'Pets Allowed' ,'opal-hotel-room-booking' ) , 'icon' => 'fa fa-car'),
		'roomservice'	=> array( 'label' => __( 'Room Service' ,'opal-hotel-room-booking' ) , 'icon' => 'fa fa-car'),
	);
	$amenities = apply_filters('opalhotel_amenities_fields' , $amenities );
	return $amenities;
}

if ( ! function_exists( 'opalhotel_get_option' ) ) {

	function opalhotel_get_option( $name = '', $default = null ) {
		return get_option( 'opalhotel_' . $name, $default );
	}

}

if ( ! function_exists( 'opalhotel_get_timezone_string' ) ) {
	/* get timezone string from WP admin setting */
	function opalhotel_get_timezone_string() {
		// if site timezone string exists, return it
	    if ( $timezone = get_option( 'timezone_string' ) )
	        return $timezone;

	    // get UTC offset, if it isn't set then return UTC
	    if ( 0 === ( $utc_offset = get_option( 'gmt_offset', 0 ) ) )
	        return 'UTC';

	    // adjust UTC offset from hours to seconds
	    $utc_offset *= 3600;

	    // attempt to guess the timezone string from the UTC offset
	    if ( $timezone = timezone_name_from_abbr( '', $utc_offset, 0 ) ) {
	        return $timezone;
	    }

	    // last try, guess timezone string manually
	    $is_dst = date( 'I' );

	    foreach ( timezone_abbreviations_list() as $abbr ) {
	        foreach ( $abbr as $city ) {
	            if ( $city['dst'] == $is_dst && $city['offset'] == $utc_offset )
	                return $city['timezone_id'];
	        }
	    }

	    // fallback to UTC
	    return 'UTC';
	}
}

if ( ! function_exists( 'opalhotel_create_page' ) ) {

	function opalhotel_create_page( $slug, $option = '', $page_title = '', $page_content = '', $post_parent = 0 ) {
	    global $wpdb;

	    $option_value     = get_option( $option );

	    if ( $option_value > 0 ) {
	        $page_object = get_post( $option_value );

	        if ( $page_object && 'page' === $page_object->post_type && ! in_array( $page_object->post_status, array( 'pending', 'trash', 'future', 'auto-draft' ) ) ) {
	            // Valid page is already in place
	            return $page_object->ID;
	        }
	    }

	    if ( strlen( $page_content ) > 0 ) {
	        // Search for an existing page with the specified page content (typically a shortcode)
	        $valid_page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status NOT IN ( 'pending', 'trash', 'future', 'auto-draft' ) AND post_content LIKE %s LIMIT 1;", "%{$page_content}%" ) );
	    } else {
	        // Search for an existing page with the specified page slug
	        $valid_page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status NOT IN ( 'pending', 'trash', 'future', 'auto-draft' )  AND post_name = %s LIMIT 1;", $slug ) );
	    }

	    $valid_page_found = apply_filters( 'opalhotel_create_page_id', $valid_page_found, $slug, $page_content );

	    if ( $valid_page_found ) {
	        if ( $option ) {
	            update_option( $option, $valid_page_found );
	        }
	        return $valid_page_found;
	    }

	    // Search for a matching valid trashed page
	    if ( strlen( $page_content ) > 0 ) {
	        // Search for an existing page with the specified page content (typically a shortcode)
	        $trashed_page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status = 'trash' AND post_content LIKE %s LIMIT 1;", "%{$page_content}%" ) );
	    } else {
	        // Search for an existing page with the specified page slug
	        $trashed_page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status = 'trash' AND post_name = %s LIMIT 1;", $slug ) );
	    }

	    if ( $trashed_page_found ) {
	        $page_id   = $trashed_page_found;
	        $page_data = array(
	            'ID'             => $page_id,
	            'post_status'    => 'publish',
	        );
	        wp_update_post( $page_data );
	    } else {
	        $page_data = array(
	            'post_status'    => 'publish',
	            'post_type'      => 'page',
	            'post_author'    => 1,
	            'post_name'      => $slug,
	            'post_title'     => $page_title,
	            'post_content'   => $page_content,
	            'post_parent'    => $post_parent,
	            'comment_status' => 'closed'
	        );
	        $page_id = wp_insert_post( $page_data );
	    }

	    if ( $option ) {
	        update_option( $option, $page_id );
	    }

	    return $page_id;
	}
}

if ( ! function_exists( 'opalhotel_currencies' ) ) {

	/**
	 * Currencies support
	 *
	 * @return mixed
	 */
	function opalhotel_currencies() {
		$currencies = array(
			'AED' => 'United Arab Emirates Dirham (د.إ)',
			'AUD' => 'Australian Dollars ($)',
			'BDT' => 'Bangladeshi Taka (৳&nbsp;)',
			'BRL' => 'Brazilian Real (R$)',
			'BGN' => 'Bulgarian Lev (лв.)',
			'CAD' => 'Canadian Dollars ($)',
			'CLP' => 'Chilean Peso ($)',
			'CNY' => 'Chinese Yuan (¥)',
			'COP' => 'Colombian Peso ($)',
			'CZK' => 'Czech Koruna (Kč)',
			'DKK' => 'Danish Krone (kr.)',
			'DOP' => 'Dominican Peso (RD$)',
			'EUR' => 'Euros (€)',
			'HKD' => 'Hong Kong Dollar ($)',
			'HRK' => 'Croatia kuna (Kn)',
			'HUF' => 'Hungarian Forint (Ft)',
			'ISK' => 'Icelandic krona (Kr.)',
			'IDR' => 'Indonesia Rupiah (Rp)',
			'INR' => 'Indian Rupee (Rs.)',
			'NPR' => 'Nepali Rupee (Rs.)',
			'ILS' => 'Israeli Shekel (₪)',
			'JPY' => 'Japanese Yen (¥)',
			'KIP' => 'Lao Kip (₭)',
			'KRW' => 'South Korean Won (₩)',
			'MYR' => 'Malaysian Ringgits (RM)',
			'MXN' => 'Mexican Peso ($)',
			'NGN' => 'Nigerian Naira (₦)',
			'NOK' => 'Norwegian Krone (kr)',
			'NZD' => 'New Zealand Dollar ($)',
			'PYG' => 'Paraguayan Guaraní (₲)',
			'PHP' => 'Philippine Pesos (₱)',
			'PLN' => 'Polish Zloty (zł)',
			'GBP' => 'Pounds Sterling (£)',
			'RON' => 'Romanian Leu (lei)',
			'RUB' => 'Russian Ruble (руб.)',
			'SGD' => 'Singapore Dollar ($)',
			'ZAR' => 'South African rand (R)',
			'SEK' => 'Swedish Krona (kr)',
			'CHF' => 'Swiss Franc (CHF)',
			'TWD' => 'Taiwan New Dollars (NT$)',
			'THB' => 'Thai Baht (฿)',
			'TRY' => 'Turkish Lira (₺)',
			'USD' => 'US Dollars ($)',
			'VND' => 'Vietnamese Dong (₫)',
			'EGP' => 'Egyptian Pound (EGP)'
		);

		return apply_filters( 'opalhotel_currencies', $currencies );
	}
}

if ( ! function_exists( 'opalhotel_get_currency' ) ) {

	/* get currency option */
	function opalhotel_get_currency() {
		return apply_filters( 'opalhotel_get_currency', get_option( 'opalhotel_currency', 'USD' ) );
	}

}

if ( ! function_exists( 'opalhotel_get_currency_symbol' ) ) {

	function opalhotel_get_currency_symbol( $currency = '' ) {
		if ( ! $currency ) {
			$currency = opalhotel_get_currency();
		}

		switch ( $currency ) {
			case 'AED' :
				$currency_symbol = 'د.إ';
				break;
			case 'AUD' :
			case 'CAD' :
			case 'CLP' :
			case 'COP' :
			case 'HKD' :
			case 'MXN' :
			case 'NZD' :
			case 'SGD' :
			case 'USD' :
				$currency_symbol = '&#36;';
				break;
			case 'BDT':
				$currency_symbol = '&#2547;&nbsp;';
				break;
			case 'BGN' :
				$currency_symbol = '&#1083;&#1074;.';
				break;
			case 'BRL' :
				$currency_symbol = '&#82;&#36;';
				break;
			case 'CHF' :
				$currency_symbol = '&#67;&#72;&#70;';
				break;
			case 'CNY' :
			case 'JPY' :
			case 'RMB' :
				$currency_symbol = '&yen;';
				break;
			case 'CZK' :
				$currency_symbol = '&#75;&#269;';
				break;
			case 'DKK' :
				$currency_symbol = 'kr.';
				break;
			case 'DOP' :
				$currency_symbol = 'RD&#36;';
				break;
			case 'EGP' :
				$currency_symbol = 'EGP';
				break;
			case 'EUR' :
				$currency_symbol = '&euro;';
				break;
			case 'GBP' :
				$currency_symbol = '&pound;';
				break;
			case 'HRK' :
				$currency_symbol = 'Kn';
				break;
			case 'HUF' :
				$currency_symbol = '&#70;&#116;';
				break;
			case 'IDR' :
				$currency_symbol = 'Rp';
				break;
			case 'ILS' :
				$currency_symbol = '&#8362;';
				break;
			case 'INR' :
				$currency_symbol = 'Rs.';
				break;
			case 'ISK' :
				$currency_symbol = 'Kr.';
				break;
			case 'KIP' :
				$currency_symbol = '&#8365;';
				break;
			case 'KRW' :
				$currency_symbol = '&#8361;';
				break;
			case 'MYR' :
				$currency_symbol = '&#82;&#77;';
				break;
			case 'NGN' :
				$currency_symbol = '&#8358;';
				break;
			case 'NOK' :
				$currency_symbol = '&#107;&#114;';
				break;
			case 'NPR' :
				$currency_symbol = 'Rs.';
				break;
			case 'PHP' :
				$currency_symbol = '&#8369;';
				break;
			case 'PLN' :
				$currency_symbol = '&#122;&#322;';
				break;
			case 'PYG' :
				$currency_symbol = '&#8370;';
				break;
			case 'RON' :
				$currency_symbol = 'lei';
				break;
			case 'RUB' :
				$currency_symbol = '&#1088;&#1091;&#1073;.';
				break;
			case 'SEK' :
				$currency_symbol = '&#107;&#114;';
				break;
			case 'THB' :
				$currency_symbol = '&#3647;';
				break;
			case 'TRY' :
				$currency_symbol = '&#8378;';
				break;
			case 'TWD' :
				$currency_symbol = '&#78;&#84;&#36;';
				break;
			case 'UAH' :
				$currency_symbol = '&#8372;';
				break;
			case 'VND' :
				$currency_symbol = '&#8363;';
				break;
			case 'ZAR' :
				$currency_symbol = '&#82;';
				break;
			default :
				$currency_symbol = $currency;
				break;
		}

		return apply_filters( 'opalhotel_get_currency_symbol', $currency_symbol, $currency );
	}
}

function opalhotel_format_price( $price, $currency = true ) {
	$position                  = get_option( 'opalhotel_price_currency_position', 'left' );
	$price_thousands_separator = get_option( 'opalhotel_price_thousands_separator', ',' );
	$price_decimals_separator  = get_option( 'opalhotel_price_decimals_separator', '.' );
	$price_number_of_decimal   = get_option( 'opalhotel_price_number_of_decimal', '1' );
	if ( ! is_numeric( $price ) )
		$price = 0;

	$js_format = '0';//'0,0.00';
	$price  = apply_filters( 'opalhotel_price_switcher', $price );
	$before = $after = '';
	$currency_symbol = opalhotel_get_currency_symbol();
	if ( $currency ) {

		switch ( $position ) {
			default:
				$before = $currency_symbol;
				break;
			case 'left_with_space':
				$before = $currency_symbol . ' ';
				break;
			case 'right':
				$after = $currency_symbol;
				break;
			case 'right_with_space':
				$after = ' ' . $currency_symbol;
		}
	}

	$js_format .= $price_thousands_separator;
	$js_format .= 0;
	$js_format .= $price_decimals_separator;
	for ( $i = 0; $i <  $price_number_of_decimal; $i++ ) {
		$js_format .= 0;
	}

	$price_format = $before . '<b data-thousand="'.esc_attr( $price_thousands_separator ).'" data-decimal="'.esc_attr( $price_decimals_separator ).'" data-numberdecimal="'.esc_attr( $price_number_of_decimal ).'" data-symbol="'.esc_attr( $currency_symbol ).'" data-format="'.esc_attr( $js_format ).'" data-price="'.esc_attr( $price ).'">' . number_format(
			$price,
			$price_number_of_decimal,
			$price_decimals_separator,
			$price_thousands_separator
		) . '</b>' . $after;

	return apply_filters( 'opalhotel_price_format', $price_format, $price, $currency );
}

if ( ! function_exists( 'opalhotel_get_countries' ) ) {

	function opalhotel_get_countries() {
		$countries = array(
			'AF' => __( 'Afghanistan', 'opal-hotel-room-booking' ),
			'AX' => __( '&#197;land Islands', 'opal-hotel-room-booking' ),
			'AL' => __( 'Albania', 'opal-hotel-room-booking' ),
			'DZ' => __( 'Algeria', 'opal-hotel-room-booking' ),
			'AD' => __( 'Andorra', 'opal-hotel-room-booking' ),
			'AO' => __( 'Angola', 'opal-hotel-room-booking' ),
			'AI' => __( 'Anguilla', 'opal-hotel-room-booking' ),
			'AQ' => __( 'Antarctica', 'opal-hotel-room-booking' ),
			'AG' => __( 'Antigua and Barbuda', 'opal-hotel-room-booking' ),
			'AR' => __( 'Argentina', 'opal-hotel-room-booking' ),
			'AM' => __( 'Armenia', 'opal-hotel-room-booking' ),
			'AW' => __( 'Aruba', 'opal-hotel-room-booking' ),
			'AU' => __( 'Australia', 'opal-hotel-room-booking' ),
			'AT' => __( 'Austria', 'opal-hotel-room-booking' ),
			'AZ' => __( 'Azerbaijan', 'opal-hotel-room-booking' ),
			'BS' => __( 'Bahamas', 'opal-hotel-room-booking' ),
			'BH' => __( 'Bahrain', 'opal-hotel-room-booking' ),
			'BD' => __( 'Bangladesh', 'opal-hotel-room-booking' ),
			'BB' => __( 'Barbados', 'opal-hotel-room-booking' ),
			'BY' => __( 'Belarus', 'opal-hotel-room-booking' ),
			'BE' => __( 'Belgium', 'opal-hotel-room-booking' ),
			'PW' => __( 'Belau', 'opal-hotel-room-booking' ),
			'BZ' => __( 'Belize', 'opal-hotel-room-booking' ),
			'BJ' => __( 'Benin', 'opal-hotel-room-booking' ),
			'BM' => __( 'Bermuda', 'opal-hotel-room-booking' ),
			'BT' => __( 'Bhutan', 'opal-hotel-room-booking' ),
			'BO' => __( 'Bolivia', 'opal-hotel-room-booking' ),
			'BQ' => __( 'Bonaire, Saint Eustatius and Saba', 'opal-hotel-room-booking' ),
			'BA' => __( 'Bosnia and Herzegovina', 'opal-hotel-room-booking' ),
			'BW' => __( 'Botswana', 'opal-hotel-room-booking' ),
			'BV' => __( 'Bouvet Island', 'opal-hotel-room-booking' ),
			'BR' => __( 'Brazil', 'opal-hotel-room-booking' ),
			'IO' => __( 'British Indian Ocean Territory', 'opal-hotel-room-booking' ),
			'VG' => __( 'British Virgin Islands', 'opal-hotel-room-booking' ),
			'BN' => __( 'Brunei', 'opal-hotel-room-booking' ),
			'BG' => __( 'Bulgaria', 'opal-hotel-room-booking' ),
			'BF' => __( 'Burkina Faso', 'opal-hotel-room-booking' ),
			'BI' => __( 'Burundi', 'opal-hotel-room-booking' ),
			'KH' => __( 'Cambodia', 'opal-hotel-room-booking' ),
			'CM' => __( 'Cameroon', 'opal-hotel-room-booking' ),
			'CA' => __( 'Canada', 'opal-hotel-room-booking' ),
			'CV' => __( 'Cape Verde', 'opal-hotel-room-booking' ),
			'KY' => __( 'Cayman Islands', 'opal-hotel-room-booking' ),
			'CF' => __( 'Central African Republic', 'opal-hotel-room-booking' ),
			'TD' => __( 'Chad', 'opal-hotel-room-booking' ),
			'CL' => __( 'Chile', 'opal-hotel-room-booking' ),
			'CN' => __( 'China', 'opal-hotel-room-booking' ),
			'CX' => __( 'Christmas Island', 'opal-hotel-room-booking' ),
			'CC' => __( 'Cocos (Keeling) Islands', 'opal-hotel-room-booking' ),
			'CO' => __( 'Colombia', 'opal-hotel-room-booking' ),
			'KM' => __( 'Comoros', 'opal-hotel-room-booking' ),
			'CG' => __( 'Congo (Brazzaville)', 'opal-hotel-room-booking' ),
			'CD' => __( 'Congo (Kinshasa)', 'opal-hotel-room-booking' ),
			'CK' => __( 'Cook Islands', 'opal-hotel-room-booking' ),
			'CR' => __( 'Costa Rica', 'opal-hotel-room-booking' ),
			'HR' => __( 'Croatia', 'opal-hotel-room-booking' ),
			'CU' => __( 'Cuba', 'opal-hotel-room-booking' ),
			'CW' => __( 'Cura&Ccedil;ao', 'opal-hotel-room-booking' ),
			'CY' => __( 'Cyprus', 'opal-hotel-room-booking' ),
			'CZ' => __( 'Czech Republic', 'opal-hotel-room-booking' ),
			'DK' => __( 'Denmark', 'opal-hotel-room-booking' ),
			'DJ' => __( 'Djibouti', 'opal-hotel-room-booking' ),
			'DM' => __( 'Dominica', 'opal-hotel-room-booking' ),
			'DO' => __( 'Dominican Republic', 'opal-hotel-room-booking' ),
			'EC' => __( 'Ecuador', 'opal-hotel-room-booking' ),
			'EG' => __( 'Egypt', 'opal-hotel-room-booking' ),
			'SV' => __( 'El Salvador', 'opal-hotel-room-booking' ),
			'GQ' => __( 'Equatorial Guinea', 'opal-hotel-room-booking' ),
			'ER' => __( 'Eritrea', 'opal-hotel-room-booking' ),
			'EE' => __( 'Estonia', 'opal-hotel-room-booking' ),
			'ET' => __( 'Ethiopia', 'opal-hotel-room-booking' ),
			'FK' => __( 'Falkland Islands', 'opal-hotel-room-booking' ),
			'FO' => __( 'Faroe Islands', 'opal-hotel-room-booking' ),
			'FJ' => __( 'Fiji', 'opal-hotel-room-booking' ),
			'FI' => __( 'Finland', 'opal-hotel-room-booking' ),
			'FR' => __( 'France', 'opal-hotel-room-booking' ),
			'GF' => __( 'French Guiana', 'opal-hotel-room-booking' ),
			'PF' => __( 'French Polynesia', 'opal-hotel-room-booking' ),
			'TF' => __( 'French Southern Territories', 'opal-hotel-room-booking' ),
			'GA' => __( 'Gabon', 'opal-hotel-room-booking' ),
			'GM' => __( 'Gambia', 'opal-hotel-room-booking' ),
			'GE' => __( 'Georgia', 'opal-hotel-room-booking' ),
			'DE' => __( 'Germany', 'opal-hotel-room-booking' ),
			'GH' => __( 'Ghana', 'opal-hotel-room-booking' ),
			'GI' => __( 'Gibraltar', 'opal-hotel-room-booking' ),
			'GR' => __( 'Greece', 'opal-hotel-room-booking' ),
			'GL' => __( 'Greenland', 'opal-hotel-room-booking' ),
			'GD' => __( 'Grenada', 'opal-hotel-room-booking' ),
			'GP' => __( 'Guadeloupe', 'opal-hotel-room-booking' ),
			'GT' => __( 'Guatemala', 'opal-hotel-room-booking' ),
			'GG' => __( 'Guernsey', 'opal-hotel-room-booking' ),
			'GN' => __( 'Guinea', 'opal-hotel-room-booking' ),
			'GW' => __( 'Guinea-Bissau', 'opal-hotel-room-booking' ),
			'GY' => __( 'Guyana', 'opal-hotel-room-booking' ),
			'HT' => __( 'Haiti', 'opal-hotel-room-booking' ),
			'HM' => __( 'Heard Island and McDonald Islands', 'opal-hotel-room-booking' ),
			'HN' => __( 'Honduras', 'opal-hotel-room-booking' ),
			'HK' => __( 'Hong Kong', 'opal-hotel-room-booking' ),
			'HU' => __( 'Hungary', 'opal-hotel-room-booking' ),
			'IS' => __( 'Iceland', 'opal-hotel-room-booking' ),
			'IN' => __( 'India', 'opal-hotel-room-booking' ),
			'ID' => __( 'Indonesia', 'opal-hotel-room-booking' ),
			'IR' => __( 'Iran', 'opal-hotel-room-booking' ),
			'IQ' => __( 'Iraq', 'opal-hotel-room-booking' ),
			'IE' => __( 'Republic of Ireland', 'opal-hotel-room-booking' ),
			'IM' => __( 'Isle of Man', 'opal-hotel-room-booking' ),
			'IL' => __( 'Israel', 'opal-hotel-room-booking' ),
			'IT' => __( 'Italy', 'opal-hotel-room-booking' ),
			'CI' => __( 'Ivory Coast', 'opal-hotel-room-booking' ),
			'JM' => __( 'Jamaica', 'opal-hotel-room-booking' ),
			'JP' => __( 'Japan', 'opal-hotel-room-booking' ),
			'JE' => __( 'Jersey', 'opal-hotel-room-booking' ),
			'JO' => __( 'Jordan', 'opal-hotel-room-booking' ),
			'KZ' => __( 'Kazakhstan', 'opal-hotel-room-booking' ),
			'KE' => __( 'Kenya', 'opal-hotel-room-booking' ),
			'KI' => __( 'Kiribati', 'opal-hotel-room-booking' ),
			'KW' => __( 'Kuwait', 'opal-hotel-room-booking' ),
			'KG' => __( 'Kyrgyzstan', 'opal-hotel-room-booking' ),
			'LA' => __( 'Laos', 'opal-hotel-room-booking' ),
			'LV' => __( 'Latvia', 'opal-hotel-room-booking' ),
			'LB' => __( 'Lebanon', 'opal-hotel-room-booking' ),
			'LS' => __( 'Lesotho', 'opal-hotel-room-booking' ),
			'LR' => __( 'Liberia', 'opal-hotel-room-booking' ),
			'LY' => __( 'Libya', 'opal-hotel-room-booking' ),
			'LI' => __( 'Liechtenstein', 'opal-hotel-room-booking' ),
			'LT' => __( 'Lithuania', 'opal-hotel-room-booking' ),
			'LU' => __( 'Luxembourg', 'opal-hotel-room-booking' ),
			'MO' => __( 'Macao S.A.R., China', 'opal-hotel-room-booking' ),
			'MK' => __( 'Macedonia', 'opal-hotel-room-booking' ),
			'MG' => __( 'Madagascar', 'opal-hotel-room-booking' ),
			'MW' => __( 'Malawi', 'opal-hotel-room-booking' ),
			'MY' => __( 'Malaysia', 'opal-hotel-room-booking' ),
			'MV' => __( 'Maldives', 'opal-hotel-room-booking' ),
			'ML' => __( 'Mali', 'opal-hotel-room-booking' ),
			'MT' => __( 'Malta', 'opal-hotel-room-booking' ),
			'MH' => __( 'Marshall Islands', 'opal-hotel-room-booking' ),
			'MQ' => __( 'Martinique', 'opal-hotel-room-booking' ),
			'MR' => __( 'Mauritania', 'opal-hotel-room-booking' ),
			'MU' => __( 'Mauritius', 'opal-hotel-room-booking' ),
			'YT' => __( 'Mayotte', 'opal-hotel-room-booking' ),
			'MX' => __( 'Mexico', 'opal-hotel-room-booking' ),
			'FM' => __( 'Micronesia', 'opal-hotel-room-booking' ),
			'MD' => __( 'Moldova', 'opal-hotel-room-booking' ),
			'MC' => __( 'Monaco', 'opal-hotel-room-booking' ),
			'MN' => __( 'Mongolia', 'opal-hotel-room-booking' ),
			'ME' => __( 'Montenegro', 'opal-hotel-room-booking' ),
			'MS' => __( 'Montserrat', 'opal-hotel-room-booking' ),
			'MA' => __( 'Morocco', 'opal-hotel-room-booking' ),
			'MZ' => __( 'Mozambique', 'opal-hotel-room-booking' ),
			'MM' => __( 'Myanmar', 'opal-hotel-room-booking' ),
			'NA' => __( 'Namibia', 'opal-hotel-room-booking' ),
			'NR' => __( 'Nauru', 'opal-hotel-room-booking' ),
			'NP' => __( 'Nepal', 'opal-hotel-room-booking' ),
			'NL' => __( 'Netherlands', 'opal-hotel-room-booking' ),
			'AN' => __( 'Netherlands Antilles', 'opal-hotel-room-booking' ),
			'NC' => __( 'New Caledonia', 'opal-hotel-room-booking' ),
			'NZ' => __( 'New Zealand', 'opal-hotel-room-booking' ),
			'NI' => __( 'Nicaragua', 'opal-hotel-room-booking' ),
			'NE' => __( 'Niger', 'opal-hotel-room-booking' ),
			'NG' => __( 'Nigeria', 'opal-hotel-room-booking' ),
			'NU' => __( 'Niue', 'opal-hotel-room-booking' ),
			'NF' => __( 'Norfolk Island', 'opal-hotel-room-booking' ),
			'KP' => __( 'North Korea', 'opal-hotel-room-booking' ),
			'NO' => __( 'Norway', 'opal-hotel-room-booking' ),
			'OM' => __( 'Oman', 'opal-hotel-room-booking' ),
			'PK' => __( 'Pakistan', 'opal-hotel-room-booking' ),
			'PS' => __( 'Palestinian Territory', 'opal-hotel-room-booking' ),
			'PA' => __( 'Panama', 'opal-hotel-room-booking' ),
			'PG' => __( 'Papua New Guinea', 'opal-hotel-room-booking' ),
			'PY' => __( 'Paraguay', 'opal-hotel-room-booking' ),
			'PE' => __( 'Peru', 'opal-hotel-room-booking' ),
			'PH' => __( 'Philippines', 'opal-hotel-room-booking' ),
			'PN' => __( 'Pitcairn', 'opal-hotel-room-booking' ),
			'PL' => __( 'Poland', 'opal-hotel-room-booking' ),
			'PT' => __( 'Portugal', 'opal-hotel-room-booking' ),
			'QA' => __( 'Qatar', 'opal-hotel-room-booking' ),
			'RE' => __( 'Reunion', 'opal-hotel-room-booking' ),
			'RO' => __( 'Romania', 'opal-hotel-room-booking' ),
			'RU' => __( 'Russia', 'opal-hotel-room-booking' ),
			'RW' => __( 'Rwanda', 'opal-hotel-room-booking' ),
			'BL' => __( 'Saint Barth&eacute;lemy', 'opal-hotel-room-booking' ),
			'SH' => __( 'Saint Helena', 'opal-hotel-room-booking' ),
			'KN' => __( 'Saint Kitts and Nevis', 'opal-hotel-room-booking' ),
			'LC' => __( 'Saint Lucia', 'opal-hotel-room-booking' ),
			'MF' => __( 'Saint Martin (French part)', 'opal-hotel-room-booking' ),
			'SX' => __( 'Saint Martin (Dutch part)', 'opal-hotel-room-booking' ),
			'PM' => __( 'Saint Pierre and Miquelon', 'opal-hotel-room-booking' ),
			'VC' => __( 'Saint Vincent and the Grenadines', 'opal-hotel-room-booking' ),
			'SM' => __( 'San Marino', 'opal-hotel-room-booking' ),
			'ST' => __( 'S&atilde;o Tom&eacute; and Pr&iacute;ncipe', 'opal-hotel-room-booking' ),
			'SA' => __( 'Saudi Arabia', 'opal-hotel-room-booking' ),
			'SN' => __( 'Senegal', 'opal-hotel-room-booking' ),
			'RS' => __( 'Serbia', 'opal-hotel-room-booking' ),
			'SC' => __( 'Seychelles', 'opal-hotel-room-booking' ),
			'SL' => __( 'Sierra Leone', 'opal-hotel-room-booking' ),
			'SG' => __( 'Singapore', 'opal-hotel-room-booking' ),
			'SK' => __( 'Slovakia', 'opal-hotel-room-booking' ),
			'SI' => __( 'Slovenia', 'opal-hotel-room-booking' ),
			'SB' => __( 'Solomon Islands', 'opal-hotel-room-booking' ),
			'SO' => __( 'Somalia', 'opal-hotel-room-booking' ),
			'ZA' => __( 'South Africa', 'opal-hotel-room-booking' ),
			'GS' => __( 'South Georgia/Sandwich Islands', 'opal-hotel-room-booking' ),
			'KR' => __( 'South Korea', 'opal-hotel-room-booking' ),
			'SS' => __( 'South Sudan', 'opal-hotel-room-booking' ),
			'ES' => __( 'Spain', 'opal-hotel-room-booking' ),
			'LK' => __( 'Sri Lanka', 'opal-hotel-room-booking' ),
			'SD' => __( 'Sudan', 'opal-hotel-room-booking' ),
			'SR' => __( 'Suriname', 'opal-hotel-room-booking' ),
			'SJ' => __( 'Svalbard and Jan Mayen', 'opal-hotel-room-booking' ),
			'SZ' => __( 'Swaziland', 'opal-hotel-room-booking' ),
			'SE' => __( 'Sweden', 'opal-hotel-room-booking' ),
			'CH' => __( 'Switzerland', 'opal-hotel-room-booking' ),
			'SY' => __( 'Syria', 'opal-hotel-room-booking' ),
			'TW' => __( 'Taiwan', 'opal-hotel-room-booking' ),
			'TJ' => __( 'Tajikistan', 'opal-hotel-room-booking' ),
			'TZ' => __( 'Tanzania', 'opal-hotel-room-booking' ),
			'TH' => __( 'Thailand', 'opal-hotel-room-booking' ),
			'TL' => __( 'Timor-Leste', 'opal-hotel-room-booking' ),
			'TG' => __( 'Togo', 'opal-hotel-room-booking' ),
			'TK' => __( 'Tokelau', 'opal-hotel-room-booking' ),
			'TO' => __( 'Tonga', 'opal-hotel-room-booking' ),
			'TT' => __( 'Trinidad and Tobago', 'opal-hotel-room-booking' ),
			'TN' => __( 'Tunisia', 'opal-hotel-room-booking' ),
			'TR' => __( 'Turkey', 'opal-hotel-room-booking' ),
			'TM' => __( 'Turkmenistan', 'opal-hotel-room-booking' ),
			'TC' => __( 'Turks and Caicos Islands', 'opal-hotel-room-booking' ),
			'TV' => __( 'Tuvalu', 'opal-hotel-room-booking' ),
			'UG' => __( 'Uganda', 'opal-hotel-room-booking' ),
			'UA' => __( 'Ukraine', 'opal-hotel-room-booking' ),
			'AE' => __( 'United Arab Emirates', 'opal-hotel-room-booking' ),
			'GB' => __( 'United Kingdom (UK)', 'opal-hotel-room-booking' ),
			'US' => __( 'United States (US)', 'opal-hotel-room-booking' ),
			'UY' => __( 'Uruguay', 'opal-hotel-room-booking' ),
			'UZ' => __( 'Uzbekistan', 'opal-hotel-room-booking' ),
			'VU' => __( 'Vanuatu', 'opal-hotel-room-booking' ),
			'VA' => __( 'Vatican', 'opal-hotel-room-booking' ),
			'VE' => __( 'Venezuela', 'opal-hotel-room-booking' ),
			'VN' => __( 'Vietnam', 'opal-hotel-room-booking' ),
			'WF' => __( 'Wallis and Futuna', 'opal-hotel-room-booking' ),
			'EH' => __( 'Western Sahara', 'opal-hotel-room-booking' ),
			'WS' => __( 'Western Samoa', 'opal-hotel-room-booking' ),
			'YE' => __( 'Yemen', 'opal-hotel-room-booking' ),
			'ZM' => __( 'Zambia', 'opal-hotel-room-booking' ),
			'ZW' => __( 'Zimbabwe', 'opal-hotel-room-booking' )
		);
		return apply_filters( 'opalhotel_countries', $countries );
	}
}


if ( ! function_exists( 'opalhotel_get_country_by_code' ) ) {

	/* get country by code use get customer country name */
	function opalhotel_get_country_by_code( $code = '' ) {
		$countries = opalhotel_get_countries();
		if ( isset( $countries[ $code ] ) ) {
			return $countries[ $code ];
		}
	}

}

if ( ! function_exists( 'opalhotel_template_path' ) ) {
	function opalhotel_template_path() {
		return OpalHotel()->template_path();
	}
}

if ( ! function_exists( 'opalhotel_get_template_part' ) ) {

	/**
	 * Get template part (for templates like the shop-loop).
	 *
	 * will prevent overrides in themes from taking priority.
	 *
	 * @access public
	 * @param mixed $slug
	 * @param string $name (default: '')
	 */
	function opalhotel_get_template_part( $slug, $name = '' ) {
		$template = '';

		// Look in yourtheme/slug-name.php and yourtheme/opalhotel/slug-name.php
		if ( $name ) {
			$template = locate_template( array( "{$slug}-{$name}.php", opalhotel_template_path() . "/{$slug}-{$name}.php" ) );
		}

		// Get default slug-name.php
		if ( ! $template && $name && file_exists( opalhotel()->plugin_path() . "/templates/{$slug}-{$name}.php" ) ) {
			$template = opalhotel()->plugin_path() . "/templates/{$slug}-{$name}.php";
		}

		// If template file doesn't exist, look in yourtheme/slug.php and yourtheme/opalhotel/slug.php
		if ( ! $template ) {
			$template = locate_template( array( "{$slug}.php", opalhotel_template_path() . "/{$slug}.php" ) );
		}

		if ( ! $template && file_exists( opalhotel()->plugin_path() . "/templates/{$slug}.php" ) ) {
			$template = opalhotel()->plugin_path() . "/templates/{$slug}.php";
		}

		// Allow 3rd party plugins to filter template file from their plugin.
		$template = apply_filters( 'opalhotel_get_template_part', $template, $slug, $name );

		if ( $template ) {
			load_template( $template, false );
		}
	}
}

if ( ! function_exists( 'opalhotel_get_template' ) ) {
	/**
	 * Get other templates (e.g. product attributes) passing attributes and including the file.
	 *
	 * @access public
	 * @param string $template_name
	 * @param array $args (default: array())
	 * @param string $template_path (default: '')
	 * @param string $default_path (default: '')
	 */
	function opalhotel_get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
		if ( $args && is_array( $args ) ) {
			extract( $args );
		}

		$located = opalhotel_locate_template( $template_name, $template_path, $default_path );

		if ( ! file_exists( $located ) ) {
			_doing_it_wrong( __FUNCTION__, sprintf( '<code>%s</code> does not exist.', $located ), '0.1' );
			return;
		}

		// Allow 3rd party plugin filter template file from their plugin.
		$located = apply_filters( 'opalhotel_get_template', $located, $template_name, $args, $template_path, $default_path );

		do_action( 'opalhotel_before_template_part', $template_name, $template_path, $located, $args );

		include( $located );

		do_action( 'opalhotel_after_template_part', $template_name, $template_path, $located, $args );
	}
}

if ( ! function_exists( 'opalhotel_get_template_content' ) ) {
	/**
	 * Like opalhotel_get_template, but returns the HTML instead of outputting.
	 * @see opalhotel_get_template
	 * @since 2.5.0
	 */
	function opalhotel_get_template_content( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
		ob_start();
		opalhotel_get_template( $template_name, $args, $template_path, $default_path );
		return ob_get_clean();
	}
}

if ( ! function_exists( 'opalhotel_locate_template' ) ) {
	/**
	 * Locate a template and return the path for inclusion.
	 *
	 * This is the load order:
	 *
	 *		yourtheme		/	$template_path	/	$template_name
	 *		yourtheme		/	$template_name
	 *		$default_path	/	$template_name
	 *
	 * @access public
	 * @param string $template_name
	 * @param string $template_path (default: '')
	 * @param string $default_path (default: '')
	 * @return string
	 */
	function opalhotel_locate_template( $template_name, $template_path = '', $default_path = '' ) {
		if ( ! $template_path ) {
			$template_path = opalhotel_template_path();
		}

		if ( ! $default_path ) {
			$default_path = opalhotel()->plugin_path() . '/templates/';
		}

		// Look within passed path within the theme - this is priority.
		$template = locate_template(
			array(
				trailingslashit( $template_path ) . $template_name,
				$template_name
			)
		);
		// Get default template/
		if ( ! $template ) {
			$template = $default_path . $template_name;
		}

		// Return what we found.
		return apply_filters( 'opalhotel_locate_template', $template, $template_name, $template_path );
	}
}

if ( ! function_exists( 'opalhotel_is_room_taxonomy' ) ) {
	/* is taxonomy page of opalhotel_room post type */
	function opalhotel_is_room_taxonomy() {
		return is_tax( get_object_taxonomies( OPALHOTEL_CPT_ROOM ) );
	}
}

if ( ! function_exists( 'opalhotel_is_hotel_taxonomy' ) ) {
	/* is taxonomy page of opalhotel_hotel post type */
	function opalhotel_is_hotel_taxonomy() {
		return is_tax( get_object_taxonomies( OPALHOTEL_CPT_HOTEL ) );
	}
}

if ( ! function_exists( 'opalhotel_i18n' ) ) {

	/* i18n */
	function opalhotel_i18n() {
		global $wp_locale, $post;

		return apply_filters( 'opalhotel_i18n', array(
	 				'ajaxurl'	=> wp_nonce_url( admin_url( 'admin-ajax.php' ), 'opalhotel_nonce', 'opalhotel-nonce' ),
	 				'simpleAjaxUrl' => admin_url( 'admin-ajax.php' ),
	 				'assetsURI'		=> OPALHOTEL_URI . 'assets/',
	 				'options'	=> opalhotel_options(),
	 				'timezone_string'	=> esc_js( opalhotel_get_timezone_string() ),
	 				'label'		=> array(
	 						'delete'			=> __( 'Delete', 'opal-hotel-room-booking' ),
	 						'unavailable'		=> __( 'Unavailable', 'opal-hotel-room-booking' ),
	 						'available'			=> __( 'Available', 'opal-hotel-room-booking' ),
	 						'geoLocationError'	=> __( 'Sorry we can not find your position right now. Please try later.', 'opal-hotel-room-booking' ),
	 						'oops'				=> __( 'Oops! Please try again.', 'opal-hotel-room-booking' )
	 					),
	 				'datepicker'	=> array(
	 						'closeText'         => __( 'Done', 'opal-hotel-room-booking' ),
					        'currentText'       => __( 'Today', 'opal-hotel-room-booking' ),
					        'monthNames'        => opalhotel_object_to_array( $wp_locale->month ),
					        'monthNamesShort'   => $wp_locale->month_abbrev,
					        'monthStatus'       => __( 'Show a different month', 'opal-hotel-room-booking' ),
					        'dayNames'          => opalhotel_object_to_array( $wp_locale->weekday ),
					        'dayNamesShort'     => opalhotel_object_to_array( $wp_locale->weekday_abbrev ),
					        'dayNamesMin'       => opalhotel_object_to_array( $wp_locale->weekday_initial ),
					        // set the date format to match the WP general date settings
					        'dateFormat'        => opalhotel_date_php_to_js( get_option( 'date_format' ) ),
					        // get the start of week from WP general setting
					        'firstDay'          => get_option( 'start_of_week' ),
					        // is Right to left language? default is false
					        'isRTL'             => isset( $wp_locale->is_rtl ) && $wp_locale->is_rtl ? true : false,
	 					),
	 				'arrival_departure_invalid'	=> __( 'Arrival date and Departure date is invalid.', 'opal-hotel-room-booking' ),
	 				'select'		=> array(
	 						'placeholder'	=> __( 'Search Package', 'opal-hotel-room-booking' ),
	 						'empty'			=> __( 'Please Select Package.', 'opal-hotel-room-booking' )
	 					),
	 				'something_wrong'		=> __( 'Something went wrong. Please try again.', 'opal-hotel-room-booking' ),
	 				'quantity_invalid'		=> __( 'Quantity is invalid. Please try again.', 'opal-hotel-room-booking' ),
	 				'is_reservation_page'	=> $post && $post->ID == opalhotel_get_page_id( 'reservation' ) ? true : false,
	 				'coupon_empty'			=> __( 'Please enter coupon code.', 'opal-hotel-room-booking' ),
	 				'require_rating'		=> __( 'Please rating before submit your review.', 'opal-hotel-room-booking' ),
	 				'datepicker_invalid'	=> __( 'Arrival date and Departure date is invalid.', 'opal-hotel-room-booking' ),
	 				'enter_amount'			=> __( 'Please enter amount.', 'opal-hotel-room-booking' ),
	 				'select_date_week'		=> __( 'Please select week days.', 'opal-hotel-room-booking' ),
	 				'nonces'				=> array(
	 						'preload_single_book_room_form'	=> wp_create_nonce( 'opalhotel-single-book-room-form' ),
	 						'get_location_by_ip'			=> wp_create_nonce( 'opalhotel-get-location-by-ip' )
	 					)
			) );
	}

	function opalhotel_object_to_array( $obj ) {
		$results = array();
		foreach ( $obj as $k => $value ) {
			$results[] = $value;
		}
		return $results;
	}

	function opalhotel_options() {
		global $wpdb;
		$sql = $wpdb->prepare("
				SELECT option_name, option_value FROM $wpdb->options
				WHERE option_name LIKE %s
			", 'opal-hotel-room-booking' . '%' );
		$results = $wpdb->get_results( $sql );

		$data = array();
		if ( $results ) {
			foreach ( $results as $name => $option ) {
				$name = substr( $option->option_name, 10 );
				$data[ $name ] = maybe_unserialize( $option->option_value );
			}
		}
		return $data;
	}

}

if ( ! function_exists( 'opalhotel_date_php_to_js' ) ) {
	/* convert date format from setting to datepicker format date*/
	function opalhotel_date_php_to_js( $format ) {
        $return = 'yy-mm-dd';
		switch( $format ) {
	        //Predefined WP date formats
	        case 'F j, Y':
	            $return = 'MM dd, yy';
	            break;
	        case 'Y/m/d':
	            $return = 'yy/mm/dd';
	            break;
	        case 'm/d/Y':
	            $return = 'mm/dd/yy';
	            break;
	        case 'd/m/Y':
	            $return = 'dd/mm/yy';
	            break;
	        case 'Y-m-d':
	            $return = 'yy-mm-dd';
	            break;
	        case 'm-d-Y':
	            $return = 'mm-dd-yy';
	            break;
	        case 'd/m/Y':
	            $return = 'dd-mm-yy';
	            break;
	        case 'default':
	            $return = 'yy-mm-dd';
	            break;
     	}
     	return $return;
	}
}

if ( ! function_exists( 'opalhotel_add_notices' ) ) {
	/* add noctice */
	function opalhotel_add_notices( $message, $type = 'error' ) {

		$notices = OpalHotel()->session->get( 'opalhotel_notices', array() );

		// Backward compatibility
		if ( 'success' === $type ) {
			$message = apply_filters( 'opalhotel_add_message', $message );
		}

		$notices[$type][] = apply_filters( 'opalhotel_add_' . $type, $message );

		OpalHotel()->session->set( 'opalhotel_notices', $notices );

	}
}

if ( ! function_exists( 'opalhotel_count_notices' ) ) {

	function opalhotel_count_notices( $type = 'error' ) {
		$notices = OpalHotel()->session->get( 'opalhotel_notices', array() );
		if ( isset( $notices[ $type ] ) ) {
			return count( $notices[ $type ] );
		}
	}

}

if ( ! function_exists( 'opalhotel_clear_notices' ) ) {
	/**
	 * Unset all notices.
	 *
	 * @since 0.1
	 */
	function opalhotel_clear_notices() {
		OpalHotel()->session->set( 'opalhotel_notices', null );
	}

}

if ( ! function_exists( 'opalhotel_print_notices' ) ) {
	/**
	 * Prints messages and errors which are stored in the session, then clears them.
	 *
	 * @since 0.1
	 */
	function opalhotel_print_notices() {

		$all_notices  = OpalHotel()->session->get( 'opalhotel_notices', array() );
		$notice_types = apply_filters( 'opalhotel_notice_types', array( 'error', 'success' ) );

		foreach ( $notice_types as $type ) {
			if ( isset( $all_notices[$type] ) ) {
				opalhotel_get_template( "notices/{$type}.php", array(
					'messages' => $all_notices[$type]
				) );
			}
		}

		opalhotel_clear_notices();
	}
}

if ( ! function_exists( 'opalhotel_print_notice_message' ) ) {
	function opalhotel_print_notice_message( $message = '', $type = 'error' ) {
		$notice_types = apply_filters( 'opalhotel_notice_types', array( 'error', 'success' ) );

		if ( in_array( $type, $notice_types ) ) {
			opalhotel_get_template( "notices/{$type}.php", array(
					'messages' => array( $message )
				) );
		}
	}
}

if ( ! function_exists( 'opalhotel_get_page_id' ) ) {

	/* get page id setting */
	function opalhotel_get_page_id( $page = 'reservation' ) {
		return apply_filters( 'opalhotel_page_id', absint( get_option( 'opalhotel_' . $page . '_page_id' ) ) );
	}
}

if ( ! function_exists( 'opalhotel_get_available_url' ) ) {
	/* available permalink */
	function opalhotel_get_available_url() {
		$url = home_url();
		if ( $page_id = opalhotel_get_page_id( 'available' ) ) {
			$url = get_permalink( $page_id );
		}

		return apply_filters( 'opalhotel_get_available_url', $url );
	}

}

if ( ! function_exists( 'opalhotel_get_reservation_url' ) ) {
	/* reservation permalink */
	function opalhotel_get_reservation_url() {
		$url = home_url();
		if ( $page_id = opalhotel_get_page_id( 'reservation' ) ) {
			$url = get_permalink( $page_id );
		}

		return apply_filters( 'opalhotel_get_reservation_url', $url );
	}

}

if ( ! function_exists( 'opalhotel_get_hotel_available_url' ) ) {
	/* available permalink */
	function opalhotel_get_hotel_available_url() {
		$url = home_url();
		if ( $page_id = opalhotel_get_page_id( 'hotel_available' ) ) {
			$url = get_permalink( $page_id );
		}

		return apply_filters( 'opalhotel_get_hotel_available_url', $url );
	}

}

if ( ! function_exists( 'opalhotel_get_checkout_url' ) ) {
	/* checkout permalink */
	function opalhotel_get_checkout_url() {
		$url = home_url();
		if ( $page_id = opalhotel_get_page_id( 'checkout' ) ) {
			$url = get_permalink( $page_id );
		}

		return apply_filters( 'opalhotel_get_checkout_url', $url );
	}

}

if ( ! function_exists( 'opalhotel_get_term_url' ) ) {
	/* term permalink */
	function opalhotel_get_term_url() {
		$url = home_url();
		if ( $page_id = opalhotel_get_page_id( 'terms' ) ) {
			$url = get_permalink( $page_id );
		}

		return apply_filters( 'opalhotel_get_term_url', $url );
	}

}

if ( ! function_exists( 'opalhotel_get_favorite_url' ) ) {
	/* checkout permalink */
	function opalhotel_get_favorite_url() {
		$url = home_url();
		if ( $page_id = opalhotel_get_page_id( 'favorited' ) ) {
			$url = get_permalink( $page_id );
		}

		return apply_filters( 'opalhotel_get_favorite_url', $url );
	}

}

if ( ! function_exists( 'opalhotel_get_max_rooms_number' ) ) {

	/**
	 * opalhotel_get_max_rooms_number get max adult in all room setting
	 * @return int
	 */
	function opalhotel_get_max_rooms_number() {
		$max = get_option( 'opalhotel_max_rooms_number', 5 );
		return apply_filters( 'opalhotel_get_max_rooms_number', absint( $max ) );
	}
}

if ( ! function_exists( 'opalhotel_get_max_adults' ) ) {

	/**
	 * opalhotel_get_max_adults get max adult in all room setting
	 * @return int
	 */
	function opalhotel_get_max_adults() {
		$max = get_option( 'opalhotel_search_available_max_adult' );

		if ( ! $max ) {
			global $wpdb;
			$sql = $wpdb->prepare( "
					SELECT MAX( meta.meta_value ) FROM $wpdb->postmeta AS meta
						INNER JOIN $wpdb->posts AS post ON post.ID = meta.post_id AND meta.meta_key = %s
					WHERE
						post.post_type = %s
						AND post.post_status = %s
				", '_adults', OPALHOTEL_CPT_ROOM, 'publish' );

			$max = $wpdb->get_var( $sql );
		}

		return apply_filters( 'opalhotel_max_adults', absint( $max ) );
	}
}

if ( ! function_exists( 'opalhotel_get_max_childs' ) ) {

	/**
	 * opalhotel_get_max_childs get max child in all room setting
	 * @return int
	 */
	function opalhotel_get_max_childs() {
		$max = get_option( 'opalhotel_search_available_max_child' );
		if ( ! $max ) {
			global $wpdb;
			$sql = $wpdb->prepare( "
					SELECT MAX( meta.meta_value ) FROM $wpdb->postmeta AS meta
						INNER JOIN $wpdb->posts AS post ON post.ID = meta.post_id AND meta.meta_key = %s
					WHERE
						post.post_type = %s
						AND post.post_status = %s
				", '_childrens', OPALHOTEL_CPT_ROOM, 'publish' );
			$max = $wpdb->get_var( $sql );
		}

		return apply_filters( 'opalhotel_max_childrens', absint( $max ) );
	}
}

if ( ! function_exists( 'opalhotel_format_id' ) ) {

	function opalhotel_format_id( $id = nul ) {
		if ( ! $id ) return;

		return '#' . $id;
	}
}

if ( ! function_exists( 'opalhotel_generate_uniqid_hash' ) ) {
	/**
	 * opalhotel_generate_uniqid_hash
	 * generate md5 string unique from array
	 * @param $args
	 * @return string
	 */
	function opalhotel_generate_uniqid_hash( $args ) {
		$string = array();
		if ( ! empty( $args ) ) {
        	ksort( $args );
			foreach ( $args as $name => $value ) {
				if ( is_array( $value ) || is_object( $value ) ) {
					$value = http_build_query( $value );
				}
				$string[] = trim( $name ) . trim( $value );
			}
		}

		return md5( implode( '_', $string ) );
	}
}

if ( ! function_exists( 'opalhotel_tax_is_enable' ) ) {
	/* is enable */
	function opalhotel_tax_is_enable() {
		return apply_filters( 'opalhotel_tax_is_enable', get_option( 'opalhotel_tax_enable', 1 ) );
	}
}

if ( ! function_exists( 'opalhotel_get_tax' ) ) {
	/* get tax setting */
	function opalhotel_get_tax() {
		return apply_filters( 'opalhotel_get_tax', get_option( 'opalhotel_tax', 0 ) );
	}
}

if ( ! function_exists( 'opalhotel_tax_enable' ) ) {

	/* is table enable */
	function opalhotel_tax_enable() {
		return apply_filters( 'opalhotel_tax_enable', get_option( 'opalhotel_tax_enable', 1 ) );
	}

}

if ( ! function_exists( 'opalhotel_tax_enable_cart' ) ) {

	/* is table enable */
	function opalhotel_tax_enable_cart() {
		return apply_filters( 'opalhotel_tax_enable_cart', get_option( 'opalhotel_tax_incl_cart', 0 ) );
	}

}

/**
 * Get endpoint URL.
 *
 * Gets the URL for an endpoint, which varies depending on permalink settings.
 *
 * @param  string $endpoint
 * @param  string $value
 * @param  string $permalink
 *
 * @return string
 */
function opalhotel_get_endpoint_url( $endpoint, $value = '', $permalink = '' ) {
	if ( ! $permalink )
		$permalink = get_permalink();

	if ( get_option( 'permalink_structure' ) ) {
		if ( strstr( $permalink, '?' ) ) {
			$query_string = '?' . parse_url( $permalink, PHP_URL_QUERY );
			$permalink    = current( explode( '?', $permalink ) );
		} else {
			$query_string = '';
		}
		$url = trailingslashit( $permalink ) . $endpoint . '/' . $value . $query_string;
	} else {
		$url = add_query_arg( $endpoint, $value, $permalink );
	}

	return apply_filters( 'opalhotel_get_endpoint_url', $url, $endpoint, $value, $permalink );
}

/* is opalhotel endpoint url */
function opalhotel_is_endpoint_url( $endpoint = '' ) {
	global $wp;
	if ( OpalHotel()->request && isset( $wp->query_vars[ $endpoint ] ) && array_keys( $endpoint, OpalHotel()->request->endpoints ) ) {
		return true;
	}
}

if ( ! function_exists( 'opalhotel_get_days_of_week' ) ) {
	function opalhotel_get_days_of_week() {
		$days = array(
				__( 'Sun', 'opal-hotel-room-booking' ),
				__( 'Mon', 'opal-hotel-room-booking' ),
				__( 'Tue', 'opal-hotel-room-booking' ),
				__( 'Wed', 'opal-hotel-room-booking' ),
				__( 'Thu', 'opal-hotel-room-booking' ),
				__( 'Fri', 'opal-hotel-room-booking' ),
				__( 'Sat', 'opal-hotel-room-booking' )
			);
		return apply_filters( 'opalhotel_get_days_of_week', $days );
	}
}

if ( ! function_exists( 'opalhotel_format_date' ) ) {
	/* format date */
	function opalhotel_format_date( $timestamp = '' ) {
		return apply_filters( 'opalhotel_format_date', date_i18n( get_option( 'date_format' ), $timestamp ), $timestamp );
	}
}

if ( ! function_exists( 'opalhotel_get_hotel' ) ) {
	function opalhotel_get_hotel( $hotel_id = null ) {
		if ( ! $hotel_id || get_post_type( $hotel_id ) !== OPALHOTEL_CPT_HOTEL ) {
			return;
		}

		return OpalHotel_Hotel::instance( $hotel_id );
	}
}

if ( ! function_exists( 'opalhotel_enable_hotel_mode' ) ) {

	/**
	 * Is Enabled Hotel Mode
	 */
	function opalhotel_enable_hotel_mode() {
		$current_theme = wp_get_theme();
		$mode = false;
		if ( $current_theme && $current_theme->get_template() !== 'paradise' ) {
			$mode = true;
		}

		return apply_filters( 'opalhotel_enable_hotel_mode', $mode );
	}
}
















