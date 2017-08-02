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

class OpalHotel_Email_Canceled extends OpalHotel_Email {

	public function __construct() {

		$this->id = 'cancelled';
		$this->title = __( 'Email Cancelled Payment', 'opal-hotel-room-booking' );
		parent::__construct();

		if ( $this->enabled === false ) {
			$this->enabled = true;
		}

		if ( $this->recipient === false ) {
			$this->recipient = get_option( 'admin_email' );
		}

		if ( $this->heading === false ) {
			$this->heading = __( 'A reservation has been cancelled.', 'opal-hotel-room-booking' );
		}

		if ( $this->subject === false ) {
			$this->subject = __( 'New Cancelled Reservation', 'opal-hotel-room-booking' );
		}

		$this->template_html = 'emails/admin-cancelled.php';
		$this->template_plain = 'emails/plain/admin-cancelled.php';

		add_action( 'opalhotel_update_status_cancelled', array( $this, 'trigger' ) );
	}

	/* get settings */
	public function admin_settings() {
		return array(
				array(
							'type'		=> 'section_start',
							'id'		=> 'cancelled_settings',
							'title'		=> __( 'Reservation Cancelled', 'opal-hotel-room-booking' ),
							'desc'		=> __( 'New emails are sent to chosen recipient(s) when a new booking is cancelled.', 'opal-hotel-room-booking' )
						),

					array(
							'type'		=> 'checkbox',
							'id'		=> 'opalhotel_email_' . $this->id . '_enable',
							'title'		=> __( 'Enable/Disable', 'opal-hotel-room-booking' ),
							'default'	=> 1
						),

					array(
							'type'		=> 'text',
							'id'		=> 'opalhotel_email_' . $this->id . '_recipient',
							'title'		=> __( 'Recipient(s)', 'opal-hotel-room-booking' ),
							'default'	=> get_option( 'admin_email' )
						),

					array(
							'type'		=> 'text',
							'id'		=> 'opalhotel_email_' . $this->id . '_heading',
							'title'		=> __( 'Email Heading', 'opal-hotel-room-booking' ),
							'default'	=> __( 'A reservation has been cancelled.', 'opal-hotel-room-booking' )
						),

					array(
							'type'		=> 'text',
							'id'		=> 'opalhotel_email_' . $this->id . '_subject',
							'title'		=> __( 'Email Subject', 'opal-hotel-room-booking' ),
							'default'	=> __( 'New Cancelled Reservation.', 'opal-hotel-room-booking' )
						),

					array(
							'type'		=> 'select',
							'id'		=> 'opalhotel_email_' . $this->id . '_type',
							'title'		=> __( 'Email Type', 'opal-hotel-room-booking' ),
							'default'	=> 'html',
							'options'	=> array(
									'html'		=> __( 'HTML', 'opal-hotel-room-booking' ),
									'plain'		=> __( 'Plain', 'opal-hotel-room-booking' )
								)
						),

					array(
							'type'		=> 'section_end',
							'id'		=> 'cancelled_settings'
						)
			);
	}

	public function trigger( $order_id ) {
		if ( ! $this->enabled || ! $this->get_recipient() ) {
			return;
		}

		$this->order = opalhotel_get_order( $order_id );
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
				'admin'		=> true,
				'plain_text'=> false,
				'email'		=> $this
			) );
	}

	/* content text/plain */
	public function get_content_plain() {
		return opalhotel_get_template_content( $this->template_plain, array(
				'order'		=> $this->order,
				'heading'	=> $this->get_heading(),
				'admin'		=> true,
				'plain_text'=> true,
				'email'		=> $this
			) );
	}

}
return new OpalHotel_Email_Canceled();