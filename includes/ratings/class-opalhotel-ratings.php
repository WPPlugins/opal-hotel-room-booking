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

require_once OPALHOTEL_INC_PATH . '/ratings/tables/class-opalhotel-table-ratings.php';
require_once OPALHOTEL_INC_PATH . '/ratings/tables/class-opalhotel-table-rating-items.php';

class OpalHotel_Ratings {

    public static function create_tables() {
        // Create Rating Table
        global $wpdb;

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        /* get database charset */
        $charset_collate = $wpdb->get_charset_collate();

        $table = $wpdb->prefix . 'opalhotel_ratings';
        if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table}'" ) != $table ) {
            $sql = "
                CREATE TABLE IF NOT EXISTS {$wpdb->prefix}opalhotel_ratings (
                    ID bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                    name varchar(255) NOT NULL,
                    description varchar(255) NOT NULL,
                    UNIQUE KEY ID (ID),
                    PRIMARY KEY  (ID)
                ) $charset_collate;
            ";
            dbDelta( $sql );
        }

        $table = $wpdb->prefix . 'opalhotel_rating_item';
        if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table}'" ) != $table ) {
            $sql = "
                CREATE TABLE IF NOT EXISTS {$wpdb->prefix}opalhotel_rating_item (
                    rating_item_ID bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                    rating_name varchar(255) NOT NULL,
                    rating_description varchar(255) NOT NULL,
                    rating_ID bigint(11) DEFAULT 0,
                    UNIQUE KEY rating_item_ID (rating_item_ID),
                    PRIMARY KEY  (rating_item_ID)
                ) $charset_collate;
            ";
            dbDelta( $sql );
        }
    }

    public static function init(){

        add_action( 'opalhotel_before_add_admin_menu_page', array( __CLASS__, 'add_menu' ), 10 );

        add_action( 'admin_init', array( __CLASS__, 'save_rating' ) );
        add_action( 'admin_init', array( __CLASS__, 'save_rating_item' ) );

        add_action( 'opalhotel_delete_group_rating', array( __CLASS__, 'delete_rating_callback' ), 1, 1 );
        add_action( 'opalhotel_delete_item_rating', array( __CLASS__, 'delete_rating_item_callback' ), 1, 1 );
    }

    /**
     * Add menu and sub menu page
     */
    public static function add_menu() {
        add_submenu_page( 'opal-hotel-room-booking', __( 'Rating Group', 'opal-hotel-room-booking' ), __( 'Rating Group', 'opal-hotel-room-booking' ), 'manage_options', 'opalhotel-rating-options', array( __CLASS__, 'print_rating_setting' ) );
        add_submenu_page( 'opal-hotel-room-booking', __('Rating Items', 'opal-hotel-room-booking'), __('Rating Items', 'opal-hotel-room-booking'), 'manage_options', 'opalhotel-rating-item-options', array( __CLASS__, 'print_rating_item_setting' ) );
    }

    public static function save_rating() {
        global $wpdb;
        if ( ! isset( $_POST['opalhotel-submit-rating-nonce'] ) )
            return;

        if ( ! wp_verify_nonce( $_POST['opalhotel-submit-rating-nonce'], 'opalhotel_submit_rating' ) ) {

           wp_die( __( 'Sorry, your nonce did not verify.', 'opal-hotel-room-booking' ) );

        }

        $rating_id = 0;
        if( isset( $_POST['ID'] ) ) {
            $rating_id = absint( $_POST['ID'] );
            $wpdb->query(
                $wpdb->prepare(
                    "UPDATE {$wpdb->prefix}opalhotel_ratings SET name = %s, description = %s WHERE ID = %d",
                    $_POST['name'], $_POST['description'], absint( $_POST['ID'] )
                )
            );
            opalhotel_add_admin_notice( __('Rating has been created successfully.', 'opal-hotel-room-booking' ), 'updated' );
        } else {
            $wpdb->insert( $wpdb->prefix.'opalhotel_ratings', array(
                'name'          => sanitize_text_field( $_POST['name'] ),
                'description'   => $_POST['description'],
            ) );
            $rating_id = absint( $wpdb->insert_id );
            opalhotel_add_admin_notice( __('Rating has been updated successfully.', 'opal-hotel-room-booking' ), 'updated' );
        }

        wp_redirect( admin_url( 'admin.php?page=opalhotel-rating-options&rating-id=' . $rating_id ) ); exit();

    }

    public static function save_rating_item(){

        global $wpdb;

        if ( ! isset( $_POST['opalhotel-submit-rating-item-nonce'] ) ) {
            return;
        }

        if ( ! wp_verify_nonce( $_POST['opalhotel-submit-rating-item-nonce'], 'opalhotel-submit-rating-item' ) ) {
            wp_die( __( 'Sorry, your nonce did not verify.', 'opal-hotel-room-booking' ) );
        }

        $name = ! empty( $_POST["name"] ) ? sanitize_text_field( $_POST["name"] ) : '';
        $description = ! empty( $_POST["description"] ) ? sanitize_text_field( $_POST["description"] ) : '';
        $rating_ID = ! empty( $_POST["rating_ID"] ) ? absint( $_POST["rating_ID"] ) : 0;

        $rating_item_ID = 0;
        if( isset( $_POST['rating-item-ID'] ) ) {
            $rating_item_ID = absint( $_POST['rating-item-ID'] );
            $wpdb->update( $wpdb->opalhotel_rating_item, array(
                    'rating_name'           => $name,
                    'rating_description'    => $description,
                    'rating_ID'             => $rating_ID
                ), array(
                    'rating_item_ID'        => $rating_item_ID
                ), array(
                    '%s',
                    '%s',
                    '%d'
                ) );
            opalhotel_add_admin_notice( __( 'Rating item has been updated successfully.', 'opal-hotel-room-booking' ), 'updated' );
        } else {
            $args = array(
                'rating_name'           => $name,
                'rating_description'    => $description,
                'rating_ID'             => $rating_ID
            );
            $wpdb->insert( $wpdb->opalhotel_rating_item, $args);
            $rating_item_ID = absint( $wpdb->insert_id );
            opalhotel_add_admin_notice( __( 'Rating item has been created successfully.', 'opal-hotel-room-booking' ), 'updated' );
        }

        wp_redirect( admin_url( 'admin.php?page=opalhotel-rating-item-options&rating-item-id=' . $rating_item_ID ) ); exit();

    }

    public static function print_rating_setting() {
        if( isset( $_GET['rating-id'] ) ) {
            $rating_id = absint( $_GET['rating-id'] );
            $rating = opalhotel_get_rating( $rating_id );
            if ( $rating ) {
                self::render_view( 'ratings/edit-rating.php', array( 'rating' => $rating ) );
            } else {
                self::render_view( 'ratings/create-rating.php' );
            }
        } else {
            self::render_view('ratings/list-ratings.php');
        }
    }

    public static function print_rating_item_setting() {

        if( isset( $_GET['rating-item-id'] ) ){
            $rating_item_id = absint( $_GET['rating-item-id'] );
            $rating_item = opalhotel_get_rating_item( $rating_item_id );
            if( $rating_item_id ){
                self::render_view( 'rating-items/edit-rating-item.php', array( 'rating_item' => $rating_item ) );
            } else {
                self::render_view( 'rating-items/create-rating-item.php' );
            }
        } else {
            self::render_view( 'rating-items/list-rating-item.php' );
        }
    }

    /**
     * Callback delete rating group
     *
     *
     * @param $rating_group_id
     */
    public static function delete_rating_callback( $rating_group_id = null ){

        global $wpdb;

        $wpdb->delete( $wpdb->opalhotel_ratings, array( 'ID' => $rating_group_id ), array( "%d" ) );

        $query = "SELECT * FROM $wpdb->opalhotel_rating_item WHERE rating_ID = %d";

        $rating_items = $wpdb->get_results( $wpdb->prepare( $query, $rating_group_id));

        if( ! empty( $rating_items ) ){
            foreach ( $rating_items as $rating_item ) {
                do_action( 'opalhotel_delete_item_rating', $rating_item->rating_item_ID );
            }

        }

    }

    /**
     * Callback delete rating item
     *
     * @param $rating_item_id
     */
    public static function delete_rating_item_callback( $rating_item_id = null ){
        global $wpdb;
        $wpdb->delete( $wpdb->opalhotel_rating_item, array( '' => $rating_item_id ), array( '%d' ) );
        $wpdb->delete( $wpdb->commentmeta, array( 'meta_key' => 'opalhotel_rating_' . $rating_item_id ), array( '%d' ) );
    }

    /**
     * Render Setting Views
     *
     * @since 1.1.7
     */
    public static function render_view( $path = '', $args = array(), $once = true ) {
        $file = dirname( __FILE__ ) . '/views/' . $path ;
        if ( file_exists( $file ) ) {
            extract( $args );
            if ( $once ) {
                require_once $file;
            } else {
                require $file;
            }
        } else {
            _doing_it_wrong( __FUNCTION__, sprintf( __( '%s file is not exists.', 'opal-hotel-room-booking' ), $file ) );
        }
    }

}

OpalHotel_Ratings::init();