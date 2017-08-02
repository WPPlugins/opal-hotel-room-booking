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

class OpalHotel_User extends OpalHotel_User_Abstract {

	static $users = null;

	// get user
	static function get_user( $user_id = null ) {

		// return object if exits
		if ( ! empty( self::$users[ $user_id ] ) ) {
			return self::$users[ $user_id ];
		}

		return self::$users[ $user_id ] = new self( $user_id );
	}

	// get current user
	static function get_current_user() {
		$user_id = get_current_user_id();
		return self::get_user( $user_id );
	}

}