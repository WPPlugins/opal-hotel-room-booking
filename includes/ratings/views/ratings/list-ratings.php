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

?>
<div class="wrap">
    <h2>
        <?php esc_html_e( 'Rating group', 'opal-hotel-room-booking' ); ?>
        <a class="add-new-h2" href="<?php echo admin_url( 'admin.php?page=opalhotel-rating-options&rating-id=' ); ?>"><?php esc_html_e( 'Add New', 'opal-hotel-room-booking' ); ?></a>
    </h2>

    <?php opalhotel_print_admin_notices() ?>

    <form method="post">
        <?php
        $table = new OpalHotel_Table_Ratings();
        $table->prepare_items();
        $table->display();
        ?>
    </form>

</div>