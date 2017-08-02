<?php

 if ( ! defined( 'ABSPATH' ) ) {
 	exit;
 }

 /**
  * @hooked OpalHotel_Emails::email_header() Output the email header
  */
 do_action( 'opalhotel_email_header', $heading, $email ); ?>

 <p><?php printf( __( 'You have received a refund request from %s. The order is as follows:', 'opal-hotel-room-booking' ), $order->get_order_number() ); ?></p>

 <?php
/**
* @hooked OpalHotel_Emails::order_confirm() Shows the order details table.
*/
do_action( 'opalhotel_email_order_confirm', $order, $admin, $email );

 /**
  * @hooked OpalHotel_Emails::order_details() Shows the order details table.
  */
 do_action( 'opalhotel_email_order_details', $order, $admin, $plain_text, $email );

 /**
  * @hooked OpalHotel_Emails::customer_details() Shows customer details
  */
 do_action( 'opalhotel_email_customer_details', $order, $admin, $plain_text, $email );

 /**
  * @hooked OpalHotel_Emails::email_footer() Output the email footer
  */
 do_action( 'opalhotel_email_footer', $email );
