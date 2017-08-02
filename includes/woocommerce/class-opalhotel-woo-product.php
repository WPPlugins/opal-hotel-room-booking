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

if ( class_exists( 'WC_Product_Simple' ) && ! class_exists( 'OpalHotel_Room_Product' ) ) :

	class WC_Product_Room extends WC_Product_Simple {

		private $data = array();

	    public function __construct( $the_product, $args = null ) {
	    	if ( ! empty( $args ) ) {
	    		foreach ( $args as $name => $value ) {
	    			$this->data[$name] = $value;
	    		}
	    	}
	        parent::__construct( $the_product, $args );
	    }

	    public function get_price() {
	        $room = OpalHotel_Room::instance( $this->post, $this->data );
	        return $room->get_price();
	    }

	    /**
	     * Check if a product is purchasable
	     */
	    public function is_purchasable() {
	        return true;
	    }

	    public function is_sold_individually() {
	    	return false;
	    }

	}

endif;
