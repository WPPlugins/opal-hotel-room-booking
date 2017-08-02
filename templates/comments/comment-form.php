<?php
/**
 * $Desc$
 *
 * @version    $Id$
 * @package    $package$
 * @author     Opal Team <info@wpopal.com >
 * @copyright  Copyright (C) 2014 wpopal.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @website  http://www.wpopal.com
 * @support  http://www.wpopal.com/support/forum.html
 */

defined( 'ABSPATH' ) || exit();

if ( ! comments_open() ) {
    return;
}

global $post;

?>
<div id="opalhotel-reviews" class="opalhotel-comment-advance">
    <h3 class="title">
        <?php esc_html_e( 'Reviews ', 'opalhotel' ); ?>
    </h3>

    <?php if ( opalhotel_get_option( 'enabled_rating' ) ) : $ratings = opalhotel_get_rating_items( $post->ID ); ?>

        <div id="opalhotel-comment-rating">
            <div class="average-rating average-rating-circle">
                <div class="progress-radial progress-<?php echo floatval( opalhotel_get_average_rating( $post->ID ) / 5 * 100 ) ?>">
                    <div class="overlay">
                        <div class="average"><?php echo esc_html( opalhotel_get_average_rating( $post->ID ) ); ?></div>
                        <?php printf( '%s', opalhotel_print_rating( opalhotel_get_average_rating( $post->ID ) ) ) ?>
                        <a href="#commentform"><?php esc_html_e( 'Write A Review', 'opal-hotel-room-booking' ); ?></a>
                    </div>
                </div>
            </div>

            <div class="detailed-rating">
                <div class="rating-general">
                    <?php if ( $ratings ) : foreach ( $ratings as $rating ) : ?>
                        <div class="rating-item">
                            <span class="rating-name"><?php echo esc_html( $rating->rating_name ) ?></span>
                            <?php printf( '%s', opalhotel_print_rating( opalhotel_get_average_rating( $post->ID, $rating->rating_item_ID ) ) ) ?>
                        </div>
                    <?php endforeach; endif; ?>
                </div>
            </div>
        </div>

    <?php endif; ?>
    <div id="opalhotel-comments">

        <?php if ( have_comments() ) : ?>
            <ol class="comment-list">
                <?php wp_list_comments( apply_filters( 'hotel_review_list_args', array( 'callback' => array(
                    'OpalHotel_Comment', 'comment_filter_advance'
                ) ) ) ); ?>
            </ol>

            <?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : ?>
                <nav class="opalhotel-pagination">
                    <?php
                        paginate_comments_links( apply_filters( 'opalhotel_comment_pagination_args', array(
                            'prev_text' => '&larr;',
                            'next_text' => '&rarr;',
                            'type'      => 'list',
                        ) ) );
                    ?>
                </nav>
            <?php endif; ?>

        <?php else : ?>

            <p class="opalhotel-noreviews"><?php esc_html_e( 'There are no reviews yet.', 'opalhotel' ); ?></p>

        <?php endif; ?>
    </div>

    <div id="opalhotel-review-form-wrapper">
        <div id="opalhotel-review-form">
            <?php
                $commenter = wp_get_current_commenter();
                $comment_form = array(
                    'title_reply'           => have_comments() ? __( 'Write A Review', 'opalhotel' ) : __( 'Be the first to review', 'opalhotel' ) . ' &ldquo;' . get_the_title() . '&rdquo;',
                    'title_reply_to'        => __( 'Leave a Reply to %s', 'opalhotel' ),
                    'comment_notes_before'  => '',
                    'comment_notes_after'   => '',
                    'fields'                => array(),
                    'label_submit'          => __( 'Leave A Review', 'opalhotel' ),
                    'logged_in_as'          => '',
                    'comment_field'         => '',
                    'class_form'            => 'opalhotel-comment-form'
                );

                if ( opalhotel_get_option( 'enabled_rating' ) ) {
                    $comment_form['comment_field'] .= '<div class="opalhotel-rating-wrapper">';
                    /* Position */
                    $rating_items = opalhotel_get_rating_items( $post->ID );
                    foreach( $rating_items as $key => $item ){
                        $comment_form['comment_field'] .= '<div class="opalhotel-star" data-type="'.esc_attr( $item->rating_item_ID ).'"><p class="comment-form-rating">';
                        $comment_form['comment_field'] .= '<label for="rating-'.esc_attr( $item->rating_item_ID ).'">' . esc_html( $item->rating_name ) .'</label></p></div>';
                    }
                    $comment_form['comment_field'] .= '</div>';

                }
                $comment_form['comment_field'] .= '<div class="inner">';
                $comment_form['comment_field'] .= '<div class="comment-form-title">' .
                                '<p><input id="title" class="form-control" name="title" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30" aria-required="true" placeholder="'.esc_attr( 'Review Title', 'opalhotel' ).'" /><p></div>';
                if ( ! is_user_logged_in() ) {
                    $comment_form['comment_field'] .= '<div class="comment-form-author">' .
                                '<p><input id="author" class="form-control" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30" aria-required="true" placeholder="'.esc_attr( 'Name', 'opalhotel' ).'" /><p></div>';
                    $comment_form['comment_field'] .= '<div class="comment-form-email">' .
                                '<p><input id="email"  class="form-control" name="email" type="text" value="' . esc_attr( $commenter['comment_author_email'] ) . '" size="30" aria-required="true" placeholder="'.esc_attr( 'Email', 'opalhotel' ).'" /><p></div>';
                }
                $comment_form['comment_field'] .= '<div class="comment-form-comment"><textarea id="comment" class="form-control" name="comment" cols="45" rows="8" aria-required="true" placeholder="'.esc_attr( 'Say something...', 'opalhotel' ).'"></textarea><p></div>';
                $comment_form['comment_field'] .= '</div>';

                comment_form( apply_filters( 'hotel_review_comment_form_args', $comment_form ) );
            ?>
        </div>
    </div>

</div>

<?php
    /**
     * opalhotel_single_reservation_form - 5
     **/
    do_action( 'opalhotel_before_after_hotel_reviews' );
?>