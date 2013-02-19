<?php
/**
 * @package    Joomla.Test
 *
 * @copyright  Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

/**
 * Class to mock JController.
 *
 * @package  Joomla.Test
 * @since    12.1
 */
class TestMockController
{
	/**
	 * Creates and instance of the mock JController object.
	 *
	 * @param   object  $test  A test object.
	 *
	 * @return  object
	 *
	 * @since   12.1
	 */
	public static function create($test)
	{
		// Collect all the relevant methods in JController.
		$methods = array(
			'execute',
			'getApplication',
			'getInput',
			'serialize',
			'unserialize',
		);

		// Create the mock.
		$mockObject = $test->getMock(
			'JControllerBase',
			$methods,
			// Constructor arguments.
			array(),
			// Mock class name.
			'',
			// Call original constructor.
			false
		);

		// TODO  Mock the input.
		TestReflection::setValue($mockObject, 'input', new JInput);

		return $mockObject;
	}
}
