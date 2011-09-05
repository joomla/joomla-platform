<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  HTML
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

require_once JPATH_PLATFORM.'/joomla/html/html/behavior.php';

/**
 * Test class for JHtmlBehavior.
 *
 * @since  11.1
 */
class JHtmlBehaviorTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @todo Implement testFramework().
	 */
	public function testFramework()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
		'This test has not been implemented yet.'
		);
	}

	/**
	 * @todo Implement testMootools().
	 */
	public function testMootools()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
		'This test has not been implemented yet.'
		);
	}

	/**
	 * @todo Implement testCaption().
	 */
	public function testCaption()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
		'This test has not been implemented yet.'
		);
	}

	/**
	 * @todo Implement testFormvalidation().
	 */
	public function testFormvalidation()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
		'This test has not been implemented yet.'
		);
	}

	/**
	 * @todo Implement testSwitcher().
	 */
	public function testSwitcher()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
		'This test has not been implemented yet.'
		);
	}

	/**
	 * @todo Implement testCombobox().
	 */
	public function testCombobox()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
		'This test has not been implemented yet.'
		);
	}

	/**
	 * @todo Implement testTooltip().
	 */
	public function testTooltip()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
		'This test has not been implemented yet.'
		);
	}

	/**
	 * @todo Implement testModal().
	 */
	public function testModal()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
		'This test has not been implemented yet.'
		);
	}

	/**
	 * @todo Implement testUploader().
	 */
	public function testUploader()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
		'This test has not been implemented yet.'
		);
	}

	/**
	 * @todo Implement testTree().
	 */
	public function testTree()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
		'This test has not been implemented yet.'
		);
	}

	/**
	 * @todo Implement testCalendar().
	 */
	public function testCalendar()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
		'This test has not been implemented yet.'
		);
	}

	/**
	 * @todo Implement testKeepalive().
	 */
	public function testKeepalive()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
		'This test has not been implemented yet.'
		);
	}

	/**
	 *
	 */
	public function test_getJSObject()
	{
		$opt = array();
		$opt['integer'] = 1;
		$opt['size'] 	= array('x' => 100, 'y'=> 200);
		$opt['string'] 	= "test";

		// Get a reference to the function
		$class  = new ReflectionClass($this->getMockForAbstractClass('JHtmlBehavior'));
		$method = $class->getMethod('_getJSObject');
		$method->setAccessible(true);

		// Execute the function
		$output = $method->invoke(null, $opt);

		// Make sure the output is the same as the input
		$this->assertEquals(json_encode($opt), $output);
	}
}
