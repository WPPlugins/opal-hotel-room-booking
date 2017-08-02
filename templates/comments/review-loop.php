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

$enabled = opalhotel_get_option( 'enabled_rating' );

?>
<li <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">

    <div id="comment-<?php comment_ID(); ?>" class="comment_container">

        <div class="comment-author">
            <?php echo get_avatar( $comment, apply_filters( 'opalhotel_review_author_avatar', '70' ), '' ); ?>
            <strong class="author"><?php comment_author(); ?></strong>
        </div>

        <div class="comment-text">
            <?php
                $rating = floatval( opalhotel_get_average_rating_comment( $comment->comment_ID ) );
                $title = get_comment_meta( $comment->comment_ID, 'opalhotel_comment_title', true );
            ?>
            <?php if ( $enabled && $rating ) : ?>

                <div class="comment-rating-wrap">
                    <label class="title">
                        <?php printf( '%s', $title? $title : opalhotel_get_comment_title( $rating ) ) ?>
                        <?php printf( '%s', opalhotel_print_rating( $rating ) ) ?>
                    </label>
                    <span class="author"><?php comment_author(); ?></span>
                    <span class="date"><?php comment_date( get_option( 'date_format' ), $comment->comment_ID ); ?> </span>
                </div>

            <?php endif; ?>

            <?php if ( ! $comment->comment_approved ) : ?>

                <p class="meta"><em><?php esc_html_e( 'Your comment is awaiting approval', 'opalhotel' ); ?></em></p>

            <?php endif; ?>
            <div class="comment-content">
                <?php comment_excerpt(); ?>
            </div>
        </div>
    </div>
</li>
