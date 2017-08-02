<?php

defined( 'ABSPATH' ) || exit();

if ( ! function_exists( 'opalhotel_get_ratings' ) ) {

	function opalhotel_get_ratings( $rating_ID = null ) {
		global $wpdb;
		$table = $wpdb->prefix . 'opalhotel_ratings';
		$ratings = array();
		if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table}'" ) == $table ) {
			$ratings = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}opalhotel_ratings" );
		}
		return apply_filters( 'opalhotel_get_ratings', $ratings );
	}

}

if ( ! function_exists( 'opalhotel_get_ratings_admin_options' ) ) {

	function opalhotel_get_ratings_admin_options() {
		$ratings = opalhotel_get_ratings();
		$options = array(
				''	=> __( 'Default', 'opal-hotel-room-booking' )
			);
		foreach ( $ratings as $rating ) {
			$options[$rating->ID] = $rating->name;
		}
		return apply_filters( 'opalhotel_get_ratings_admin_options', $options );
	}

}

if ( ! function_exists( 'opalhotel_get_rating' ) ) {

	function opalhotel_get_rating( $rating_id = null ) {
		global $wpdb;
		$sql = $wpdb->prepare("
				SELECT * FROM {$wpdb->prefix}opalhotel_ratings WHERE ID = %d
			", $rating_id );
		return apply_filters( 'opalhotel_get_rating', $wpdb->get_row( $sql ) );

	}
}

if ( ! function_exists( 'opalhotel_get_rating_items' ) ) {

	function opalhotel_get_rating_items( $post_id = null ) {
		$defaults = array();

		// comfort
		$comfort = new stdClass();
		$comfort->rating_item_ID = 'comfort';
		$comfort->rating_name = __( 'Comfort', 'opal-hotel-room-booking' );
		$comfort->rating_description = '';
		$defaults[] = $comfort;

		// position
		$position = new stdClass();
		$position->rating_item_ID = 'position';
		$position->rating_name = __( 'Position', 'opal-hotel-room-booking' );
		$position->rating_description = '';
		$defaults[] = $position;

		// price
		$price = new stdClass();
		$price->rating_item_ID = 'price';
		$price->rating_name = __( 'Price', 'opal-hotel-room-booking' );
		$price->rating_description = '';
		$defaults[] = $price;

		// quantity
		$quantity = new stdClass();
		$quantity->rating_item_ID = 'quantity';
		$quantity->rating_name = __( 'Quantity', 'opal-hotel-room-booking' );
		$quantity->rating_description = '';
		$defaults[] = $quantity;

		$rating_items = apply_filters( 'opalhotel_default_rating_items', $defaults );

		$rating_id = get_post_meta( $post_id, 'opalhotel_rating_id', true );
		if ( ! $post_id || ! $rating_id )
			return $rating_items;

		$rating = opalhotel_get_rating( $rating_id );
		global $wpdb;
		$sql = $wpdb->prepare("
				SELECT * FROM $wpdb->opalhotel_rating_item WHERE rating_ID = %d
			", $rating->ID );

		return apply_filters( 'opalhotel_get_rating_items', $wpdb->get_results( $sql ) );
	}

}

if ( ! function_exists( 'opalhotel_get_rating_item' ) ) {

	function opalhotel_get_rating_item( $rating_item_ID = null ) {
		global $wpdb;
		$sql = $wpdb->prepare( "
				SELECT * FROM $wpdb->opalhotel_rating_item WHERE rating_item_ID = %d
			", $rating_item_ID );

		return apply_filters( 'opalhotel_get_rating_item', $wpdb->get_row( $sql ) );
	}

}

if ( ! function_exists( 'opalhotel_get_average_rating_comment' ) ) {

	function opalhotel_get_average_rating_comment( $comment_id = null ) {
		global $wpdb;
		$comment = get_comment( $comment_id );
		$post_id = $comment->comment_post_ID;
		$rating_id = opalhotel_get_post_rating_id( $post_id );

		$average = get_comment_meta( $comment_id, 'opalhotel_rating_advance_' . $rating_id, true );
		return apply_filters( 'opalhotel_get_average_rating_comment', $average, $comment_id );
	}

}

if ( ! function_exists( 'opalhotel_get_post_rating_id' ) ) {

	function opalhotel_get_post_rating_id( $post_id = null ) {
		$rating_id = get_post_meta( $post_id, 'opalhotel_rating_id', true );
		return apply_filters( 'opalhotel_get_post_rating_id', $rating_id ? $rating_id : opalhotel_get_option( 'comment_rating' ) );
	}

}

if ( ! function_exists( 'opalhotel_get_sum_rating_item_post' ) ) {

	/**
	 * Get Sum rating of post 
	 */
	function opalhotel_get_sum_rating_item_post( $post_id = null, $rating_item_ID = null ) {
		global $wpdb;
		$sql = $wpdb->prepare( "
				SELECT SUM( meta.meta_value ) FROM $wpdb->commentmeta AS meta
				INNER JOIN $wpdb->comments AS cm ON cm.comment_ID = meta.comment_id
				WHERE cm.comment_post_ID = %d
					AND meta.meta_key = %s
			", $post_id, 'opalhotel_rating_' . $rating_item_ID );
		$sum = $wpdb->get_var( $sql );
		return apply_filters( 'opalhotel_get_sum_rating_item_post', $sum, $post_id, $rating_item_ID );
	}
}

if ( ! function_exists( 'opalhotel_print_rating' ) ) {
    /**
     * Print rating
     * @param int $star
     * @since 1.0.0
     * @return html
     */
    function opalhotel_print_rating( $star = 5, $full = true ) {
        $count = 5;
        if ( ! $full )
            $count = $star;

        $html = array();
        $html[] = '<ul class="opalhotel-review-stars" itemprop="aggregateRating" itemscope="" itemtype="http://schema.org/AggregateRating">';
        for ( $i = 1; $i <= $count; $i++ ) {
            if ( ( $i <= $star ) || ( ( $i - $star ) <= 0.25 && ( $i - $star ) > 0 ) ) {
                $html[] = '<li><i class="fa fa-star"></i></li>';
            } else if( ( $i - $star ) < 0.75 && ( $i - $star ) >= 0.25 ){
                $html[] = '<li><i class="fa fa-star-half-empty"></i></li>';
            } else {
                $html[] = '<li><i class="fa fa-star-o"></i></li>';
            }
        }
        $html[] = '</ul>';

        return implode( '', $html );
    }
}

if ( ! function_exists( 'opalhotel_get_review_count' ) ) {

	/**
	 * count all reviews
	 */
	function opalhotel_get_review_count( $post_id = null ) {
		return count( get_comments( array( 'post_id' => $post_id, 'status' => 'approve' ) ) );
	}

}

if ( ! function_exists( 'opalhotel_get_review_has_rating_count' ) ) {

	/**
	 * count all reviews has rating
	 */
	function opalhotel_get_review_has_rating_count( $post_id = null, $rating_item_ID = null ) {
		$meta_key = 'opalhotel_rating';
		if ( $rating_item_ID ) {
			$meta_key .= '_' . $rating_item_ID;
		}

		if ( ! $post_id ) {
			global $post;
			$post_id = $post->ID;
		}

		if ( $rating_item_ID ) {
			$count = count( get_comments( array(
					'post_id' 	=> $post_id,
					'status' 	=> 'approve',
					'meta_key'	=> $meta_key
				) ) );
		} else {
			global $wpdb;
			$sql = $wpdb->prepare("
					SELECT comments.comment_ID FROM $wpdb->comments AS comments
					INNER JOIN $wpdb->commentmeta AS meta ON meta.comment_id = comments.comment_ID
					WHERE
						meta.meta_key LIKE %s
						AND comments.comment_post_ID = %d
						AND comments.comment_approved = %d
					GROUP BY comments.comment_ID
				", '%' . $meta_key . '%', $post_id, 1 );

			$count = count( $wpdb->get_results( $sql ) );
		}

		return apply_filters( 'opalhotel_total_review_has_rating', absint( $count ), $post_id, $rating_item_ID );
	}

}

if ( ! function_exists( 'opalhotel_get_average_rating' ) ) {
	/**
	 * get average post rating
	 *
	 * @since 1.1.7
	 */
	function opalhotel_get_average_rating( $post_id = null, $rating_item_id = null, $calculate = false ) {
		$meta_key = 'opalhotel_rating';
		if ( $rating_item_id ) {
			$meta_key .= '_' . $rating_item_id;
		}

		if ( $calculate ) {

			$count = opalhotel_get_review_has_rating_count( $post_id, $rating_item_id );
			global $wpdb;
			if ( $rating_item_id ) {
				$sql = $wpdb->prepare("
						SELECT SUM( cmmeta.meta_value ) FROM $wpdb->commentmeta AS cmmeta
						INNER JOIN $wpdb->comments AS cm ON cm.comment_ID = cmmeta.comment_id
						WHERE cm.comment_post_ID = %d
						AND cmmeta.meta_key = %s
					", $post_id, $meta_key );
			} else {
				$rating_items = opalhotel_get_rating_items( $post_id );
				$meta_keys = array();
				foreach ( $rating_items as $rating_item ) {
					$meta_keys[] = 'opalhotel_rating_' . $rating_item->rating_item_ID;
				}
				$sql = $wpdb->prepare("
						SELECT SUM( cmmeta.meta_value ) FROM $wpdb->commentmeta AS cmmeta
						INNER JOIN $wpdb->comments AS cm ON cm.comment_ID = cmmeta.comment_id
						WHERE cm.comment_post_ID = %d
						AND cmmeta.meta_key IN ( '".implode( '\', \'', $meta_keys )."' )
					", $post_id, '%' . $meta_key . '%' );
				$count = $count * count( $meta_keys );
			}
			$total = $wpdb->get_var( $sql );

			$average = $count ? $total / $count : 0;
		} else {
			if ( ! $rating_item_id ) {
				$average = get_post_meta( $post_id, 'opalhotel_average_rating', true );
			} else {
				$average = get_post_meta( $post_id, 'opalhotel_average_rating_item_' . $rating_item_id, true );
			}
		}

		return apply_filters( 'opalhotel_averger_rating_hotel', $average, $post_id );
	}
}

if ( ! function_exists( 'opalhotel_get_rating_count' ) ) {

	/**
	 * Get rating count by star and rating item id
	 */
	function opalhotel_get_rating_count( $post_id = null, $star = 5 ) {
		return count( get_comments( array( 'post_id' => $post_id, 'status' => 'approve', 'meta_key' => 'opalhotel_rating', 'meta_value' => absint( $star ) ) ) );
	}
}

if ( ! function_exists( 'opalhotel_get_rating_percent' ) ) {

	/**
	 * opalhote get rating percent
	 */
	function opalhotel_get_rating_percent( $post_id = null, $star = 5, $rating_item_id = null ) {
		$total = opalhotel_get_review_has_rating_count( $post_id, $rating_item_id );
		if ( ! $total ) { return 0; }
		if ( $rating_item_id ) {
			$sum = opalhotel_get_sum_rating_item_post( $post_id, $rating_item_id );
			$percent = number_format( $sum / ( $total * 5 ) * 100, 2 );
		} else {
			$percent = number_format( opalhotel_get_rating_count( $post_id, $star, $rating_item_id ) * 100 / $total, 2 );
		}
		return apply_filters( 'opalhotel_rating_percent', $percent, $star, $post_id );
	}

}