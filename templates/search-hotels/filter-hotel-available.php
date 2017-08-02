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

global $post;

$stars = ! empty( $_REQUEST['hotel-stars'] ) ? $_REQUEST['hotel-stars'] : array();
$amenities_array = ! empty( $_REQUEST['amenities'] ) ? $_REQUEST['amenities'] : array();
$rating = ! empty( $_REQUEST['rating'] ) ? $_REQUEST['rating'] : array();
?>

<div class="opalhotel-ajax-filter-hotel">

	<div class="filter-section">
		<h4 class="section-title">
			<?php esc_html_e( 'Price', 'opal-hotel-room-booking' ); ?>
			<a class="toggle"></a>
		</h4>
		<div class="price">
			<?php echo opalhotel_slide_ranger_template( array(
								'id'	=> 'price',
								'input_min' => opalhotel_get_option( 'search_min_price', 0 ),
								'input_max'	=> opalhotel_get_option( 'search_max_price', 2500 ),
								'min_value'	=> isset( $_REQUEST['min-price'] ) ? sanitize_text_field( $_REQUEST['min-price'] ) : 0,
								'max_value'	=> isset( $_REQUEST['max-price'] ) ? sanitize_text_field( $_REQUEST['max-price'] ) : 0
							) ); ?>
		</div>
	</div>
	<div class="filter-section">
		<h4 class="section-title">
			<?php esc_html_e( 'Hotel Stars', 'opal-hotel-room-booking' ); ?>
			<a class="toggle"></a>
		</h4>
		<ul>
			<li>
				<input type="checkbox" name="hotel-stars[]" value="5" id="hotel-stars-5" <?php echo esc_attr( in_array( 5, $stars ) ? ' checked' : '' ) ?>/>
				<label for="hotel-stars-5"><?php printf( '%s', opalhotel_print_rating( 5, false ) ) ?></label>
				<span class="count"><?php printf( '%s', opalhotel_count_hotel_star( 5 ) ) ?></span>
			</li>
			<li>
				<input type="checkbox" name="hotel-stars[]" value="4" id="hotel-stars-4" <?php echo esc_attr( in_array( 4, $stars ) ? ' checked' : '' ) ?>/>
				<label for="hotel-stars-4"><?php printf( '%s', opalhotel_print_rating( 4, false ) ) ?></label>
				<span class="count"><?php printf( '%s', opalhotel_count_hotel_star( 4 ) ) ?></span>
			</li>
			<li>
				<input type="checkbox" name="hotel-stars[]" value="3" id="hotel-stars-3" <?php echo esc_attr( in_array( 3, $stars ) ? ' checked' : '' ) ?>/>
				<label for="hotel-stars-3"><?php printf( '%s', opalhotel_print_rating( 3, false ) ) ?></label>
				<span class="count"><?php printf( '%s', opalhotel_count_hotel_star( 3 ) ) ?></span>
			</li>
			<li>
				<input type="checkbox" name="hotel-stars[]" value="2" id="hotel-stars-2" <?php echo esc_attr( in_array( 2, $stars ) ? ' checked' : '' ) ?>/>
				<label for="hotel-stars-2"><?php printf( '%s', opalhotel_print_rating( 2, false ) ) ?></label>
				<span class="count"><?php printf( '%s', opalhotel_count_hotel_star( 2 ) ) ?></span>
			</li>
			<li>
				<input type="checkbox" name="hotel-stars[]" value="1" id="hotel-stars-1" <?php echo esc_attr( in_array( 1, $stars ) ? ' checked' : '' ) ?>/>
				<label for="hotel-stars-1"><?php printf( '%s', opalhotel_print_rating( 1, false ) ) ?></label>
				<span class="count"><?php printf( '%s', opalhotel_count_hotel_star( 1 ) ) ?></span>
			</li>
		</ul>
	</div>
	<div class="filter-section">
		<h4 class="section-title">
			<?php esc_html_e( 'Amenities', 'opal-hotel-room-booking' ); ?>
			<a class="toggle"></a>
		</h4>
		<?php
			$amenities = get_posts( array(
					'post_type'			=> OPALHOTEL_CPT_ANT,
					'post_status'		=> 'publish',
					'posts_per_page'	=> -1
				) );
		?>
		<?php if ( $amenities ) : ?>

			<ul>
				<?php foreach ( $amenities as $amenity ) : ?>
					<li>
						<input type="checkbox" name="amenities[]" value="<?php echo esc_attr( $amenity->ID ) ?>" id="amenities-<?php echo esc_attr( $amenity->ID ) ?>" <?php echo esc_attr( in_array( $amenity->ID, $amenities_array ) ? ' checked' : '' ) ?>/>
						<label for="amenities-<?php echo esc_attr( $amenity->ID ) ?>"><?php echo esc_html( $amenity->post_title ); ?></label>
						<span class="count"><?php printf( '%s', opalhotel_count_amenity_hotel( $amenity->ID ) ) ?></span>
					</li>
				<?php endforeach; ?>
			</ul>

		<?php endif; ?>
	</div>
	<div class="filter-section">
		<h4 class="section-title">
			<?php esc_html_e( 'Rating', 'opal-hotel-room-booking' ); ?>
			<a class="toggle"></a>
		</h4>
		<ul>
			<li>
				<input type="checkbox" name="rating[]" value="5" id="hotel-rating-5" <?php echo esc_attr( in_array( 5, $rating ) ? ' checked' : '' ) ?>/>
				<label for="hotel-rating-5"><?php printf( '%s', opalhotel_print_rating( 5, false ) ) ?></label>
				<span class="count"><?php printf( '%s', opalhotel_count_hotel_rating( 5 ) ) ?></span>
			</li>
			<li>
				<input type="checkbox" name="rating[]" value="4" id="hotel-rating-4" <?php echo esc_attr( in_array( 4, $rating ) ? ' checked' : '' ) ?>/>
				<label for="hotel-rating-4"><?php printf( '%s', opalhotel_print_rating( 4, false ) ) ?></label>
				<span class="count"><?php printf( '%s', opalhotel_count_hotel_rating( 4 ) ) ?></span>
			</li>
			<li>
				<input type="checkbox" name="rating[]" value="3" id="hotel-rating-3" <?php echo esc_attr( in_array( 3, $rating ) ? ' checked' : '' ) ?>/>
				<label for="hotel-rating-3"><?php printf( '%s', opalhotel_print_rating( 3, false ) ) ?></label>
				<span class="count"><?php printf( '%s', opalhotel_count_hotel_rating( 3 ) ) ?></span>
			</li>
			<li>
				<input type="checkbox" name="rating[]" value="2" id="hotel-rating-2" <?php echo esc_attr( in_array( 2, $rating ) ? ' checked' : '' ) ?>/>
				<label for="hotel-rating-2"><?php printf( '%s', opalhotel_print_rating( 2, false ) ) ?></label>
				<span class="count"><?php printf( '%s', opalhotel_count_hotel_rating( 2 ) ) ?></span>
			</li>
			<li>
				<input type="checkbox" name="rating[]" value="1" id="hotel-rating-1" <?php echo esc_attr( in_array( 1, $rating ) ? ' checked' : '' ) ?>/>
				<label for="hotel-rating-1"><?php printf( '%s', opalhotel_print_rating( 1, false ) ) ?></label>
				<span class="count"><?php printf( '%s', opalhotel_count_hotel_rating( 1 ) ) ?></span>
			</li>
		</ul>
	</div>

</div>