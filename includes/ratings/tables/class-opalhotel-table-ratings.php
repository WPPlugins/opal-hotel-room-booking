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

if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class OpalHotel_Table_Ratings extends WP_List_Table {

    /** Class constructor */
    public function __construct() {

        parent::__construct( array(
                'singular' => __( 'rating-group', 'opal-hotel-room-booking' ), //singular name of the listed records
                'plural'   => __( 'rating-groups', 'opal-hotel-room-booking' ), //plural name of the listed records
                'ajax'     => false //does this table support ajax?
            ) );

    }

    /**
     * Delete a customer record.
     *
     * @param int $id customer ID
     */
    public static function delete_rating_group( $id ) {
        do_action( 'opalhotel_delete_group_rating', $id );
    }

    /**
     * Returns the count of records in the database.
     *
     * @return null|string
     */
    public static function record_count() {
        global $wpdb;

        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}opalhotel_ratings";

        return $wpdb->get_var( $sql );
    }

    /** Text displayed when no customer data is available */
    public function no_items() {
       esc_html_e( 'No items avaliable.', 'sp' );
    }

    /**
     * Render a column when no column specific method exist.
     *
     * @param array $item
     * @param string $column_name
     *
     * @return mixed
     */
    public function column_default( $item, $column_name ) {

        switch ( $column_name ) {
            case 'name':
                return sprintf( '<a href="%s">%s</a>', admin_url( 'admin.php?page=opalhotel-rating-options&rating-id=' . $item->ID ), $item->$column_name );
                break;
            case 'description':
                return sprintf( '<p>%s</p>', $item->$column_name );
                break;
            default:
                return print_r( $item, true ); //Show the whole array for troubleshooting purposes
                break;
        }
    }

    /**
     * Render the bulk edit checkbox
     *
     * @param array $item
     *
     * @return string
     */
    function column_cb( $item ) {
        return sprintf(
            '<input type="checkbox" name="bulk-delete[]" value="%s" />', $item->ID
        );
    }

    /**
     * Method for name column
     *
     * @param array $item an array of DB data
     *
     * @return string
     */
    function column_name( $item ) {
        $actions = array(
            'edit'      => sprintf( '<a href="%s">%s</a>', admin_url( 'admin.php?page=opalhotel-rating-options&rating-id=' . $item->ID ), __( 'Edit', 'opal-hotel-room-booking' ) ),
            'delete'    => sprintf( '<a href="%s&action=%s&id=%s">%s</a>', admin_url( 'admin.php?page=opalhotel-rating-options' ), 'delete', $item->ID, __( 'Delete', 'opal-hotel-room-booking' ) ),
        );
        $title = '<strong><a href="'. admin_url( 'admin.php?page=opalhotel-rating-options&rating-id=' . $item->ID ).'">' . $item->name . '</a></strong>';

        return sprintf( '%s %s', $title, $this->row_actions( $actions ) );
        return $title;
    }

    /**
     *  Associative array of columns
     *
     * @return array
     */
    function get_columns() {
        $columns = array(
                'cb'            => '<input type="checkbox" />',
                'name'          => __( 'Name', 'opal-hotel-room-booking' ),
                'description'   => __( 'Description', 'opal-hotel-room-booking' )
            );

        return $columns;
    }

    /**
     * Columns to make sortable.
     *
     * @return array
     */
    public function get_sortable_columns() {
        $sortable_columns = array(
            'name' => array( 'name', true )
        );

        return $sortable_columns;
    }

    function get_bulk_actions() {

        $bulk_actions = array(
            'delete'    => __( 'Delete', 'opal-hotel-room-booking' )
        );

        return $bulk_actions;
    }

    public function get_hidden_columns() {
        return array();
    }

    /**
     * Handles data query and filter, sorting, and pagination.
     */
    public function prepare_items() {

        /** Process bulk action */
        $this->process_bulk_action();

        global $wpdb;

        $sql = "SELECT * FROM {$wpdb->prefix}opalhotel_ratings";

        if ( ! empty( $_REQUEST['orderby'] ) ) {
            $sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
            $sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
        }

        $columns = $this->get_columns();
        $hidden = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array( $columns, $hidden, $sortable );

        $per_page     = 5;
        $total_items  = self::record_count();

        $this->set_pagination_args( array(
            'total_items' => $total_items, //WE have to calculate the total number of items
            'per_page'    => $per_page //WE have to determine how many items to show on a page
        ) );

        $current_page = $this->get_pagenum();

        $sql .= " LIMIT " . $per_page  . " OFFSET " . ( $current_page - 1 ) * $per_page;

        $this->items = $wpdb->get_results( $sql );
    }

    public function process_bulk_action() {

        // If the delete bulk action is triggered
        $action = $this->current_action();
        if ( $action == 'delete') {

            if(isset($_POST['bulk-delete'])){
                $delete_ids = esc_sql( $_POST['bulk-delete'] );

                // loop over the array of record IDs and delete them
                foreach ( $delete_ids as $id ) {
                    self::delete_rating_group( $id );
                }
            }

            if( $_GET['id'] ){
                self::delete_rating_group( absint( $_GET['id'] ) );
            }

            opalhotel_add_admin_notice( __( 'Rating has been removed successfully.', 'opal-hotel-room-booking' ), 'updated' );
        }

    }

}
