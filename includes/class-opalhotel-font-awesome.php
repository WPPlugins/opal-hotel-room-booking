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

class OpalHotel_Font_Awesome{

    private $prefix;

    private $data = array();

    public function __construct( $path = '', $fa_css_prefix = 'fa' ) {
        $this->prefix = $fa_css_prefix;

        if ( ! $path ) {
            $path = apply_filters( 'opalhotel_font_awesome_path', OPALHOTEL_PATH . '/assets/libraries/font-awesome/css/font-awesome.css' );
        }
        $css = file_get_contents( $path );

        $pattern = '/\.('.$fa_css_prefix.'-(?:\w+(?:-)?)+):before\s+{\s*content:\s*"(.+)";\s+}/';

        preg_match_all($pattern, $css, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {

            // Set Basic Data
            $item = array();
            $item['class'] = $match[1];
            $item['unicode'] = $match[2];
            $this->data[] = $item;
        }

    }

    public function getIcons() {
        return $this->data;
    }

    public function getPrefix() {
        return (string) $this->prefix;
    }

}