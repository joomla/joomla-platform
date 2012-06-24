<?php

/**
 * @package     Joomla.Platform
 * @subpackage  FileSystem
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * A File system accessor for reading/writing lines
 *
 * @package     Joomla.Platform
 * @subpackage  FileSystem
 *
 * @since       12.2
 */
abstract class JFilesystemAccessorLine
{
	/**
	 * Read a line from a file
	 *
	 * @param   JFilesystemElementFile  $file    The file to be read.
	 * @param   integer                 $length  The maximum number of characters read.
	 *
	 * @return  mixed  The data read, or FALSE on failure.
	 *
	 * @link    http://php.net/manual/en/function.fgets.php
	 *
	 * @since   12.2
	 */
	public static function read(JFilesystemElementFile $file, $length = null)
	{
		if ($length === null)
		{
			$return = fgets($file->handle);
		}
		else
		{
			$return = fgets($file->handle, $length);
		}
		return $return;
	}

	/**
	 * Write a line to a file
	 *
	 * @param   JFilesystemElementFile  $file    The file to be written.
	 * @param   string                  $data    The string that is to be written.
	 * @param   integer                 $length  The maximum number of characters written.
	 *
	 * @return  mixed  The number of bytes written, or FALSE on failure.
	 *
	 * @see     JFilesystemAccessorContents::write
	 *
	 * @since   12.2
	 */
	public static function write(JFilesystemElementFile $file, $data, $length = null)
	{
		if ($length === null)
		{
			return JFilesystemAccessorContents::write($file, $data . "\n");
		}
		else
		{
			return JFilesystemAccessorContents::write($file, substr($data, 0, $length) . "\n");
		}
	}

	/**
	 * Pull lines from a file
	 *
	 * @param   JFilesystemElementFile  $file    The file to be read.
	 * @param   integer                 $length  The maximum number of characters read.
	 *
	 * @return  mixed  The lines, or FALSE on failure.
	 *
	 * @since   12.2
	 */
	public static function pull(JFilesystemElementFile $file, $length = null)
	{
		$array = array();
		$file->open('r');
		foreach ($file->iterateLine($length) as $line)
		{
			if ($line === false)
			{
				// @codeCoverageIgnoreStart
				break;

				// @codeCoverageIgnoreEnd
			}
			else
			{
				$array[] = $line;
			}
		}
		$file->close();
		return $array;
	}

	/**
	 * Push lines to a file
	 *
	 * @param   JFilesystemElementFile  $file    The file to be written.
	 * @param   mixed                   $data    The data that is to be written.
	 * @param   integer                 $length  The maximum number of characters written.
	 *
	 * @return  mixed  The number of bytes written, or FALSE on failure.
	 *
	 * @since   12.2
	 */
	public static function push(JFilesystemElementFile $file, $data, $length = null)
	{
		$file->open('w');
		$return = 0;
		foreach ($data as $line)
		{
			$nb_bytes = static::write($file, $line, $length);
			if ($nb_bytes === false)
			{
				// @codeCoverageIgnoreStart
				$file->close();
				return false;

				// @codeCoverageIgnoreEnd
			}
			else
			{
				$return = $return + $nb_bytes;
			}
		}
		$file->close();
		return $return;
	}
}
