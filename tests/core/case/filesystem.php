<?php

/**
 * @package     Joomla.UnitTest
 * @subpackage  FileSystem
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * Abstract test case class for file system testing.
 *
 * @package  Joomla.Test
 * @since    12.1
 */
abstract class TestCaseFilesystem extends TestCase
{
	protected static $system = null;

	public static function setupBeforeClass()
	{
		$parts = JString::splitCamelCase(get_called_class());
		$class = 'TestCaseFilesystemSystem' . $parts[count($parts) - 2];
		static::$system = $class::getSystem();
	}

	/**
	 * Sets up the fixture.
	 *
	 * This method is called before a test is executed.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	protected function setUp()
	{
		if (empty(static::$system))
		{
			$this->markTestSkipped('There is no file system.');
		}
		
		// Create a temporary directory
		JFilesystemElementDirectory::getInstance(JPATH_TESTS . '/tmp/filesystem', static::$system)->create();

		parent::setUp();
	}

	/**
	 * Remove created files
	 *
	 * This method is called after a test is executed.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	protected function tearDown()
	{
		if (!empty(static::$system))
		{
			// Make sure previous test files are cleaned up
			$directory = JFilesystemElementDirectory::getInstance(JPATH_TESTS . '/tmp/filesystem', static::$system);
			if ($directory->exists)
			{
				$directory->delete();
			}
		}
		parent::tearDown();
	}
}
