<?php
/**
 * @Author: brainos
 * @Date:   2016-04-23 23:45:27
 * @Last Modified by:   someone
 * @Last Modified time: 2016-05-03 23:35:34
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

class OpalHotel_Abstract_Shortcode {

	/* shortcode tag */
	protected $shortcode = null;

	/* constructor */
	public function __construct() {

		/* allow hook */
		$this->shortcode = apply_filters( 'opalhotel_shortcode_' . $this->shortcode, $this->shortcode );

		/* add shortcode */
		add_shortcode( $this->shortcode, array( $this, 'add_shortcode' ) );

	}

	/* add shortcode callback */
	public function add_shortcode( $atts = array(), $content = null ) {

		/* before shortcode hook */
		do_action( 'opalhotel_before_shortcode', $this->shortcode );

		/* render shortcode */
		$this->render( $atts, $content );

		/* after shortcode hook */
		do_action( 'opalhotel_after_shortcode', $this->shortcode );
	}

	/* render */
	protected function render( $atts = array(), $content = null ) {}

}