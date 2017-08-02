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

class OpalHotel_Post_Type_Booking extends OpalHotel_Abstract_Post_Type {

	/* post type */
	public $post_type = null;

	/* post type args */
	public $post_type_args = null;

	public function __construct() {

		/* post type name*/
		$this->post_type = OPALHOTEL_CPT_BOOKING;

		/* post type args register */
		$this->post_type_args = array(
            'labels'             => array(
                'name'               => _x( 'Bookings', 'post type general name', 'opal-hotel-room-booking' ),
                'singular_name'      => _x( 'Book', 'post type singular name', 'opal-hotel-room-booking' ),
                'menu_name'          => __( 'Bookings', 'opal-hotel-room-booking' ),
                'parent_item_colon'  => __( 'Parent Item:', 'opal-hotel-room-booking' ),
                'all_items'          => __( 'Bookings', 'opal-hotel-room-booking' ),
                'view_item'          => __( 'View Book', 'opal-hotel-room-booking' ),
                'add_new_item'       => __( 'Add Book', 'opal-hotel-room-booking' ),
                'add_new'            => __( 'Add Book', 'opal-hotel-room-booking' ),
                'edit_item'          => __( 'Edit Book', 'opal-hotel-room-booking' ),
                'update_item'        => __( 'Update Book', 'opal-hotel-room-booking' ),
                'search_items'       => __( 'Search Book', 'opal-hotel-room-booking' ),
                'not_found'          => __( 'No book found', 'opal-hotel-room-booking' ),
                'not_found_in_trash' => __( 'No book found in Trash', 'opal-hotel-room-booking' ),
            ),
            'public'             => false,
            'query_var'          => true,
            'publicly_queryable' => false,
            'show_ui'            => true,
            'has_archive'        => false,
            'capability_type'    => 'post',
            'map_meta_cap'       => true,
            'show_in_menu'       => 'opal-hotel-room-booking',
            'show_in_admin_bar'  => true,
            'show_in_nav_menus'  => true,
            'supports'           => array( 'none' ),
            'hierarchical'       => false,
            'rewrite'            => array( 'slug' => _x( 'book', 'URL slug', 'opal-hotel-room-booking' ), 'with_front' => false, 'feeds' => true ),
            'menu_position'      => 10,
            'menu_icon'          => 'dashicons-admin-home'
        );

		parent::__construct();
        /* custom message update room */
        add_filter( 'post_updated_messages', array( $this, 'updated_messages' ) );

        /* register status */
        add_action( 'init', array( $this, 'register_booking_status' ) );
	}

    /* custom messages */
    public function updated_messages( $messages ) {
        $post             = get_post();
        $post_type        = get_post_type( $post );
        $post_type_object = get_post_type_object( $post_type );
        if ( ! in_array( $post_type, array( OPALHOTEL_CPT_BOOKING ) ) ) {
            return $messages;
        }

        $messages[ OPALHOTEL_CPT_BOOKING ] = array(
            0  => '', // Unused. Messages start at index 1.
            1  => __( 'Book updated.', 'opal-hotel-room-booking' ),
            2  => __( 'Custom field updated.', 'opal-hotel-room-booking' ),
            3  => __( 'Custom field deleted.', 'opal-hotel-room-booking' ),
            4  => __( 'Book updated.', 'opal-hotel-room-booking' ),
            /* translators: %s: date and time of the revision */
            5  => isset( $_GET['revision'] ) ? sprintf( __( 'Book restored to revision from %s', 'opal-hotel-room-booking' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
            6  => __( 'Book published.', 'opal-hotel-room-booking' ),
            7  => __( 'Book saved.', 'opal-hotel-room-booking' ),
            8  => __( 'Book submitted.', 'opal-hotel-room-booking' ),
            9  => sprintf(
                __( 'Book scheduled for: <strong>%1$s</strong>.', 'opal-hotel-room-booking' ),
                // translators: Publish box date format, see http://php.net/date
                date_i18n( __( 'M j, Y @ G:i', 'opal-hotel-room-booking' ), strtotime( $post->post_date ) )
            ),
            10 => __( 'Book draft updated.', 'opal-hotel-room-booking' )
        );

        if ( $post_type_object->publicly_queryable ) {
            $permalink = get_permalink( $post->ID );

            $view_link = sprintf( ' <a href="%s">%s</a>', esc_url( $permalink ), __( 'View Book', 'opal-hotel-room-booking' ) );
            $messages[ $post_type ][1] .= $view_link;
            $messages[ $post_type ][6] .= $view_link;
            $messages[ $post_type ][9] .= $view_link;

            $preview_permalink = add_query_arg( 'preview', 'true', $permalink );
            $preview_link = sprintf( ' <a target="_blank" href="%s">%s</a>', esc_url( $preview_permalink ), __( 'Preview book', 'opal-hotel-room-booking' ) );
            $messages[ $post_type ][8]  .= $preview_link;
            $messages[ $post_type ][10] .= $preview_link;
        }
        return $messages;
    }

    /**
     * register_booking_status
     * @return var
     */
    public function register_booking_status() {
        /* cancelled */
        register_post_status( 'opalhotel-cancelled', apply_filters( 'opalopalhotel_cancelled_status', array(
            'label'                     => _x( 'Cancelled', 'Booking status', 'opal-hotel-room-booking' ),
            'public'                    => true,
            'exclude_from_search'       => false,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'label_count'               => _n_noop( 'Cancelled <span class="count">(%s)</span>', 'Cancelled <span class="count">(%s)</span>' ),
        ) ) );

        /* On Hold */
        register_post_status( 'opalhotel-on-hold', apply_filters( 'opalopalhotel_on-hold_status', array(
            'label'                     => _x( 'On Hold', 'Booking status', 'opal-hotel-room-booking' ),
            'public'                    => true,
            'exclude_from_search'       => false,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'label_count'               => _n_noop( 'On Hold <span class="count">(%s)</span>', 'On Hold <span class="count">(%s)</span>' ),
        ) ) );

        /* pending */
        register_post_status( 'opalhotel-pending', apply_filters( 'opalopalhotel_pending_status', array(
            'label'                     => _x( 'Pending', 'Booking status', 'opal-hotel-room-booking' ),
            'public'                    => true,
            'exclude_from_search'       => false,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'label_count'               => _n_noop( 'Pending <span class="count">(%s)</span>', 'Pending <span class="count">(%s)</span>' ),
        ) ) );

        /* processing */
        register_post_status( 'opalhotel-processing', apply_filters( 'opalopalhotel_processing_status', array(
            'label'                     => _x( 'Processing', 'Booking status', 'opal-hotel-room-booking' ),
            'public'                    => true,
            'exclude_from_search'       => false,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'label_count'               => _n_noop( 'Processing <span class="count">(%s)</span>', 'Processing <span class="count">(%s)</span>' ),
        ) ) );

        /* Refunded */
        register_post_status( 'opalhotel-refunded', apply_filters( 'opalopalhotel_refunded_status', array(
            'label'                     => _x( 'Refunded', 'Booking status', 'opal-hotel-room-booking' ),
            'public'                    => true,
            'exclude_from_search'       => false,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'label_count'               => _n_noop( 'Refunded <span class="count">(%s)</span>', 'Refunded <span class="count">(%s)</span>' ),
        ) ) );

        /* completed */
        register_post_status( 'opalhotel-completed', apply_filters( 'opalopalhotel_completed_status', array(
            'label'                     => _x( 'Completed', 'Booking status', 'opal-hotel-room-booking' ),
            'public'                    => true,
            'exclude_from_search'       => false,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'label_count'               => _n_noop( 'Completed <span class="count">(%s)</span>', 'Completed <span class="count">(%s)</span>' ),
        ) ) );

    }
}

new OpalHotel_Post_Type_Booking();
