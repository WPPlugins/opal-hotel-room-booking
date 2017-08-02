<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

class OpalHotel_Abstract_Taxonomy {

	/* taxonomy name */
	protected $taxonomy = null;

	/* taxonomy args */
	protected $taxonomy_args = array();

	/* post types uses */
	protected $post_types = array();

	public function __construct() {

		// register in init hook
		add_action( 'init', array( $this, 'register_taxonomy' ), 20 );
	}

	public function register_taxonomy(){

		/* taxonomy args */
        $this->taxonomy_args = apply_filters( 'opalhotel_register_taxonomy_args', $this->taxonomy_args, $this->taxonomy );
        /* post types uses */
        $this->post_types = apply_filters( 'opalhotel_register_taxonomy_post_types', $this->post_types, $this->taxonomy );

        /* register */
        register_taxonomy(
        	$this->taxonomy,
            $this->post_types,
            $this->taxonomy_args
        );
	}

}