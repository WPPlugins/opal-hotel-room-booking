<?php
/**
 * Email Footer
 *
 * This template can be overridden by copying it to yourtheme/opalhotel/emails/email-footer.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>
															</div>
														</td>
                                                    </tr>
                                                </table>
                                                <!-- End Content -->
                                            </td>
                                        </tr>
                                    </table>
                                    <!-- End Body -->
                                </td>
                            </tr>
                        	<tr>
                            	<td align="center" valign="top">
                                    <!-- Footer -->
                                	<table border="0" cellpadding="10" cellspacing="0" width="600" id="opalhotel_mail_template_footer">
                                    	<tr>
                                        	<td valign="top">
                                                <table border="0" cellpadding="10" cellspacing="0" width="100%">
                                                    <tr>
                                                        <td colspan="2" valign="middle" id="opalhotel_mail_footer_wrapper">
                                                        	<h3><?php printf( '%s', get_option( 'opalhotel_hotel_name' ) ) ?></h3>
                                                            <address>
                                                                <p><?php printf( '%s', get_option( 'opalhotel_hotel_address' ) ) ?></p>
                                                                <p><?php printf( '%s', get_option( 'opalhotel_hotel_city' ) ) ?></p>
                                                                <p><?php printf( '%s', get_option( 'opalhotel_hotel_state' ) ) ?></p>
                                                                <p><?php printf( '%s', get_option( 'opalhotel_hotel_country' ) ) ?></p>
                                                                <p><?php printf( __( 'Fax: %s' ), get_option( 'opalhotel_hotel_fax_number' ) ) ?></p>
                                                                <p><?php printf( __( 'Tel: %s' ), get_option( 'opalhotel_hotel_phone_number' ) ) ?></p>
                                                            </address>
                                                            <a href="mailto:<?php printf( '%s', get_option( 'opalhotel_hotel_email_address' ) ) ?>"><?php printf( __( 'Email: %s' ), get_option( 'opalhotel_hotel_email_address' ), get_option( 'admin_email' ) ) ?></a>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                    <!-- End Footer -->
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
    </body>
</html>
