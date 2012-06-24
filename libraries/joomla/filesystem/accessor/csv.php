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
abstract class JFilesystemAccessorCsv
{
	/**
	 * Read csv data from a file
	 *
	 * @param   JFilesystemElementFile  $file       The file to be read.
	 * @param   integer                 $length     The longest line (in characters) to be found in the CSV file.
	 * @param   string                  $delimiter  The optional delimiter parameter sets the field delimiter (one character only).
	 * @param   string                  $enclosure  The optional enclosure parameter sets the field enclosure (one character only).
	 * @param   string                  $escape     Set the escape character (one character only).
	 *
	 * @return  mixed  The data read, or FALSE or NULL on failure.
	 *
	 * @link    http://php.net/manual/en/function.fgetcsv.php
	 *
	 * @since   12.2
	 */
	public static function read(JFilesystemElementFile $file, $length = 0, $delimiter = ',', $enclosure = '"', $escape = '\\')
	{
		return fgetcsv($file->handle, $length, $delimiter, $enclosure, $escape);
	}

	/**
	 * Write csv data to a file
	 *
	 * @param   JFilesystemElementFile  $file       The file to be written.
	 * @param   array                   $fields     The csv data that is to be written.
	 * @param   string                  $delimiter  The optional delimiter parameter sets the field delimiter (one character only).
	 * @param   string                  $enclosure  The optional enclosure parameter sets the field enclosure (one character only).
	 *
	 * @return  mixed  The number of bytes written, or FALSE on failure.
	 *
	 * @link    http://php.net/manual/en/function.fputcsv.php
	 *
	 * @since   12.2
	 */
	public static function write(JFilesystemElementFile $file, $fields, $delimiter = ',', $enclosure = '"')
	{
		return fputcsv($file->handle, $fields, $delimiter, $enclosure);
	}

	/**
	 * Pull csv data from a file
	 *
	 * @param   JFilesystemElementFile  $file       The file to be read.
	 * @param   integer                 $length     The longest line (in characters) to be found in the CSV file.
	 * @param   string                  $delimiter  The optional delimiter parameter sets the field delimiter (one character only).
	 * @param   string                  $enclosure  The optional enclosure parameter sets the field enclosure (one character only).
	 * @param   string                  $escape     Set the escape character (one character only).
	 *
	 * @return  mixed  The csv data, or FALSE on failure.
	 *
	 * @link    http://php.net/manual/en/function.fgetcsv.php
	 *
	 * @since   12.2
	 */
	public static function pull(JFilesystemElementFile $file, $length = 0, $delimiter = ',', $enclosure = '"', $escape = '\\')
	{
		$array = array();
		$file->open('r');
		foreach ($file->iterateCsv($length, $delimiter, $enclosure, $escape) as $csv)
		{
			if (is_array($csv))
			{
				$array[] = $csv;
			}
		}
		$file->close();
		return $array;
	}

	/**
	 * Push csv data to a file
	 *
	 * @param   JFilesystemElementFile  $file       The file to be written.
	 * @param   mixed                   $data       The csv data that is to be written.
	 * @param   string                  $delimiter  The optional delimiter parameter sets the field delimiter (one character only).
	 * @param   string                  $enclosure  The optional enclosure parameter sets the field enclosure (one character only).
	 *
	 * @return  mixed  The number of bytes written, or FALSE on failure.
	 *
	 * @link    http://php.net/manual/en/function.fputcsv.php
	 *
	 * @since   12.2
	 */
	public static function push(JFilesystemElementFile $file, $data, $delimiter = ',', $enclosure = '"')
	{
		$file->open('w');
		$return = 0;
		foreach ($data as $fields)
		{
			$nb_bytes = fputcsv($file->handle, $fields, $delimiter, $enclosure);
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
