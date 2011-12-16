<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Content
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

/**
 * Joomla Unit Test Mock Class for JContent
 *
 * @package     Joomla.UnitTest
 * @subpackage  Content
 * @since       12.1
 */
class JContentMock
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
	public static function create(JoomlaDatabaseTestCase $test, $prefix, $type, $factory = null, $db = null, $app = null, $user = null)
	{
		// Convert the type to an object if necessary.
		if (is_string($type))
		{
			$alias = $type;

			// Get a content type object.
			$type = new JContentType($factory, $db);
			$type->title = $alias;
			$type->alias = $alias;
		}

		// Load the other mocks if not set.
		$db		= isset($db) ? $db : $test->getMockDatabase();
		$app	= isset($app) ? $app : $test->getMockWeb();

		// Create the mock.
		$mockObject = $test->getMock(
			'JContent',
			array('canCheckout', 'assertIsLoaded'),
			// Constructor arguments.
			array($prefix, $type, $factory, $db, $app, $user),
			// Mock class name.
			'',
			// Call original constructor.
			true
		);

		return $mockObject;
	}
}