<?php

/**
 * @package     Joomla.UnitTest
 * @subpackage  FileSystem
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * A unit test class for FTP filesystem
 *
 * @package     Joomla.UnitTest
 * @subpackage  FileSystem
 *
 * @since       12.1
 */
abstract class TestCaseFilesystemSystemFtp
{
	protected static $system;
	protected static $path;

	protected static function getFtp()
	{
		// First let's look to see if we have a FTP prefix defined or in the environment variables.
		if (defined('JTEST_FILESYSTEM_FTP') || getenv('JTEST_FILESYSTEM_FTP'))
		{
			$ftp = defined('JTEST_FILESYSTEM_FTP') ? JTEST_FILESYSTEM_FTP : getenv('JTEST_FILESYSTEM_FTP');
			if (preg_match(chr(1) . '(ftps?://[^/]*)(.*)' . chr(1), $ftp, $matches))
			{
				static::$system = JFilesystem::getInstance($matches[1], array('ftp' => array('overwrite' => true)));
				static::$path = $matches[2];
			}
			else
			{
				static::$system = false;
				static::$path = false;
			}
		}
		else
		{
			static::$system = false;
			static::$path = false;
		}
	}

	public static function getSystem()
	{
		if (!isset(static::$system))
		{
			self::getFtp();
		}
		return static::$system;
	}

	public static function getPath()
	{
		if (!isset(static::$path))
		{
			self::getFtp();
		}
		return static::$path;		
	}
}
