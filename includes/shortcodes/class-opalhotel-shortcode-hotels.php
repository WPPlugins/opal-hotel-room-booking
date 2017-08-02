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

class OpalHotel_Shortcode_Hotels extends OpalHotel_Abstract_Shortcode {

	/* shortcode name */
	public $shortcode = null;

	public function __construct() {
		$this->shortcode = 'opalhotel_hotels';
		parent::__construct();
	}

	/* render */
	public function render( $atts = array(), $content = null ) {
		$atts = shortcode_atts( array(
				'posts_per_page'	=> 5,
				'category'			=> null,
				'order'				=> 'DESC',
				'orderby'			=> 'date',
				'post__in'			=> array(),
				'columns'			=> 3,
				'layout'			=> '',
				'style'				=> '',
				'pagination'		=> 1,
				'just'				=> '',
				'ajax'				=> 0
			), $atts );
		OpalHotel_Shortcodes::hotels( $atts );
	}

}
