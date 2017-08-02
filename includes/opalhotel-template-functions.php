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
 * Get classname for loops based on $opalhotel_loop global.
 * @since 2.6.0
 * @return string
 */
function opalhotel_get_loop_class() {
	global $opalhotel_loop;
	$opalhotel_loop['loop']    = ! empty( $opalhotel_loop['loop'] ) ? $opalhotel_loop['loop'] + 1 : 1;
	$opalhotel_loop['columns'] = ! empty( $opalhotel_loop['columns'] ) ? $opalhotel_loop['columns'] : opalhotel_loop_columns();

	$cls = floor( 12 / $opalhotel_loop['columns'] );

	$gridcols = apply_filters( 'opalhotel_loop_grid_column_class', 'opalhotel_hotel grid-column grid-column-' . $cls );

	$c = $opalhotel_loop['loop'] - 1;

	return $gridcols . ' ' . ( $c % $opalhotel_loop['columns'] == 0 ? 'first-child': '' );
}

function opalhotel_display_modes( $query ){
	opalhotel_get_template( 'globals/display-mode.php', array( 'query' => $query ) );
}

if ( ! function_exists( 'opalhotel_loop_columns' ) ) {

	function opalhotel_loop_columns( $default = 3 ) {
		return apply_filters( 'opalhotel_loop_columns', opalhotel_get_option( 'loop_columns', $default ) );
	}

}

function opalhotel_loop_sortable( $query ){
	if ( is_post_type_archive( OPALHOTEL_CPT_ROOM ) || is_post_type_archive( OPALHOTEL_CPT_HOTEL ) ) {
		return;
	}
	opalhotel_get_template( 'globals/sorter.php', array( 'query' => $query ) );
}

if ( ! function_exists( 'opalhotel_after_process_step' ) ) {

	function opalhotel_after_process_step( $atts ) {
		$step = isset( $_REQUEST['step'] ) ? abs( $_REQUEST['step'] ) : 1;

		$html = array();
		$arrival = isset( $_REQUEST['arrival_datetime'] ) ? sanitize_text_field( $_REQUEST['arrival_datetime'] ) : '';
		$depature = isset( $_REQUEST['departure_datetime'] ) ? sanitize_text_field( $_REQUEST['departure_datetime'] ) : '';
		$adult = isset( $_REQUEST['adult'] ) ? absint( $_REQUEST['adult'] ) : 1;
		$child = isset( $_REQUEST['child'] ) ? absint( $_REQUEST['child'] ) : 1;
		$room_type = isset( $_REQUEST['room_type'] ) ? absint( $_REQUEST['room_type'] ) : 0;

		global $post;
		$current_page_id = isset( $_REQUEST['current_page_id'] ) ? absint( $_REQUEST['current_page_id'] ) : ( ( $post && $post->ID ) ? $post->ID : 0 );

		if ( $step === 2 ) {
			$html[] = '<div class="opalhotel-reservation-step">';
			$html[] = '<a href="#" class="button button-default reservation_step pull-left" data-step="1" data-arrival="' . esc_attr($arrival) . '" data-departure="' . esc_attr($depature) . '" data-adult="'.esc_attr($adult).'" data-child="'.esc_attr($child).'" data-room-type="'.esc_attr( $room_type ).'" data-pageid="'.esc_attr($current_page_id).'">' . __( 'Check Availability', 'opal-hotel-room-booking' ) . '</a>';
			$html[] = '<a href="#" class="button button-primary-inverse reservation_step pull-right" data-step="3" data-arrival="' . $arrival . '" data-departure="' . esc_attr($depature) . '" data-adult="'.esc_attr($adult).'" data-child="'.esc_attr($child).'" data-room-type="'.esc_attr($room_type).'" data-pageid="'.esc_attr($current_page_id).'">' . __( 'Reservation', 'opal-hotel-room-booking' ) . '</a>';
			$html[] = '</div>';
		} else if ( $step === 3 ) {
			$html[] = '<div class="opalhotel-reservation-step">';
			$html[] = '<a href="#" class="opalhotel-button-submit reservation_step prev choose-room button button-default" data-step="2" data-arrival="' . esc_attr($arrival) . '" data-departure="' . esc_attr($depature) . '" data-adult="'.esc_attr($adult).'" data-child="'.esc_attr($child).'" data-room-type="'.esc_attr( $room_type ).'" data-pageid="'.esc_attr($current_page_id).'">' . __( 'Choose Room', 'opal-hotel-room-booking' ) . '</a>';
			$html[] = '</div>';
		}

		echo implode( '', $html );
	}

}

if ( ! function_exists( 'opalhotel_calendar_search_template_step' ) ) {
	function opalhotel_calendar_search_template_step() {
		opalhotel_get_template( 'search/calendar.php' );
	}
}

if ( ! function_exists( 'opalhotel_search_results_template_step' ) ) {
	function opalhotel_search_results_template_step() {
		opalhotel_get_template( 'search/results.php' );
		// opalhotel_get_template( 'search/form-results.php' );
	}
}

if ( ! function_exists( 'opalhotel_make_a_reservation_step' ) ) {
	function opalhotel_make_a_reservation_step() {
		opalhotel_get_template( 'checkout/checkout.php' );
	}
}

if ( ! function_exists( 'opalhotel_confirmation_step' ) ) {
	function opalhotel_confirmation_step( $step = null, $atts = array() ) {
		/* order received */
		opalhotel_get_template( 'checkout/order-received.php', array( 'order' => isset( $atts['reservation-received'] ) ? OpalHotel_Order::instance( $atts['reservation-received'] ) : null ) );
	}
}

if ( ! function_exists( 'opalhotel_setup_shorcode_content' ) ) {
	/* setup search result check available form */
	function opalhotel_setup_shorcode_content( $content ) {
		global $post;

		// Fix error conflict with VC
		if( trim( $post->post_content ) != trim( $content ) ) {
			return $content;
		}

		/* timestamp */
		$arrival = isset( $_GET['arrival_datetime'] ) && $_GET['arrival_datetime'] ? sanitize_text_field( $_GET['arrival_datetime'] ) : false;
		$departure = isset( $_GET['departure_datetime'] ) && $_GET['departure_datetime'] ? sanitize_text_field( $_GET['departure_datetime'] ) : false;

		/* adults & childrens */
		$adults = isset( $_GET['adult'] ) && $_GET['adult'] ? absint( $_GET['adult'] ) : 0;
		$child = isset( $_GET['child'] ) && $_GET['child'] ? absint( $_GET['child'] ) : 0;
		$room_type = isset( $_GET['room_type'] ) && $_GET['room_type'] ? absint( $_GET['room_type'] ) : false;
		/* get setting page, valid check available page */
		$search_id = opalhotel_get_page_id( 'available' );
		$reservation_id = opalhotel_get_page_id( 'reservation' );
		$account_id = opalhotel_get_page_id( 'account' );
		$checkout_id = opalhotel_get_page_id( 'checkout' );
		if ( $search_id && is_page( $search_id ) ) {
			if ( $arrival && $departure ) {
				$shortcode = '[opalhotel_check_available template="form-results" arrival="' . $arrival . '" departure="' . $departure . '" adult="' . $adults . '" child="' . $child . '" room_type="'. $room_type .'"]';
				$content = do_shortcode( $shortcode );
			}
		} else if ( $reservation_id && is_page( $reservation_id ) ) {
			if ( $arrival && $departure ) {
				$shortcode = '[opalhotel_reservation step="2" arrival="' . $arrival . '" departure="' . $departure . '" adult="' . $adults . '" child="' . $child . '" room_type="'. absint( $room_type ) .'"]';
				$content = do_shortcode( $shortcode );
			}
		} else if ( $account_id && is_page( $account_id ) ) {
			$content = '[opalhotel_account]';
		} else if ( $checkout_id && is_page( $checkout_id ) ) {
			$content = '[opalhotel_checkout]';
		}

		return $content;
	}
}

if ( ! function_exists( 'opalhotel_reservation_reviews' ) ) {

	/* available review selected */
	function opalhotel_reservation_reviews() {
		opalhotel_get_template( 'search/review.php' );
	}
}

if ( ! function_exists( 'opalhotel_template_single_title' ) ) {

	/* title of single room*/
	function opalhotel_template_single_title() {
		opalhotel_get_template( 'single-room/title.php' );
	}
}

if ( ! function_exists( 'opalhotel_template_single_gallery' ) ) {

	/* gallery of single room */
	function opalhotel_template_single_gallery() {
		opalhotel_get_template( 'single-room/gallery.php' );
	}

}

if ( ! function_exists( 'opalhotel_template_single_price' ) ) {

	/* base price of single room */
	function opalhotel_template_single_price() {
		opalhotel_get_template( 'single-room/price.php' );
	}
}

/* ARCHIVE */
if ( ! function_exists( 'opalhotel_loop_item_thumbnail' ) ) {

	/* loop thumbnail */
	function opalhotel_loop_item_thumbnail() {
		opalhotel_get_template( 'loop/thumbnail.php' );
	}
}

if ( ! function_exists( 'opalhotel_loop_item_title' ) ) {

	/* get title room of archive template */
	function opalhotel_loop_item_title() {
		opalhotel_get_template( 'loop/title.php' );
	}
}

if ( ! function_exists( 'opalhotel_loop_item_description' ) ) {

	/* get description room */
	function opalhotel_loop_item_description() {
		opalhotel_get_template( 'loop/descriptions.php' );
	}
}

if ( ! function_exists( 'opalhotel_loop_item_details' ) ) {

	/* get details button room */
	function opalhotel_loop_item_details() {
		opalhotel_get_template( 'loop/details.php' );
	}
}

if ( ! function_exists( 'opalhotel_archive_loop_item_booknow' ) ) {

	/* get book now button room */
	function opalhotel_archive_loop_item_booknow() {
		opalhotel_get_template( 'loop/book-now.php' );
	}
}

if ( ! function_exists( 'opalhotel_loop_room_modal' ) ) {

	/* room modal */
	function opalhotel_loop_room_modal() {
		opalhotel_get_template( 'loop/modal.php' );
	}
}

if ( ! function_exists( 'opalhotel_room_available_packages' ) ) {

	/* room modal */
	function opalhotel_room_available_packages() {
		opalhotel_get_template( 'search/loop/packages.php' );
	}
}

if ( ! function_exists( 'opalhotel_room_available_optional' ) ) {

	/* room modal */
	function opalhotel_room_available_optional() {
		opalhotel_get_template( 'search/loop/optional.php' );
	}
}

if ( ! function_exists( 'opalhotel_loop_item_view_details' ) ) {

	/* get view detail room */
	function opalhotel_loop_item_view_details() {
		opalhotel_get_template( 'loop/view-details.php' );
	}
}

if ( ! function_exists( 'opalhotel_loop_item_price' ) ) {

	/* room price */
	function opalhotel_loop_room_price() {
		opalhotel_get_template( 'loop/price.php' );
	}
}

if ( ! function_exists( 'opalhotel_loop_room_rating' ) ) {

	/* room price */
	function opalhotel_loop_room_rating() {
		opalhotel_get_template( 'loop/rating.php' );
	}
}

if ( ! function_exists( 'opalhotel_loop_item_room_available_price' ) ) {

	/* room price */
	function opalhotel_loop_item_room_available_price() {
		opalhotel_get_template( 'search/loop/price.php' );
	}
}

if ( ! function_exists( 'opalhotel_loop_item_room_available_pricing' ) ) {

	/* room price */
	function opalhotel_loop_item_room_available_pricing() {
		opalhotel_get_template( 'search/loop/pricing.php' );
	}
}

if ( ! function_exists( 'opalhotel_loop_item_room_available_button' ) ) {

	/* room price */
	function opalhotel_loop_item_room_available_button() {
		opalhotel_get_template( 'search/loop/button.php' );
	}
}

if ( ! function_exists( 'opalhotel_archive_pagination' ) ) {

	/* pagination archive, taxonomy */
	function opalhotel_archive_pagination( $query = null, $args = array(), $atts = array() ) {
		if ( ! $query ) {
			global $wp_query;
			$query = $wp_query;
		}
		opalhotel_get_template( 'pagination.php', array( 'query' => $query, 'args' => $args, 'atts' => $atts ) );
	}
}

if ( ! function_exists( 'opalhotel_archive_print_postcount' ) ) {

	/* pagination archive, taxonomy */
	function opalhotel_archive_print_postcount( $query = null ) {
		if ( ! $query ) {
			global $wp_query;
			$query = $wp_query;
		}
		$paged = max( 1, get_query_var( 'paged' ) );
		if ( isset( $_REQUEST['paged'] ) ) {
			$paged = absint( $_REQUEST['paged'] );
		}
		?>
			<p class="count-results">
				<?php
					$per_page = $query->get( 'posts_per_page' );
					$total    = $query->found_posts;
					$first    = ( $per_page * $paged ) - $per_page + 1;
					$last     = min( $total, $query->get( 'posts_per_page' ) * $paged );

					if ( $total <= $per_page || -1 === $per_page ) {
						printf( _n( 'Showing the single result', 'Showing all %d results', $total, 'opal-hotel-room-booking' ), $total );
					} else {
						printf( _nx( 'Showing the single result', 'Showing %1$d&ndash;%2$d of %3$d results', $total, '%1$d = first, %2$d = last, %3$d = total', 'opal-hotel-room-booking' ), $first, $last, $total );
					}
				?>
			</p>
		<?php
	}
}

/* END ARCHIVE */
if ( ! function_exists( 'opalhotel_template_setup_room' ) ) {

	/* set global $room */
	function opalhotel_template_setup_room( $post ) {
		/* unset old data */
		unset( $GLOBALS[OPALHOTEL_CPT_ROOM] );

		if ( is_int( $post ) ) {
			$post = get_post( $post );
		}

		if ( empty( $post->post_type ) || ! in_array( $post->post_type, array( OPALHOTEL_CPT_ROOM ) ) ) {
			return;
		}

		/* get room */
		$GLOBALS[OPALHOTEL_CPT_ROOM] = opalhotel_get_room( $post );

		return $GLOBALS[OPALHOTEL_CPT_ROOM];
	}
}

if ( ! function_exists( 'opalhotel_template_setup_body_class' ) ) {

	/* body class */
	function opalhotel_template_setup_body_class( $class ) {
		global $post;
		$opalhotel_class = array();
		if ( is_post_type_archive( OPALHOTEL_CPT_ROOM ) ) {

			$opalhotel_class[] = 'opalhotel-archive';

        } else if ( opalhotel_is_room_taxonomy() ) {
        	$opalhotel_class[] = 'opalhotel-tax';

            if ( is_tax( 'opalhotel_room_cat' ) ) {
            	$opalhotel_class[] = 'opalhotel-room-cat';
            } else if ( is_tax( 'opalhotel_room_tag' ) ) {
                $opalhotel_class[] = 'opalhotel-room-tax';
            }

        } else if ( is_single() && get_post_type() === OPALHOTEL_CPT_ROOM ) {
        	$opalhotel_class[] = 'opalhotel-single';
        } else if ( is_page() ) {
        	$opalhotel = array(
        			opalhotel_get_page_id( 'available' ),
        			opalhotel_get_page_id( 'cart' ),
        			opalhotel_get_page_id( 'checkout' ),
        			opalhotel_get_page_id( 'account' ),
        			opalhotel_get_page_id( 'terms' ),
        			opalhotel_get_page_id( 'reservation' )
        		);
        	if ( in_array( $post->ID, $opalhotel ) ) {
        		$opalhotel_class[] = 'opalhotel-page';
        	}

        	if ( $post->ID == opalhotel_get_page_id( 'reservation' ) ) {
        		$opalhotel_class[] = 'opalhotel-reservation';
        	}
        }

        if ( ! empty( $opalhotel_class ) ) {
        	$opalhotel_class[] = 'opal-hotel-room-booking';
        	$class = array_merge_recursive( $opalhotel_class, $class );
        }

		return $class;
	}
}

if ( ! function_exists( 'opalhotel_template_setup_post_class' ) ) {

	/* set up post class */
	function opalhotel_template_setup_post_class( $class ) {

		return $class;
	}
}

if ( ! function_exists( 'opalhotel_single_room_details' ) ) {
	function opalhotel_single_room_details() {
		opalhotel_get_template( 'single-room/room-details.php' );
	}
}

if ( ! function_exists( 'opalhotel_single_room_attribute' ) ) {
	function opalhotel_single_room_attribute() {
		opalhotel_get_template( 'single-room/room-details/details.php' );
	}
}
if ( ! function_exists( 'opalhotel_single_room_description' ) ) {
	function opalhotel_single_room_description() {
		opalhotel_get_template( 'single-room/room-details/descriptions.php' );
	}
}
if ( ! function_exists( 'opalhotel_single_room_pricing_plan' ) ) {
	function opalhotel_single_room_pricing_plan() {
		opalhotel_get_template( 'single-room/room-details/pricing-plans.php' );
	}
}
if ( ! function_exists( 'opalhotel_single_room_overall' ) ) {
	function opalhotel_single_room_overall() {
		opalhotel_get_template( 'single-room/overall.php' );
	}
}

if ( ! function_exists( 'opalhotel_single_related_room' ) ) {
	function opalhotel_single_related_room() {
		opalhotel_get_template( 'single-room/related.php' );
	}
}

if ( ! function_exists( 'opalhotel_single_reservation_form' ) ) {
	function opalhotel_single_reservation_form( $layout = '' ) {
		opalhotel_dynamic_check_availability( array(
				'adult'		=> 1,
				'child'		=> 1,
				'room_type'	=> true,
				'layout'	=> $layout
			) );
	}
}

if ( ! function_exists( 'opalhotel_dynamic_check_availability' ) ) {

	/* dynamic availability form */
	function opalhotel_dynamic_check_availability( $args ) {
		if( isset( $args['layout'] ) && $args['layout'] === 'horizontal' ){
			opalhotel_get_template( 'search/horizontal-form-check-available.php', $args );
		} else {
			opalhotel_get_template( 'search/form-check-available.php', $args );
		}
	}
}

if ( ! function_exists( 'opalhotel_reservation_order_confirm_template' ) ) {
	/* order received */
	function opalhotel_reservation_order_confirm_template( $order ) {
		opalhotel_get_template( 'checkout/received/order-confirm.php', array( 'order' => $order ) );
	}
}

if ( ! function_exists( 'opalhotel_reservation_order_details_template' ) ) {
	/* order received */
	function opalhotel_reservation_order_details_template( $order ) {
		opalhotel_get_template( 'checkout/received/order-details.php', array( 'order' => $order ) );
	}
}

if ( ! function_exists( 'opalhotel_reservation_customer_details_template' ) ) {
	/* order received */
	function opalhotel_reservation_customer_details_template( $order ) {
		opalhotel_get_template( 'checkout/received/order-customer-details.php', array( 'order' => $order ) );
	}
}

if ( ! function_exists( 'opalhotel_select_number' ) ) {

	/*
	 * 'max' => integer
	 * 'min' => integer
	 * create select dropdown with number
	 */
	function opalhotel_select_number( $args = array() ) {
		$args = wp_parse_args( $args, array(
				'min'		=> 0,
				'max'		=> 0,
				'none'		=> 0,
				'selected'	=> 0,
				'name'		=> '',
				'class'		=> '',
				'id'		=> '',
				'selected'	=> '',
				'placeholder'	=> false
			) );

		extract( $args );
		$options = array(

			);

		if ( $args['none'] ) {
			$options[] = esc_html( $args['none'] );
		}
		for ( $i = $args['min']; $i <= $args['max']; $i++ ) {
			$options[$i] = $i;
		}

		ob_start();
		opalhotel_print_select( array(
				'options'	=> $options,
				'class'		=> array( $args['class'] . ' opalhotel-select' ),
				'selected'	=> $selected,
				'name'		=> $name,
				'id'		=> $id,
				'placeholder'	=> $placeholder
			) );
		return ob_get_clean();
	}

}

if ( ! function_exists( 'opalhotel_template_alert_underscore' ) ) {

	/**
	 * opalhotel_template_alert_underscore
	 * @return javascript template
	 */
	function opalhotel_template_alert_underscore() {
?>
	<script type="text/html" id="tmpl-opalhotel-alert">
		<div class="opalhotel_backbone_modal_content">
			<form>
				<header>
					<h2>
						<# if ( data.message ) { #>
							{{{ data.message }}}
						<# } #>
					</h2>
					<a href="#" class="opalhotel_button_close"><i class="fa fa-times" aria-hidden="true"></i></a>
				</header>
				<footer class="center">

					<input type="hidden" name="action" value="{{ data.action }}">
					<!-- <button type="reset" class="opalhotel-button-cancel opalhotel_button_close"><?php //_e( 'No', 'opal-hotel-room-booking' ) ?></button> -->
					<button type="submit" class="opalhotel-button opalhotel-button-submit"><?php esc_html_e( 'OK', 'opal-hotel-room-booking' ); ?></button>

				</footer>
			</form>
		</div>
		<div class="opalhotel_backbone_modal_overflow"></div>
	</script>

	<script type="text/html" id="tmpl-opalhotel-room-pricing">
		<div class="opalhotel_backbone_modal_content">
			<form class="">
				<header>
					<h2>
						<# if ( data.message ) { #>
							{{{ data.message }}}
						<# } #>
					</h2>
					<a href="#" class="opalhotel_button_close"><i class="fa fa-times" aria-hidden="true"></i></a>
				</header>
				<div class="container">
					<input type="number" step="any" name="price" value="{{ data.price }}"/>
				</div>
				<footer class="center">

					<input type="hidden" name="action" value="{{ data.action }}">
					<button type="reset" class="opalhotel-button-cancel opalhotel_button_close"><?php esc_html_e( 'Cancel', 'opal-hotel-room-booking' ) ?></button>
					<button type="submit" class="opalhotel-button opalhotel-button-submit"><?php esc_html_e( 'Save', 'opal-hotel-room-booking' ); ?></button>

				</footer>
			</form>
		</div>
		<div class="opalhotel_backbone_modal_overflow"></div>
	</script>

	<script type="text/html" id="tmpl-opalhotel-single-room-available">
		<div class="opalhotel-room-modal-wrapper">
			<div class="opalhotel-room-modal">
				<div class="content">
					<h3><?php esc_html_e( 'Book Now', 'opal-hotel-room-booking' ); ?></h3>
					<form class="opalhotel-single-book-room opalhotel_form_section" action="">
						<div class="row opalhotel_datepick_wrap">
							<div class="col-md-4 col-xs-12">
								<div class="opalhotel-form-field-group">
									<i class="fa fa-calendar-o" aria-hidden="true"></i>
									<input type="text" name="arrival" placeholder="<?php esc_attr_e( 'Arrival Date', 'opal-hotel-room-booking' ); ?>" class="opalhotel-has-datepicker opalhotel-arrival-date" data-end="opalhotel-departure-date" />
								</div>
							</div>
							<div class="col-md-4 col-xs-12">
								<div class="opalhotel-form-field-group">
									<i class="fa fa-calendar-o" aria-hidden="true"></i>
									<input type="text" name="departure" placeholder="<?php esc_attr_e( 'Departure Date', 'opal-hotel-room-booking' ); ?>" class="opalhotel-has-datepicker opalhotel-departure-date" data-start="opalhotel-arrival-date" />
								</div>
							</div>
							<div class="col-md-4 col-xs-12">
								<input type="hidden" name="action" value="opalhotel_load_room_available_data" />
								<input type="hidden" name="room_id" value="{{ data.room_id }}" />
								<?php wp_nonce_field( 'opalhotel-single-room-available','nonce' ); ?>
								<button type="submit" class="opalhotel-button-submit button button-primary"><?php esc_html_e( 'Check Avaiable', 'opal-hotel-room-booking' ); ?></button>
							</div>
						</div>
					</form>

				</div>
			</div>
			<div class="overlay"></div>
		</div>
	</script>

	<script type="text/html" id="tmpl-opalhotel-map-marker-content">
    	<# if ( data.thumbnail ) { #>
			<a href="{{ data.permalink }}" class="thumb" style="background-image: url( {{{ data.thumbnail }}} )"></a>
		<# } #>
        <section class="content">
            <h1 class="header">
            	<a href="{{ data.permalink }}">{{ data.title }}</a>
            </h1>
            <# if ( data.address ) { #>
	            <small>
	            	<i class="fa fa-map-marker" aria-hidden="true"></i>
	            	{{ data.address }}
	            </small>
            <# } #>
            <# if ( data.rooms_count ) { #>
	            <small>
	            	<i class="fa fa-check" aria-hidden="true"></i>
	            	{{ data.rooms_count }}
	            </small>
            <# } #>

        </section>
	</script>

	<script type="text/html" id="tmpl-opalhotel-marker-icon">
		<# var uniqueID = new Date().getTime(); #>
		<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0px" y="0px" viewBox="0 0 34.085 41.95" enable-background="new 0 0 34.085 41.95" xml:space="preserve" width="60px" height="75px">
			<g>
				<ellipse fill="#eaeaea" cx="17.042" cy="39.93" rx="4.841" ry="2.021"></ellipse>
				<path fill="#fff" d="M34.085,17.042C34.085,7.63,26.455,0,17.042,0S0,7.63,0,17.042c0,7.318,4.621,13.54,11.098,15.955   l5.945,5.945l5.945-5.945C29.463,30.583,34.085,24.36,34.085,17.042z"></path>
				<g>
					<circle cx="17" cy="17" r="14.5" fill="#fff"></circle>
				</g>
			</g>
			<g>
				<clipPath id="{{ uniqueID }}">
					<circle class="" cx="17" cy="17" r="15" fill="#fff"></circle>
				</clipPath>
				<# if ( data.thumbnail ) { #>
					<image clip-path="url(#{{ uniqueID }})" xlink:href="{{ data.thumbnail }}" x="0" y="0" width="65px" height="35px"></image>
				<# } #>
			</g>
		</svg>
	</script>

	<script type="text/html" id="tmpl-opalhotel-recenter-icon">
		<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="40px" height="40px" viewBox="0 0 40 40">
			<g>
				<path fill="#010" d="M48.416,39.763c0,4.784-3.863,8.647-8.627,8.647c-4.782,0-8.647-3.863-8.647-8.647   c0-4.771,3.865-8.627,8.647-8.627C44.553,31.136,48.416,34.992,48.416,39.763z M43.496,79.531V66.088l3.998-0.01l-7.716-13.35 l-7.72,13.359h3.992v13.442H43.496z M0,43.481h13.463v4.008l13.362-7.715l-13.367-7.726l0.005,3.998H0.005L0,43.481z M79.536,36.045H66.089v-3.987l-13.365,7.715l13.365,7.706v-3.988l13.447-0.01V36.045z M36.056,0.005v13.442l-3.998,0.011 l7.72,13.362l7.716-13.362h-3.998V0.005H36.056z"/>
			</g>
		</svg>
	</script>

	<script type="text/html" id="tmpl-opalhotel-map-countries-list">
		<?php $countries = opalhotel_get_option( 'countries_data', array() ); ?>
		<ul>
			<?php foreach ( $countries as $country ) : $code = strtolower( $country->alpha_2_code ); ?>
				<li data-country="<?php echo esc_attr( $country->country ) ?>" data-lat="<?php echo esc_attr( $country->latitude_average ) ?>" data-lng="<?php echo esc_attr( $country->longitude_average ) ?>" data-alpha_3_code="<?php echo esc_attr( $country->alpha_3_code ) ?>">
					<?php if ( file_exists( OPALHOTEL_PATH . '/assets/images/flags/' . $code .'.svg' ) ) : ?>
						<img src="<?php echo esc_url( OPALHOTEL_URI . 'assets/images/flags/' . $code .'.svg' ) ?>" />
						<span><?php echo esc_html( $country->country ); ?></span>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
	</script>
<?php
	}

}

if ( ! function_exists( 'opalhotel_dropdown_country' ) ) {

	function opalhotel_dropdown_country( $args = array() ) {
		$args = wp_parse_args( $args, array(
					'name'	=> '',
					'none'	=> __( '---Select Country---', 'opal-hotel-room-booking' ),
					'selected'	=> '',
					'disabled'	=> false
			) );

		$html = array();
		$html[] = '<select class="opalhotel-select" name="' . esc_attr( $args['name'] ) . '" id="' . esc_attr( $args['name'] ) . '" '. ( $args['disabled'] ? ' disabled' : '' ) .'>';
		$html[] = '<option value="">' . esc_html( $args['none'] ) . '</option>';

		$countries = opalhotel_get_countries();
		foreach ( $countries as $code => $text ) {
			$html[] = '<option value="' . esc_attr( $code ) . '"'. selected( $args['selected'], $code, false ) .'>' . esc_html( $text ) . '</option>';
		}

		$html[] = '</select>';

		return implode( '', $html );
	}
}

if ( ! function_exists( 'opalhotel_hotel_loop_item_labels' ) ) {

	function opalhotel_hotel_loop_item_labels() {
		opalhotel_get_template( 'loop-hotel/labels.php' );
	}
}

if ( ! function_exists( 'opalhotel_hotel_loop_item_rating' ) ) {

	function opalhotel_hotel_loop_item_rating() {
		opalhotel_get_template( 'loop-hotel/rating.php' );
	}
}

if ( ! function_exists( 'opalhotel_hotel_loop_item_star' ) ) {

	function opalhotel_hotel_loop_item_star() {
		opalhotel_get_template( 'loop-hotel/star.php' );
	}
}

if ( ! function_exists( 'opalhotel_hotel_loop_item_address' ) ) {

	function opalhotel_hotel_loop_item_address() {
		opalhotel_get_template( 'loop-hotel/address.php' );
	}
}

if ( ! function_exists( 'opalhotel_hotel_loop_item_thumbnail' ) ) {

	function opalhotel_hotel_loop_item_thumbnail() {
		opalhotel_get_template( 'loop-hotel/thumbnail.php' );
	}
}

if ( ! function_exists( 'opalhotel_hotel_loop_item_discount' ) ) {

	function opalhotel_hotel_loop_item_discount() {
		opalhotel_get_template( 'loop-hotel/discount.php' );
	}
}

if ( ! function_exists( 'opalhotel_hotel_loop_item_actions' ) ) {

	function opalhotel_hotel_loop_item_actions() {
		opalhotel_get_template( 'loop-hotel/actions.php' );
	}
}

if ( ! function_exists( 'opalhotel_hotel_loop_item_title' ) ) {

	function opalhotel_hotel_loop_item_title() {
		opalhotel_get_template( 'loop-hotel/title.php' );
	}
}

if ( ! function_exists( 'opalhotel_hotel_loop_item_description' ) ) {

	function opalhotel_hotel_loop_item_description() {
		opalhotel_get_template( 'loop-hotel/descriptions.php' );
	}
}

if ( ! function_exists( 'opalhotel_hotel_loop_item_view_details' ) ) {

	function opalhotel_hotel_loop_item_view_details() {
		opalhotel_get_template( 'loop-hotel/view-details.php' );
	}
}

if ( ! function_exists( 'opalhotel_hotel_loop_item_address' ) ) {
	function opalhotel_hotel_loop_item_address() {
		opalhotel_get_template( 'loop-hotel/address.php' );
	}
}

if ( ! function_exists( 'opalhotel_hotel_loop_item_includes' ) ) {
	function opalhotel_hotel_loop_item_includes() {
		opalhotel_get_template( 'loop-hotel/includes.php' );
	}
}

if ( ! function_exists( 'opalhotel_hotel_loop_item_price' ) ) {
	function opalhotel_hotel_loop_item_price() {
		opalhotel_get_template( 'loop-hotel/price.php' );
	}
}

if ( ! function_exists( 'opalhotel_hotel_loop_item_book_button' ) ) {
	function opalhotel_hotel_loop_item_book_button() {
		opalhotel_get_template( 'loop-hotel/book-now.php' );
	}
}

if ( ! function_exists( 'opalhotel_hotel_loop_item_count_room_availalbe' ) ) {
	function opalhotel_hotel_loop_item_count_room_availalbe() {
		opalhotel_get_template( 'loop-hotel/count-room-available.php' );
	}
}

if ( ! function_exists( 'opalhotel_single_hotel_title' ) ) {
	function opalhotel_single_hotel_title() {
		opalhotel_get_template( 'single-hotel/title.php' );
	}
}

if ( ! function_exists( 'opalhotel_single_hotel_tabs' ) ) {
	function opalhotel_single_hotel_tabs() {
		opalhotel_get_template( 'single-hotel/tabs.php' );
	}
}

if ( ! function_exists( 'opalhotel_single_hotel_thumbnail' ) ) {
	function opalhotel_single_hotel_thumbnail() {
		opalhotel_get_template( 'single-hotel/thumbnail.php' );
	}
}

if ( ! function_exists( 'opalhotel_single_hotel_gallery' ) ) {
	function opalhotel_single_hotel_gallery() {
		opalhotel_get_template( 'single-hotel/gallery.php' );
	}
}

if ( ! function_exists( 'opalhotel_single_hotel_description' ) ) {
	function opalhotel_single_hotel_description() {
		opalhotel_get_template( 'single-hotel/descriptions.php' );
	}
}

if ( ! function_exists( 'opalhotel_single_hotel_amenities' ) ) {
	function opalhotel_single_hotel_amenities() {
		opalhotel_get_template( 'single-hotel/amenities.php' );
	}
}

if ( ! function_exists( 'opalhotel_single_hotel_think_to_do' ) ) {
	function opalhotel_single_hotel_think_to_do() {
		opalhotel_get_template( 'single-hotel/think-to-do.php' );
	}
}

if ( ! function_exists( 'opalhotel_single_hotel_rooms' ) ) {
	function opalhotel_single_hotel_rooms() {
		opalhotel_get_template( 'single-hotel/rooms.php' );
	}
}

if ( ! function_exists( 'opalhotel_print_hotel_information_v2' ) ) {
	function opalhotel_print_hotel_information_v2() {
		opalhotel_get_template( 'single-hotel/hotel-info-v2.php' );
	}
}

if ( ! function_exists( 'opalhotel_preview_hide_gallery' ) ) {
	function opalhotel_preview_hide_gallery() {
		return false;
	}
}

if ( ! function_exists( 'opalhotel_hotel_types' ) ) {

	/**
	 * Hotels Type
	 *
	 * @return array hotel - motel
	 */
	function opalhotel_hotel_types() {
		return apply_filters( 'opalhotel_hotel_types', array(
            		'hotel'		=> __( 'Hotel', 'opal-hotel-room-booking' ),
            		'motel'		=> __( 'Motel', 'opal-hotel-room-booking' )
            	) );
	}
}

if ( ! function_exists( 'opalhotel_single_hotel_tabs_filter' ) ) {

	/**
	 * Single Hotel Tabs
	 */
	function opalhotel_single_hotel_tabs_filter() {
		$tabs = array(
				'description'	=> array(
						'label'		=> __( 'Description', 'opal-hotel-room-booking' ),
						'callback' 	=> 'opalhotel_single_hotel_description'
					),
				'amenities'		=> array(
						'label'		=> __( 'Amenities', 'opal-hotel-room-booking' ),
						'callback'	=> 'opalhotel_single_hotel_amenities'
					),
				'reviews'		=> array(
						'label'		=> __( 'Reviews', 'opal-hotel-room-booking' ),
						'callback'	=> 'comments_template'
					),
				'thinks_to_do'	=> array(
						'label'		=> __( 'Things To Do', 'opal-hotel-room-booking' ),
						'callback'	=> 'opalhotel_single_hotel_think_to_do'
					)
			);
		return apply_filters( 'opalhotel_single_hotel_tabs', $tabs );
	}
}

if ( ! function_exists( 'opalhotel_paginate_links' ) ) {
	/**
	 * 'opalhotel_paginate_links' functions
	 *
	 */
	function opalhotel_paginate_links( $args = '' ) {
		global $wp_query, $wp_rewrite;

		// Setting up default values based on the current URL.
		$pagenum_link = html_entity_decode( get_pagenum_link() );
		$url_parts    = explode( '?', $pagenum_link );

		// Get max pages and current page out of the current query, if available.
		$total   = isset( $wp_query->max_num_pages ) ? $wp_query->max_num_pages : 1;
		$current = get_query_var( 'paged' ) ? intval( get_query_var( 'paged' ) ) : 1;

		// Append the format placeholder to the base URL.
		$pagenum_link = trailingslashit( $url_parts[0] ) . '%_%';

		// URL base depends on permalink settings.
		$format  = $wp_rewrite->using_index_permalinks() && ! strpos( $pagenum_link, 'index.php' ) ? 'index.php/' : '';
		$format .= $wp_rewrite->using_permalinks() ? user_trailingslashit( $wp_rewrite->pagination_base . '/%#%', 'paged' ) : '?paged=%#%';

		$defaults = array(
			'base' => $pagenum_link, // http://example.com/all_posts.php%_% : %_% is replaced by format (below)
			'format' => $format, // ?page=%#% : %#% is replaced by the page number
			'total' => $total,
			'current' => $current,
			'show_all' => false,
			'prev_next' => true,
			'prev_text' => __('&laquo; Previous'),
			'next_text' => __('Next &raquo;'),
			'end_size' => 1,
			'mid_size' => 2,
			'type' => 'plain',
			'add_args' => array(), // array of query args to add
			'add_fragment' => '',
			'before_page_number' => '',
			'after_page_number' => ''
		);

		$args = wp_parse_args( $args, $defaults );

		if ( ! is_array( $args['add_args'] ) ) {
			$args['add_args'] = array();
		}

		// Merge additional query vars found in the original URL into 'add_args' array.
		if ( isset( $url_parts[1] ) ) {
			// Find the format argument.
			$format = explode( '?', str_replace( '%_%', $args['format'], $args['base'] ) );
			$format_query = isset( $format[1] ) ? $format[1] : '';
			wp_parse_str( $format_query, $format_args );

			// Find the query args of the requested URL.
			wp_parse_str( $url_parts[1], $url_query_args );

			// Remove the format argument from the array of query arguments, to avoid overwriting custom format.
			foreach ( $format_args as $format_arg => $format_arg_value ) {
				unset( $url_query_args[ $format_arg ] );
			}

			$args['add_args'] = array_merge( $args['add_args'], urlencode_deep( $url_query_args ) );
		}

		// Who knows what else people pass in $args
		$total = (int) $args['total'];
		if ( $total < 2 ) {
			return;
		}
		$current  = (int) $args['current'];
		$end_size = (int) $args['end_size']; // Out of bounds?  Make it the default.
		if ( $end_size < 1 ) {
			$end_size = 1;
		}
		$mid_size = (int) $args['mid_size'];
		if ( $mid_size < 0 ) {
			$mid_size = 2;
		}
		$add_args = $args['add_args'];
		$r = '';
		$page_links = array();
		$dots = false;

		if ( $args['prev_next'] && $current && 1 < $current ) :
			$link = str_replace( '%_%', 2 == $current ? '' : $args['format'], $args['base'] );
			$link = str_replace( '%#%', $current - 1, $link );
			if ( $add_args )
				$link = add_query_arg( $add_args, $link );
			$link .= $args['add_fragment'];

			/**
			 * Filters the paginated links for the given archive pages.
			 *
			 * @since 3.0.0
			 *
			 * @param string $link The paginated link URL.
			 */
			$page_links[] = '<a class="prev page-numbers" href="' . esc_url( apply_filters( 'paginate_links', $link ) ) . '" data-paged="'.esc_attr( $current - 1 ).'">' . $args['prev_text'] . '</a>';
		endif;
		for ( $n = 1; $n <= $total; $n++ ) :
			if ( $n == $current ) :
				$page_links[] = "<span class='page-numbers current' data-paged='".esc_attr( $current )."'>" . $args['before_page_number'] . number_format_i18n( $n ) . $args['after_page_number'] . "</span>";
				$dots = true;
			else :
				if ( $args['show_all'] || ( $n <= $end_size || ( $current && $n >= $current - $mid_size && $n <= $current + $mid_size ) || $n > $total - $end_size ) ) :
					$link = str_replace( '%_%', 1 == $n ? '' : $args['format'], $args['base'] );
					$link = str_replace( '%#%', $n, $link );
					if ( $add_args )
						$link = add_query_arg( $add_args, $link );
					$link .= $args['add_fragment'];

					/** This filter is documented in wp-includes/general-template.php */
					$page_links[] = "<a class='page-numbers' href='" . esc_url( apply_filters( 'paginate_links', $link ) ) . "' data-paged='".esc_attr( $n )."'>" . $args['before_page_number'] . number_format_i18n( $n ) . $args['after_page_number'] . "</a>";
					$dots = true;
				elseif ( $dots && ! $args['show_all'] ) :
					$page_links[] = '<span class="page-numbers dots">' . __( '&hellip;' ) . '</span>';
					$dots = false;
				endif;
			endif;
		endfor;
		if ( $args['prev_next'] && $current && $current < $total ) :
			$link = str_replace( '%_%', $args['format'], $args['base'] );
			$link = str_replace( '%#%', $current + 1, $link );
			if ( $add_args )
				$link = add_query_arg( $add_args, $link );
			$link .= $args['add_fragment'];

			/** This filter is documented in wp-includes/general-template.php */
			$page_links[] = '<a class="next page-numbers" href="' . esc_url( apply_filters( 'paginate_links', $link ) ) . '" data-paged="'.esc_attr( $current + 1 ).'">' . $args['next_text'] . '</a>';
		endif;
		switch ( $args['type'] ) {
			case 'array' :
				return $page_links;

			case 'list' :
				$r .= "<ul class='page-numbers'>\n\t<li>";
				$r .= join("</li>\n\t<li>", $page_links);
				$r .= "</li>\n</ul>\n";
				break;

			default :
				$r = join("\n", $page_links);
				break;
		}
		return $r;
	}
}

if ( ! function_exists( 'opalhotel_get_posts_destination' ) ) {
	/**
	 * Get All Posts of destination taxonomy
	 *
	 * @since 1.1.7
	 */
	function opalhotel_get_posts_destination( $id = null ) {
		$posts = get_posts(
				array(
					'post_type' 		=> OPALHOTEL_CPT_HOTEL,
				    'post_status' 		=> 'publish',
				    'posts_per_page' 	=> -1,
				    'tax_query' 		=> array(
				        array(
				            'taxonomy' 	=> OPALHOTEL_TXM_HOTEL_DES,
				            'field' 	=> 'id',
				            'terms' 	=> $id
				        )
				    )
				)
			);
		$results = array();
		foreach ( $posts as $post ) {
			$results[] = $post->ID;
		}
		return apply_filters( 'opalhotel_get_posts_destination', $results );
	}
}

if ( ! function_exists( 'opalhotel_slide_ranger_template' ) ) {
	function opalhotel_slide_ranger_template( $data = array() ){
			$default = array(
				'id'		=> 'price',
				'unit' 		=> opalhotel_get_currency_symbol(),
				'decimals' 	=> opalhotel_get_option( 'price_number_of_decimal', 2 ),
				'input_min'	 => 0,
				'input_max'  => 10000,
				'min_value'	 => 0,
				'max_value'  => 10000,
			);
			$data['max_value'] = ( ! isset( $data['max_value'] ) || $data['max_value'] == 0 ) ? floatval( $default['input_max'] ) : $data['max_value'];
			$data = wp_parse_args( $data, $default );

			extract( $data );
		?>
		<div class="opalhotel-slide-ranger" data-unit="<?php echo esc_attr( $unit ); ?>" data-decimals="<?php echo esc_attr( $decimals ); ?>" >
			<div class="slide-ranger-bar" data-min="<?php echo esc_attr( $input_min ); ?>" data-max="<?php echo esc_attr( $input_max ); ?>"></div>
		 	<label class="title">
				<span class="slide-ranger-min-label"></span>
				<i>-</i>
				<span class="slide-ranger-max-label"></span>
			</label>

		  	<input type="hidden" class="slide-ranger-min-input" autocomplete="off" name="min-<?php echo esc_attr( $id ); ?>" value="<?php echo floatval( $min_value ) ; ?>" />
		  	<input type="hidden" name="max-<?php echo esc_attr( $id ); ?>" autocomplete="off" class="slide-ranger-max-input" value="<?php echo floatval( $max_value ); ?>" />
	  	</div>
		<?php
	}
}

if ( ! function_exists( 'opalhotel_count_hotel_star' ) ) {

	/**
	 * Count Hotel by Star
	 * 
	 * @since 1.1.7
	 */
	function opalhotel_count_hotel_star( $star = 5 ) {
		$args = array(
				'post_type'			=> OPALHOTEL_CPT_HOTEL,
				'posts_per_page'	=> -1,
				'meta_key'			=> '_star',
				'meta_value'		=> $star
			);
		$hotels = get_posts( $args );
		return apply_filters( 'opalhotel_count_hotel_star', count( $hotels ), $star ); 
	}

}

if ( ! function_exists( 'opalhotel_count_amenity_hotel' ) ) {

	/**
	 * Count Hotels of Amenities
	 *
	 * @param $ID
	 * @return count
	 */
	function opalhotel_count_amenity_hotel( $amenity_id = 0 ) {
		global $wpdb;

		$sql = $wpdb->prepare("
				SELECT COUNT( hotelmeta.post_id ) FROM $wpdb->postmeta AS hotelmeta
				INNER JOIN $wpdb->posts AS hotels ON hotels.ID = hotelmeta.post_id
				INNER JOIN $wpdb->posts AS amenities ON amenities.ID = hotelmeta.meta_value
				WHERE amenities.post_type = %s
					AND amenities.post_status = %s
					AND hotels.post_type = %s 
					AND hotels.post_status = %s
					AND hotelmeta.meta_key = %s
					AND hotelmeta.meta_value = %d
				GROUP BY amenities.ID
			", OPALHOTEL_CPT_ANT, 'publish', OPALHOTEL_CPT_HOTEL, 'publish', '_amenity', $amenity_id );

		return apply_filters( 'opalhotel_count_amenity_hotel', $wpdb->get_var( $sql ), $amenity_id );
	}

}

if ( ! function_exists( 'opalhotel_count_hotel_rating' ) ) {
	
	/**
	 * OpalHotel Count Hotel Rating
	 *
	 * @since 1.1.7
	 */
	function opalhotel_count_hotel_rating( $star = 5 ) {
		global $wpdb;
		
	}
}

if ( ! function_exists( 'opalhotel_print_room_hotels' ) ) {

	/**
	 * get hotels template file
	 */
	function opalhotel_print_room_hotels() {
		opalhotel_get_template( 'single-room/room-details/hotels.php' );
	}

}

if ( ! function_exists( 'opalhotel_print_room_packages_discounts' ) ) {
	/**
	 * get packages template file
	 */
	function opalhotel_print_room_packages_discounts() {
		opalhotel_get_template( 'single-room/room-details/packages-discounts.php' );
	}
}

if ( ! function_exists( 'opalhotel_print_dropdown' ) ) {

	/**
	 * print plugin's dropdown style
	 */
	function opalhotel_print_dropdown( $args = array() ) {
		$args = wp_parse_args( $args, array(
				'label'		=> '',
				'name'		=> '',
				'options'	=> array(),
				'option_attributes'	=> array(),
				'selected'	=> '',
				'class'		=> array()
			) );
		extract( $args );

		?>
			<div class="opalhotel-btn-group<?php echo esc_attr( $class ? ' ' . implode( ' ', $class ) : '' ) ?>">
				<a href="javascript:void(0)" class="btn"><?php printf( '%s : <strong>%s</strong>', $label, $options[ $selected ] ) ?></a>
				<ul class="opalhotel-dropdown-menu">
					<?php foreach( $options as $value => $text ) : ?>
						<li>
							<a href="javascript:void(0)" class="<?php echo esc_attr( $selected == $value ? ' active' : '' ) ?>" data-value="<?php echo esc_attr( $value ) ?>">
								<?php echo esc_html( $text ) ?>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
				<select name="<?php echo esc_attr( $name ) ?>" class="hide dropdown<?php echo esc_attr( $class ? ' ' . implode( ' ', $class ) : '' ) ?>">
					<?php foreach( $options as $value => $text ) : ?>
						<option value="<?php echo esc_attr( $value ) ?>" <?php selected( $selected, $value ) ?> data-value="<?php echo esc_attr( isset( $option_attributes[$value] ) ? $option_attributes[$value] : '' ) ?>" >
							<?php echo esc_html( $text ); ?>
						</option>
				<?php endforeach; ?>
				</select>
			</div>
		<?php
	}

}

if ( ! function_exists( 'opalhotel_print_select' ) ) {

	/**
	 * 
	 * @param $options array
	 * @param $selected array || string || integer
	 *
	 * @return mixed html
	 */
	function opalhotel_print_select( $args = array() ) {
		$args = wp_parse_args( $args, array(
				'options'		=> array(),
				'options_attr'	=> array(),
				'selected'		=> '',
				'placeholder'	=> __( 'Filter options ...', 'opal-hotel-room-booking' ),
				'class'			=> array(),
				'wrapper_class'	=> array(),
				'id'			=> '',
				'name'			=> ''
			) );

		extract( $args );

		$name = $name ? $name : $id;
		$wrapper_class[] = 'opalhotel-select-wrapper';
		$class[] = 'opalhotel-select';
		$selected_text = isset( $options[$selected] ) ? $options[$selected] : $placeholder;
		?>
			<div class="<?php echo esc_attr( implode( ' ', $wrapper_class ) ) ?>">
				<!-- <span class="value"><?php //echo esc_html( $selected_text ); ?></span>
				<div class="list-options" tabindex="-1">
					<div class="filter"><input type="text" placeholder="<?php //echo esc_attr( $placeholder ) ?>" /></div>
					<ul>
						<?php //foreach ( $options as $val => $text ) : ?>
							<li<?php //printf( '%s', $class ? ' class="'.implode( ' ', $class ).'"' : '' ) ?> data-value="<?php //echo esc_attr( $val ) ?>"><?php //echo esc_html( $text ) ?></li>
						<?php //endforeach; ?>
					</ul>
				</div> -->
				<select class="<?php echo esc_attr( implode( ' ', $class ) ) ?>" name="<?php echo esc_attr( $name ) ?>" id="<?php echo esc_attr( $id ) ?>">
					<?php if ( $placeholder ) : ?>
						<option value=""><?php echo esc_html( $placeholder ) ?></option>
					<?php endif; ?>
					<?php foreach ( $options as $val => $text ) : ?>
						<option value="<?php echo esc_attr( $val ) ?>" <?php selected( $selected, $val ) ?> data-value="<?php echo esc_attr( isset( $options_attr[$val] ) ? $options_attr[$val] : '' ) ?>"><?php echo esc_html( $text ) ?></option>
					<?php endforeach; ?>
				</select>
			</div>
		<?php
	}

}

if ( ! function_exists( 'opalhotel_print_map_hotel' ) ) {

	/**
	 * 'opalhotel_print_map_hotel' print google map
	 * 
	 * @param $args array()
	 * @return mixed
	 */
	function opalhotel_print_map_hotel( $args = array() ) {
		$args = wp_parse_args( $args, array(
				'id'		=> uniqid(),
				'center'	=> '',
				'width'		=> '100%',
				'height'	=> '500px',
				'zoom'		=> 12,
				'places'	=> opalhotel_map_hotels_data()
			) );

		extract( $args );

		?>
			<div class="opalhotel-map-wrapper">
				<div id="<?php echo esc_attr( $id ) ?>" class="opalhotel-map-master" style="width: <?php echo esc_attr( $width ) ?>; height:<?php echo esc_attr( $height ) ?>" data-places="<?php echo esc_attr( json_encode( $places ) ) ?>" data-zoom="<?php echo esc_attr( $zoom ) ?>">
					<div class="opalhotel-loading-container">
						<div class="loading-content"><span class="fa fa-spin fa-spinner"></span> <?php esc_html_e( 'Loading map...', 'opal-hotel-room-booking' ); ?></div>
					</div>
				</div>
			</div>
		<?php

	}

}

if ( ! function_exists( 'opalhotel_map_hotels_data' ) ) {

	/**
	 * get hotels data
	 *
	 * @since 1.1.7
	 */
	function opalhotel_map_hotels_data( $query = null ) {
		if ( ! $query ) {
			$args = array(
					'post_type'			=> OPALHOTEL_CPT_HOTEL,
					'post_status'		=> 'publish',
					'posts_per_page' 	=> -1
				);

			// get hotels
			$query = new WP_Query( $args );
		}

		$data = array();

		if ( $query->have_posts() ) {

			while ( $query->have_posts() ) {
				$query->the_post();
				global $post;
				$map = get_post_meta( get_the_ID(), '_map', true );
				if ( ! isset( $map['latitude'] ) || ! isset( $map['longitude'] ) ) continue;
				$rooms_count = isset( $post->available ) ? absint( $post->available ) : 0;
				$data[] = array(
						'id'			=> get_the_ID(),
						'title'			=> get_the_title(),
						'permalink'		=> get_the_permalink(),
						'thumbnail' 	=> get_the_post_thumbnail_url( get_the_ID() ),
						'lat'			=> floatval( $map['latitude'] ),
						'lng'			=> floatval( $map['longitude'] ),
						'address'		=> isset( $map['address'] ) ? esc_html( $map['address'] ) : '',
						'content'		=> get_the_content(),
						'rooms_count'	=> sprintf( _n( '%d room left', '%d rooms left', $rooms_count, 'opal-hotel-room-booking' ), $rooms_count )
					);
			}

			wp_reset_postdata();
		}

		return apply_filters( 'opalhotel_map_hotels_data', $data );

	}

}




