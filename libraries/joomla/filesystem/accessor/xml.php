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
 * A File system accessor for reading/writing XML contents
 *
 * @package     Joomla.Platform
 * @subpackage  FileSystem
 *
 * @since       12.2
 */
abstract class JFilesystemAccessorXml
{
	/**
	 * Pull entire file into a n XML object
	 *
	 * @param   JFilesystemElementFile  $file        The file to be read.
	 * @param   string                  $class_name  Object class.
	 * @param   integer                 $options     Additional Libxml parameters. .
	 * @param   string                  $ns          Namespace prefix or URI.
	 * @param   boolean                 $is_prefix   TRUE if ns is a prefix, FALSE if it's a URI.
	 *
	 * @return  mixed  The xml or FALSE on failure.
	 *
	 * @link    http://php.net/manual/en/function.simplexml-load-file.php
	 *
	 * @since   12.2
	 */
	public static function pull(JFilesystemElementFile $file, $class_name = 'SimpleXMLElement', $options = 0, $ns = '', $is_prefix = false)
	{
		return simplexml_load_file($file->fullpath, $class_name, $options, $ns, $is_prefix);
	}

	/**
	 * Push an xml data to a file
	 *
	 * @param   JFilesystemElementFile  $file  The file to be written.
	 * @param   SimpleXMLElement        $xml   The xml to write.
	 *
	 * @return  mixed  The number of bytes that were written to the file, or FALSE on failure.
	 *
	 * @link    http://php.net/manual/en/function.file-put-contents.php
	 * @link    http://php.net/manual/en/simplexmlelement.asxml.php
	 *
	 * @since   12.2
	 */
	public static function push(JFilesystemElementFile $file, SimpleXMLElement $xml)
	{
		return file_put_contents($file->fullpath, $xml->asXml());
	}
}
