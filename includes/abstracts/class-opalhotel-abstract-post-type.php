<?php
/**
 * @Author: brainos
 * @Date:   2016-04-24 08:51:31
 * @Last Modified by:   opalhotel_remove_extra
 * @Last Modified time: 2016-04-25 19:53:53
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

class OpalHotel_Abstract_Post_Type {

	/* post type name */
	protected $post_type = null;

	/* post type args register */
	protected $post_type_args = array();

	public function __construct() {

		// register in init hook
		add_action( 'init', array( $this, 'register_post_type' ) );

        add_filter( 'manage_' . $this->post_type . '_posts_columns', array( $this, 'custom_cols' ) );
        add_action( 'manage_' . $this->post_type . '_posts_custom_column', array( $this, 'custom_cols_content' ), 10, 2 );
	}

	/* register single post type */
	public function register_post_type() {

		/* register function */
		$this->post_type_args = apply_filters( 'opalhotel_register_post_type', $this->post_type_args, $this->post_type );
		register_post_type( $this->post_type, $this->post_type_args );
	}

	/* custom column post type fields */
	public function custom_cols( $columns ) {
		return $columns;
	}

	/* custom column content display */
	public function custom_cols_content( $column, $post_id ) {}

}
