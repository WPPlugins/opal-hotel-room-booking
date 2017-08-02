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

class OpalHotel_Table_Rating_Items extends WP_List_Table {

    /** Class constructor */
    public function __construct() {

        parent::__construct( array(
            'singular' => __( 'rating-item', 'opal-hotel-room-booking' ), //singular name of the listed records
            'plural'   => __( 'rating-items', 'opal-hotel-room-booking' ), //plural name of the listed records
            'ajax'     => false //does this table support ajax?
        ) );

    }

    protected function extra_tablenav( $which ) {

        global $wpdb;

        if( isset( $_REQUEST['rating_group'] ) ){
            $_SESSION['opalhotel_filtered_rating_group'] = $_REQUEST['rating_group'];
        }

        $query = "SELECT * FROM {$wpdb->prefix}opalhotel_ratings ORDER BY ID";

        $ratings = $wpdb->get_results( $query );

        $selected = isset( $_SESSION['opalhotel_filtered_rating_group'] ) ? $_SESSION['opalhotel_filtered_rating_group'] : '';

        if( $which == 'top' ){
            ?>
            <select name="rating_group">
                <option value=""><?php esc_html_e( 'All group', 'opal-hotel-room-booking' ) ?></option>
                <?php foreach ( $ratings as $rating ) : ?>
                    <option <?php selected( $selected, $rating->ID ) ?> value="<?php echo esc_attr( $rating->ID ) ?>"><?php echo esc_html( $rating->name ) ?></option>
                <?php endforeach; ?>
            </select>

            <input type="submit" name="filter_action" id="post-query-submit" class="button" value="Filter">
            <?php
        }

    }

    /**
     * Delete a customer record.
     *
     * @param int $id customer ID
     */
    public static function delete_rating_item( $id ) {
        do_action('opalhotel_delete_item_rating', $id);
    }

    /**
     * Returns the count of records in the database.
     *
     * @return null|string
     */
    public static function record_count() {
        global $wpdb;

        if ( isset( $_REQUEST['rating_group'] ) ){
            $group_id = $_REQUEST['rating_group'];
        } elseif ( isset( $_SESSION['opalhotel_filtered_rating_group'] ) ) {
            $group_id = (int) $_SESSION['opalhotel_filtered_rating_group'];
        } else {
            $group_id = 0;
        }

        $sql = "SELECT COUNT(*) FROM $wpdb->opalhotel_rating_item";

        if ( $group_id ) {
            $sql .= " WHERE rating_ID = " . $group_id;
        }

        return $wpdb->get_var( $sql );
    }

    /** Text displayed when no customer data is available */
    public function no_items() {
       esc_html_e( 'No items avaliable.', 'opal-hotel-room-booking' );
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
            case 'description':
                return $item[ 'rating_' . $column_name ];
            default:
                return print_r( $item, true ); //Show the whole array for troubleshooting purposes
        }
    }

    public function column_group_label($item){
        $html = '<a class="" href="admin.php?page=opalhotel-rating-options&rating-id='.$item['rating_ID'].'">'.$item['group_label'].'</a>';
        return $html;
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
            '<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['rating_item_ID']
        );
    }

    /**
     * Method for name column
     *
     * @param array $item an array of DB data
     *
     * @return string
     */
    function column_label( $item ) {
        $title = '<strong><a class="" href="'.admin_url( 'admin.php?page="opalhotel-rating-item-options&rating-item-id=' . $item['rating_item_ID'] ).'">' . $item['rating_name'] . '</a></strong>';

        return $title;
    }

    /**
     *  Associative array of columns
     *
     * @return array
     */
    function get_columns() {
        $columns = array(
                'cb'                => '<input type="checkbox" />',
                'label'             => __( 'Label', 'opal-hotel-room-booking' ),
                'description'       => __( 'Description', 'opal-hotel-room-booking' ),
                'group_label'       => __( 'Group', 'opal-hotel-room-booking' )
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
            'label' => array( 'label', true ),
            'weight' => array( 'weight', false )
        );

        return $sortable_columns;
    }

    function get_bulk_actions() {

        $bulk_actions = array(
            'delete'    => __( 'Delete', 'opal-hotel-room-booking' )
        );

        return $bulk_actions;
    }

    public function get_hidden_columns()
    {
        return array();
    }

    /**
     * Handles data query and filter, sorting, and pagination.
     */
    public function prepare_items() {

        /** Process bulk action */
        $this->process_bulk_action();

        if ( isset( $_REQUEST['rating_group'] ) ){
            $group_id = $_REQUEST['rating_group'];
        } elseif ( isset( $_SESSION['opalhotel_filtered_rating_group'] ) ) {
            $group_id = (int) $_SESSION['opalhotel_filtered_rating_group'];
        } else {
            $group_id = 0;
        }

        global $wpdb;

        $sql = "SELECT item.*, group_tbl.name AS group_label";
        $sql .= " FROM {$wpdb->prefix}opalhotel_rating_item AS item";
        $sql .= " LEFT JOIN $wpdb->opalhotel_ratings AS group_tbl";
        $sql .= " ON item.rating_ID = group_tbl.ID";

        if ( $group_id ) {
            $sql .= " WHERE item.rating_ID = " . $group_id;
        }

        if ( ! empty( $_REQUEST['orderby'] ) ) {
            $sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
            $sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
        }

        $columns = $this->get_columns();
        $hidden = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);

        $per_page     = 5;
        $current_page = $this->get_pagenum();
        $total_items  = self::record_count();

        $this->set_pagination_args( array(
            'total_items' => $total_items, //WE have to calculate the total number of items
            'per_page'    => $per_page //WE have to determine how many items to show on a page
        ) );

        $sql .= " LIMIT " . $per_page  . " OFFSET " . ( $current_page - 1 ) * $per_page;

        $this->items = $wpdb->get_results( $sql, 'ARRAY_A' );
    }

    public function process_bulk_action() {

        // If the delete bulk action is triggered
        if ( $this->current_action() == 'delete') {

            if(isset($_POST['bulk-delete'])){
                $delete_ids = esc_sql( $_POST['bulk-delete'] );

                // loop over the array of record IDs and delete them
                foreach ( $delete_ids as $id ) {
                    self::delete_rating_item( $id );
                }
            }

            if($_GET['id']){
                self::delete_rating_item( absint( $_GET['id'] ) );
            }

            echo '<div class="updated"><p>' . __('Delete rating items bulk action processed successfully', 'opal-hotel-room-booking' ) . '</p></div>';
        }

    }

}
