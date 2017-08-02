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

class OpalHotel_Widget_Hotels extends WP_Widget {

	/* widget base ID */
	public $id = 'opalhotel-hotels';

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			$this->id,
			__( 'OpalHotel: Hotels', 'opal-hotel-room-booking' ),
			array( 'description' => __( 'Hotels Display', 'opal-hotel-room-booking' ), ) // Args
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
		// OpalHotel_Shortcodes::hotels( $instance );
		global $opalhotel_shortcodes;
		if ( isset( $opalhotel_shortcodes['hotels'] ) ) {
			$opalhotel_shortcodes['hotels']->render( $instance );
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
			$title = ! empty( $instance['title'] ) ? $instance['title'] : '';
			$posts_per_page = ! empty( $instance['posts_per_page'] ) ? absint( $instance['posts_per_page'] ) : 5;
			$columns = ! empty( $instance['columns'] ) ? absint( $instance['columns'] ) : 3;
			$categories = ! empty( $instance['category'] ) ? $instance['category'] : array();
			$layout = ! empty( $instance['layout'] ) ? sanitize_text_field( $instance['layout'] ) : 'grid';
			$pagination = ! empty( $instance['pagination'] ) ? absint( $instance['pagination'] ) : 0;
			$order = ! empty( $instance['order'] ) ? absint( $instance['order'] ) : 0;
			$orderby = ! empty( $instance['orderby'] ) ? absint( $instance['orderby'] ) : 0;
			$just = ! empty( $instance['just'] ) ? sanitize_text_field( $instance['just'] ) : '';
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
				<label for="<?php echo $this->get_field_id( 'columns' ); ?>"><?php esc_html_e( 'Columns:', 'opal-hotel-room-booking' ); ?></label>
				<input class="widefat opalhotel-widget-input" id="<?php echo $this->get_field_id( 'columns' ); ?>" name="<?php echo $this->get_field_name( 'columns' ); ?>" type="number" step="1" min ="1" max="6" value="<?php echo esc_attr( $columns ); ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'just' ); ?>"><?php esc_html_e( 'Just show:', 'opal-hotel-room-booking' ); ?></label>
				<select class="widefat opalhotel-widget-input" name="<?php echo $this->get_field_name( 'just' ); ?>[]">
					<option value="" <?php selected( $just, '' ); ?>><?php esc_html_e( 'Default', 'opal-hotel-room-booking' ); ?></option>
					<option value="featured" <?php selected( $just, 'featured' ); ?>><?php esc_html_e( 'Featured', 'opal-hotel-room-booking' ); ?></option>
					<option value="recommended" <?php selected( $just, 'recommended' ); ?>><?php esc_html_e( 'Recommended', 'opal-hotel-room-booking' ); ?></option>
					<option value="popular" <?php selected( $just, 'popular' ); ?>><?php esc_html_e( 'Popular', 'opal-hotel-room-booking' ); ?></option>
				</select>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'order' ); ?>"><?php esc_html_e( 'Order:', 'opal-hotel-room-booking' ); ?></label>
				<select class="widefat opalhotel-widget-input" name="<?php echo $this->get_field_name( 'order' ); ?>[]">
					<option value="ASC" <?php selected( $order, 'ASC' ); ?>><?php esc_html_e( 'ASC', 'opal-hotel-room-booking' ); ?></option>
					<option value="DESC" <?php selected( $order, 'DESC' ); ?>><?php esc_html_e( 'DESC', 'opal-hotel-room-booking' ); ?></option>
				</select>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'orderby' ); ?>"><?php esc_html_e( 'Order by:', 'opal-hotel-room-booking' ); ?></label>
				<select class="widefat opalhotel-widget-input" name="<?php echo $this->get_field_name( 'orderby' ); ?>[]">
					<option value="ID" <?php selected( $orderby, 'ID' ); ?>><?php esc_html_e( 'ID', 'opal-hotel-room-booking' ); ?></option>
					<option value="date" <?php selected( $orderby, 'date' ); ?>><?php esc_html_e( 'Date', 'opal-hotel-room-booking' ); ?></option>
					<option value="comment_count" <?php selected( $orderby, 'comment_count' ); ?>><?php esc_html_e( 'Comment Count', 'opal-hotel-room-booking' ); ?></option>
					<option value="rating" <?php selected( $orderby, 'rating' ); ?>><?php esc_html_e( 'Rating', 'opal-hotel-room-booking' ); ?></option>
					<option value="price" <?php selected( $orderby, 'price' ); ?>><?php esc_html_e( 'Price', 'opal-hotel-room-booking' ); ?></option>
				</select>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'category' ); ?>"><?php esc_html_e( 'Categories:', 'opal-hotel-room-booking' ); ?></label>
				<?php
					$terms = get_terms( array(
					    'taxonomy' => 'opalhotel_hotel_cat',
					    'hide_empty' => false,
					) );
				?>
				<select class="widefat opalhotel-widget-input" name="<?php echo $this->get_field_name( 'category' ); ?>[]" multiple>
					<option value=""><?php esc_html_e( 'No Categories', 'opal-hotel-room-booking' ); ?></option>
					<?php foreach ( $terms as $term ) : ?>
						<option value="<?php echo esc_attr( $term->slug ) ?>"<?php echo in_array( $term->slug, $categories ) ? ' selected' : '' ?>><?php echo esc_html( $term->name ); ?></option>
					<?php endforeach; ?>
				</select>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'layout' ); ?>"><?php esc_html_e( 'Layout:', 'opal-hotel-room-booking' ); ?></label>
				<select class="widefat opalhotel-widget-input" name="<?php echo $this->get_field_name( 'layout' ); ?>">
					<option value="grid" <?php selected( $layout, 'grid' ); ?>><?php esc_html_e( 'Grid', 'opal-hotel-room-booking' ); ?></option>
					<option value="list" <?php selected( $layout, 'list' ); ?>><?php esc_html_e( 'List', 'opal-hotel-room-booking' ); ?></option>
				</select>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'style' ); ?>">
					<?php esc_html_e( 'Style:', 'opal-hotel-room-booking' ); ?>
					<span class="description"><?php esc_html_e( 'Style for Grid Layout', 'opal-hotel-room-booking' ); ?></span>
				</label>
				<select class="widefat opalhotel-widget-input" name="<?php echo $this->get_field_name( 'style' ); ?>">
					<option value="" <?php selected( $layout, '' ); ?>><?php esc_html_e( 'Style 1', 'opal-hotel-room-booking' ); ?></option>
					<option value="style2" <?php selected( $layout, 'style2' ); ?>><?php esc_html_e( 'Style 2', 'opal-hotel-room-booking' ); ?></option>
				</select>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'pagination' ); ?>">
					<?php esc_html_e( 'Pagination:', 'opal-hotel-room-booking' ); ?>
				</label>
				<select class="widefat opalhotel-widget-input" name="<?php echo $this->get_field_name( 'pagination' ); ?>">
					<option value="" <?php selected( $layout, '' ); ?>><?php esc_html_e( 'None', 'opal-hotel-room-booking' ); ?></option>
					<option value="1" <?php selected( $layout, 1 ); ?>><?php esc_html_e( 'Yes', 'opal-hotel-room-booking' ); ?></option>
					<option value="2" <?php selected( $layout, 2 ); ?>><?php esc_html_e( 'Ajax', 'opal-hotel-room-booking' ); ?></option>
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
		$instance['columns'] = ( ! empty( $new_instance['columns'] ) ) ? absint( $new_instance['columns'] ) : 0;
		$instance['category'] = ( ! empty( $new_instance['category'] ) ) ? $new_instance['category'] : array();
		$instance['layout'] = ( ! empty( $new_instance['layout'] ) ) ? sanitize_text_field( $new_instance['layout'] ) : 'grid';
		$instance['pagination'] = ( ! empty( $new_instance['pagination'] ) ) ? sanitize_text_field( $new_instance['pagination'] ) : 0;
		$instance['order'] = ( ! empty( $new_instance['order'] ) ) ? sanitize_text_field( $new_instance['order'] ) : 0;
		$instance['orderby'] = ( ! empty( $new_instance['orderby'] ) ) ? sanitize_text_field( $new_instance['orderby'] ) : 0;
		$instance['just'] = ( ! empty( $new_instance['just'] ) ) ? sanitize_text_field( $new_instance['just'] ) : '';
		return $instance;
	}
}