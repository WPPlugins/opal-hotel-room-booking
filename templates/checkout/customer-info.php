<?php
/**
 * The template for displaying room content within loops
 *
 * This template can be overridden by copying it to yourtheme/opalhotel/checkout/customer-info.php.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

$user = opalhotel_get_current_customer();

?>

<h3 class="opalhotel-section-title hide"><?php esc_html_e( 'Payment Details', 'opal-hotel-room-booking' ); ?></h3>

<div class="opalhotel_reservation_customer_group left">

	<div class="opalhotel-form-group">
		<label for="opalhotel_customer_first_name">
			<?php esc_html_e( 'First Name', 'opal-hotel-room-booking' ); ?>
			<span class="required">*</span>
		</label>
		<input type="text" name="opalhotel_customer_first_name" id="opalhotel_customer_first_name" value="<?php echo esc_attr( $user->first_name ) ?>"/>
	</div>

	<div class="opalhotel-form-group">
		<label for="opalhotel_customer_email">
			<?php esc_html_e( 'Email', 'opal-hotel-room-booking' ); ?>
			<span class="required">*</span>
		</label>
		<input type="email" name="opalhotel_customer_email" id="opalhotel_customer_email" value="<?php echo esc_attr( $user->email ) ?>"/>
	</div>

	<div class="opalhotel-form-group">
		<label for="opalhotel_customer_city">
			<?php esc_html_e( 'City', 'opal-hotel-room-booking' ); ?>
			<span class="required">*</span>
		</label>
		<input type="text" name="opalhotel_customer_city" id="opalhotel_customer_city" value="<?php echo esc_attr( $user->city ) ?>"/>
	</div>

	<div class="opalhotel-form-group">
		<label for="opalhotel_customer_country">
			<?php esc_html_e( 'Country', 'opal-hotel-room-booking' ); ?>
			<span class="required">*</span>
		</label>
		<?php opalhotel_print_select( array(
			'id'		=> 'opalhotel_customer_country',
			'options'	=> opalhotel_get_countries(),
			'selected'	=> $user->country
		) ); ?>
	</div>

	<div class="opalhotel-form-group">
		<label for="opalhotel_customer_address">
			<?php esc_html_e( 'Address', 'opal-hotel-room-booking' ); ?>
		</label>
		<textarea name="opalhotel_customer_address" id="opalhotel_customer_address" placeholder="<?php esc_attr_e( 'Enter your address.', 'opal-hotel-room-booking' ); ?>" rows="5"><?php echo esc_html( $user->address ); ?></textarea>
	</div>

</div>

<div class="opalhotel_reservation_customer_group right">

	<div class="opalhotel-form-group">
		<label for="opalhotel_customer_last_name">
			<?php esc_html_e( 'Last Name', 'opal-hotel-room-booking' ); ?>
			<span class="required">*</span>
		</label>
		<input type="text" name="opalhotel_customer_last_name" id="opalhotel_customer_last_name" value="<?php echo esc_attr( $user->last_name ) ?>"/>
	</div>

	<div class="opalhotel-form-group">
		<label for="opalhotel_customer_phone">
			<?php esc_html_e( 'Phone', 'opal-hotel-room-booking' ); ?>
			<span class="required">*</span>
		</label>
		<input type="text" name="opalhotel_customer_phone" id="opalhotel_customer_phone" value="<?php echo esc_attr( $user->phone ) ?>"/>
	</div>

	<div class="opalhotel-form-group">
		<label for="opalhotel_customer_state">
			<?php esc_html_e( 'State', 'opal-hotel-room-booking' ); ?>
			<span class="required">*</span>
		</label>
		<input type="text" name="opalhotel_customer_state" id="opalhotel_customer_state"  value="<?php echo esc_attr( $user->state ) ?>"/>
	</div>

	<div class="opalhotel-form-group">
		<label for="opalhotel_customer_postcode">
			<?php esc_html_e( 'Postcode', 'opal-hotel-room-booking' ); ?>
			<span class="required">*</span>
		</label>
		<input type="text" name="opalhotel_customer_postcode" id="opalhotel_customer_postcode" value="<?php echo esc_attr( $user->postcode ) ?>"/>
	</div>

	<div class="opalhotel-form-group">
		<label for="opalhotel_customer_notes">
			<?php esc_html_e( 'Addtional Notes', 'opal-hotel-room-booking' ); ?>
		</label>
		<textarea name="opalhotel_customer_notes" id="opalhotel_customer_notes" placeholder="<?php esc_attr_e( 'Notes.', 'opal-hotel-room-booking' ); ?>" rows="5"><?php echo esc_html( $user->notes ); ?></textarea>
	</div>

</div>
