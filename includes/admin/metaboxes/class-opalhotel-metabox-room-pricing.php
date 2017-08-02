<?php
/**
 * @Author: brainos
 * @Date:   2016-04-24 20:12:41
 * @Last Modified by:   someone
 * @Last Modified time: 2016-05-02 10:47:29
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

class OpalHotel_MetaBox_Room_Pricing {

	/* render */
	public static function render( $post ) {
		$datetimezone = new DateTimeZone( opalhotel_get_timezone_string() );
		?>
			<div id="opalhotel-room-pricing">
				<div id="opalhotel_pricing_panel">

					<h3><?php esc_html_e( 'Update Pricing', 'opal-hotel-room-booking' ); ?></h3>
					<div class="opalhotel_datepick_wrap panel update_pricing">
						<p>
							<input type="text" name="pricing_arrival" class="pricing_arrival opalhotel-has-datepicker" data-end="pricing_departure" placeholder="<?php esc_attr_e( 'Arrival', 'opal-hotel-room-booking' ); ?>" />
							<input type="text" name="pricing_departure" class="pricing_departure opalhotel-has-datepicker" data-start="pricing_arrival" placeholder="<?php esc_attr_e( 'Departure', 'opal-hotel-room-booking' ); ?>" />
						</p>
						<p>
						<?php
							$days = opalhotel_get_days_of_week();
							for( $i = 0; $i <= 6; $i++ ) {
								?>
									<label>
										<?php echo esc_html( $days[$i] ); ?>
										<input type="checkbox" name="week_days[]" value="<?php echo esc_attr( $i ); ?>" />
									</label>
								<?php
							}
						?>
						</p>
						<p>
							<select name="price_type">
								<option value="new_price"><?php esc_html_e( 'New Price', 'opal-hotel-room-booking' ); ?></option>
								<option value="subtract_price"><?php esc_html_e( 'Subtract Price', 'opal-hotel-room-booking' ); ?></option>
								<option value="append_price"><?php esc_html_e( 'Add To Price', 'opal-hotel-room-booking' ); ?></option>
								<option value="increase_percent"><?php esc_html_e( 'Increase % Price', 'opal-hotel-room-booking' ); ?></option>
								<option value="decrease_percent"><?php esc_html_e( 'Decrease % Price', 'opal-hotel-room-booking' ); ?></option>
							</select>
							<input type="number" step="any" name="amount" />
						</p>
						<a href="#" id="opalhotel_update_pricing" class="button"><?php esc_html_e( 'Update', 'opal-hotel-room-booking' ); ?></a>
					</div>
					<!-- Date picker wraper -->
					<h3><?php esc_html_e( 'Filter', 'opal-hotel-room-booking' ); ?></h3>
					<div class="opalhotel_datepick_wrap panel filter_pricing">
						<select name="month">
							<?php
								for ( $m = 1; $m <= 12; $m++ ) {
									$month = date('F', mktime( 0, 0, 0, $m, 1, date( 'Y' ) ) );
									?>
										<option value="<?php echo esc_attr( $m ); ?>"<?php selected( absint( date( 'm' ) ), $m ) ?>><?php echo esc_html( $month ); ?></option>
									<?php
							    }
							?>
						</select>
						<select name="year">
							<?php
							for ( $y = date( 'Y' ); $y <= date( 'Y' ) + 3; $y++ ) {
								?>
									<option value="<?php echo esc_attr( $y ); ?>"<?php selected( absint( date( 'Y' ) ), $y ) ?>><?php echo esc_html( $y ) ?></option>
								<?php
							}
							?>
						</select>
						<a href="#" id="opalhotel_pricing_filter" class="button"><?php esc_html_e( 'Filter', 'opal-hotel-room-booking' ); ?></a>
					</div>
				</div>

				<!--Full Calendar -->
				<div class="calendar" data-timezone="<?php echo esc_attr( $datetimezone->getName() ); ?>"></div>
			</div>
		<?php
	}

}
