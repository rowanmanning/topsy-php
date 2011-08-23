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
// TESTS
//============================================================

/**
 * Topsy Test Case.
 * 
 * @todo write tests for `Topsy::request`.
 */
class TopsyTest extends PHPUnit_Framework_TestCase {
	
	/**
	 * @covers Topsy::calculate_url
	 * @dataProvider getTestEndpointURLs
	 */
	public function testURLCalculation($endpoint, $expected_result) {
		
		$instance = new Topsy();
		$reflection_method = new ReflectionMethod($instance, 'calculate_url');
		$reflection_method->setAccessible(true);
		
		$this->assertEquals($expected_result, $reflection_method->invoke($instance, $endpoint));
		
	}
	
	/**
	 * Data-provider for testURLCalculation.
	 */
	public function getTestEndpointURLs() {
		return array(
			array('example', 'http://otter.topsy.com/example.json'),
			array('/example', 'http://otter.topsy.com/example.json'),
		);
	}
	
}

//============================================================
// end of file
