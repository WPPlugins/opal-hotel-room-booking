<?php

 if ( ! defined( 'ABSPATH' ) ) {
 	exit;
 }

 /**
  * @hooked OpalHotel_Emails::email_header() Output the email header
  */
echo "=== " . $heading . " ===\n\n"; ?>

<?php printf( __( 'You have a reservation completed from %s. The order is as follows:', 'opal-hotel-room-booking' ), get_option( 'blogname' ) ) . "\n\n"; ?>

<?php

/**
* @hooked OpalHotel_Emails::order_details() Shows the order details table.
*/
do_action( 'opalhotel_email_order_details', $order, $admin, $plain_text, $email ) . "\n\n";

echo "____________________________________________________________\n\n";
/**
* @hooked OpalHotel_Emails::customer_details() Shows customer details
*/
do_action( 'opalhotel_email_customer_details', $order, $admin, $plain_text, $email ) . "\n\n";

echo "____________________________________________________________\n\n";
/**
* @hooked OpalHotel_Emails::email_footer() Output the email footer
*/
do_action( 'opalhotel_email_footer', $email ) . "\n\n";

echo "____________________________________________________________\n\n";