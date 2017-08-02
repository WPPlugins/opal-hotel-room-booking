<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit();
}

?>

<div class="wrap">
    <h2><?php esc_html_e( 'Add New Group Rating', 'opal-hotel-room-booking' ); ?></h2>

    <?php opalhotel_print_admin_notices() ?>

    <form method="post" id="add-new-rating-item-form">
        <table class="form-table">
            <tbody>
            <tr valign="top">
                <th scope="row"><?php esc_html_e( 'Name', 'opal-hotel-room-booking' ); ?></th>
                <td>
                    <input id="name" name="name" type="text" maxlength="255" cols="100" placeholder="<?php esc_attr_e( 'Enter a name...', 'opal-hotel-room-booking' ); ?>" required class="regular-text" value="" />
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php esc_html_e( 'Description', 'opal-hotel-room-booking' ); ?></th>
                <td>
                    <input id="desciption" name="description" type="text" maxlength="255" cols="100" placeholder="<?php esc_html_e( 'Enter a description...', 'opal-hotel-room-booking' ); ?>" required class="regular-text" value="" />
                </td>
            </tr>
            </tbody>
        </table>
        <p><input id="add-new-rating-item-btn" class="button button-primary" value="<?php esc_html_e( 'Save Changes', 'opal-hotel-room-booking' ); ?>" type="submit" /></p>
        <?php wp_nonce_field( 'opalhotel_submit_rating', 'opalhotel-submit-rating-nonce' ); ?>
    </form>
</div>