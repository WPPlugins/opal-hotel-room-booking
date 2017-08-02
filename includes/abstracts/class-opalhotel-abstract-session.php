<?php
/**
 * @Author: brainos
 * @Date:   2016-04-24 07:42:54
 * @Last Modified by:   someone
 * @Last Modified time: 2016-05-15 12:54:49
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

abstract class OpalHotel_Abstract_Session {

	/** @var array $_data  */
	protected $_data = array();

	/** @var bool $_dirty When something changes */
	protected $_dirty = false;

	/**
	 * __get function.
	 *
	 * @param mixed $key
	 * @return mixed
	 */
	public function __get( $key ) {
		return $this->get( $key );
	}

	/**
	 * __set function.
	 *
	 * @param mixed $key
	 * @param mixed $value
	 */
	public function __set( $key, $value ) {
		$this->set( $key, $value );
	}

	 /**
	 * __isset function.
	 *
	 * @param mixed $key
	 * @return bool
	 */
	public function __isset( $key ) {
		return isset( $this->_data[ sanitize_title( $key ) ] );
	}

	/**
	 * __unset function.
	 *
	 * @param mixed $key
	 */
	public function __unset( $key ) {
		if ( isset( $this->_data[ $key ] ) ) {
			unset( $this->_data[ $key ] );
			$this->_dirty = true;
		}
	}

	/**
	 * Get a session variable.
	 *
	 * @param string $key
	 * @param  mixed $default used if the session variable isn't set
	 * @return mixed value of session variable
	 */
	public function get( $key, $default = null ) {
		$key = sanitize_key( $key );
		return isset( $this->_data[ $key ] ) ? $this->_data[ $key ] : $default;
	}

	/**
	 * Set a session variable.
	 *
	 * @param string $key
	 * @param mixed $value
	 */
	public function set( $key, $value ) {
		if ( $value !== $this->get( $key ) ) {
			$this->_data[ sanitize_key( $key ) ] = $value;
			$this->_dirty = true;
			/* trigger update session */
			do_action( 'opalhotel_update_session', $this->_data, $this->_dirty );
		}
	}

}
