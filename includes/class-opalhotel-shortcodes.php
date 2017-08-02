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

class OpalHotel_Shortcodes {

	public static function init() {
		$shortcodes = array(
				'form_room_available',
				'form_hotel_available',
				'hotel_available',
				'hotel_available_results',
				'hotel_available_filter',
				// 'reservation',
				// 'rooms',
				// 'hotels',
				'rooms_lastest_deals',
				'hotels_lastest_deals',
				'hotels_recent_discount',
				'rooms_best_price',
				'hotels_best_price',
				'hotel_destination',
				'checkout',
				'nearby_map',
				'map_hotels',
				'split_map',
				'favorited'
			);

		foreach ( $shortcodes as $shortcode ) {
			add_shortcode( 'opalhotel_' . $shortcode, array( __CLASS__, $shortcode ) );
		}
	}

	/**
	 * [opalhotel_form_room_available]
	 *
	 */
	public static function form_room_available( $atts = array() ) {
		$atts = shortcode_atts( array(
				'layout'				=> ''
			), $atts );
		if( isset( $atts['layout'] ) && $atts['layout'] === 'horizontal' ) {
			opalhotel_get_template( 'search/horizontal-form-check-available.php', $atts );
		} else {
			opalhotel_get_template( 'search/form-check-available.php', $atts );
		}
	}

	/**
	 * Reservation
	 * [opalhotel_reservation]
	 * @since 1.1.7
	 */
	public static function reservation( $atts = array() ) {

		$atts = shortcode_atts( array(
				'step'					=> isset( $_REQUEST['step'] ) ? absint( $_REQUEST['step'] ) : 1,
				'reservation-received'	=> isset( $_REQUEST['reservation-received'] ) ? absint( $_REQUEST['reservation-received'] ) : 0
			), $atts );

		if ( isset( $atts['arrival_datetime'], $atts['departure_datetime'] ) && $atts['arrival_datetime'] && $atts['departure_datetime'] ) {
			$atts['arrival'] = $atts['arrival_datetime'];
			$atts['departure'] = $atts['departure_datetime'];
		}

		opalhotel_get_template( 'reservation.php', array( 'atts' => $atts ) );

	}

	/**
	 * Form Check Hotel Available
	 * [opalhotel_form_hotel_available]
	 * @since 1.1.7
	 */
	public static function form_hotel_available( $atts = array() ) {
		$atts = shortcode_atts( array(
				'layout'	=> 'horizontal'
			), $atts );
		extract( $atts );
		opalhotel_get_template_part( 'search-hotels/form-search', $layout );
	}

	/**
	 * Hotel search available
	 * [opalhotel_hotel_available]
	 */
	public static function hotel_available( $atts = array() ) {
		$atts = shortcode_atts( array(
				'columns'	=> 3,
				'map'		=> 1,
				'height'	=> '500px'
			), $atts );
		extract( $atts );

		global $opalhotel_loop;
		$opalhotel_loop['columns'] = $columns;

		opalhotel_get_template( 'shortcodes/map-hotel-results.php', array( 'atts' => $atts ) );
		// $opalhotel_loop = null;
	}

	public static function hotel_available_results( $atts = array() ) {
		$atts = shortcode_atts( array(
				'columns'	=> 3,
				'height'	=> '500px'
			), $atts );
		extract( $atts );

		global $opalhotel_loop;
		$opalhotel_loop['columns'] = $columns;
		// Add Count Room Available
		add_action( 'opalhotel_hotel_loop_item_includes', 'opalhotel_hotel_loop_item_count_room_availalbe', 6 );
		opalhotel_get_template( 'search-hotels/results.php', array( 'atts' => $atts ) );
		// Add Count Room Available
		remove_action( 'opalhotel_hotel_loop_item_includes', 'opalhotel_hotel_loop_item_count_room_availalbe', 6 );
		// $opalhotel_loop = null;
	}

	/**
	 * Hotel Available Filter
	 *
	 * @since 1.1.7
	 */
	public static function filter_hotel_available( $atts = array() ) {
		opalhotel_get_template( 'search-hotels/filter-hotel-available.php', $atts );
	}

	/**
	 * Get Lastest Room ID
	 *
	 * support for hotel lastest
	 */
	public static function get_lastest_room_ids( $number = 5 ) {
		global $wpdb;
		$sql = $wpdb->prepare( "
				SELECT metaID.meta_value FROM $wpdb->opalhotel_order_itemmeta AS metaID
					INNER JOIN $wpdb->opalhotel_order_items AS items ON items.order_item_id = metaID.opalhotel_order_item_id AND metaID.meta_key = %s
					INNER JOIN $wpdb->posts AS orders ON orders.ID = items.order_id
				WHERE orders.post_status IN ( %s, %s, %s )
					AND items.order_item_type = %s
					AND orders.post_type = %s
					GROUP BY metaID.meta_value
					ORDER BY orders.post_date DESC
					LIMIT %d
			", 'product_id', 'opalhotel-completed', 'opalhotel-pending', 'opalhotel-processing', 'room', OPALHOTEL_CPT_BOOKING, $number );

		$room_ids = $wpdb->get_col( $sql );
		return apply_filters( 'opalhotel_lastest_room_ids', $room_ids, $number );
	}

	/**
	 * Rooms
	 *
	 * @since 1.1.7
	 */
	public static function rooms( $atts = array() ) {
		$atts = shortcode_atts( array(
				'posts_per_page'	=> 5,
				'category'			=> '',
				'tags'				=> '',
				'lastest'			=> 0,
				'order'				=> 'DESC',
				'orderby'			=> 'date',
				'post__in'			=> array(),
				'columns'			=> 3,
				'layout'			=> '',
				'style'				=> '',
				'pagination'		=> 0
			), $atts );

		extract( $atts );

		$args = array(
				'post_type'			=> OPALHOTEL_CPT_ROOM,
				'posts_per_page'	=> $posts_per_page,
				'order'				=> $order
			);

		if ( ! $layout && isset( $_REQUEST['sortable'] ) && $_REQUEST['sortable'] ) {
			$orderby = in_array( $_REQUEST['sortable'], array( 3, 4 ) ) ? 'rating' : 'price';
			$args['orderby'] = 'meta_value_num';
		}

		if ( $orderby === 'rating' ) {
			$args['meta_key'] = 'opalhotel_average_rating';
			switch ( $_REQUEST['sortable'] ) {
				case 3:
						$args['order'] = 'ASC';
					break;
				case 4:
						$args['order'] = 'DESC';
					break;
				
				default:
					# code...
					break;
			}
		} elseif( $orderby === 'price' ) {
			$args['meta_key'] = '_base_price';
			switch ( $_REQUEST['sortable'] ) {
				case 1:
						$args['order'] = 'ASC';
					break;
				case 2:
						$args['order'] = 'DESC';
					break;

				default:
					# code...
					break;
			}
		} else {
			$args['ordeby'] = $orderby;
		}

		$taxonomy = array();
		if ( $category ) {
			$taxonomy[] = array(
					'taxonomy'	=> OPALHOTEL_TXM_ROOM_CAT,
					'field'		=> 'slug',
					'terms'		=> $category,
					'operator'	=> 'IN'
				);
		}

		if ( $tags ) {
			$taxonomy[] = array(
					'taxonomy'	=> OPALHOTEL_TXM_ROOM_TAG,
					'field'		=> 'slug',
					'terms'		=> $tags,
					'operator'	=> 'IN'
				);
		}

		if ( $taxonomy ) {
			$args['relation'] = 'OR';
		}

		if ( $lastest ) {
			$room_ids = self::get_lastest_room_ids();
			$args['post__in'] = array_merge( $post__in, $room_ids );
		}

		if ( $pagination ) {
			$args['paged'] = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
		}
		$args = apply_filters( 'opalhotel_shortcode_widget_rooms_args', $args );
		$query = new WP_Query( $args );

		self::loop_room( $query, $args, $atts, __METHOD__ );
	}

	/**
	 * Hotels
	 */
	public static function hotels( $atts = array() ) {
		$atts = shortcode_atts( array(
				'posts_per_page'	=> 15,
				'category'			=> null,
				'order'				=> 'DESC',
				'orderby'			=> 'date',
				'post__in'			=> array(),
				'columns'			=> 3,
				'layout'			=> '',
				'style'				=> '',
				'pagination'		=> 0,
				'just'				=> '',
				'ajax'				=> 0
			), $atts );

		extract( $atts );

		$paged = max( 1, get_query_var( 'paged' ) );
		if ( isset( $_REQUEST['paged'] ) ) {
			$paged = absint( $_REQUEST['paged'] );
		}

		$meta_query = array();
		if ( $category ) {
			if ( is_string( $category ) ) {
				$category = array_map( 'trim', explode( ',', $category ) );
			}
		}
		$args = array(
				'post_type'			=> OPALHOTEL_CPT_HOTEL,
				'posts_per_page'	=> absint( $posts_per_page ),
				'order'				=> $order,
				'paged'				=> $paged
			);

		if ( ! in_array( $orderby, array( 'price', 'rating' ) ) ) {
			$args['orderby'] = $orderby;
		} else {
			$args['meta_key'] = 'opalhotel_average_rating';
		}

		if ( $category ) {
			$args['tax_query'] = array();
			$args['tax_query'][] = array(
					'taxonomy'	=> OPALHOTEL_TXM_HOTEL_CAT,
					'field'		=> 'slug',
					'terms'		=> $category,
					'operator'	=> 'IN'
				);
		}

		if ( $just ) {
			switch ( $jsut ) {
				case 'featured':
						$meta_query[] = array(
								'key'		=> '_featured',
								'value'		=> 1,
								'compare'	=> '='
							);
					break;
				case 'recommended':
						$meta_query[] = array(
								'key'		=> '_recommended',
								'value'		=> 1,
								'compare'	=> '='
							);
					break;
				case 'popular':
						$meta_query[] = array(
								'key'		=> '_popular_views',
								'value'		=> 1,
								'compare'	=> '='
							);
						$args['meta_key']	= '_popular_views';
						$args['orderby']	= 'meta_value_num';
						$args['order']		= 'DESC';
					break;
				
				default:
					# code...
					break;
			}
		}

		if ( $post__in ) {
			if ( is_string( $post__in ) ) {
				$post__in = array_map( 'trim', explode( ',', $post__in ) );
			}
			$args['post__in'] = $post__in;
		}

		if ( $meta_query ) {
			if ( count( $meta_query ) ) {
				$meta_query[] = array(
						'relation'	=> 'AND'
					);
			}

			$args['meta_query'] = array_merge( $args['meta_query'], $meta_query );
		}

		if ( ( $layout && $orderby === 'price' ) || ( ! $layout && isset( $_REQUEST['sortable'] ) && $_REQUEST['sortable'] ) ) {
			add_filter( 'posts_fields', array( __CLASS__, 'hotel_posts_fields' ) );
			add_filter( 'posts_join', array( __CLASS__, 'hotel_posts_join' ) );
			add_filter( 'posts_orderby', array( __CLASS__, 'hotel_order_price' ) );
		}

		$query = new WP_Query( apply_filters( 'opalhotel_shortcode_hotels', $args ) );

		if ( ( $layout && $orderby === 'price' ) || ( ! $layout && isset( $_REQUEST['sortable'] ) && $_REQUEST['sortable'] ) ) {
			remove_filter( 'posts_fields', array( __CLASS__, 'hotel_posts_fields' ) );
			remove_filter( 'posts_join', array( __CLASS__, 'hotel_posts_join' ) );
			remove_filter( 'posts_orderby', array( __CLASS__, 'hotel_order_price' ) );
		}
		self::loop_hotel( $query, $args, $atts );
	}

	/**
	 * Hotels Lastest Deals
	 *
	 * @since 1.1.7
	 */
	public static function rooms_lastest_deals( $atts = array() ) {
		$atts = shortcode_atts( array(
				'posts_per_page' 	=> 5,
				'layout'			=> '',
				'columns'			=> 3,
				'pagination' 		=> 0
			), $atts );
		extract( $atts );

		$id = self::get_lastest_room_ids();
		$args = array(
				'post_type'			=> OPALHOTEL_CPT_ROOM,
				'post__in'			=> $id,
				'posts_per_page'	=> $posts_per_page
			);
		$query = new WP_Query( $args );

		self::loop_room( $query, $args, $atts, __METHOD__ );

	}

	public static function hotels_recent_discount( $atts = array() ) {
		$atts = shortcode_atts( array(
				'posts_per_page' 	=> 5,
				'layout'	=> '',
				'style'		=> '',
				'columns'	=> 3,
				'pagination'=> 0
			), $atts );
		extract( $atts );

		add_filter( 'posts_join', array( __CLASS__, 'hotel_join_recent_discount' ) );
		add_filter( 'posts_where', array( __CLASS__, 'hotel_where_recent_discount' ) );
		add_filter( 'posts_orderby', array( __CLASS__, 'hotel_orderby_recent_discount' ) );
		add_filter( 'posts_groupby', array( __CLASS__, 'hotel_groupby_recent_discount' ) );

		$args = array(
				'post_type'			=> OPALHOTEL_CPT_HOTEL,
				'posts_per_page'	=> $posts_per_page
			);
		$query = new WP_Query( $args );
		self::loop_hotel( $query, $args, $atts, __METHOD__ );

		remove_filter( 'posts_join', array( __CLASS__, 'hotel_join_recent_discount' ) );
		remove_filter( 'posts_where', array( __CLASS__, 'hotel_where_recent_discount' ) );
		remove_filter( 'posts_orderby', array( __CLASS__, 'hotel_orderby_recent_discount' ) );
		remove_filter( 'posts_groupby', array( __CLASS__, 'hotel_groupby_recent_discount' ) );
	}

	/**
	 * Lastest Deals Hotels join filter
	 *
	 */
	public static function hotel_join_recent_discount( $join ) {
		global $wpdb;

		$join .= " INNER JOIN $wpdb->postmeta AS roommeta ON roommeta.meta_value = $wpdb->posts.ID";
		$join .= " INNER JOIN $wpdb->posts AS rooms ON rooms.ID = roommeta.post_id";
		$join .= " INNER JOIN $wpdb->postmeta AS baseprice ON baseprice.post_id = rooms.ID";
		$join .= " INNER JOIN $wpdb->opalhotel_pricing AS pricing ON pricing.room_id = rooms.ID";

		return $join;
	}

	/**
	 * Lastest Deals Hotels where filter
	 *
	 */
	public static function hotel_where_recent_discount( $where ) {
		global $wpdb;

		$where .= " AND rooms.post_type = '" . OPALHOTEL_CPT_ROOM . "' AND rooms.post_status = 'publish'";
		$where .= " AND roommeta.meta_key = '_hotel' AND baseprice.meta_key = '_base_price'";
		$where .= " AND DATE( NOW() ) < pricing.arrival";
		$where .= " AND pricing.price < baseprice.meta_value";

		return $where;
	}

	/**
	 * Lastest Deals Hotels ordeby filter
	 *
	 */
	public static function hotel_orderby_recent_discount( $orderby ) {
		global $wpdb;

		$orderby .= ", pricing.price ASC";
		return $orderby;
	}

	/**
	 * Lastest Deals Hotels groupby filter
	 */
	public static function hotel_groupby_recent_discount( $groupby ) {
		global $wpdb;

		$groupby .= " $wpdb->posts.ID";

		return $groupby;
	}

	/**
	 * Hotels Lastest Deals
	 *
	 * @since 1.1.7
	 */
	public static function hotels_lastest_deals( $atts = array() ) {
		$atts = shortcode_atts( array(
				'posts_per_page' 	=> 5,
				'layout'	=> '',
				'style'		=> '',
				'columns'	=> 3,
				'pagination' => 0
			), $atts );
		extract( $atts );

		add_filter( 'posts_join', array( __CLASS__, 'hotel_join_lastest_deals' ) );
		add_filter( 'posts_where', array( __CLASS__, 'hotel_where_lastest_deals' ) );
		add_filter( 'posts_orderby', array( __CLASS__, 'hotel_orderby_lastest_deals' ) );
		add_filter( 'posts_groupby', array( __CLASS__, 'hotel_groupby_lastest_deals' ) );

		$args = array(
				'post_type'			=> OPALHOTEL_CPT_HOTEL,
				'posts_per_page'	=> $posts_per_page
			);
		$query = new WP_Query( $args );
		self::loop_hotel( $query, $args, $atts, __METHOD__ );

		remove_filter( 'posts_join', array( __CLASS__, 'hotel_join_lastest_deals' ) );
		remove_filter( 'posts_where', array( __CLASS__, 'hotel_where_lastest_deals' ) );
		remove_filter( 'posts_orderby', array( __CLASS__, 'hotel_orderby_lastest_deals' ) );
		remove_filter( 'posts_groupby', array( __CLASS__, 'hotel_groupby_lastest_deals' ) );
	}

	/**
	 * Hotel Lastest Deals filter
	 */
	public static function hotel_join_lastest_deals( $join ) {
		global $wpdb;

		$join .= " INNER JOIN $wpdb->postmeta AS roommeta ON roommeta.meta_value = $wpdb->posts.ID";
		$join .= " INNER JOIN $wpdb->posts AS rooms ON rooms.ID = roommeta.post_id";
		$join .= " INNER JOIN $wpdb->postmeta AS pricemeta ON pricemeta.post_id = rooms.ID";
		$join .= " LEFT JOIN $wpdb->opalhotel_pricing AS pricing ON pricing.room_id = rooms.ID";

		return $join;
	}

	/**
	 * Hotel Lastest Deals filter
	 */
	public static function hotel_where_lastest_deals( $where ) {
		global $wpdb;

		$where .= " AND roommeta.meta_key = '_hotel'";
		$where .= " AND rooms.post_type = '". OPALHOTEL_CPT_ROOM ."'";
		$where .= " AND rooms.post_status = 'publish'";
		$where .= " AND pricemeta.meta_key = '_base_price'";
		$where .= " AND (";
			$where .= " ( pricing.arrival > '" . current_time( 'mysql' ) . '\'';
			$where .= " AND pricing.price < CAST(pricemeta.meta_value AS unsigned) )";
			$where .= " OR ( pricing.arrival IS NULL )";
		$where .= " )";

		return $where;
	}

	/**
	 * Hotel Lastest Deals filter
	 */
	public static function hotel_orderby_lastest_deals( $orderby ) {
		$orderby .= ", pricemeta.meta_value DESC";
		return $orderby;
	}

	/**
	 * Hotel Lastest Deals filter
	 */
	public static function hotel_groupby_lastest_deals( $groupby ) {
		global $wpdb;
		$groupby .= " $wpdb->posts.ID";
		return $groupby;
	}

	/**
	 * Loop Rooms
	 *
	 * @since 1.1.7
	 */
	public static function loop_room( $query, $args = array(), $atts = array(), $call = '' ) {
		extract( $atts );

		global $opalhotel_loop;
		$opalhotel_loop['columns'] = $columns;
		$atts['type'] = 'room';

		do_action( 'opalhotel_pre_loop_room_shortcode', $query, $args, $atts, $call );
		if ( $layout ) :
			remove_action( 'opalhotel_before_room_loop', 'opalhotel_before_loop', 1 );
			remove_action( 'opalhotel_before_room_loop', 'opalhotel_loop_sortable' , 9 );
			remove_action( 'opalhotel_before_room_loop', 'opalhotel_display_modes' , 10 );
			remove_action( 'opalhotel_before_room_loop', 'opalhotel_after_loop' , 999 );
		endif;
		?>

		<?php if ( $query->have_posts() ) : ?>
			<div class="opalhotel-loops-wrapper" data-nonce="<?php echo esc_attr( wp_create_nonce( 'opalhotel-sortable-layout' ) ) ?>" data-args="<?php echo esc_attr( maybe_serialize( $args ) ) ?>" data-atts="<?php echo esc_attr( maybe_serialize( $atts ) ) ?>">
				
				<?php do_action( 'opalhotel_before_room_loop' ) ?>

				<?php $layout = ! $layout ? opalhotel_loop_display_mode( 'grid' ) : $layout; ?>

				<div class="opalhotel opalhotel-wrapper opalhotel-rooms opalhotel-shortcode-widget grid-row" data-nonce="<?php echo esc_attr( wp_create_nonce( 'opalhotel-sortable-layout' ) ) ?>" data-args="<?php echo esc_attr( maybe_serialize( $args ) ) ?>" data-atts="<?php echo esc_attr( maybe_serialize( $atts ) ) ?>">

					<?php while( $query->have_posts() ) { $query->the_post(); ?>

						<?php opalhotel_get_template_part( 'content-room', $layout ); ?>

					<?php } ?>

					<?php if ( $pagination ) : ?>
						<?php switch ( $pagination ) {
							case 1:
								opalhotel_get_template( 'pagination.php', array( 'query' => $query, 'args' => $args, 'atts' => $atts ) );
								break;
							case 2:
								opalhotel_get_template( 'pagination.php', array( 'query' => $query, 'ajax' => true, 'args' => $args, 'atts' => $atts ) );
								break;

							default:
								break;
						} ?>
					<?php endif; ?>

				</div>
			</div>
		<?php wp_reset_postdata(); endif; ?>
		<?php
		do_action( 'opalhotel_after_loop_room_shortcode', $query, $args, $atts, $call );
		unset( $opalhotel_loop );
	}

	/**
	 * Loop Hotels
	 *
	 * @since 1.1.7
	 */
	public static function loop_hotel( $query, $args = array(), $atts = array(), $call = '' ) {
		extract( $atts );

		global $opalhotel_loop;
		$opalhotel_loop['columns'] = $columns;
		$atts['type'] = 'hotel';

		do_action( 'opalhotel_pre_loop_hotel_shortcode', $query, $args, $atts, $call );

		if ( $layout ) :
			remove_action( 'opalhotel_before_hotel_loop', 'opalhotel_before_loop', 1 );
			remove_action( 'opalhotel_before_hotel_loop', 'opalhotel_loop_sortable' , 9 );
			remove_action( 'opalhotel_before_hotel_loop', 'opalhotel_display_modes' , 10 );
			remove_action( 'opalhotel_before_hotel_loop', 'opalhotel_after_loop' , 999 );
		endif;
		?>

		<div class="opalhotel-loops-wrapper" data-nonce="<?php echo esc_attr( wp_create_nonce( 'opalhotel-sortable-layout' ) ) ?>" data-args="<?php echo esc_attr( maybe_serialize( $args ) ) ?>" data-atts="<?php echo esc_attr( maybe_serialize( $atts ) ) ?>">
			<?php if ( $query->have_posts() ) : ?>

				<?php do_action( 'opalhotel_before_hotel_loop' ) ?>

				<div class="opalhotel-wrapper opalhotel opalhotel-main hotels opal-hotel-<?php echo esc_attr( ! $layout ? opalhotel_loop_display_mode( $layout ) : $layout ) ?>">
					<?php if ( ( ! $layout && opalhotel_loop_display_mode() === 'grid' ) || $layout === 'grid' ) : ?>
						<div class="grid-row<?php echo esc_attr( $style ? ' ' . $style : '' ) ?>">
							<?php while ( $query->have_posts() ) : $query->the_post(); ?>
								<?php opalhotel_get_template_part( 'content-hotel-grid', $style ); ?>
							<?php endwhile; ?>

							<?php if ( $pagination ) : ?>

								<?php switch ( $pagination ) {
									case 1:
										opalhotel_archive_print_postcount( $query );
										opalhotel_get_template( 'pagination.php', array( 'query' => $query, 'args' => $args, 'atts' => $atts ) );
										break;
									case 2:
										opalhotel_get_template( 'pagination.php', array( 'query' => $query, 'ajax' => true, 'args' => $args, 'atts' => $atts ) );
										break;

									default:
										break;
								} ?>

							<?php endif; ?>
						</div>
					<?php elseif ( ( ! $layout && opalhotel_loop_display_mode() === 'list' ) || $layout === 'list' ): ?>
						<?php while ( $query->have_posts() ) : $query->the_post(); ?>
							<?php opalhotel_get_template_part( 'content-hotel', 'list' ); ?>
						<?php endwhile; ?>

						<?php if ( $pagination ) : ?>

							<?php switch ( $pagination ) {
								case 1:
									opalhotel_archive_print_postcount( $query );
									opalhotel_get_template( 'pagination.php', array( 'query' => $query, 'args' => $args, 'atts' => $atts ) );
									break;
								case 2:
									opalhotel_get_template( 'pagination.php', array( 'query' => $query, 'ajax' => true, 'args' => $args, 'atts' => $atts ) );
									break;

								default:
									break;
							} ?>
						<?php endif; ?>
					<?php endif; ?>
				</div>
			<?php wp_reset_postdata(); endif; ?>
		</div>

		<?php
		do_action( 'opalhotel_after_loop_hotel_shortcode', $query, $args, $atts, $call );

		unset( $opalhotel_loop['columns'] );
	}

	/**
	 * Shortcode showup the best room price
	 */
	public static function rooms_best_price( $atts = array() ) {
		$atts = shortcode_atts( array(
				'posts_per_page'	=> 5,
				'layout'			=> '',
				'columns'			=> 3,
				'style'				=> ''
			), $atts );
		extract( $atts );

		$args = apply_filters( 'opalhotel_query_rooms_best_price', array(
				'post_type'			=> OPALHOTEL_CPT_ROOM,
				'posts_per_page'	=> $posts_per_page,
				'post_status'		=> 'publish',
				'meta_key'			=> '_base_price',
				'orderby'			=> 'meta_value_num',
				'order'				=> 'ASC'
			) );

		$query = new WP_Query( $args );
		self::loop_room( $query, $args, $atts, __METHOD__ );
	}

	/**
	 * Shortcode showup the best room price
	 */
	public static function hotels_best_price( $atts = array() ) {
		$atts = shortcode_atts( array(
				'posts_per_page'	=> 5,
				'layout'			=> '',
				'columns'			=> 3,
				'style'				=> '',
				'pagination'		=> 0
			), $atts );
		extract( $atts );

		$args = array(
				'post_type'			=> OPALHOTEL_CPT_HOTEL,
				'posts_per_page'	=> $posts_per_page,
				'post_status'		=> 'publish'
			);
		add_filter( 'posts_join', array( __CLASS__, 'hotel_join_best_price' ) );
        add_filter( 'posts_where', array( __CLASS__, 'hotel_where_best_price' ) );
        add_filter( 'posts_orderby', array( __CLASS__, 'hotel_orderby_best_price' ) );
        add_filter( 'posts_groupby', array( __CLASS__, 'hotel_groupby_best_price' ) );
		$query = new WP_Query( $args );
		remove_filter( 'posts_join', array( __CLASS__, 'hotel_join_best_price' ) );
        remove_filter( 'posts_where', array( __CLASS__, 'hotel_where_best_price' ) );
        remove_filter( 'posts_orderby', array( __CLASS__, 'hotel_orderby_best_price' ) );
        remove_filter( 'posts_groupby', array( __CLASS__, 'hotel_groupby_best_price' ) );
		self::loop_hotel( $query, $args, $atts, __METHOD__ );
	}

	/**
	 * Hotel Join Best Price
	 *
	 * @since 1.1.7
	 * @return sql string
	 */
	public static function hotel_join_best_price( $join ) {
		global $wpdb;
        $join .= " INNER JOIN $wpdb->postmeta AS roommeta ON roommeta.meta_value = $wpdb->posts.ID";
        $join .= " INNER JOIN $wpdb->posts AS rooms ON rooms.ID = roommeta.post_id";
        $join .= " INNER JOIN $wpdb->postmeta AS pricemeta ON pricemeta.post_id = rooms.ID";

        return $join;
	}

	/**
	 * Hotel where Best Price
	 *
	 * @since 1.1.7
	 * @return sql string
	 */
	public static function hotel_where_best_price( $where ) {
		global $wpdb;

        $where .= " AND roommeta.meta_key = '_hotel'";
        $where .= " AND rooms.post_type = '". OPALHOTEL_CPT_ROOM ."'";
        $where .= " AND rooms.post_status = 'publish'";
        $where .= " AND pricemeta.meta_key = '_base_price'";
        return $where;
	}

	/**
	 * Hotel orderby Best Price
	 *
	 * @since 1.1.7
	 * @return sql string
	 */
	public static function hotel_orderby_best_price( $orderby ) {
		$orderby = "pricemeta.meta_value ASC";
        return $orderby;
	}

	/**
	 * Hotel groupby Best Price
	 *
	 * @since 1.1.7
	 * @return sql string
	 */
	public static function hotel_groupby_best_price( $groupby ) {
		global $wpdb;
        $groupby .= " $wpdb->posts.ID";
        return $groupby;
	}

	/**
	 * Top Destination Showup as Gallery
	 *
	 * @since 1.1.7
	 */
	public static function hotel_destination( $atts = array() ) {
		$atts = shortcode_atts( array(
				'posts_per_page'	=> 5,
				'image_size'		=> 'thumbnail'
			), $atts );

		opalhotel_get_template( 'shortcodes/hotel-destination.php', $atts );
	}

	public static function single_hotel_destination( $atts = array() ) {
		$atts = shortcode_atts( array(
				'destination_id'	=> '',
				'image_size'		=> 'thumbnail'
			), $atts );
		opalhotel_get_template( 'shortcodes/single-hotel-destination.php', $atts );
	}

	public static function hotel_posts_fields( $fields ) {
		global $wpdb;
		$rooms = $wpdb->prepare( "
				SELECT MIN( CAST( metaPrice.meta_value AS unsigned ) ) AS price FROM $wpdb->postmeta AS metaPrice
					INNER JOIN $wpdb->posts AS r ON r.ID = metaPrice.post_id AND metaPrice.meta_key = %s
					INNER JOIN $wpdb->postmeta AS rmeta ON rmeta.post_id = metaPrice.post_id AND rmeta.meta_key = %s
				WHERE
					r.post_type = %s 
					AND r.post_status = %s
					AND rmeta.meta_value = $wpdb->posts.ID
			", '_base_price', '_hotel', OPALHOTEL_CPT_ROOM, 'publish' );
		$fields .= ", ( $rooms ) AS price ";
		return $fields;
	}

	/**
	 * Join Hotel Price Filter
	 */
	public static function hotel_posts_join( $join ) {
		global $wpdb;
		if ( isset( $_REQUEST['sortable'] ) && in_array( $_REQUEST['sortable'], array( 3, 4 ) ) ) {
			$join .= " LEFT JOIN $wpdb->postmeta AS ratingmt ON ratingmt.post_ID = $wpdb->posts.ID AND ratingmt.meta_key = 'opalhotel_average_rating'";
		}
		return $join;
	}

	/**
	 * Order Hotel Price
	 */
	public static function hotel_order_price( $orderby ) {
		global $wpdb;

		$orderby = " price ASC";
		if ( isset( $_REQUEST['sortable'] ) ) {
			switch ( $_REQUEST['sortable'] ) {
				case 1:
						$orderby = " price ASC";
					break;

				case 2:
					$orderby = " price DESC";
				break;

				case 3:
					$orderby = " ratingmt.meta_value ASC";
				break;

				case 4:
					$orderby = " ratingmt.meta_value DESC";
				break;
				
				default:
						$orderby = " price ASC";
					break;
			}
		}
		return $orderby;
	}

	/**
	 * Print checkout form
	 *
	 * @since 1.1.7
	 */
	public static function checkout() {
		OpalHotel()->checkout->checkout_form();
	}

	/**
	 * Split hotel map
	 *
	 * @since 1.1.7
	 */
	public static function nearby_map( $atts = array() ) {
		$atts = shortcode_atts( array(
				'number'	=> get_option( 'posts_per_page' ),
				'width'		=> '100%',
				'height'	=> '100%',
				'center'	=> '',
				'zoom'		=> 12
			), $atts );

		opalhotel_get_template( 'shortcodes/map-nearby.php', $atts );
	}

	/**
	 * Map hotels
	 *
	 * @since 1.1.7
	 */
	public static function map_hotels( $atts = array() ) {
		opalhotel_print_map_hotel();
	}

	public static function split_map( $atts = array() ) {
		$atts = shortcode_atts( array(
				'columns'	=> 2,
				'height'	=> '500px'
			), $atts );
		extract($atts);

		global $opalhotel_loop;

		$opalhotel_loop['columns'] = ! empty( $opalhotel_loop['columns'] ) ? $opalhotel_loop['columns'] : $columns;
		opalhotel_get_template( 'shortcodes/split-map.php', array( 'atts' => $atts ) );
		unset( $opalhotel_loop['columns'] );
	}

	public static function favorited( $atts = array() ) {
		if ( ! is_user_logged_in() ) {
			opalhotel_print_notice( __( 'Opps. You don\'t have permission to access.', 'opal-hotel-room-booking' ) );
		} else {
			$post_ids = get_user_meta( get_current_user_id(), '_opalhotel_favorited' );
			self::hotels( array(
					'post__in'		=> $post_ids,
					'pagination'	=> 1
				) );
		}
	}

}

OpalHotel_Shortcodes::init();