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

abstract class OpalHotel_User_Abstract {

	public $user 	= null;

	public $id 		= null;

	function __construct( $user = null ) {

		if ( is_numeric( $user ) && ( $user = get_user_by( 'ID', $user ) ) ) {
			$this->user = $user;
			$this->id 	= $this->user->ID;
		} else if ( $user instanceof WP_User  ) {
			$this->user = $user;
			$this->id 	= $this->user->ID;
		}

		if ( ! $user ) {
			$current_user = wp_get_current_user();
			$this->id = $current_user->ID;
		}
	}

	function __get( $key ) {
		if ( ! isset( $this->{$key} ) || ! method_exists( $this, $key ) ) {
			return get_user_meta( $this->id,  $key, true );
		}
	}

}