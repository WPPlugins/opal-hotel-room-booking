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

class OpalHotel_Email_Customer_Processing extends OpalHotel_Email {

	public function __construct() {

		$this->id = 'processing';
		$this->title = __( 'Processing Payment', 'opal-hotel-room-booking' );
		parent::__construct();

		if ( ! $this->enabled ) {
			$this->enabled = true;
		}

		if ( ! $this->recipient ) {
			$this->recipient = get_option( 'admin_email' );
		}

		if ( ! $this->heading ) {
			$this->heading = __( 'Your reservation has been received', 'opal-hotel-room-booking' );
		}

		if ( ! $this->subject ) {
			$this->subject = __( 'Your reservation has been received.', 'opal-hotel-room-booking' );
		}

		$this->template_html = 'emails/customer-processing.php';
		$this->template_plain = 'emails/plain/customer-processing.php';

		add_action( 'opalhotel_update_status_pending_to_processing', array( $this, 'trigger' ) );
		add_action( 'opalhotel_update_status_pending_to_on-hold', array( $this, 'trigger' ) );
	}

	/* get settings */
	public function admin_settings() {
		return array(
				array(
							'type'		=> 'section_start',
							'id'		=> 'completed_settings',
							'title'		=> __( 'Customer Completed Payment', 'opal-hotel-room-booking' ),
							'desc'		=> __( 'An email notification sent to customer after payment with Processing status.', 'opal-hotel-room-booking' )
						),

					array(
							'type'		=> 'checkbox',
							'id'		=> 'opalhotel_email_' . $this->id . '_enable',
							'title'		=> __( 'Enable/Disable', 'opal-hotel-room-booking' ),
							'default'	=> 1
						),

					array(
							'type'		=> 'text',
							'id'		=> 'opalhotel_email_' . $this->id . '_heading',
							'title'		=> __( 'Email Heading', 'opal-hotel-room-booking' ),
							'default'	=> __( 'Thank you for your reservation', 'opal-hotel-room-booking' )
						),

					array(
							'type'		=> 'text',
							'id'		=> 'opalhotel_email_' . $this->id . '_subject',
							'title'		=> __( 'Email Subject', 'opal-hotel-room-booking' ),
							'default'	=> __( 'Thank you for your reservation.', 'opal-hotel-room-booking' )
						),

					array(
							'type'		=> 'select',
							'id'		=> 'opalhotel_email_' . $this->id . '_type',
							'title'		=> __( 'Email Subject', 'opal-hotel-room-booking' ),
							'default'	=> 'html',
							'options'	=> array(
									'html'		=> __( 'HTML', 'opal-hotel-room-booking' ),
									'plain'		=> __( 'Plain', 'opal-hotel-room-booking' )
								)
						),

					array(
							'type'		=> 'section_end',
							'id'		=> 'completed_settings'
						)
			);
	}

	public function trigger( $order_id ) {
		if ( ! $this->enabled || ! $this->get_recipient() ) {
			return;
		}

		$this->order = opalhotel_get_order( $order_id );
		$this->recipient = $this->order->customer_email;
		$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );

	}

	public function get_heading() {
		return apply_filters( 'opalhotel_email_' . $this->id . '_heading', $this->format_string( $this->heading ) );
	}

	/* content html */
	public function get_content_html() {
		return opalhotel_get_template_content( $this->template_html, array(
				'order'		=> $this->order,
				'heading'	=> $this->get_heading(),
				'admin'		=> false,
				'plain_text'=> true,
				'email'		=> $this
			) );
	}

	/* content text/plain */
	public function get_content_plain() {
		return opalhotel_get_template_content( $this->template_plain, array(
				'order'		=> $this->order,
				'heading'	=> $this->get_heading(),
				'admin'		=> false,
				'plain_text'=> true,
				'email'		=> $this
			) );
	}

}
return new OpalHotel_Email_Customer_Processing();