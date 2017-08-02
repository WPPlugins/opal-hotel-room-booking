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

class OpalHotel_Emails {

	/* email types */
	public $emails = null;

	/* protected $instance instead. singleton */
	protected static $instance = null;

	static function instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/* email constructor */
	public function __construct() {

		$this->init();
		// $this->send_transactional_email();
		/* email header */
		add_action( 'opalhotel_email_header', array( $this, 'email_header' ) );
		/* email footer */
		add_action( 'opalhotel_email_footer', array( $this, 'email_footer' ) );

		/* customer details */
		add_action( 'opalhotel_email_order_confirm', array( $this, 'order_confirm' ), 10, 3 );
		add_action( 'opalhotel_email_customer_details', array( $this, 'customer_details' ), 10, 3 );
		add_action( 'opalhotel_email_order_details', array( $this, 'order_details' ), 10, 3 );
	}

	/* protected init emails */
	protected function init(){

		require_once 'emails/class-opalhotel-email.php';
		$this->emails[] = require_once 'emails/class-opalhotel-email-new-booking.php';
		$this->emails[] = require_once 'emails/class-opalhotel-email-cancelled.php';
		$this->emails[] = require_once 'emails/class-opalhotel-email-customer-completed-booking.php';
		$this->emails[] = require_once 'emails/class-opalhotel-email-customer-processing-booking.php';
		$this->emails[] = require_once 'emails/class-opalhotel-email-refunded.php';

		do_action( 'opalhotel_emails_init', $this );

	}

	/* return $this->emails */
	public function get_mails() {
		return $this->emails;
	}

	/* email header */
	public function email_header( $heading ) {
		opalhotel_get_template( 'emails/email-header.php', array( 'heading' => $heading ) );
	}

	/* email footer */
	public function email_footer() {
		opalhotel_get_template( 'emails/email-footer.php' );
	}

	public function order_confirm( $order, $admin, $email ) {
		opalhotel_get_template( 'emails/customer-confirm.php', array(
				'order'		=> $order,
				'admin'		=> $admin,
				'email'		=> $email
			) );
	}

	/* order details */
	public function order_details( $order, $admin, $email ) {
		opalhotel_get_template( 'emails/order-details.php', array(
				'order'		=> $order,
				'admin'		=> $admin,
				'email'		=> $email
			) );
	}

	/* customer details */
	public function customer_details( $order, $admin, $email ) {
		opalhotel_get_template( 'emails/customer-details.php', array(
				'order'		=> $order,
				'admin'		=> $admin,
				'email'		=> $email
			) );
	}

}