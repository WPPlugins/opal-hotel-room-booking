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

if ( ! function_exists( 'opalhotel_get_order' ) ) {
    function opalhotel_get_order( $order_id = null ) {
        return OpalHotel_Order::instance( $order_id );
    }
}

if ( ! function_exists( 'opalhotel_get_order_id_by_key' ) ) {
    function opalhotel_get_order_id_by_key( $order_key = null ) {
        $args = array(
            'posts_per_page'   => 1,
            'post_type'        => OPALHOTEL_CPT_BOOKING,
            'post_status'      => 'any',
            'meta_key'         => '_order_key',
            'meta_value'       => $order_key
        );

        $posts = get_posts( $args );
        if ( $posts ) {
            return current( $posts );
        }
    }
}

if ( ! function_exists( 'opalhotel_create_new_order' ) ) {

    /* create new order return $order_id */
    function opalhotel_create_new_order( $params = array() ) {
        $params = wp_parse_args( $params, array(
                'post_type'     => OPALHOTEL_CPT_BOOKING,
                'post_status'   => 'opalhotel-pending',
                'post_title'    => 'New Booking'
            ) );

        $order_id =  wp_insert_post( $params );

        do_action( 'opalhotel_create_new_order', $order_id, $params );

        return apply_filters( 'opalhotel_create_new_order_result', $order_id );
    }

}

if ( ! function_exists( 'opalhotel_update_order' ) ) {

    /* update order */
    function opalhotel_update_order( $params = array() ) {
        if ( isset( $params['ID'] ) ) {
            return wp_update_post( $params );
        } else {
            return opalhotel_create_new_order( $params );
        }
    }
}

if ( ! function_exists( 'opalhotel_update_order_status' ) ) {

    function opalhotel_update_order_status( $order_id = null, $order_status = 'pending' ) {
        $order = OpalHotel_Order::instance( $order_id );
        return $order->update_status( $order_status );
    }

}

if ( ! function_exists( 'opalhotel_get_order_statuses' ) ) {

    /* get order status */
    function opalhotel_get_order_statuses( $order_id = null ) {
        $statuses = get_post_stati();
        $status = array();
        foreach ( $statuses AS $key => $name ) {
            if ( strpos( $key, 'opalhotel-' ) === 0 ) {
                $status[ $key ] = ucwords( implode( ' ', explode( '-', substr( $name, strlen( 'opalhotel-' ) ) ) ) );
            }
        }

        return $status;
    }
}

if ( ! function_exists( 'opalhotel_get_order_status_description' ) ) {

    function opalhotel_get_order_status_description( $status = '' ) {
        $text = '';
        switch ( $status ) {
            case 'opalhotel-pending':
                    $text = __( 'New Booking, not verified.', 'opal-hotel-room-booking' );
                break;

            case 'opalhotel-cancelled':
                    $text = __( 'Cancelled status, no longer valid.', 'opal-hotel-room-booking' );
                break;

            case 'opalhotel-refunded':
                    $text = __( 'Booking refunded.', 'opal-hotel-room-booking' );
                break;

            case 'opalhotel-on-hold':
                    $text = __( 'Booking verify process incomplete.', 'opal-hotel-room-booking' );
                break;

            case 'opalhotel-processing':
                    $text = __( 'Booking offline, pay on arrival.', 'opal-hotel-room-booking' );
                break;

            case 'opalhotel-completed':
                    $text = __( 'Payment already completed', 'opal-hotel-room-booking' );
                break;

            default:
                    $text = __( 'No status.', 'opal-hotel-room-booking' );
                break;
        }

        return sprintf( '<span class="opalhotel_order_status %s">%s</span>', $status, $text );
    }
}

if ( ! function_exists( 'opalhotel_get_order_status' ) ) {

    /* get order status */
    function opalhotel_get_order_status( $order_id = null ) {
        if ( get_post_type( $order_id ) !== OPALHOTEL_CPT_BOOKING ) {
            return false;
        }

        $status = get_post_status( $order_id );
        if ( strpos( $status, 'opalhotel-' ) === 0 ) {
            return substr( $status, 10 );
        }
    }
}

if ( ! function_exists( 'opalhotel_get_order_status_label' ) ) {

    /* get status label format */
    function opalhotel_get_order_status_label( $order_id = null ){
        global $wp_post_statuses;
        $status = get_post_status( $order_id );
        if ( array_key_exists( $status, $wp_post_statuses ) ) {
            return sprintf( __( '<span class="opalhotel_order_status %s">%s</span>', 'opal-hotel-room-booking' ), $status, $wp_post_statuses[$status]->label );
        }
    }
}

if ( ! function_exists( 'opalhotel_remove_order_items' ) ) {

    /* remove all order items and order item meta */
    function opalhotel_remove_order_items( $order_id = null ) {
        if ( $order_id ) {
            $order = OpalHotel_Order::instance( $order_id );
            return $order->remove_order_items();
        }
    }
}

if ( ! function_exists( 'opalhotel_get_product_class' ) ) {

	/*
	 * @params $product_id integer
	 * @return class process
	 */
	function opalhotel_get_product_class( $product_id = null, $args = null ) {

		/* return false if product id is not exists */
		if ( ! $product_id ) {
			return false;
		}
		/* post type */
		$post_type = get_post_type( $product_id );
		$classname = false;
		if ( strpos( $post_type, 'opalhotel_' ) ===  0  ) {
			$classname = 'OpalHotel_' . ucfirst( substr( $post_type, strlen( 'opalhotel_' ) ) ) ;
		}

		if ( ! $classname ) {
			return false;
		}

		return apply_filters( 'opalhotel_get_product_class' , new $classname( $product_id, $args ), $classname );

	}

}

if ( ! function_exists( 'opalhotel_get_order_items' ) ) {
    function opalhotel_get_order_items( $order_id = null, $item_type = 'room', $parent = null ) {
        global $wpdb;

        $query  = '';
        if ( ! $parent ) {
            $query = $wpdb->prepare("
                    SELECT oporder.* FROM $wpdb->opalhotel_order_items AS oporder
                        LEFT JOIN $wpdb->posts AS post ON oporder.order_id = post.ID
                    WHERE post.ID = %d
                        AND oporder.order_item_type = %s
                ", $order_id, $item_type );
        } else {
            $query = $wpdb->prepare("
                    SELECT oporder.* FROM $wpdb->opalhotel_order_items AS oporder
                        LEFT JOIN $wpdb->posts AS post ON oporder.order_id = post.ID
                    WHERE post.ID = %d
                        AND oporder.order_item_type = %s
                        AND oporder.order_item_parent = %d
                ", $order_id, $item_type, $parent );
        }

        return apply_filters( 'opalhotel_get_order_items', $wpdb->get_results( $query ), $order_id, $item_type, $parent );
    }
}

/* insert new order item */
if ( ! function_exists( 'opalhotel_add_order_item' ) ) {
    function opalhotel_add_order_item( $order_id = null, $param = array() ) {
        global $wpdb;

        $order_id = absint( $order_id );

        if ( ! $order_id )
            return false;

        $defaults = array(
            'order_item_name'       => '',
            'order_item_type'       => 'room',
        );

        $param = wp_parse_args( $param, $defaults );

        $wpdb->insert(
            $wpdb->prefix . 'opalhotel_order_items',
            array(
                'order_item_name'       => $param['order_item_name'],
                'order_item_type'       => $param['order_item_type'],
                'order_item_parent'     => isset( $param['order_item_parent'] ) ? $param['order_item_parent'] : null,
                'order_id'              => $order_id
            ),
            array(
                '%s', '%s', '%d', '%d'
            )
        );

        $item_id = absint( $wpdb->insert_id );

        do_action( 'opalhotel_new_order_item', $item_id, $param, $order_id );

        return $item_id;
    }
}

if ( ! function_exists( 'opalhotel_get_order_item' ) ) {
    function opalhotel_get_order_item( $order_item_id = null ) {
        global $wpdb;

        $query = $wpdb->prepare("
                SELECT * FROM $wpdb->opalhotel_order_items WHERE order_item_id = %d
            ", $order_item_id );

        return apply_filters( 'opalhotel_get_order_item', $wpdb->get_row( $query ), $order_item_id );
    }
}

// update order item
if ( ! function_exists( 'opalhotel_update_order_item' ) ) {
    function opalhotel_update_order_item( $item_id = null, $param = array() ) {
        global $wpdb;

        $update = $wpdb->update( $wpdb->prefix . 'opalhotel_order_items', $param, array( 'order_item_id' => $item_id ) );

        if ( false === $update ) {
            return false;
        }

        do_action( 'opalhotel_update_order_item', $item_id, $param );

        return true;
    }
}

if ( ! function_exists( 'opalhotel_remove_order_item' ) ) {
    function opalhotel_remove_order_item( $order_item_id = null ) {
        global $wpdb;

        /* remove item */
        $wpdb->delete( $wpdb->opalhotel_order_items, array(
                'order_item_id'     => $order_item_id
            ), array( '%d' ) );

        /* remove item meta */
        $wpdb->delete( $wpdb->opalhotel_order_itemmeta, array(
                'opalhotel_order_item_id'     => $order_item_id
            ), array( '%d' ) );

        /* remove subitem */
        $wpdb->delete( $wpdb->opalhotel_order_items, array(
                'order_item_parent'     => $order_item_id
            ), array( '%d' ) );

        do_action( 'opalhotel_remove_order_item', $order_item_id );
    }
}

if ( ! function_exists( 'opalhotel_get_parent_order_item' ) ) {
    function opalhotel_get_parent_order_item( $order_item_id = null ) {
        global $wpdb;
        $query = $wpdb->prepare("
                SELECT order_item.order_item_parent FROM $wpdb->opalhotel_order_items AS order_item
                WHERE
                    order_item.order_item_id = %d
                    LIMIT 1
            ", $order_item_id );

        return $wpdb->get_var( $query );
    }
}

if ( ! function_exists( 'opalhotel_get_sub_item_order_item_id' ) ) {
    function opalhotel_get_sub_item_order_item_id( $order_item_id = null ) {
        global $wpdb;
        $query = $wpdb->prepare("
                SELECT order_item.order_item_id FROM $wpdb->opalhotel_order_items AS order_item
                WHERE
                    order_item.order_item_parent = %d
            ", $order_item_id );

        return $wpdb->get_col( $query );
    }
}

if ( ! function_exists( 'opalhotel_remove_sub_items' ) ) {
    function opalhotel_remove_sub_items( $order_item_id = null ) {
        global $wpdb;
        $wpdb->query( $wpdb->prepare( "DELETE FROM itemmeta USING {$wpdb->prefix}opalhotel_order_itemmeta itemmeta INNER JOIN {$wpdb->prefix}opalhotel_order_items items WHERE itemmeta.opalhotel_order_item_id = items.order_item_id and items.order_item_parent = %d", $order_item_id ) );
        $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}opalhotel_order_items WHERE order_item_parent = %d", $order_item_id ) );
    }
}

if ( ! function_exists( 'opalhotel_empty_order_items' ) ) {
    function opalhotel_empty_order_items( $order_id = null ) {
        global $wpdb;

        $sql = $wpdb->prepare("
                DELETE opalhotel_order_item, opalhotel_order_itemmeta
                    FROM $wpdb->opalhotel_order_items as opalhotel_order_item
                    LEFT JOIN $wpdb->opalhotel_order_itemmeta as opalhotel_order_itemmeta ON opalhotel_order_item.order_item_id = opalhotel_order_itemmeta.opalhotel_order_item_id
                WHERE
                    opalhotel_order_item.order_id = %d
            ", $order_id );

        return $wpdb->query( $sql );
    }
}

// add order item meta
if ( ! function_exists( 'opalhotel_add_order_item_meta' ) ) {
    function opalhotel_add_order_item_meta( $item_id = null, $meta_key = null, $meta_value = null, $unique = false ) {
        return add_metadata( 'opalhotel_order_item', $item_id, $meta_key, $meta_value, $unique );
    }
}

// update order item meta
if ( ! function_exists( 'opalhotel_update_order_item_meta' ) ) {
    function opalhotel_update_order_item_meta( $item_id = null, $meta_key = null, $meta_value = null, $prev_value = false ) {
        return update_metadata( 'opalhotel_order_item', $item_id, $meta_key, $meta_value, $prev_value );
    }
}

// get order item meta
function opalhotel_get_order_item_meta( $item_id = null, $key = nul, $single = true ) {
    return get_metadata( 'opalhotel_order_item', $item_id, $key, $single );
}

// delete order item meta
function opalhotel_delete_order_item_meta( $item_id = null, $meta_key = null, $meta_value = '', $delete_all = false ) {
    return delete_metadata( 'opalhotel_order_item', $item_id, $meta_key, $meta_value, $delete_all );
}
