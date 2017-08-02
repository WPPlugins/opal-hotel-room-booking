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

$room = opalhotel_get_room( get_the_ID() );
/* get packages */
$packages = $room->get_packages();
$extra = $room->get_extras_all_details();
$extra_adults = isset( $extra['adult'] ) ? $extra['adult'] : array();
$extra_child = isset( $extra['child'] ) ? $extra['child'] : array();

$max_adult = $room->get_max_adults_allowed();
$max_child = $room->get_max_childrens_allowed();
$adult = isset( $_REQUEST['adult'] ) ? absint( $_REQUEST['adult'] ) : 1;
$child = isset( $_REQUEST['child'] ) ? absint( $_REQUEST['child'] ) : 0;
?>

<!-- package wraper -->
<div class="opalhotel-room-packages room-choose-packages" id="room-packages-<?php echo esc_attr( $room->id ); ?>">
	<!-- collapse title -->
	<div class="opalhotel-room-package-wrapper">
		<h5><?php esc_html_e( 'Optional', 'opal-hotel-room-booking' ); ?></h5>
		<div class="opalhotel-room-package-item grid-row">
			<div class="clearfix grid-column-6 grid-column">
				<h6 class="package-title pull-left">
					<span for="package-id-<?php echo esc_attr( $room->id ) ?>">
						<?php esc_html_e( 'Adults', 'opal-hotel-room-booking' ); ?>
					</span>
				</h6>
			</div>
			<div class="clearfix grid-column-6 grid-column">
				<h6 class="package-title pull-left">
					<?php
						$options = $options_attr = array();
						for ( $i = 1; $i <= $max_adult; $i++ ) :
							$options[$i] = $i;
							$options_attr[$i] = isset( $extra_adults[$i] ) ? $extra_adults[$i] : 0;
						endfor;

						// opalhotel_print_select( array(
						// 		'options'	=> $options,
						// 		'options_attr'=> $options_attr,
						// 		'selected'	=> $adult,
						// 		'class'		=> array( 'people', 'select-price' ),
						// 		'id'		=> 'adult'
						// 	) );
					?>
					<select name="adult" class="people select-price opalhotel-select">
						<?php for ( $i = 1; $i <= $max_adult; $i++ ) : ?>
							<option value="<?php echo esc_attr( $i ); ?>" <?php selected( $adult, $i ); ?> data-value="<?php echo esc_attr( isset( $extra_adults[$i] ) ? $extra_adults[$i] : 0 ) ?>"><?php echo absint( $i ); ?></option>
						<?php endfor; ?>
					</select>
				</h6>
			</div>
		</div>
		<div class="opalhotel-room-package-item grid-row">
			<div class="clearfix grid-column-6 grid-column">
				<h6 class="package-title pull-left">
					<span for="package-id-<?php echo esc_attr( $room->id ) ?>">
						<?php esc_html_e( 'Children', 'opal-hotel-room-booking' ); ?>
					</span>
				</h6>
			</div>
			<div class="clearfix grid-column-6 grid-column">
				<h6 class="package-title pull-left">
					<select name="child" class="people select-price opalhotel-select">
						<?php for ( $i = 0; $i <= $max_child; $i++ ) : ?>
							<option value="<?php echo esc_attr( $i ); ?>" <?php selected( $child, $i ); ?> data-value="<?php echo esc_attr( isset( $extra_child[$i] ) ? $extra_child[$i] : 0 ) ?>"><?php echo absint( $i ); ?></option>
						<?php endfor; ?>
					</select>
				</h6>
			</div>
		</div>
		<?php if ( ! empty( $packages ) ) : ?>
			<h5><?php esc_html_e('Option Packages', 'opal-hotel-room-booking');?></h5>
			<?php foreach ( $packages as $k => $package ) : ?>

				<!-- each package -->
				<div class="opalhotel-room-package-item package grid-row" data-type="<?php echo esc_attr( $package->package_type ) ?>">

					<div class="clearfix grid-column-6 grid-column">

						<h6 class="package-title pull-left">
							<span for="package-id-<?php echo esc_attr( $package->id ) ?>-<?php echo esc_attr( $room->id ) ?>">
								<?php echo esc_html( $package->post_title ); ?>
							</span>
						</h6>
						<a href="#package-id-<?php echo esc_attr( $package->id ) ?>-<?php echo esc_attr( $room->id ) ?>" class="opalhotel-view-package-details opalhotel-popup-inline"></a>
						<div class="opalhotel-package-desc hide">
							<?php printf( '%s', apply_filters( 'the_content', $package->post_content ) ) ?>
						</div>

					</div>

					<div class="opalhotel-room-package-content grid-column-4 grid-column">

						<!-- price -->
						<div class="opalhotel-package-price">
							<?php if ( $package->package_type === 'package' ) : ?>
								<span class="price-title">
									<input type="number" min="1" step="1" name="packages[qty][<?php echo esc_attr( $package->id ) ?>]" value="1" class="package-qty" />
								</span>
							<?php endif; ?>
							<span class="price-value" data-price="<?php echo esc_attr( $package->get_price() ) ?>">
								<?php printf( __( '%s', 'opal-hotel-room-booking' ), opalhotel_format_price( $package->get_price_display( $package->get_price() ) ) ) ?>
							</span>
							<span class="price-unit"><?php printf( ' / %s', opalhotel_get_package_label( $package->id ) ) ?></span>
						</div>
						<!-- end price -->
					</div>
					<div class="opalhotel-room-checked grid-column-2 grid-column">
						<input type="checkbox" class="pull-right checked-package" name="packages[checked][<?php echo esc_attr( $package->id ) ?>]" id="package-id-<?php echo esc_attr( $package->id ) ?>-<?php echo esc_attr( $room->id ) ?>" />
					</div>
				</div>
				<!-- end each package -->

			<?php endforeach; ?>
		<?php endif; ?>
		<div class="button-actions clearfix">
			<div class="pull-right">
			<?php opalhotel_get_template( 'search/loop/button.php', array( 'room' => $room ) ); ?>
			</div>
		</div>
	</div>

</div>
<!-- end package wraper -->
