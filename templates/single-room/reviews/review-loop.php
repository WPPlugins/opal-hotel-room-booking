<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
$enabled = get_option( 'opalhotel_enabled_rating' );

?>
<li <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">

    <div id="comment-<?php comment_ID(); ?>" class="comment_container">

        <div class="comment-author">
            <?php echo get_avatar( $comment, apply_filters( 'opalhotel_review_author_avatar', '40' ), '' ); ?>
            <strong class="author"><?php comment_author(); ?></strong>
        </div>

        <div class="comment-text">
            <p class="meta">
                <time datetime="<?php echo get_comment_date( 'c' ); ?>">
                    <?php echo get_comment_date( get_option( 'date_format' ) ); ?>
                    <?php esc_html_e( 'at', 'opal-hotel-room-booking' ); ?>
                    <?php echo get_comment_time( get_option( 'time_format' ) ); ?>
                </time>
            </p>
            <?php if ( $enabled ) : ?>
                <?php $rating = intval( get_comment_meta( $comment->comment_ID, 'opalhotel_rating_comfort', true ) ); ?>
                <?php if ( $rating ) : ?>

                    <div class="comment-rating-wrap">
                        <label><?php esc_html_e( 'Comfort', 'opal-hotel-room-booking' ); ?></label>
                        <?php echo opalhotel_print_rating( $rating ); ?>
                    </div>

                <?php endif; ?>

                <?php $rating = intval( get_comment_meta( $comment->comment_ID, 'opalhotel_rating_position', true ) ); ?>
                <?php if ( $rating ) : ?>

                    <div class="comment-rating-wrap">
                        <label><?php esc_html_e( 'Position', 'opal-hotel-room-booking' ); ?></label>
                        <?php echo opalhotel_print_rating( $rating ); ?>
                    </div>

                <?php endif; ?>

                <?php $rating = intval( get_comment_meta( $comment->comment_ID, 'opalhotel_rating_price', true ) ); ?>
                <?php if ( $rating ) : ?>

                    <div class="comment-rating-wrap">
                        <label><?php esc_html_e( 'Price', 'opal-hotel-room-booking' ); ?></label>
                        <?php echo opalhotel_print_rating( $rating ); ?>
                    </div>

                <?php endif; ?>

                <?php $rating = intval( get_comment_meta( $comment->comment_ID, 'opalhotel_rating_quantity', true ) ); ?>
                <?php if ( $rating ) : ?>

                    <div class="comment-rating-wrap">
                        <label><?php esc_html_e( 'Quantity', 'opal-hotel-room-booking' ); ?></label>
                        <?php echo opalhotel_print_rating( $rating ); ?>
                    </div>

                <?php endif; ?>

                <?php if ( ! $comment->comment_approved ) : ?>

                    <p class="meta"><em><?php esc_html_e( 'Your comment is awaiting approval', 'opal-hotel-room-booking' ); ?></em></p>

                <?php endif; ?>
            <?php endif; ?>
            <div class="comment-content"><?php comment_text(); ?></div>
        </div>
    </div>
</li>