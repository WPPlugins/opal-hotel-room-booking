<?php
/**
 * The template for displaying room content within single
 *
 * This template can be overridden by copying it to yourtheme/opalhotel/single-room/reiews.php.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( ! comments_open() ) {
    return;
}

global $opalhotel_room;

?>
<div id="opalhotel-reviews">

    <div id="opalhotel-comments">
        <h4 class="room-single-title">
            <?php
                if ( get_option( 'opalhotel_enabled_rating' ) && ( $count = $opalhotel_room->get_review_count() ) )
                    printf( _n( '%s review for %s', '%s reviews for %s', $count, 'opal-hotel-room-booking' ), $count, get_the_title() );
                else
                   esc_html_e( 'Clients\' Reviews ', 'opal-hotel-room-booking' );
            ?>
        </h4>

        <?php if ( have_comments() ) : ?>
            <ol class="comment-list">
                <?php wp_list_comments( apply_filters( 'opalhotel_room_review_list_args', array( 'callback' => array(
                    'OpalHotel_Comment', 'comment_filter'
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

            <p class="opalhotel-noreviews"><?php esc_html_e( 'There are no reviews yet.', 'opal-hotel-room-booking' ); ?></p>

        <?php endif; ?>
    </div>

    <div id="opalhotel-review-form-wrapper">
        <div id="opalhotel-review-form">
            <?php
                $commenter = wp_get_current_commenter();
                $comment_form = array(
                    'title_reply'          => have_comments() ? __( 'Add a review', 'opal-hotel-room-booking' ) : __( 'Be the first to review', 'opal-hotel-room-booking' ) . ' &ldquo;' . get_the_title() . '&rdquo;',
                    'title_reply_to'       => __( 'Leave a Reply to %s', 'opal-hotel-room-booking' ),
                    'comment_notes_before' => '',
                    'comment_notes_after'  => '',
                    'fields'               => array(
                        'author' => '<p class="comment-form-author">' . '<label for="author">' . __( 'Name', 'opal-hotel-room-booking' ) . ' <span class="required">*</span></label> ' .
                            '<input id="author" class="form-control" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30" aria-required="true" /></p>',
                        'email'  => '<p class="comment-form-email"><label for="email">' . __( 'Email', 'opal-hotel-room-booking' ) . ' <span class="required">*</span></label> ' .
                            '<input id="email"  class="form-control" name="email" type="text" value="' . esc_attr( $commenter['comment_author_email'] ) . '" size="30" aria-required="true" /></p>',
                    ),
                    'label_submit'  => __( 'Submit', 'opal-hotel-room-booking' ),
                    'logged_in_as'  => '',
                    'comment_field' => '',
                    'class_form'    => 'opalhotel-comment-form'
                );

                if ( get_option( 'opalhotel_enabled_rating' ) ) {
                    $comment_form['comment_field'] = '<div class="opalhotel-rating-warpper">';
                    /* Comfort */
                    $comment_form['comment_field'] .= '<div class="opalhotel-star" data-type="comfort"><p class="comment-form-rating">';
                    $comment_form['comment_field'] .= '<label for="rating-comfort">' . __( 'Comfort', 'opal-hotel-room-booking' ) .'</label>
                    </p></div>';

                    /* Position */
                    $comment_form['comment_field'] .= '<div class="opalhotel-star" data-type="position"><p class="comment-form-rating">';
                    $comment_form['comment_field'] .= '<label for="rating-position">' . __( 'Position', 'opal-hotel-room-booking' ) .'</label>
                    </p></div>';

                    /* Price */
                    $comment_form['comment_field'] .= '<div class="opalhotel-star" data-type="price"><p class="comment-form-rating">';
                    $comment_form['comment_field'] .= '<label for="rating-price">' . __( 'Price', 'opal-hotel-room-booking' ) .'</label>
                    </p></div>';

                    /* Quantity */
                    $comment_form['comment_field'] .= '<div class="opalhotel-star" data-type="quantity"><p class="comment-form-rating">';
                    $comment_form['comment_field'] .= '<label for="rating-quantity">' . __( 'Quantity', 'opal-hotel-room-booking' ) .'</label>
                    </p></div>';
                    $comment_form['comment_field'] .= '</div>';
                }

                $comment_form['comment_field'] .= '<div class="comment-form-comment"><label for="comment">' . __( 'Your Review', 'opal-hotel-room-booking' ) . '</label><textarea id="comment" class="form-control" name="comment" cols="45" rows="8" aria-required="true"></textarea></div>';
                comment_form( apply_filters( 'opalhotel_product_review_comment_form_args', $comment_form ) );
            ?>
        </div>
    </div>

</div>

<?php
    /**
     * opalhotel_single_reservation_form - 5
     **/
    do_action( 'opalhotel_before_after_room_reviews' );
?>