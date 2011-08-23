<?php
/**
 * Topsy Tests.
 * 
 * @author Rowan Manning <info@rowanmanning.co.uk>
 * @copyright Copyright 2011, Rowan Manning
 * @license Dual licensed under the MIT or GPL Version 2 licenses.
 * @filesource
 */

//============================================================
// BOOTSTRAP
//============================================================

/**
 * Test AutoLoader
 */
class TestAutoLoader {
	
	/**
	 * Load a class from the 'lib' directory
	 */
	public function load($class_name) {
		
		// calculate class path
		$class_name = trim($class_name, '\\');
		$class_file = dirname(__FILE__) . '/../lib/' . str_replace('\\', '/', $class_name) . '.php';
		
		// load class
		if (file_exists($class_file)) {
			require_once $class_file;
			return true;
		}
		
	}
	
	/**
	 * Register the auto-loader
	 */
	public function register() {
		
		spl_autoload_register(array($this, 'load'));
		
	}
	
	/**
	 * Unregister the auto-loader
	 */
	public function unregister() {
		
		spl_autoload_unregister(array($this, 'load'));
		
	}
	
}

// register the autoloader
$autoloader = new TestAutoLoader();
$autoloader->register();

//============================================================
// end of file
