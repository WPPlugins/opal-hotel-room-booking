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

class OpalHotel_Comment {

    /**
     * Constructor
     */
    public function __construct(){
        add_action( 'opalhotel_after_single_room_main', array( $this, 'comments_template' ), 10 );
        add_filter( 'comments_template', array( $this, 'comment_template' ) );
        add_action( 'comment_post', array( __CLASS__, 'update_rating' ), 10, 2 );

        add_filter( 'manage_edit-comments_columns', array( $this, 'comments_column' ), 10, 2 );
        add_filter( 'manage_comments_custom_column', array( $this, 'comments_custom_column' ), 10, 2 );

        add_action( 'comment_unapproved_to_approved', array( __CLASS__, 'update_comment' ) );
        add_action( 'comment_approved_to_unapproved', array( __CLASS__, 'update_comment' ) );
        add_action( 'comment_spam_to_approved', array( __CLASS__, 'update_comment' ) );
        add_action( 'comment_approved_to_spam', array( __CLASS__, 'update_comment' ) );
        add_action( 'comment_approved_to_trash', array( __CLASS__, 'update_comment' ) );
        add_action( 'comment_trash_to_approved', array( __CLASS__, 'update_comment' ) );

    }

    /**
     * Add comment rating
     *
     * @param int $comment_id
     */
    public static function update_rating( $comment_id, $approved ) {
        $comment = get_comment( $comment_id );
        $post_id = absint( $comment->comment_post_ID );
        $rating_id = opalhotel_get_post_rating_id( $comment->comment_post_ID );

        if ( ! in_array( get_post_type( $post_id ), array( OPALHOTEL_CPT_ROOM, OPALHOTEL_CPT_HOTEL ) ) ) {
            return;
        }
        if ( ! isset( $_POST['opalhotel_rating'] ) || empty( $_POST['opalhotel_rating'] ) ) {
            return;
        }

        if ( ! empty( $_POST['title'] ) ) {
            update_comment_meta( $comment_id, 'opalhotel_comment_title', sanitize_text_field( $_POST['title'] ) );
        }
        $rates = $_POST['opalhotel_rating'];

        $total = count( $rates );
        $sum = 0;
        foreach ( $rates as $id => $value ) {
            $sum = $sum + absint( $value );
            $id = sanitize_text_field( $id );
            // save comment rating
            update_comment_meta( $comment_id, 'opalhotel_rating_' . $id, $value, true );
        }

        update_comment_meta( $comment_id, 'opalhotel_rating_advance_' . $rating_id, floatval( $sum / $total ) );

        if ( $approved ) {
            self::update_comment( $comment );
        }
    }

    public static function update_comment( $comment ) {
        $post_id = $comment->comment_post_ID;
        $post_type = get_post_type( $post_id );
        if ( ! in_array( $post_type, array( OPALHOTEL_CPT_HOTEL, OPALHOTEL_CPT_ROOM ) ) ) return;

        $averger_rating = opalhotel_get_average_rating( $post_id, false, true );

        // average rating
        update_post_meta( $post_id, 'opalhotel_average_rating', $averger_rating );

        // rating_id
        $rating_items = opalhotel_get_rating_items( $post_id );
        foreach ( $rating_items as $rating_item ) {
            update_post_meta( $post_id, 'opalhotel_average_rating_item_' . $rating_item->rating_item_ID, opalhotel_get_average_rating( $post_id, $rating_item->rating_item_ID, true ) );
        }
    }

    public function comments_template() {
        if ( comments_open() ) {
            comments_template();
        }
    }

    public static function comment_filter( $comment, $args, $depth ) {
        opalhotel_get_template( 'single-room/reviews/review-loop.php', array( 'comment' => $comment, 'args' => $args, 'depth' => $depth ) );
    }

    public static function comment_filter_advance( $comment, $args, $depth ) {
        opalhotel_get_template( 'comments/review-loop.php', array( 'comment' => $comment, 'args' => $args, 'depth' => $depth ) );
    }

    /**
     * Load template for reviews if we found a file in theme/plugin directory
     *
     * @param string $template
     * @return string
     */
    public function comment_template( $template ) {
        if ( ! in_array( get_post_type(), array( OPALHOTEL_CPT_ROOM, OPALHOTEL_CPT_HOTEL ) ) ) {
            return $template;
        }

        $folder = get_post_type() === OPALHOTEL_CPT_ROOM ? 'single-room' : 'comments';

        $check_dirs = array(
            trailingslashit( get_stylesheet_directory() ) . OpalHotel::instance()->template_path(),
            trailingslashit( get_template_directory() ) . OpalHotel::instance()->template_path(),
            trailingslashit( get_stylesheet_directory() ),
            trailingslashit( get_template_directory() ),
            trailingslashit( OpalHotel::instance()->plugin_path() . '/templates' )
        );

        foreach ( $check_dirs as $dir ) {
            if ( file_exists( trailingslashit( $dir ) . $folder .'/comment-form.php' ) ) {
                return trailingslashit( $dir ) . $folder . '/comment-form.php';
            }
        }
        return $template;
    }

    /* add comment column for rating */
    public function comments_column( $columns ) {
        $columns['opalhotel_rating']   = __( 'Rating Room', 'opal-hotel-room-booking' );
        return $columns;
    }

    /* display comment column rating */
    public function comments_custom_column( $column, $comment_id ) {
        $html = '';
        $comment = get_comment( $comment_id );
        $post_id = $comment->comment_post_ID;
        if ( get_post_type( $post_id ) !== OPALHOTEL_CPT_ROOM ) {
            return;
        }
        if ( $column === 'opalhotel_rating' ) {
            $html = array();
            $html[] = '<div class="opalhotel_rating">';
            $rating_items = opalhotel_get_rating_items( $post_id );

            foreach ( $rating_items as $key => $item ) {
                $rating = get_comment_meta( $comment_id, 'opalhotel_rating_' . $item->rating_item_ID, true );
                $html[] =   '<div class="opalhotel_tiptip" data-tip="' . sprintf( __( 'Rated %d star for %s', 'opal-hotel-room-booking' ), $rating, $item->rating_name ) . '">';
                ob_start();
                echo opalhotel_print_rating( $rating );
                $html[] =       ob_get_clean();
                $html[] =   '</div>';
            }
            $html[] =  '</div>';
            $html = implode( '', $html);
        } else {
            $html = __( 'No rating', 'opal-hotel-room-booking' );
        }
        printf( '%s', $html );
    }

}

new OpalHotel_Comment();