<?php
/**
 * @package    Joomla.Test
 *
 * @copyright  Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

/**
 * Class to mock JDocument.
 *
 * @package  Joomla.Test
 * @since    12.1
 */
class TestMockDocument
{
	/**
	 * Creates and instance of the mock JLanguage object.
	 *
	 * @param   object  $test  A test object.
	 *
	 * @return  object
	 *
	 * @since   11.3
	 */
	public static function create($test)
	{
		// Collect all the relevant methods in JDatabase.
		$methods = array(
			'parse',
			'render',
			'test',
		);

		// Create the mock.
		$mockObject = $test->getMock(
			'JDocument',
			$methods,
			// Constructor arguments.
			array(),
			// Mock class name.
			'',
			// Call original constructor.
			false
		);

		// Mock selected methods.
		$test->assignMockReturns(
			$mockObject, array(
				'parse' => $mockObject,
				// An additional 'test' method for confirming this object is successfully mocked.
				'test' => 'ok'
			)
		);

		return $mockObject;
	}
}
