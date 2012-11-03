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
 * A File system accessor for reading/writing Ini contents
 *
 * @package     Joomla.Platform
 * @subpackage  FileSystem
 *
 * @since       12.2
 */
abstract class JFilesystemAccessorIni
{
	/**
	 * pull entire ini file into an array
	 *
	 * @param   JFilesystemElementFile  $file              The file to be read.
	 * @param   boolean                 $process_sections  Tells to get a multidimensional array, with the section names and settings included.
	 * @param   integer                 $scanner_mode      Can either be INI_SCANNER_NORMAL (default) or INI_SCANNER_RAW.
	 *                                                     If INI_SCANNER_RAW is supplied, then option values will not be parsed. 
	 *
	 * @return  mixed  Associative array on success, or FALSE on failure.
	 *
	 * @link    http://php.net/manual/en/function.parse-ini-file.php
	 *
	 * @since   12.2
	 */
	public static function pull(JFilesystemElementFile $file, $process_sections = false, $scanner_mode = INI_SCANNER_NORMAL)
	{
		return parse_ini_file($file->fullpath, $process_sections, $scanner_mode);
	}

	/**
	 * push an ini data to a file
	 *
	 * @param   JFilesystemElementFile  $file  The file to be written.
	 * @param   mixed                   $ini   The ini to write.
	 *
	 * @return  mixed  The number of bytes that were written to the file, or FALSE on failure.
	 *
	 * @see     JFilesystemElementAccessorLine::write
	 *
	 * @since   12.2
	 */
	public static function push(JFilesystemElementFile $file, $ini)
	{
		$return = 0;
		$file->open('w');
		foreach ($ini as $key1 => $value1)
		{
			if (is_array($value1))
			{
				$nb_bytes = JFilesystemAccessorLine::write($file, "\n" . '[' . $key1 . ']' . "\n");
				if ($nb_bytes === false)
				{
					// @codeCoverageIgnoreStart
					$file->close();
					return false;

					// @codeCoverageIgnoreEnd
				}
				$return = $return + $nb_bytes;
				foreach ($value1 as $key2 => $value2)
				{
					$nb_bytes = static::writeEntry($file, $key2, $value2);
					if ($nb_bytes === false)
					{
						// @codeCoverageIgnoreStart
						$file->close();
						return false;

						// @codeCoverageIgnoreEnd
					}
					$return = $return + $nb_bytes;
				}
			}
			else
			{
				$nb_bytes = static::writeEntry($file, $key1, $value1);
				if ($nb_bytes === false)
				{
					// @codeCoverageIgnoreStart
					$file->close();
					return false;

					// @codeCoverageIgnoreEnd
				}
				$return = $return + $nb_bytes;
			}
		}
		$file->close();
		return $return;
	}

	/**
	 * write an ini entry to a file
	 *
	 * @param   JFilesystemElementFile  $file   The file to be written.
	 * @param   string                  $key    The ini key.
	 * @param   mixed                   $value  The ini value.
	 *
	 * @return  mixed  The number of bytes that were written to the file, or FALSE on failure.
	 *
	 * @see     JFilesystemElementAccessorLine::write
	 *
	 * @since   12.2
	 */
	public static function writeEntry(JFilesystemElementFile $file, $key, $value)
	{
		if (is_bool($value))
		{
			if ($value)
			{
				return JFilesystemAccessorLine::write($file, $key . '=true');
			}
			else
			{
				return JFilesystemAccessorLine::write($file, $key . '=false');
			}
		}
		elseif (is_int($value) || is_float($value))
		{
			return JFilesystemAccessorLine::write($file, $key . '=' . $value);
		}
		else
		{
			return JFilesystemAccessorLine::write($file, $key . '="' . addcslashes($value, '"') . '"');
		}
	}
}
