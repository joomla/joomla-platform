<?php

/**
 * @package     Joomla.UnitTest
 * @subpackage  FileSystem
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */


/**
 * A Test File system accessor for reading/writing simple contents
 *
 * @package     Joomla.UnitTest
 * @subpackage  FileSystem
 *
 * @since       12.1
 */
class TestFilesystemAccessorContents
{
	protected static $data;

	/**
	 * Read data from a file
	 *
	 * @param   JFilesystemElementFile  $file    The file to be read.
	 *
	 * @return  string|FALSE  The data read, or FALSE on failure.
	 *
	 * @since   12.1
	 */
	public static function read(JFilesystemElementFile $file)
	{
		return static::$data;
	}

	/**
	 * Write data to a file
	 *
	 * @param   JFilesystemElementFile  $file    The file to be written.
	 * @param   string                  $data    The string that is to be written.
	 *
	 * @return  int|FALSE  The number of bytes written, or FALSE on failure.
	 *
	 * @since   12.1
	 */
	public static function write(JFilesystemElementFile $file, $data)
	{
		static::$data = $data;
		return strlen($data);
	}

	/**
	 * Pull data from a file
	 *
	 * @param   JFilesystemElementFile  $file    The file to be pulled.
	 *
	 * @return  string|FALSE  The data read, or FALSE on failure.
	 *
	 * @since   12.1
	 */
	public static function pull(JFilesystemElementFile $file)
	{
		return static::$data;
	}

	/**
	 * Push data to a file
	 *
	 * @param   JFilesystemElementFile  $file    The file to be pushed.
	 *
	 * @return  int|FALSE  The number of bytes written, or FALSE on failure.
	 *
	 * @since   12.1
	 */
	public static function push(JFilesystemElementFile $file)
	{
		return static::$data;
	}
}

function test_reader_contents(JFilesystemElementFile $file)
{
	return 'Hello';
}

function test_writer_contents(JFilesystemElementFile $file, $data)
{
	return strlen($data);
}

function test_puller_contents(JFilesystemElementFile $file)
{
	return 'Hello';
}

function test_pusher_contents(JFilesystemElementFile $file, $data)
{
	return strlen($data);
}

