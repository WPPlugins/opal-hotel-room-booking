<?php
/**
 * Email Header
 *
 * This template can be overridden by copying it to yourtheme/opalhotel/emails/email-header.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>
<!DOCTYPE html>
<html dir="<?php echo is_rtl() ? 'rtl' : 'ltr'?>">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title><?php echo get_bloginfo( 'name', 'display' ); ?></title>
        <style type="text/css">
            #opalhotel_mail_wrapper {
                background-color: #f5f5f5;
                margin: 0;
                padding: 70px 0 70px 0;
                -webkit-text-size-adjust: none !important;
                width: 100%;
            }
            #opalhotel_mail_template_container {
                box-shadow: 0 1px 4px rgba(0,0,0,0.1) !important;
                background-color: #fff;
                border: 1px solid #d3ced2;
                border-radius: 3px !important;
            }
            #opalhotel_mail_template_header {
                background-color: #557da1;
                border-radius: 3px 3px 0 0 !important;
                color: #49657d;
                border-bottom: 0;
                font-weight: bold;
                line-height: 100%;
                vertical-align: middle;
                font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
            }
            #opalhotel_mail_template_header h1 {
                color: #fff;
                text-align: center;
            }
            #opalhotel_mail_template_footer{
                border:0;
                background-color: #49657d;
            }
            #opalhotel_mail_body_content {
                background-color: #fff;
            }
            #opalhotel_mail_body_content table td {
                padding: 48px;
            }
            #opalhotel_mail_body_content table td td {
                padding: 12px;
            }
            #opalhotel_mail_body_content table td th {
                padding: 12px;
            }
            #opalhotel_mail_body_content p {
                margin: 0 0 16px;
            }
            #opalhotel_mail_body_content_inner {
                color: #49657d;
                font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
                font-size: 14px;
                line-height: 150%;
                text-align: <?php echo is_rtl() ? 'right' : 'left'; ?>;
            }
            #opalhotel_mail_header_wrapper{
                padding: 20px;
                display: block;
            }
            .opalhotel_order_confirm_order{
                overflow: hidden;
                border-bottom: 1px dashed #d3ced2;
            }
            .opalhotel_order_confirm_order .column{
                display: inline-block;
                padding: 10px 10px 10px 0;
                margin-right: 10px;
                border-right: 1px dashed #d3ced2;
                font-size: 12px;
                font-weight: 600;
                max-width: 16%;
                vertical-align: top;
                width: auto;
            }
            .opalhotel_order_confirm_order .column:last-of-type{
                border: none;
            }
            .opalhotel_order_confirm_order .column label{
                font-weight: 400;
                text-transform: uppercase;
                font-size: 12px;
                display: block;
                margin-right: 10px;
            }
            .opalhotel_order_customer_details strong,
            .opalhotel_order_customer_details small{
                display: block;
                font-size: 13px;
            }
            .opalhotel-checkout-review .opalhotel-reservation-available-review,
            .opalhotel_order_details{
                background-color: transparent;
                border-bottom: 1px dashed #d3ced2;
            }
            .opalhotel-checkout-review .opalhotel-available-review-item,
            .opalhotel_order_details .opalhotel-order-item-details{
                border-top: 1px dashed #d3ced2;
                border-left: 1px dashed #d3ced2;
                border-right: 1px dashed #d3ced2;
                border-bottom: none;
                font-size: 13px;
                padding: 10px 20px;
                font-weight: 600;
            }
            .opalhotel-review-price {
                float: right;
                font-style: italic;
            }
            .opalhotel_reservation_available_room_info, .opalhotel_order_item_room_info {
                text-align: right;
                font-size: 12px;
                font-weight: 400;
                font-style: italic;
            }
            #opalhotel_mail_footer_wrapper{
                padding: 10px;
                display: block;
                overflow: hidden;
            }
            #opalhotel_mail_footer_wrapper *{
                font-size: 14px;
                color: #fff;
            }
            #opalhotel_mail_footer_wrapper h3{
                text-align: center;
                font-size: 18px;
                margin: 0;
                padding: 10px;
            }
            #opalhotel_mail_footer_wrapper address p{
                display: inline-block;
                float: left;
                margin: 0px 10px 0 0;
                color: #fff;
            }
            h1 {
                color: #49657d;
                font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
                font-size: 30px;
                font-weight: 300;
                line-height: 150%;
                text-align: center;
                margin: 0;
                text-align: <?php echo is_rtl() ? 'right' : 'left'; ?>;
                text-shadow: 0 1px 0 #d3ced2;
                -webkit-font-smoothing: antialiased;
            }
            h2 {
                color: #fff;
                display: block;
                font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
                font-size: 18px;
                font-weight: bold;
                line-height: 130%;
                margin: 16px 0 8px;
                text-align: <?php echo is_rtl() ? 'right' : 'left'; ?>;
            }
            h3 {
                color: #49657d;
                display: block;
                font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
                font-size: 16px;
                font-weight: bold;
                line-height: 130%;
                margin: 16px 0 8px;
                text-align: <?php echo is_rtl() ? 'right' : 'left'; ?>;
            }
            a {
                color: #49657d;
                font-weight: normal;
                text-decoration: underline;
            }
            img {
                border: none;
                display: inline;
                font-size: 14px;
                font-weight: bold;
                height: auto;
                line-height: 100%;
                outline: none;
                text-decoration: none;
                text-transform: capitalize;
            }
        </style>
	</head>
    <body <?php echo is_rtl() ? 'rightmargin' : 'leftmargin'; ?>="0" marginwidth="0" topmargin="0" marginheight="0" offset="0">
    	<div id="opalhotel_mail_wrapper" dir="<?php echo is_rtl() ? 'rtl' : 'ltr'?>">
        	<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%">
            	<tr>
                	<td align="center" valign="top">
						<div id="opalhotel_mail_template_header_image">
	                		<?php
	                			printf( __( '<a href="%s">%s</a>', 'oaplhotel' ), site_url(), wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES ) )
	                		?>
						</div>
                    	<table border="0" cellpadding="0" cellspacing="0" width="600" id="opalhotel_mail_template_container">
                        	<tr>
                            	<td align="center" valign="top">
                                    <!-- Header -->
                                	<table border="0" cellpadding="0" cellspacing="0" width="600" id="opalhotel_mail_template_header">
                                        <tr>
                                            <td id="opalhotel_mail_header_wrapper">
                                            	<h1><?php echo $heading; ?></h1>
                                            </td>
                                        </tr>
                                    </table>
                                    <!-- End Header -->
                                </td>
                            </tr>
                        	<tr>
                            	<td align="center" valign="top">
                                    <!-- Body -->
                                	<table border="0" cellpadding="0" cellspacing="0" width="600" id="opalhotel_mail_template_body">
                                    	<tr>
                                            <td valign="top" id="opalhotel_mail_body_content">
                                                <!-- Content -->
                                                <table border="0" cellpadding="20" cellspacing="0" width="100%">
                                                    <tr>
                                                        <td valign="top">
                                                            <div id="opalhotel_mail_body_content_inner">
