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

class OpalHotel_Widget_Rooms_Lastest_Deals extends WP_Widget {

	/* widget base ID */
	public $id = 'opalhotel-rooms-lastest-deals';

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			$this->id,
			__( 'OpalHotel: Rooms Lastest Deals', 'opal-hotel-room-booking' ),
			array( 'description' => __( 'Rooms Lastest Deals widget', 'opal-hotel-room-booking' ), ) // Args
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

		OpalHotel_Shortcodes::rooms_lastest_deals( $instance );

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
		$columns = ! empty( $instance['columns'] ) ? absint( $instance['columns'] ) : 3;
		$layout = ! empty( $instance['layout'] ) ? sanitize_text_field( $instance['layout'] ) : '';
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php esc_html_e( 'Title:' ); ?></label>
			<input class="widefat opalhotel-widget-input" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'posts_per_page' ); ?>"><?php esc_html_e( 'Number:' ); ?></label>
			<input class="widefat opalhotel-widget-input" id="<?php echo $this->get_field_id( 'posts_per_page' ); ?>" name="<?php echo $this->get_field_name( 'posts_per_page' ); ?>" type="number" step="1" min="1" value="<?php echo esc_attr( $posts_per_page ); ?>">
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'columns' ); ?>"><?php esc_html_e( 'Columns:' ); ?></label>
			<input class="widefat opalhotel-widget-input" id="<?php echo $this->get_field_id( 'columns' ); ?>" name="<?php echo $this->get_field_name( 'columns' ); ?>" type="number" step="1" min="1" value="<?php echo esc_attr( $columns ); ?>">
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'layout' ); ?>"><?php esc_html_e( 'Layout:' ); ?></label>
			<select class="widefat opalhotel-widget-input" id="<?php echo $this->get_field_id( 'layout' ); ?>" name="<?php echo $this->get_field_name( 'layout' ); ?>">
				<option value="" <?php selected( $layout, '' ) ?>><?php esc_html_e( 'Global', 'opal-hotel-room-booking' ); ?></option>
				<option value="grid" <?php selected( $layout, 'grid' ) ?>><?php esc_html_e( 'Grid', 'opal-hotel-room-booking' ); ?></option>
				<option value="list" <?php selected( $layout, 'list' ) ?>><?php esc_html_e( 'List', 'opal-hotel-room-booking' ); ?></option>
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
		$instance['posts_per_page'] = ! empty( $new_instance['posts_per_page'] ) ? absint( $new_instance['posts_per_page'] ) : 5;
		$instance['columns'] = ! empty( $new_instance['columns'] ) ? absint( $new_instance['columns'] ) : 5;
		$instance['layout'] = ! empty( $new_instance['layout'] ) ? sanitize_text_field( $new_instance['layout'] ) : '';
		return $instance;
	}
}