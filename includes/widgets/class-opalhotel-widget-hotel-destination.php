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

class OpalHotel_Widget_Hotel_Destination extends WP_Widget {

	/* widget base ID */
	public $id = 'opalhotel-hotel-destination';

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			$this->id,
			__( 'OpalHotel: Hotel\'s destination', 'opal-hotel-room-booking' ),
			array( 'description' => __( 'Hotel\s destination', 'opal-hotel-room-booking' ), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		echo $args['before_widget'];
		if ( $instance['title'] ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
		}

		OpalHotel_Shortcodes::hotel_destination( $instance );

		echo $args['after_widget'];
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
			$title = ! empty( $instance['title'] ) ? $instance['title'] : '';
			$posts_per_page = ! empty( $instance['posts_per_page'] ) ? absint( $instance['posts_per_page'] ) : 5;
		?>
			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php esc_html_e( 'Title:', 'opal-hotel-room-booking' ); ?></label>
				<input class="widefat opalhotel-widget-input" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'posts_per_page' ); ?>"><?php esc_html_e( 'Numbers:', 'opal-hotel-room-booking' ); ?></label>
				<input class="widefat opalhotel-widget-input" id="<?php echo $this->get_field_id( 'posts_per_page' ); ?>" name="<?php echo $this->get_field_name( 'posts_per_page' ); ?>" type="number" step="1" min="0" max="6" value="<?php echo esc_attr( $posts_per_page ); ?>" />
			</p>
			<p>
				<?php $sizes = get_intermediate_image_sizes(); global $_wp_additional_image_sizes; ?>
				<label for="<?php echo $this->get_field_id( 'image_size' ); ?>"><?php esc_html_e( 'Image Size:', 'opal-hotel-room-booking' ); ?></label>
				<select name="<?php echo $this->get_field_name( 'image_size' ); ?>" id="<?php echo $this->get_field_id( 'image_size' ); ?>">
					<option value="full"><?php esc_html_e( 'Full', 'opal-hotel-room-booking' ); ?></option>
					<?php foreach ( $sizes as $size ) : ?>
						<option value="<?php echo esc_attr( $size ) ?>">
							<?php
								if ( in_array( $size, array('thumbnail', 'medium', 'medium_large', 'large') ) ) {
									$width  = get_option( "{$size}_size_w" );
									$height = get_option( "{$size}_size_h" );
								} elseif ( isset( $_wp_additional_image_sizes[ $size ] ) ) {
									$width = $_wp_additional_image_sizes[ $size ]['width'];
									$height = $_wp_additional_image_sizes[ $size ]['height'];
								}
								echo esc_html( $width . 'x' . $height );
							?>
						</option>
					<?php endforeach; ?>
				</select>
			</p>
		<?php
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['posts_per_page'] = ( ! empty( $new_instance['posts_per_page'] ) ) ? absint( $new_instance['posts_per_page'] ) : 0;
		$instance['image_size']	= ! empty( $instance['image_size'] ) ? sanitize_text_field( $instance['image_size'] ) : 'thumbnail';
		return $instance;
	}
}