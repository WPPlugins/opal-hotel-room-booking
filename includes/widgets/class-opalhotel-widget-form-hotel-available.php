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
	exit();
}

class OpalHotel_Widget_Form_Hotel_Available extends WP_Widget {

	/* widget base ID */
	public $id = 'opalhotel-form-hotel-available';

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			$this->id,
			__( 'OpalHotel: Check Hotel Available', 'opal-hotel-room-booking' ),
			array( 'description' => __( 'Hotel Available Form', 'opal-hotel-room-booking' ), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		echo $args['before_widget'];

		if ( ! empty( $instance['layout'] ) ) {
			echo '<div class="'.esc_attr( $instance['layout'] ).'">';
		}
		if ( $instance['title'] ) {
			echo $args['before_title'] . '<span class="'. esc_attr( ! empty( $instance['layout'] ) ? $instance['layout'] : '' ) .'">' . apply_filters( 'widget_title', $instance['title'], $instance, $this->id ) . '</span>' . $args['after_title'];
		}

		OpalHotel_Shortcodes::form_hotel_available( $instance );

		if ( ! empty( $instance['layout'] ) ) {
			echo '</div>';
		}

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
		$title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Reservation', 'opal-hotel-room-booking' );
		$layout = ! empty( $instance['layout'] ) ? $instance['layout'] : 'vertical';
		?>
			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php esc_html_e( 'Title:', 'opal-hotel-room-booking' ); ?></label>
				<input class="widefat opalhotel-widget-input" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'layout' ); ?>"><?php esc_html_e( 'Layout:', 'opal-hotel-room-booking' ); ?></label>
				<select class="widefat opalhotel-widget-input" id="<?php echo $this->get_field_id( 'layout' ); ?>" name="<?php echo $this->get_field_name( 'layout' ); ?>">
					<option value="overlap" <?php selected( $layout, 'overlap' ) ?>><?php esc_html_e( 'OverLap', 'opal-hotel-room-booking' ); ?></option>
					<option value="vertical" <?php selected( $layout, 'vertical' ) ?>><?php esc_html_e( 'Vertical', 'opal-hotel-room-booking' ); ?></option>
					<option value="middle" <?php selected( $layout, 'middle' ) ?>><?php esc_html_e( 'Middle', 'opal-hotel-room-booking' ); ?></option>
					<option value="horizontal" <?php selected( $layout, 'horizontal' ) ?>><?php esc_html_e( 'Horizontal', 'opal-hotel-room-booking' ); ?></option>
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
		$instance['layout'] = ( ! empty( $new_instance['layout'] ) ) ? sanitize_text_field( $new_instance['layout'] ) : 'vertical';

		return $instance;
	}
}