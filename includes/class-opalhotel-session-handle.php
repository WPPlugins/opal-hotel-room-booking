<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

ob_start();
if ( ! session_id() ) {
	@session_start();
}
ob_end_clean();

class OpalHotel_Session_Handle extends OpalHotel_Abstract_Session
{

	// instance
	static $_instance = null;

	/* table storge */
	protected $_table = null;

	/* current user id */
	public $_user_id = null;

	/* cookie name */
	public $_cookie = '';
	public $_session = '';

	/* has user cookie */
	public $_has_cookie = false;

	public $_expire = null;

	/** @var array $_data  */
	protected $_data = array();

	/** @var bool $_dirty When something changes */
	protected $_dirty = false;

	function __construct()
	{

		/*
		 * cookie name
		 * COOKIEHASH constant is md5 hash of your siteurl so that you can be assured the cookie is unique
		 */
		$this->_cookie = $this->_session = 'opalhotel_' . COOKIEHASH;

		/* get cookie data */
		$this->_has_cookie = $this->get_cookie_data();
		if ( $this->_has_cookie && $this->_expire && $this->_user_id ) {

			/* override expire time before expire 1 hour */
			if ( $this->_expire <= time() - 1 * HOUR_IN_SECONDS ) {
				/* set new expire session time */
				$this->_expire = $this->get_expiry();
			}
		} else {

			/* get user id */
			$this->_user_id = $this->generate_user_id();
			/* get expiration time user session in our database */
			$this->_expire = $this->get_expiry();
		}

		// get all session data of single current user
		$this->_data = $this->get_session_data();

		/* shutdown save session database before php shutdown */
		// add_action( 'shutdown', array( $this, 'save' ) );
		// add_action( 'opalhotel_cart_set_session', array( $this, 'save' ) );
		add_action( 'opalhotel_update_session', array( $this, 'save' ), 10, 2 );
		/* destroy session when user logout */
		add_action( 'wp_logout', array( $this, 'session_destroy' ) );

		/* save user id as cookie storge when has cart */
		add_action( 'opalhotel_user_maybe_has_cart', array( $this, 'set_user_cookie' ) );
	}

	/* get session data */
	public function get_session_data() {
		global $wpdb;

		$session = array();
		if ( isset( $_SESSION[ $this->_session ] ) ) {
			$session = maybe_unserialize( $_SESSION[ $this->_session ] );
		}

		return apply_filters( 'opalhotel_get_session_data', $session );
	}

	/* generate user id */
	public function generate_user_id() {
		/* return current user id if logged in */
		if ( is_user_logged_in() ) {
			return get_current_user_id();
		} else {
			require_once( ABSPATH . 'wp-includes/class-phpass.php');
			$hasher = new PasswordHash( 8, false );

			/* return random string if not logged in*/
			return md5( $hasher->get_random_bytes( 42 ) );
		}
	}

	/* set cookie when user has cart item */
	public function set_user_cookie( $is_empty ) {

		/* save user session if cart is not empty */
		if ( ! $is_empty && $this->_dirty ) {
			$value = $this->_user_id . '||' . $this->_expire;
			setcookie( $this->_cookie, $value, $this->_expire, COOKIEPATH ? COOKIEPATH : '/', COOKIE_DOMAIN );

			/* has cookie */
			$this->_has_cookie = false;
		}
	}

	/* has session */
	public function has_session() {
		// $this->_has_cookie when user has cart
		// is_user_logged_in() when user logged in
		// $_COOKIE[ $this->_cookie ] when user's browser has cookie
		return $this->_has_cookie || is_user_logged_in() || isset( $_COOKIE[ $this->_cookie ] );
	}

	/* get cookie data. Eg. user_id, expire time */
	private function get_cookie_data() {
		if ( empty( $_COOKIE[ $this->_cookie ] ) ) {
			return false;
		}

		list( $this->_user_id, $this->_expire ) = explode( '||', $_COOKIE[ $this->_cookie ] );

		return true;
	}

	/* expiry session time */
	public function get_expiry() {
		return apply_filters( 'opalhotel_session_expire', time() + 24 * 60 * 60 );
	}

	/* save */
	public function save() {
		if ( ! $this->_dirty ) {
			return;
		}

		$_SESSION[ $this->_session ] = maybe_serialize( $this->_data );
	}

	/*
	 * session_destroy
	 *
	 */
	public function session_destroy() {

		/* empty cart */
		OpalHotel::instance()->cart->empty_cart();
		/* delete session */

		$this->delete_session();
		$this->_data = array();
		$this->_dirty = true;
	}

	/* delete session from database */
	public function delete_session() {
		unset( $_SESSION[ $this->_session ] );
	}

	static function instance()
	{
		if( ! empty( self::$_instance ) ) {
			return self::$_instance;
		}

		return self::$_instance = new self();
	}

}
