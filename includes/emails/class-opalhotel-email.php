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

class OpalHotel_Email{

	/**
	 * Email method ID.
	 * @var String
	 */
	public $id;

	/**
	 * Email method title.
	 * @var string
	 */
	public $title;

	/**
	 * 'yes' if the method is enabled.
	 * @var string yes, no
	 */
	public $enabled;

	/**
	 * Plain text template path.
	 * @var string
	 */
	public $template_plain;

	/**
	 * HTML template path.
	 * @var string
	 */
	public $template_html;

	/**
	 * Template path.
	 * @var string
	 */
	public $template_base;

	/**
	 * Recipients for the email.
	 * @var string
	 */
	public $recipient;

	/**
	 * Heading for the email content.
	 * @var string
	 */
	public $heading;

	/**
	 * Subject for the email.
	 * @var string
	 */
	public $subject;

	/**
	 * Object this email is for, for example a customer, product, or email.
	 * @var email_type
	 */
	public $email_type;

	/**
	 * Strings to find in subjects/headings.
	 * @var array
	 */
	public $find = array();

	/**
	 * Strings to replace in subjects/headings.
	 * @var array
	 */
	public $replace = array();

	/**
	 * True when the email notification is sent to customers.
	 * @var bool
	 */
	protected $customer_email = false;

	/**
	 *  List of preg* regular expression patterns to search for,
	 *  used in conjunction with $plain_replace.
	 *  https://raw.github.com/ushahidi/wp-silcc/master/class.html2text.inc
	 *  @var array $plain_search
	 *  @see $plain_replace
	 */
	public $plain_search = array(
		"/\r/",                                          // Non-legal carriage return
		'/&(nbsp|#160);/i',                              // Non-breaking space
		'/&(quot|rdquo|ldquo|#8220|#8221|#147|#148);/i', // Double quotes
		'/&(apos|rsquo|lsquo|#8216|#8217);/i',           // Single quotes
		'/&gt;/i',                                       // Greater-than
		'/&lt;/i',                                       // Less-than
		'/&#38;/i',                                      // Ampersand
		'/&#038;/i',                                     // Ampersand
		'/&amp;/i',                                      // Ampersand
		'/&(copy|#169);/i',                              // Copyright
		'/&(trade|#8482|#153);/i',                       // Trademark
		'/&(reg|#174);/i',                               // Registered
		'/&(mdash|#151|#8212);/i',                       // mdash
		'/&(ndash|minus|#8211|#8722);/i',                // ndash
		'/&(bull|#149|#8226);/i',                        // Bullet
		'/&(pound|#163);/i',                             // Pound sign
		'/&(euro|#8364);/i',                             // Euro sign
		'/&#36;/',                                       // Dollar sign
		'/&[^&\s;]+;/i',                                 // Unknown/unhandled entities
		'/[ ]{2,}/'                                      // Runs of spaces, post-handling
	);

	/**
	 *  List of pattern replacements corresponding to patterns searched.
	 *  @var array $plain_replace
	 *  @see $plain_search
	 */
	public $plain_replace = array(
		'',                                             // Non-legal carriage return
		' ',                                            // Non-breaking space
		'"',                                            // Double quotes
		"'",                                            // Single quotes
		'>',                                            // Greater-than
		'<',                                            // Less-than
		'&',                                            // Ampersand
		'&',                                            // Ampersand
		'&',                                            // Ampersand
		'(c)',                                          // Copyright
		'(tm)',                                         // Trademark
		'(R)',                                          // Registered
		'--',                                           // mdash
		'-',                                            // ndash
		'*',                                            // Bullet
		'£',                                            // Pound sign
		'EUR',                                          // Euro sign. € ?
		'$',                                            // Dollar sign
		'',                                             // Unknown/unhandled entities
		' '                                             // Runs of spaces, post-handling
	);

	public function __construct() {
		$this->enabled = get_option( 'opalhotel_email_' . $this->id . '_enable' );
		$this->recipient = get_option( 'opalhotel_email_' . $this->id . '_recipient' );
		$this->heading = get_option( 'opalhotel_email_' . $this->id . '_heading' );
		$this->subject = get_option( 'opalhotel_email_' . $this->id . '_subject' );
		$this->email_type = get_option( 'opalhotel_email_' . $this->id . '_type', 'html' );
	}

	public function send( $to, $subject, $message, $headers, $attachments ) {
		add_filter( 'wp_mail_from', array( $this, 'get_from_address' ), 999 );
		add_filter( 'wp_mail_from_name', array( $this, 'get_from_name' ), 999 );
		add_filter( 'wp_mail_content_type', array( $this, 'get_content_type' ), 999 );

		$message = apply_filters( 'opalhotel_mail_content', $this->style_inline( $message ) );
		$return  = wp_mail( $to, $subject, $message, $headers, $attachments );

		remove_filter( 'wp_mail_from', array( $this, 'get_from_address' ), 999 );
		remove_filter( 'wp_mail_from_name', array( $this, 'get_from_name' ), 999 );
		remove_filter( 'wp_mail_content_type', array( $this, 'get_content_type' ), 999 );

		return $return;
	}

	/* get option */
	public function get_option( $name = null, $default = null ) {
		if ( ! $name ) {
			return $default;
		}

		return get_option( 'opalhotel_email_' . $this->id . '_' . $name, $default );
	}

	/* format string special character */
	public function format_string( $content = '' ) {
		return apply_filters( 'opalhotel_email_' . $this->id . '_format_string', str_replace( $this->find, $this->replace, $content ) );
	}

	/* get email subject */
	public function get_subject() {
		return apply_filters( 'opalhotel_email_' . $this->id . '_subject', $this->format_string( $this->subject ) );
	}

	public function get_heading() {
		return apply_filters( 'opalhotel_email_' . $this->id . '_heading', $this->format_string( $this->heading ) );
	}

	/* get email content */
	public function get_content() {

		if ( 'plain' === $this->get_email_type() ) {
			$email_content = preg_replace( $this->plain_search, $this->plain_replace, strip_tags( $this->get_content_plain() ) );
		} else {
			$email_content = $this->get_content_html();
		}

		return wordwrap( $email_content, 70 );
	}

	/* content html */
	public function get_content_html() {}

	/* content text/plain */
	public function get_content_plain() {}

	/* get email from sender */
	public function get_from_address() {
		return apply_filters( 'opalhotel_email_from_address', $this->get_option( 'opalhotel_email_from_address', get_option( 'admin_email' ) ) );
	}

	/* from name */
	public function get_from_name() {
		return apply_filters( 'opalhotel_email_from_name', $this->get_option( 'opalhotel_email_from_name', wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES ) ) );
	}

	/* email type */
	public function get_email_type() {
		return $this->email_type && class_exists( 'DOMDocument' ) ? $this->email_type : 'plain';
	}

	/* email content type */
	public function get_content_type() {
		switch ( $this->get_email_type() ) {
			case 'html' :
				return 'text/html';
			case 'multipart' :
				return 'multipart/alternative';
			default :
				return 'text/plain';
		}
	}

	/* style */
	public function style_inline( $message ) {
		return $message;
	}

	/* get headers */
	public function get_headers() {
		return apply_filters( 'opalhotel_email_headers', "Content-Type: " . $this->get_content_type() . "\r\n", $this->id );
	}

	/* get recipient */
	public function get_recipient() {
		return apply_filters( 'opalhotel_email_' . $this->id . '_recipient', $this->recipient );
	}

	/* get attachment files */
	public function get_attachments() {
		return array();
	}

}