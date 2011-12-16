<?php
/**
 * @package    Joomla.UnitTest
 * @copyright  Copyright (C) 2005 - 2011 Open Source Matters. All rights reserved.
 * @license    GNU General Public License
 */

/**
 * Mock class for JCache.
 *
 * @package  Joomla.UnitTest
 * @since    12.1
 */
class JCacheGlobalMock
{
	/**
	 * A mock cache store.
	 *
	 * @var    array
	 * @since  12.1
	 */
	public static $cache = array();

	/**
	 * Creates and instance of the mock JApplication object.
	 *
	 * @param   object  $test  A test object.
	 * @param   array   $data  Data to prime the cache with.
	 *
	 * @return  object
	 *
	 * @since   12.1
	 */
	public static function create($test, $data = array())
	{
		self::$cache = $data;

		// Collect all the relevant methods in JConfig.
		$methods = array(
			'get',
			'store',
		);

		// Create the mock.
		$mockObject = $test->getMock(
			'JCache',
			$methods,
			// Constructor arguments.
			array(),
			// Mock class name.
			'',
			// Call original constructor.
			false
		);

		$test->assignMockCallbacks(
			$mockObject,
			array(
				'get' => array(get_called_class(), 'mockGet'),
				'store' => array(get_called_class(), 'mockStore'),
			)
		);

		return $mockObject;
	}

	/**
	 * Callback for the cache get method.
	 *
	 * @param  string  $id  The name of the cache key to retrieve.
	 *
	 * @return mixed  The value of the key or null if it does not exist.
	 *
	 * @since  12.1
	 */
	public function mockGet($id)
	{
		return isset(self::$cache[$id]) ? self::$cache[$id] : null;
	}

	/**
	 * Callback for the cache get method.
	 *
	 * @param  string  $key    The name of the cache key.
	 * @param  string  $group  Dummy group.
	 *
	 * @return mixed  The value of the key or null if it does not exist.
	 *
	 * @since  12.1
	 */
	public function mockStore($value, $id)
	{
		self::$cache[$id] = $value;
	}
}
