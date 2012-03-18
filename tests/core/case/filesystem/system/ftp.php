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
	public static function getSystem()
	{
		// First let's look to see if we have a FTP prefix defined or in the environment variables.
		if (defined('JTEST_FILESYSTEM_FTP_CREDENTIAL') || getenv('JTEST_FILESYSTEM_FTP_CREDENTIAL'))
		{
			$credentials = defined('JTEST_FILESYSTEM_FTP_CREDENTIAL') ? JTEST_FILESYSTEM_FTP_CREDENTIAL : getenv('JTEST_FILESYSTEM_FTP_CREDENTIAL');
		}
		else
		{
			return null;
		}
		return JFilesystem::getInstance('ftp://' . $credentials . '@localhost', array('ftp' => array('overwrite' => true)));
	}
}
