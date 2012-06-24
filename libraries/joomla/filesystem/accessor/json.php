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
 * A File system accessor for reading/writing JSON contents
 *
 * @package     Joomla.Platform
 * @subpackage  FileSystem
 *
 * @since       12.2
 */
abstract class JFilesystemAccessorJson
{
	/**
	 * Read a JSON value
	 *
	 * @param   JFilesystemElementFile  $file   The file to be read.
	 * @param   boolean                    $assoc  When TRUE, returned objects will be converted into associative arrays.
	 * @param   integer                     $depth  User specified recursion depth.
	 *
	 * @return  mixed                   Decoded JSON value.
	 *
	 * @link    http://php.net/manual/en/function.json-decode.php
	 *
	 * @see     JFilesystemAccessorLine::read
	 *
	 * @since   12.2
	 */
	public static function read(JFilesystemElementFile $file, $assoc = false, $depth = 512)
	{
		$line = JFilesystemAccessorLine::read($file);
		if ($line === false)
		{
			return false;
		}
		else
		{
			return json_decode($line, $assoc, $depth);
		}
	}

	/**
	 * Write a JSON string to a file
	 *
	 * @param   JFilesystemElementFile  $file    The file to be written.
	 * @param   mixed                   $value   The value to write.
	 * @param   integer                     $options Bitmask consisting of
	 *                                  JSON_HEX_QUOT, JSON_HEX_TAG, JSON_HEX_AMP, JSON_HEX_APOS, JSON_NUMERIC_CHECK, JSON_FORCE_OBJECT.
	 *                                  JSON_NUMERIC_CHECK option was added in PHP 5.3.3
	 *                                  JSON_BIGINT_AS_STRING, JSON_PRETTY_PRINT, JSON_UNESCAPED_SLASHES, JSON_UNESCAPED_UNICODE were added in PHP 5.4
	 *
	 * @return  mixed  The number of bytes that were written to the file, or FALSE on failure.
	 *
	 * @link    http://php.net/manual/en/function.json-encode.php
	 *
	 * @see     JFilesystemAccessorLine::write
	 *
	 * @since   12.2
	 */
	public static function write(JFilesystemElementFile $file, $value, $options = 0)
	{
		return JFilesystemAccessorLine::write($file, json_encode($value, $options));
	}

	/**
	 * Pull entire JSON file
	 *
	 * @param   JFilesystemElementFile  $file   The file to be read.
	 * @param   boolean                    $assoc  When TRUE, returned objects will be converted into associative arrays.
	 * @param   integer                     $depth  User specified recursion depth.
	 *
	 * @return  mixed                   Decoded JSON value.
	 *
	 * @link    http://php.net/manual/en/function.json-decode.php
	 *
	 * @since   12.2
	 */
	public static function pull(JFilesystemElementFile $file, $assoc = false, $depth = 512)
	{
		$array = array();
		$file->open('r');
		foreach ($file->iterateJson($assoc, $depth) as $json)
		{
			if (is_object($json) || is_array($json))
			{
				$array[] = $json;
			}
		}
		$file->close();
		return $array;
	}

	/**
	 * Push JSON data to a file
	 *
	 * @param   JFilesystemElementFile  $file    The file to be written.
	 * @param   mixed       $data    The value to write.
	 * @param   integer                     $options Bitmask consisting of
	 *                                  JSON_HEX_QUOT, JSON_HEX_TAG, JSON_HEX_AMP, JSON_HEX_APOS, JSON_NUMERIC_CHECK, JSON_FORCE_OBJECT.
	 *                                  JSON_NUMERIC_CHECK option was added in PHP 5.3.3
	 *                                  JSON_BIGINT_AS_STRING, JSON_PRETTY_PRINT, JSON_UNESCAPED_SLASHES, JSON_UNESCAPED_UNICODE were added in PHP 5.4
	 *
	 * @return  mixed  The number of bytes that were written to the file, or FALSE on failure.
	 *
	 * @link    http://php.net/manual/en/function.file-put-contents.php
	 * @link    http://php.net/manual/en/function.json-encode.php
	 *
	 * @since   12.2
	 */
	public static function push(JFilesystemElementFile $file, $data, $options = 0)
	{
		$file->open('w');
		$return = 0;
		foreach ($data as $json)
		{
			$nb_bytes = static::write($file, $json, $options);
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
