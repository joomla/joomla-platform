<?php

/**
 * @package     Joomla.UnitTest
 * @subpackage  FileSystem
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * A unit test class for PHP filesystem
 *
 * @package     Joomla.UnitTest
 * @subpackage  FileSystem
 *
 * @since       12.1
 */
abstract class TestCaseFilesystemSystemPhp
{
	public static function getSystem()
	{
		return JFilesystem::getInstance();
	}

	public static function getPath()
	{
		return JPATH_TESTS . '/tmp/filesystem';
	}
}
