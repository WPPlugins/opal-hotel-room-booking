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

class OpalHotel_Post_Type_Hotel extends OpalHotel_Abstract_Post_Type {

	/* post type */
	public $post_type = null;

	/* post type args */
	public $post_type_args = null;

	public function __construct() {

		/* post type name*/
		$this->post_type = OPALHOTEL_CPT_HOTEL;

		/* post type args register */
		$this->post_type_args = array(
            'labels'             => array(
                'name'               => _x( 'Hotels', 'post type general name', 'opal-hotel-room-booking' ),
                'singular_name'      => _x( 'Hotel', 'post type singular name', 'opal-hotel-room-booking' ),
                'menu_name'          => __( 'Hotels', 'opal-hotel-room-booking' ),
                'parent_item_colon'  => __( 'Parent Item:', 'opal-hotel-room-booking' ),
                'all_items'          => __( 'Hotels', 'opal-hotel-room-booking' ),
                'view_item'          => __( 'View Hotel', 'opal-hotel-room-booking' ),
                'add_new_item'       => __( 'Add Hotel', 'opal-hotel-room-booking' ),
                'add_new'            => __( 'Add Hotel', 'opal-hotel-room-booking' ),
                'edit_item'          => __( 'Edit Hotel', 'opal-hotel-room-booking' ),
                'update_item'        => __( 'Update Hotel', 'opal-hotel-room-booking' ),
                'search_items'       => __( 'Search Hotel', 'opal-hotel-room-booking' ),
                'not_found'          => __( 'No hotel found', 'opal-hotel-room-booking' ),
                'not_found_in_trash' => __( 'No hotel found in Trash', 'opal-hotel-room-booking' ),
                'featured_image'        => __( 'Hotel Image', 'opal-hotel-room-booking' ),
                'set_featured_image'    => __( 'Set hotel image', 'opal-hotel-room-booking' ),
                'remove_featured_image' => __( 'Remove hotel image', 'opal-hotel-room-booking' ),
                'use_featured_image'    => __( 'Use as hotel image', 'opal-hotel-room-booking' ),
            ),
            'public'             => true,
            'query_var'          => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'has_archive'        => ( $hotels_page_id = opalhotel_get_page_id( 'hotels' ) ) && get_post( $hotels_page_id ) ? get_page_uri( $hotels_page_id ) : 'hotels',
            'capability_type'    => 'post',
            'map_meta_cap'       => true,
            'show_in_menu'       => true,
            'show_in_admin_bar'  => true,
            'show_in_nav_menus'  => true,
            'supports'           => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'comments', 'author' ),
            'hierarchical'       => false,
            'rewrite'            => array( 'slug' => _x( 'hotels', 'URL slug', 'opal-hotel-room-booking' ), 'with_front' => false, 'feeds' => true ),
            'menu_position'      => 10,
            'menu_icon'          => 'dashicons-admin-home'
        );

		parent::__construct();

        /* custom message update hotel */
        add_filter( 'post_updated_messages', array( $this, 'updated_messages' ) );

        add_filter( 'manage_' . OPALHOTEL_CPT_HOTEL . '_posts_columns', array( __CLASS__, 'manage_posts_columns' ), 99 );
        add_action( 'manage_' . OPALHOTEL_CPT_HOTEL . '_posts_custom_column', array( __CLASS__, 'manage_posts_custom_columns' ), 1 );
    }

    /* custom messages */
    public function updated_messages( $messages ) {
        $post             = get_post();
        $post_type        = get_post_type( $post );
        $post_type_object = get_post_type_object( $post_type );
        if ( ! in_array( $post_type, array( OPALHOTEL_CPT_HOTEL ) ) ) {
            return $messages;
        }

        $messages[ OPALHOTEL_CPT_HOTEL ] = array(
            0  => '', // Unused. Messages start at index 1.
            1  => __( 'Hotel updated.', 'opal-hotel-room-booking' ),
            2  => __( 'Custom field updated.', 'opal-hotel-room-booking' ),
            3  => __( 'Custom field deleted.', 'opal-hotel-room-booking' ),
            4  => __( 'Hotel updated.', 'opal-hotel-room-booking' ),
            /* translators: %s: date and time of the revision */
            5  => isset( $_GET['revision'] ) ? sprintf( __( 'Hotel restored to revision from %s', 'opal-hotel-room-booking' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
            6  => __( 'Hotel published.', 'opal-hotel-room-booking' ),
            7  => __( 'Hotel saved.', 'opal-hotel-room-booking' ),
            8  => __( 'Hotel submitted.', 'opal-hotel-room-booking' ),
            9  => sprintf(
                __( 'Hotel scheduled for: <strong>%1$s</strong>.', 'opal-hotel-room-booking' ),
                // translators: Publish box date format, see http://php.net/date
                date_i18n( __( 'M j, Y @ G:i', 'opal-hotel-room-booking' ), strtotime( $post->post_date ) )
            ),
            10 => __( 'Hotel draft updated.', 'opal-hotel-room-booking' )
        );

        if ( $post_type_object->publicly_queryable ) {
            $permalink = get_permalink( $post->ID );

            $view_link = sprintf( ' <a href="%s">%s</a>', esc_url( $permalink ), __( 'View Hotel', 'opal-hotel-room-booking' ) );
            $messages[ $post_type ][1] .= $view_link;
            $messages[ $post_type ][6] .= $view_link;
            $messages[ $post_type ][9] .= $view_link;

            $preview_permalink = add_query_arg( 'preview', 'true', $permalink );
            $preview_link = sprintf( ' <a target="_blank" href="%s">%s</a>', esc_url( $preview_permalink ), __( 'Preview room', 'opal-hotel-room-booking' ) );
            $messages[ $post_type ][8]  .= $preview_link;
            $messages[ $post_type ][10] .= $preview_link;
        }
        return $messages;
    }

    /**
     * Manage Posts Columns
     *
     * @since 1.1.7
     */
    public static function manage_posts_columns( $columns ) {
        $add_columns = array(
                'thumb'     => '<span class="opalhotel_tiptip" data-tip="' . esc_attr__( 'Image', 'opal-hotel-room-booking' ) . '"><i class="fa fa-file-image-o" aria-hidden="true"></i></span>',
                'star'      => '<i class="fa fa-star"></i>',
                'checkin'   => __( 'Checkin', 'opal-hotel-room-booking' ),
                'checkout'  => __( 'Checkout', 'opal-hotel-room-booking' ),
                'rooms'     => __( 'Rooms', 'opal-hotel-room-booking' )
            );
        $columns = array_merge( $columns, $add_columns );

        return $columns;
    }

    /**
     * Manage Posts Custom Columns
     *
     * @since 1.1.7
     */
    public static function manage_posts_custom_columns( $column ) {
        global $post;
        $hotel = opalhotel_get_hotel( $post->ID );
        switch ( $column ) {
            case 'thumb':
                echo '<a href="' . get_edit_post_link( $post->ID ) . '">'
                    . opalhotel_room_placeholder_image( 'room_thumb' )
                    . '</a>';
                break;
            case 'star':
                    printf( translate_nooped_plural( _n_noop( '%s star', '%s stars' ), $hotel->star, 'opal-hotel-room-booking' ), $hotel->star );
                break;
            case 'checkin':
                    echo esc_html( $hotel->checkin_time );
                break;
            case 'checkout':
                    echo esc_html( $hotel->checkout_time );
                break;
            case 'rooms':
                    printf( '<a href="%s">%s</a>', admin_url( 'edit.php?post_type=' . OPALHOTEL_CPT_ROOM . '&hotel_id=' . $post->ID ), opalhotel_get_rooms_hotel_count( $post->ID ) );
                break;

            default:
                echo '<pre>'; print_r( $hotel ); echo '</pre>';
                break;
        }
    }

}

new OpalHotel_Post_Type_Hotel();