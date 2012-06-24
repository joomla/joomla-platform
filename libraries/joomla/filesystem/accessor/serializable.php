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
 * A File system accessor for reading/writing csv data
 *
 * @package     Joomla.Platform
 * @subpackage  FileSystem
 *
 * @since       12.2
 */
abstract class JFilesystemAccessorSerializable
{
	/**
	 * Read serializable data from a file
	 *
	 * @param   JFilesystemElementFile  $file  The file to be read.
	 *
	 * @return  mixed  The data read.
	 *
	 * @link    http://php.net/manual/en/function.unserialize.php
	 *
	 * @see     JFilesystemAccessorLine::read
	 *
	 * @since   12.2
	 */
	public static function read(JFilesystemElementFile $file)
	{
		$line = JFilesystemAccessorLine::read($file);
		if ($line === false)
		{
			return false;
		}
		else
		{
			return unserialize(str_replace(array('\n', '\\\\'), array("\n", '\\'), $line));
		}
	}

	/**
	 * Write serializable data to a file
	 *
	 * @param   JFilesystemElementFile  $file          The file to be written.
	 * @param   mixed                   $serializable  The data to be serialized.
	 *
	 * @return  mixed  The number of bytes written, or FALSE on failure.
	 *
	 * @link    http://php.net/manual/en/function.serialize.php
	 *
	 * @see     JFilesystemAccessorLine::write
	 *
	 * @since   12.2
	 */
	public static function write(JFilesystemElementFile $file, $serializable)
	{
		return JFilesystemAccessorLine::write($file, addcslashes(serialize($serializable), "\n\\"));
	}

	/**
	 * Pull serializable data from a file
	 *
	 * @param   JFilesystemElementFile  $file  The file to be read.
	 *
	 * @return  array  The unserialized data
	 *
	 * @since   12.2
	 */
	public static function pull(JFilesystemElementFile $file)
	{
		$array = array();
		$file->open('r');
		foreach ($file->iterateSerializable() as $unserialized)
		{
			if ($unserialized !== false)
			{
				$array[] = $unserialized;
			}
		}
		$file->close();
		return $array;
	}

	/**
	 * Push lines to a file
	 *
	 * @param   JFilesystemElementFile  $file  The file to be written.
	 * @param   mixed                   $data  The data that is to be written.
	 *
	 * @return  mixed  The number of bytes written, or FALSE on failure.
	 *
	 * @since   12.2
	 */
	public static function push(JFilesystemElementFile $file, $data)
	{
		$file->open('w');
		$return = 0;
		foreach ($data as $serializable)
		{
			$nb_bytes = static::write($file, $serializable);
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
