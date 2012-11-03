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
 * A File system accessor for reading/writing a character
 *
 * @package     Joomla.Platform
 * @subpackage  FileSystem
 *
 * @since       12.2
 */
abstract class JFilesystemAccessorCharacter
{
	/**
	 * Read a character from a file
	 *
	 * @param   JFilesystemElementFile  $file  The file to be read.
	 *
	 * @return  mixed  The character read, or FALSE on failure.
	 *
	 * @link    http://php.net/manual/en/function.fgetc.php
	 *
	 * @since   12.2
	 */
	public static function read(JFilesystemElementFile $file)
	{
		return fgetc($file->handle);
	}

	/**
	 * Write a character to a file
	 *
	 * @param   JFilesystemElementFile  $file       The file to be written.
	 * @param   string                  $character  The character that is to be written.
	 *
	 * @return  mixed  The number of bytes written, or FALSE on failure.
	 *
	 * @see     JFilesystemAccessorContents::write
	 *
	 * @since   12.2
	 */
	public static function write(JFilesystemElementFile $file, $character)
	{
		return JFilesystemAccessorContents::write($file, $character, 1);
	}
}
