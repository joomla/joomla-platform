<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Content
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

/**
 * Joomla Unit Test Mock Class for JContentHelper
 *
 * @package     Joomla.UnitTest
 * @subpackage  Content
 * @since       12.1
 */
class JContentFactoryMock
{
	/**
	 * Creates and instance of the mock JContentHelper object.
	 *
	 * @param   JoomlaTestCase  $test  A test object.
	 *
	 * @return  object
	 *
	 * @since   11.3
	 */
	public static function create(JoomlaDatabaseTestCase $test)
	{
		// Collect all the relevant methods in JContentHelper.
		$methods = array('foo');

		$mockObject = $test->getMockBuilder('JContentFactory');

		// Create the mock.
		$mockObject = $test->getMock(
			'JContentFactory',
			$methods,
			// Constructor arguments.
			array(),
			// Mock class name.
			'',
			// Call original constructor.
			false
		);

		return $mockObject;
	}
}