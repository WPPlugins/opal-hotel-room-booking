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

class OpalHotel_Admin_MetaBoxes {

	/**
	 * Is meta boxes saved once?
	 *
	 * @var boolean
	 */
	private static $saved_meta_boxes = false;

	/**
	 * Meta box error messages.
	 *
	 * @var array
	 */
	public static $meta_box_errors  = array();

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 30 );
		add_action( 'save_post', array( $this, 'save_meta_boxes' ), 1, 2 );
		add_action( 'cmb2_admin_init', array( $this, 'hotel_discount_meta' ), 11 );
		add_action( 'cmb2_admin_init', array( $this, 'hotel_data' ), 12 );
		add_action( 'cmb2_admin_init', array( $this, 'hotels_think_to_do' ), 12 );
		/**
		 * Save Order Meta Boxes.
		 *
		 * In order:
		 *      Save the order items.
		 *      Save the order totals.
		 *      Save the order downloads.
		 *      Save order data - also updates status and sends out admin emails if needed. Last to show latest data.
		 *      Save actions - sends out other emails. Last to show latest data.
		 */
		// add_action( 'opalhotel_process_opalhotel_room_meta', 'OpalHotel_MetaBox_Booking::save', 10, 2 );
		add_action( 'opalhotel_process_opalhotel_room_meta', 'OpalHotel_MetaBox_Room_Gallery::save', 10, 2 );
		add_action( 'opalhotel_process_opalhotel_hotel_meta', 'OpalHotel_MetaBox_Room_Gallery::save', 10, 2 );
		add_action( 'opalhotel_process_opalhotel_room_meta', 'OpalHotel_MetaBox_Room_Data::save', 10, 2 );
		add_action( 'opalhotel_process_opalhotel_coupon_meta', 'OpalHotel_MetaBox_Coupon_Data::save', 10, 2 );
		add_action( 'opalhotel_process_opalhotel_package_meta', 'OpalHotel_MetaBox_Package_Data::save', 10, 2 );
		add_action( 'opalhotel_process_booking_order_meta', 'OpalHotel_MetaBox_Booking_Data::save', 10, 2 );
		add_action( 'opalhotel_process_booking_order_meta', 'OpalHotel_MetaBox_Booking_Item::save', 10, 2 );
		add_action( 'opalhotel_process_booking_order_meta', 'OpalHotel_MetaBox_Booking_Action::save', 10, 2 );
		add_action( 'opalhotel_process_' . OPALHOTEL_CPT_ANT . '_meta', 'OpalHotel_MetaBox_Amenity_Data::save', 10, 2 );
		add_action( 'opalhotel_process_' . OPALHOTEL_CPT_ANT . '_meta', 'OpalHotel_MetaBox_Amenity_Builder::save', 10, 2 );

		// Error handling (for showing errors from meta boxes on next page load)
		add_action( 'admin_notices', array( $this, 'output_errors' ) );
		add_action( 'shutdown', array( $this, 'save_errors' ) );
	}

	/**
	 * Add an error message.
	 * @param string $text
	 */
	public static function add_error( $text ) {
		self::$meta_box_errors[] = $text;
	}

	/**
	 * Save errors to an option.
	 */
	public function save_errors() {
		update_option( 'opalhotel_meta_box_errors', self::$meta_box_errors );
	}

	/**
	 * Show any stored error messages.
	 */
	public function output_errors() {
		$errors = maybe_unserialize( get_option( 'opalhotel_meta_box_errors' ) );

		if ( ! empty( $errors ) ) {

			echo '<div id="opalhotel_errors" class="error notice is-dismissible">';

			foreach ( $errors as $error ) {
				echo '<p>' . wp_kses_post( $error ) . '</p>';
			}

			echo '</div>';

			// Clear
			delete_option( 'opalhotel_meta_box_errors' );
		}
	}

	/**
	 * Add Opal Meta boxes.
	 */
	public function add_meta_boxes() {

		remove_meta_box( 'slugdiv', 'opalhotel_coupon', 'normal' );
		remove_meta_box( 'slugdiv', 'opalhotel_package', 'normal' );
		remove_meta_box( 'submitdiv', OPALHOTEL_CPT_BOOKING, 'side' );
		remove_meta_box( 'mymetabox_revslider_0', OPALHOTEL_CPT_ANT, 'normal' );

		/* room pricing */
		add_meta_box(
			'opalhotel-room-pricing',
			__( 'Room Pricing', 'opal-hotel-room-booking' ),
			'OpalHotel_MetaBox_Room_Pricing::render',
			OPALHOTEL_CPT_ROOM,
			'advanced',
			'low'
		);

		/* room data */
		add_meta_box(
			'opalhotel-room-setting',
			__( 'Room Data', 'opal-hotel-room-booking' ),
			'OpalHotel_MetaBox_Room_Data::render',
			OPALHOTEL_CPT_ROOM,
			'normal',
			'high'
		);

		/* galleries room */
		add_meta_box(
			'opalhotel-room-images',
			__( 'Gallery', 'opal-hotel-room-booking' ),
			'OpalHotel_MetaBox_Room_Gallery::render',
			array( OPALHOTEL_CPT_ROOM, OPALHOTEL_CPT_HOTEL ),
			'side',
			'high'
		);

		/* coupon data */
		add_meta_box(
			'opalhotel-coupon-data',
			__( 'Coupon Data', 'opal-hotel-room-booking' ),
			'OpalHotel_MetaBox_Coupon_Data::render',
			'opalhotel_coupon',
			'advanced',
			'high'
		);

		/* package data */
		add_meta_box(
			'opalhotel-package-data',
			__( 'Package Data', 'opal-hotel-room-booking' ),
			'OpalHotel_MetaBox_Package_Data::render',
			'opalhotel_package',
			'advanced',
			'high'
		);

		/* booking data */
		add_meta_box(
			'opalhotel-booking-data',
			__( 'Book Data', 'opal-hotel-room-booking' ),
			'OpalHotel_MetaBox_Booking_Data::render',
			OPALHOTEL_CPT_BOOKING,
			'advanced',
			'high'
		);

		/* booking items */
		add_meta_box(
			'opalhotel-booking-item',
			__( 'Book Items', 'opal-hotel-room-booking' ),
			'OpalHotel_MetaBox_Booking_Item::render',
			OPALHOTEL_CPT_BOOKING,
			'advanced',
			'high'
		);

		/* booking action */
		add_meta_box(
			'opalhotel-booking-action',
			__( 'Book Action', 'opal-hotel-room-booking' ),
			'OpalHotel_MetaBox_Booking_Action::render',
			OPALHOTEL_CPT_BOOKING,
			'side',
			'high'
		);

		/* amenity fields */
		add_meta_box(
			'opalhotel-amenity-fields',
			__( 'Amenity Data', 'opal-hotel-room-booking' ),
			'OpalHotel_MetaBox_Amenity_Builder::render',
			'opalhotel_amenities',
			'advanced',
			'high'
		);

		/* amenity action */
		add_meta_box(
			'opalhotel-amenity-data',
			__( 'Amenity Data', 'opal-hotel-room-booking' ),
			'OpalHotel_MetaBox_Amenity_Data::render',
			'opalhotel_amenities',
			'advanced',
			'high'
		);
	}

	public function hotel_discount_meta() {
		$prefix = '_';
		$cmb = new_cmb2_box( array(
				'id'			=> 'opalhotel_hotel_discount_data',
	            'title'		 	=> __( 'Hotel Discount', 'opal-hotel-room-booking' ),
	            'object_types' 	=> array( OPALHOTEL_CPT_HOTEL ), // post type
	            'context' 		=> 'normal', //  'normal', 'advanced', or 'side'
	            'priority' 		=> 'high', //  'high', 'core', 'default' or 'low'
	            'show_names' 	=> true, // Show field names on the left
			) );

		$group_field_id = $cmb->add_field( array(
				'id'          => '_discount_group',
				'type'        => 'group',
				'description' => __( 'Generates discount plans', 'opal-hotel-room-booking' ),
				// 'repeatable'  => false, // use false if you want non-repeatable group
				'options'     => array(
					'group_title'   => __( 'Line {#}', 'opal-hotel-room-booking' ), // since version 1.1.4, {#} gets replaced by row number
					'add_button'    => __( 'Add Another Line', 'opal-hotel-room-booking' ),
					'remove_button' => __( 'Remove Line', 'opal-hotel-room-booking' ),
					'sortable'      => true, // beta
					// 'closed'     => true, // true to have the groups closed by default
				),
			) );

		// Id's for group's fields only need to be unique for the group. Prefix is not needed.
		$cmb->add_group_field( $group_field_id, array(
				'name' => __( 'Start Date', 'opal-hotel-room-booking' ),
				'id'   => 'start',
				'type' => 'text_date',
				// 'repeatable' => true, // Repeatable fields are supported w/in repeatable groups (for most types)
				'date_format'	=> 'Y-m-d'
			) );

		// Id's for group's fields only need to be unique for the group. Prefix is not needed.
		$cmb->add_group_field( $group_field_id, array(
				'name' => __( 'End Date', 'opal-hotel-room-booking' ),
				'id'   => 'end',
				'type' => 'text_date',
				// 'repeatable' => true, // Repeatable fields are supported w/in repeatable groups (for most types)
				'date_format'	=> 'Y-m-d'
			) );

		// Id's for group's fields only need to be unique for the group. Prefix is not needed.
		// $cmb->add_group_field( $group_field_id, array(
		// 		'name' => __( 'Discount Type', 'opal-hotel-room-booking' ),
		// 		'id'   => 'type',
		// 		'type' => 'radio_inline',
		// 		// 'repeatable' => true, // Repeatable fields are supported w/in repeatable groups (for most types)
		// 		'options'	=> array(
		// 				'percent'	=> __( 'Percent', 'opal-hotel-room-booking' ),
		// 				'amount'	=> __( 'Amount', 'opal-hotel-room-booking' )
		// 			),
		// 		'default'	=> 'percent'
		// 	) );

		// Id's for group's fields only need to be unique for the group. Prefix is not needed.
		$cmb->add_group_field( $group_field_id, array(
				'name' => __( 'Discount Percent', 'opal-hotel-room-booking' ),
				'id'   => 'value',
				'type' => 'text',
				'attributes' => array(
					'type' => 'number',
					'pattern' => '\d*',
					'min'	=> 0,
					'max'	=> 100
				),
			) );
	}

	public function hotel_data() {
		$prefix = '_';
        $cmb = new_cmb2_box( array(
	            'id' 			=> 'opalhotel_map',
	            'title'		 	=> __( 'Hotel Data', 'opal-hotel-room-booking' ),
	            'object_types' 	=> array( OPALHOTEL_CPT_HOTEL ), // post type
	            'context' 		=> 'normal', //  'normal', 'advanced', or 'side'
	            'priority' 		=> 'high', //  'high', 'core', 'default' or 'low'
	            'show_names' 	=> true, // Show field names on the left
            ) );

        $cmb->add_field( array(
            'name' 	=> __( 'Featured', 'opal-hotel-room-booking' ),
            'id' 	=> $prefix . 'featured',
            'type' 	=> 'radio_inline',
            'options'	=> array(
            		1	=> __( 'Yes', 'opal-hotel-room-booking' ),
            		0	=> __( 'No', 'opal-hotel-room-booking' )
            	)
        ) );

        $cmb->add_field( array(
            'name' 	=> __( 'Recommended', 'opal-hotel-room-booking' ),
            'id' 	=> $prefix . 'recommended',
            'type' 	=> 'radio_inline',
            'options'	=> array(
            		1	=> __( 'Yes', 'opal-hotel-room-booking' ),
            		0	=> __( 'No', 'opal-hotel-room-booking' )
            	)
        ) );

        $cmb->add_field( array(
            'name' 	=> __( 'Type', 'opal-hotel-room-booking' ),
            'desc' 	=> __( 'Hotel or Motel?', 'opal-hotel-room-booking' ),
            'id' 	=> $prefix . 'type',
            'type' 	=> 'select',
            'options'	=> opalhotel_hotel_types()
        ) );

        $cmb->add_field( array(
            'name' => __( 'Star', 'opal-hotel-room-booking' ),
            'desc' => __( 'Star', 'opal-hotel-room-booking' ),
            'id' 	=> $prefix . 'star',
            'type' => 'select',
            'options'	=> apply_filters( 'opalhotel_hotel_star', array(
            		'1'		=> __( '1', 'opal-hotel-room-booking' ),
            		'2'		=> __( '2', 'opal-hotel-room-booking' ),
            		'3'		=> __( '3', 'opal-hotel-room-booking' ),
            		'4'		=> __( '4', 'opal-hotel-room-booking' ),
            		'5'		=> __( '5', 'opal-hotel-room-booking' )
            	) )
        ) );

        $cmb->add_field( array(
            'name' 	=> __( 'Checkin Time', 'opal-hotel-room-booking' ),
            'desc' 	=> __( 'Time start', 'opal-hotel-room-booking' ),
            'id' 	=> $prefix . 'checkin_time',
            'type' 	=> 'opalhotel_time'
        ) );

        $cmb->add_field( array(
            'name' 	=> __( 'Checkout Time', 'opal-hotel-room-booking' ),
            'desc' 	=> __( 'Time start', 'opal-hotel-room-booking' ),
            'id' 	=> $prefix . 'checkout_time',
            'type' 	=> 'opalhotel_time'
        ) );

        $cmb->add_field( array(
            'name' 	=> __( 'Layout', 'opal-hotel-room-booking' ),
            'desc' 	=> __( 'Select Single Layout', 'opal-hotel-room-booking' ),
            'id' 	=> $prefix . 'opalhotel_layout',
            'type' 	=> 'select',
            'options'	=> opalhotel_single_hotel_layouts( true )
        ) );

        $cmb->add_field( array(
            'name' 	=> __( 'Address', 'opal-hotel-room-booking' ),
            'desc' 	=> __( 'Hotel\'s address.', 'opal-hotel-room-booking' ),
            'id' 	=> $prefix . 'address',
            'type' 	=> 'text'
        ) );

        $cmb->add_field( array(
            'name' 	=> __( 'Phone', 'opal-hotel-room-booking' ),
            'desc' 	=> __( 'Hotel\'s phone.', 'opal-hotel-room-booking' ),
            'id'	=> $prefix . 'phone',
            'type' 	=> 'text'
        ) );

     	$cmb->add_field( array(
            'name' 	=> __( 'Website', 'opal-hotel-room-booking' ),
            'desc' 	=> __( 'Website address.', 'opal-hotel-room-booking' ),
            'id' 	=> $prefix . 'website',
            'type' 	=> 'text'
        ) );

        $cmb->add_field( array(
            'name' 	=> __( 'Video', 'opal-hotel-room-booking' ),
            'desc' 	=> __( 'Youtube video.', 'opal-hotel-room-booking' ),
            'id' 	=> $prefix . 'video',
            'type' 	=> 'text'
        ) );

        $cmb->add_field( array(
            'name' 	=> __( 'Image', 'opal-hotel-room-booking' ),
            'desc' 	=> __( 'Hotel Image.', 'opal-hotel-room-booking' ),
            'id' 	=> $prefix . 'image',
            'type' 	=> 'file'
        ) );

        $cmb->add_field( array(
            'name' 	=> __( 'Amenities', 'opal-hotel-room-booking' ),
            'desc' 	=> __( 'Amenities.', 'opal-hotel-room-booking' ),
            'id' 	=> $prefix . 'amenities',
            'type' 	=> 'multicheck_inline',
            'options'	=> opalhotel_get_amenities()
        ) );

        $cmb->add_field( array(
            'name' 	=> __( 'Map', 'opal-hotel-room-booking' ),
            'desc' 	=> __( 'Drag the marker to set the exact location', 'opal-hotel-room-booking' ),
            'id' 	=> $prefix . 'map',
            'type' 	=> 'opalhotel_map'
        ) );

        do_action( 'opalhotel_hotel_map_meta_data', $cmb, $prefix );
	}

	public function hotels_think_to_do() {
		$prefix = '_think_to_do_';
        $cmb = new_cmb2_box( array(
            'id' 			=> 'opalhotel_hotel_think_to_do',
            'title'		 	=> __( 'Think To Do', 'opal-hotel-room-booking' ),
            'object_types' 	=> array( OPALHOTEL_CPT_HOTEL ), // post type
            'context' 		=> 'normal', //  'normal', 'advanced', or 'side'
            'priority' 		=> 'high', //  'high', 'core', 'default' or 'low'
            'show_names' 	=> true, // Show field names on the left
                ) );

        $cmb->add_field( array(
            'name'       	=> __( 'Description', 'opal-hotel-room-booking' ),
            'desc'       	=> __( 'Reviews', 'opal-hotel-room-booking' ),
            'id'         	=> $prefix . 'description',
			'type' 			=> 'textarea_small'
        ) );

        // $group_field_id is the field id string, so in this case: $prefix . 'demo'
        $group_id = $cmb->add_field( array(
            'id'                => $prefix . 'section',
            'type'              => 'group',
            'description'       => __( 'Think to do', 'opal-hotel-room-booking' ),
            'options'           => array(
                'group_title'   => 'Section {#}',
                'add_button'    => 'Add Another Section',
                'remove_button' => 'Remove Section',
                'sortable'      => true
            )
        ) );

        $cmb->add_group_field( $group_id, array(
            'id'            => $prefix . 'image',
            'name'          => __( 'Image', 'opal-hotel-room-booking' ),
            'type'          => 'file',
            'desc'          => __( 'Image', 'opal-hotel-room-booking' )
        ) );
        $cmb->add_group_field( $group_id, array(
            'id'            => $prefix . 'title',
            'name'          => __( 'Title', 'opal-hotel-room-booking' ),
            'type'          => 'text'
        ) );
        $cmb->add_group_field( $group_id, array(
            'id'            => $prefix . 'short_description',
            'name'          => __( 'Short Description', 'opal-hotel-room-booking' ),
            'type'          => 'textarea_small'
        ) );
	}

	/**
	 * Check if we're saving, the trigger an action based on the post type.
	 *
	 * @param  int $post_id
	 * @param  object $post
	 */
	public function save_meta_boxes( $post_id, $post ) {
		// $post_id and $post are required
		if ( empty( $post_id ) || empty( $post ) || self::$saved_meta_boxes ) {
			return;
		}

		// Dont' save meta boxes for revisions or autosaves
		if ( defined( 'DOING_AUTOSAVE' ) || is_int( wp_is_post_revision( $post ) ) ) {
			return;
		}

		// Check the nonce
		if ( empty( $_POST['opalhotel_meta_nonce'] ) || ! wp_verify_nonce( $_POST['opalhotel_meta_nonce'], 'opalhotel_save_data' ) ) {
			return;
		}

		// Check the post being saved == the $post_id to prevent triggering this call for other save_post events
		if ( empty( $_POST['post_ID'] ) || $_POST['post_ID'] != $post_id ) {
			return;
		}

		// Check user has permission to edit
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// We need this save event to run once to avoid potential endless loops. This would have been perfect:
		self::$saved_meta_boxes = true;

		// Check the post type
		if ( in_array( $post->post_type, array( OPALHOTEL_CPT_BOOKING ) ) ) {
			do_action( 'opalhotel_process_booking_order_meta', $post_id, $post );
		} else {
			do_action( 'opalhotel_process_' . $post->post_type . '_meta', $post_id, $post );
		}
	}

}

new OpalHotel_Admin_MetaBoxes();
