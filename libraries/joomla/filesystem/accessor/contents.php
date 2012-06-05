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
 * A File system accessor for reading/writing simple contents
 *
 * @package     Joomla.Platform
 * @subpackage  FileSystem
 *
 * @since       12.2
 */
abstract class JFilesystemAccessorContents
{
	/**
	 * Read data from a file
	 *
	 * @param   JFilesystemElementFile  $file    The file to be read.
	 * @param   int                     $length  The maximum number of characters read.
	 *
	 * @return  string|FALSE  The data read, or FALSE on failure.
	 *
	 * @link    http://php.net/manual/en/function.fread.php
	 *
	 * @since   12.2
	 */
	public static function read(JFilesystemElementFile $file, $length)
	{
		return fread($file->handle, $length);
	}

	/**
	 * Write data to a file
	 *
	 * @param   JFilesystemElementFile  $file    The file to be written.
	 * @param   string                  $data    The string that is to be written.
	 * @param   int                     $length  The maximum number of characters written.
	 *
	 * @return  int|FALSE  The number of bytes written, or FALSE on failure.
	 *
	 * @link    http://php.net/manual/en/function.fwrite.php
	 *
	 * @since   12.2
	 */
	public static function write(JFilesystemElementFile $file, $data, $length = null)
	{
		if ($length !== null)
		{
			return fwrite($file->handle, $data, $length);
		}
		else
		{
			return fwrite($file->handle, $data);
		}
	}

	/**
	 * Pull an entire file into a string
	 *
	 * @param   JFilesystemElementFile  $file    The file to be pulled.
	 * @param   int                     $offset  The offset where the reading starts on the original stream.
	 * @param   int                     $maxlen  Maximum length of data read.
	 *
	 * @return  string|FALSE  The read data or FALSE on failure.
	 *
	 * @link    http://php.net/manual/en/function.file-get-contents.php
	 *
	 * @since   12.2
	 */
	public static function pull(JFilesystemElementFile $file, $offset = -1, $maxlen = null)
	{
		if ($maxlen === null)
		{
			return file_get_contents($file->fullpath, $file->use_include_path, $file->system->context, $offset);
		}
		else
		{
			return file_get_contents($file->fullpath, $file->use_include_path, $file->system->context, $offset, $maxlen);
		}
	}

	/**
	 * Push a string to a file
	 *
	 * @param   JFilesystemElementFile  $file   The file to be pushed.
	 * @param   mixed                   $data   The data to write. Can be either a string, an array or a stream resource.
	 * @param   int                     $flags  The value of flags can be any combination of the following flags, joined with the binary | operator:
	 *                                          -FILE_APPEND If file filename already exists, append the data to the file instead of overwriting it.
	 *                                          -LOCK_EX Acquire an exclusive lock on the file while proceeding to the writing.
	 *
	 * @return  int|FALSE  The number of bytes that were written to the file, or FALSE on failure.
	 *
	 * @link    http://php.net/manual/en/function.file-put-contents.php
	 *
	 * @since   12.2
	 */
	public static function push(JFilesystemElementFile $file, $data, $flags = 0)
	{
		return file_put_contents($file->fullpath, $data, $flags | ($file->use_include_path ? FILE_USE_INCLUDE_PATH : 0), $file->system->context);
	}

}
