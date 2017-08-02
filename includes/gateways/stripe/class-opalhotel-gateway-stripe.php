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
 * Stripe
 */
class OpalHotel_Gateway_Stripe extends OpalHotel_Abstract_Gateway {

	/* sandbox mode */
	public $sandbox = false;

	/* gateway id */
	public $id 		= null;

	/* gateway title */
	public $title 	= null;

	/* description */
	public $description = null;

	/* enabled */
	public $enabled = false;

	/* paypal email */
	protected $email = false;

	/* api endpoint url */
	protected $api_endpoint = 'https://api.stripe.com/v1';

	protected $secret_key = null;
	protected $publish_key = null;

	protected $card_number = null;
	protected $card_exp_month = null;
	protected $card_exp_year = null;
	protected $card_cvc = null;

	public function __construct() {

		/* init gateway id */
		$this->id = 'stripe';

		$this->title = __( 'Stripe', 'opal-hotel-room-booking' );

		$this->description = __( 'Payment Via Credit Card Information', 'opal-hotel-room-booking' );

		$this->enabled = get_option( 'opalhotel_stripe_enable', 1 );

		$this->sandbox = get_option( 'opalhotel_stripe_sandbox_mode', 0 );

		$this->init();
	}

	/* initialize */
	public function init() {

		if ( $this->sandbox ) {
			$this->secret_key = get_option( 'opalhotel_stripe_test_secret_key' );
			$this->publish_key = get_option( 'opalhotel_stripe_test_publish_key' );
		} else {
			$this->secret_key = get_option( 'opalhotel_stripe_live_secret_key' );
			$this->publish_key = get_option( 'opalhotel_stripe_live_publish_key' );
		}
	}

	/* get settings */
	public function admin_settings() {
		return array(
				array(
							'type'		=> 'section_start',
							'id'		=> 'stripe_settings',
							'title'		=> __( 'Stripe', 'opal-hotel-room-booking' ),
							'desc'		=> __( 'Stripe gateway support payment by Credit Card.', 'opal-hotel-room-booking' )
						),

					array(
							'type'		=> 'checkbox',
							'id'		=> 'opalhotel_stripe_enable',
							'title'		=> __( 'Enable/Disable', 'opal-hotel-room-booking' ),
							'desc'		=> __( 'Enable PayPal', 'opal-hotel-room-booking' ),
							'default'	=> 1
						),

					array(
							'type'		=> 'checkbox',
							'id'		=> 'opalhotel_stripe_sandbox_mode',
							'title'		=> __( 'Sanbox mode', 'opal-hotel-room-booking' ),
							'desc'		=> __( 'Sandbox mode on is the test environment', 'opal-hotel-room-booking' ),
							'default'	=> 1
						),

					array(
							'type'		=> 'text',
							'id'		=> 'opalhotel_stripe_test_secret_key',
							'title'		=> __( 'Test Secret Key', 'opal-hotel-room-booking' ),
							'desc'		=> __( 'Test Secret Key', 'opal-hotel-room-booking' )
						),

					array(
							'type'		=> 'text',
							'id'		=> 'opalhotel_stripe_test_publish_key',
							'title'		=> __( 'Test Publish Key', 'opal-hotel-room-booking' ),
							'desc'		=> __( 'Test Publish Key', 'opal-hotel-room-booking' )
						),

					array(
							'type'		=> 'text',
							'id'		=> 'opalhotel_stripe_live_secret_key',
							'title'		=> __( 'Live Secret Key', 'opal-hotel-room-booking' ),
							'desc'		=> __( 'Live Secret Key', 'opal-hotel-room-booking' )
						),

					array(
							'type'		=> 'text',
							'id'		=> 'opalhotel_stripe_live_publish_key',
							'title'		=> __( 'Live Publish Key', 'opal-hotel-room-booking' ),
							'desc'		=> __( 'Live Publish Key', 'opal-hotel-room-booking' )
						),

					array(
							'type'		=> 'section_end',
							'id'		=> 'stripe_settings'
						)
			);
	}

	/* is enabled. ready to payment */
	public function is_enabled() {
		return $this->enabled && $this->secret_key && $this->publish_key;
	}

	/**
	 * Display form payment
	 */
	public function form() {
		ob_start();
		?>
			<div id="opalhotel-stripe-form">
			    <div class="opalhotel-form-group">
			        <label for="cc-number" class="label-field">
			        	<?php esc_html_e( 'Card Number', 'opal-hotel-room-booking' ) ?>
			        	<abbr class="required" title="required">*</abbr>
			        </label>
			        <input name="credit-card[cc-number]" id="cc-number" type="tel" class="required input-text credit-card-cc-number" autocomplete="cc-number" placeholder="•••• •••• •••• ••••" />
			    </div>

			    <div class="opalhotel-form-group">
			        <label for="cc-exp" class="label-field">
			        	<?php esc_html_e( 'Expiry (MM/YY)', 'opal-hotel-room-booking' ) ?>
			        	<abbr class="required" title="required">*</abbr>
			        </label>
			        <input name="credit-card[cc-exp]" id="cc-exp" type="tel" class="required input-text credit-card-cc-exp" autocomplete="cc-exp" placeholder="•• / ••••" />
			    </div>

			    <div class="opalhotel-form-group">
			        <label for="cc-cvc" class="label-field">
			        	<?php esc_html_e( 'Card Code (CVC)', 'opal-hotel-room-booking' ) ?>
			        	<abbr class="required" title="required">*</abbr>
			        </label>
			        <input name="credit-card[cc-cvc]" id="cc-cvc" type="tel" class="required input-text credit-card-cc-cvc" autocomplete="off" placeholder="•••" />
			    </div>

			</div>
		<?php
		return ob_get_clean();
	}

	/* validate stripe payment fields */
	public function validate_fields( $posted ) {
		$validated = array();

		if ( ! $this->secret_key || ! $this->publish_key ) {
			$validated[] = __( '<strong>ERROR</strong>: ', 'opal-hotel-room-booking' ) . __( 'Invalid Secret, Publish Stripe key.', 'opal-hotel-room-booking' );
        }

		if ( ! class_exists( 'CreditCard' ) ) {
			require_once dirname( __FILE__ ) . '/CreditCard.php';
		}
		
		$fields = isset( $posted['credit-card'] ) ? $posted['credit-card'] : array();
		$card = array();
		if ( empty( $fields['cc-number'] ) ) {
			$validated[] = __( 'Credit Card number is required.', 'opal-hotel-room-booking' );
		} else {
			$card = CreditCard::validCreditCard( $fields['cc-number'] );
			if ( ! $card['valid'] ) {
				$validated[] = __( 'Credit Card number is invalid.', 'opal-hotel-room-booking' );
			} else {
				$this->card_number = $fields['cc-number'];
			}
		}

		if ( empty( $fields['cc-exp'] ) ) {
			$validated[] = __( 'Credit Card expiry is required.', 'opal-hotel-room-booking' );
		} else {
			list( $month, $year ) = array_map( 'trim', explode( '/', $fields['cc-exp'] ) );
			if( ! CreditCard::validDate( $year, $month ) ) {
				$validated[] = __( 'Credit Card expiry is invalid.', 'opal-hotel-room-booking' );
			} else {
				$this->card_exp_year = $year;
				$this->card_exp_month = $month;
			}
		}

		/**
		 * validate CVC card
		 */
		if ( empty( $fields['cc-cvc'] ) ) {
			$validated[] = __( 'Credit Card CVC is required.', 'opal-hotel-room-booking' );
		} else {
			if ( ! $card['type'] || ! CreditCard::validCvc( $fields['cc-cvc'], $card['type'] ) ) {
				$validated[] = __( 'Credit Card CVC is invalid.', 'opal-hotel-room-booking' );
			} else {
				$this->card_cvc = $fields['cc-cvc'];
			}
		}

		return apply_filters( 'opalhotel_payment_gateway_validate_fields', ! empty( $validated ) ? $validated : true );
	}

	/* process payment */
	public function payment_process( $order_id = null, $page = null ) {

        /**
         * Generate Token
         */
        $tokens = $this->request( 'tokens', array(
			            'card' => array(
			                'number' 	=> $this->card_number,
			                'exp_month' => $this->card_exp_month,
			                'exp_year' 	=> $this->card_exp_year,
			                'cvc' 		=> $this->card_cvc,
			            )
                ) );
        if ( is_wp_error( $tokens ) || ! $tokens->id ) {
        	return array(
					'status'	=> false,
					'message'	=> __( '<strong>ERROR</strong>: ', 'opal-hotel-room-booking' ) . __( 'Stripe counld not create tooken for your Credit Card Information.', 'opal-hotel-room-booking' )
				);
        }

        $token = $tokens->id;

        $order = opalhotel_get_order( $order_id );
        $customer_id = $order->stripe_id;
        if ( ! $customer_id ) {
            $params = array(
                'description' => sprintf( '#%s - %s', $order->customer_id, $order->customer_email ),
                'source' => $token
            );
            // create customer
            $response = $this->request( 'customers', $params );

            if ( is_wp_error( $response ) ) {
            	return array(
					'status'	=> false,
					'message'	=> __( '<strong>ERROR</strong>: ', 'opal-hotel-room-booking' ) . $response->get_error_message()
				);
            }

            $customer_id = $response->id;

            update_post_meta( $order_id, '_stripe_id', $customer_id );
        }

        $params = array(
            'amount' => round( $order->get_total() * 100 ),
            'currency' => $order->payment_currency,
            'customer' => $customer_id,
            'description' => sprintf(
                    __( 'Payment ID ', 'opal-hotel-room-booking' ) . '%s', $order_id
            )
        );

        // insert new charges stripe
        $response = $this->request( 'charges', $params );

		$result = array(
				'status'	=> true
			);

        if ( $response && ! is_wp_error( $response ) && $response->id ) {
            $order->update_status( 'completed' );

			OpalHotel()->cart->empty_cart();

			if ( $page ) {
				$result['redirect']	= $this->get_return_url( $order );
			} else {
				ob_start();
				echo do_shortcode( '[opalhotel_reservation step="4" reservation-received="' . $order->id . '"]' );
				$result['reservation'] = ob_get_clean();
				$result['step']	= 4;
				$result['reservation-received'] = $order->id;
			}
        } else {
        	$order->update_status( 'on-hold' );
        	$result['status']	= false;
        	$result['message']	= __( '<strong>ERROR</strong>: ', 'opal-hotel-room-booking' ) . $response->get_error_message();
        }

		return apply_filters( 'opalhotel_payment_stripe_result', $result, $order );
	}

    /**
     * Make Stripe Request API
     */
    protected function request( $api = 'charges', $params = array() ) {
        $response = wp_safe_remote_post( $this->api_endpoint . '/' . $api, array(
			            'method' => 'POST',
			            'headers' => array(
			                'Authorization' => 'Basic ' . base64_encode( $this->secret_key . ':' )
			            ),
			            'body' => $params,
			            'timeout' => 70,
			            'sslverify' => false,
			            'user-agent' => 'Opal Hotel ' . OPALHOTEL_VERSION
        ) );

        if ( ! is_wp_error( $response ) ) {
        	/**
        	 * Retrieve body response
        	 */
            $body = wp_remote_retrieve_body( $response );

            if ( $body )
                $body = json_decode( $body );

            if ( ! empty( $body->error ) ) {
                return new WP_Error( 'stripe_error', $body->error->message );
            }

            if ( empty( $body->id ) ) {
                return new WP_Error( 'stripe_error', __( 'OOP. Process Error', 'opal-hotel-room-booking' ) );
            }

            return $body;
        }

        return new WP_Error( 'stripe_error', $response->get_error_message() );
    }

}
