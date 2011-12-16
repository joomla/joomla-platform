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
class JContentHelperMock
{
	/**
	 * Creates and instance of the mock JContentHelper object.
	 *
	 * @param   object  $test  A test object.
	 *
	 * @return  object  Mock object for JContentHelper.
	 *
	 * @since   12.1
	 */
	public static function create($test)
	{
		// Collect all the relevant methods in JContentHelper.
		$methods = array(
			'getTypes'
		);

		// Create the mock.
		$mockObject = $test->getMock(
			'JContentHelper',
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