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
 * A file system handling class
 *
 * @property-read  string    $prefix   The file system prefix.
 * @property-read  resource  $context  The stream context.
 *
 * @package     Joomla.Platform
 * @subpackage  FileSystem
 *
 * @since       12.2
 */
class JFilesystem
{
	/**
	 * @var  array  Array of instances
	 *
	 * @since   12.2
	 */
	protected static $instances = array();

	/**
	 * @var  string  $prefix  The file system prefix.
	 *
	 * @since   12.2
	 */
	private $_prefix;

	/**
	 * @var  resource  $context  The stream context.
	 *
	 * @since   12.2
	 */
	private $_context;

	/**
	 * Constructor
	 *
	 * @param   string  $prefix   The file system prefix.
	 * @param   array   $options  The stream context options.
	 * @param   array   $params   The stream context params.
	 *
	 * @link    http://fr.php.net/manual/en/function.stream-context-create.php
	 *
	 * @since   12.2
	 */
	protected function __construct($prefix,  $options,  $params)
	{
		$this->_prefix = $prefix;
		$this->_context = stream_context_create($options, $params);
	}

	/**
	 * Magic getter.
	 *
	 * <code>
	 * // FTP file system using options and parameters
	 * $ftp_filesystem = JFilesystem::getInstance(
	 *     'ftp://user:password@example.com',
	 *     array('ftp' => array('overwrite' => true)),
	 *     array('notification' => 'my_stream_notification_callback')
	 * );
	 *
	 * // assign 'ftp://user:password@example.com' to $prefix
	 * $prefix = $ftp_file_system->prefix;
	 *
	 * // assign the stream context to $context
	 * $context = $ftp_file_system->context;
	 * </code>
	 *
	 * @param   string  $property  The property name.
	 *
	 * @return  mixed  The property value.
	 *
	 * @throw   InvalidArgumentException
	 *
	 * @since   12.2
	 */
	public function __get($property)
	{
		switch ($property)
		{
			case 'prefix':
				return $this->_prefix;
				break;
			case 'context':
				return $this->_context;
				break;
			default:
				throw new InvalidArgumentException(sprintf('Undefined property: %s::%s', get_called_class(), $property));
				break;
		}
	}

	/**
	 * Get an instance of a file system.
	 *
	 * <code>
	 * // classical file system
	 * $filesystem = JFilesystem::getInstance();
	 *
	 * // FTP file system using options and parameters
	 * $ftp_filesystem = JFilesystem::getInstance(
	 *     'ftp://user:password@example.com',
	 *     array('ftp' => array('overwrite' => true)),
	 *     array('notification' => 'my_stream_notification_callback')
	 * );
	 * </code>
	 *
	 * @param   string  $prefix   The file system prefix.
	 * @param   array   $options  The stream context options.
	 * @param   array   $params   The stream context params.
	 *
	 * @return  JFilesystem  The file system instance.
	 *
	 * @since   12.2
	 */
	public static function getInstance($prefix = '', array $options = array(), array $params = array())
	{
		// Get file system signature
		$signature = md5($prefix . serialize($options) . serialize($params));

		// Create the file system if it does not exist
		if (!isset(static::$instances[$signature]))
		{
			static::$instances[$signature] = new static($prefix, $options, $params);
		}

		// Return the file system
		return static::$instances[$signature];
	}

	/**
	 * Get an instance of a file
	 *
	 * <code>
	 * // Get a file
	 * $file = JFilesystem::getInstance()->getFile('/path/to/file');
	 *
	 * // Get a file using use_include_path
	 * $file = JFilesystem::getInstance()->getFile('searchfile', 'r', true);
     *
	 * // Get a file and open it for writing
	 * $file = JFilesystem::getInstance()->getFile('/path/to/writefile', 'w');
	 * </code>
	 *
	 * @param   string   $path              The file path.
	 * @param   mixed    $mode              The file opening mode.
	 * @param   boolean  $use_include_path  TRUE if you want to search for the file in the include_path. 
	 *
	 * @return  JFilesystemElementFile  The file instance.
	 *
	 * @throw   RuntimeException
	 *
	 * @see     JFilesystemElement::getInstance
	 *
	 * @since   12.2
	 */
	public function getFile($path, $mode = null, $use_include_path = false)
	{
		return JFilesystemElementFile::getInstance($path, $this, $mode, $use_include_path);
	}

	/**
	 * Get an instance of a directory
	 *
	 * <code>
	 * // Get a directory
	 * $directory = JFilesystem::getInstance()->getDirectory('/path/to/mydirectory');
	 * </code>
	 *
	 * @param   string  $path  The directory path.
	 *
	 * @return  JFilesystemElementDirectory  The directory instance.
	 *
	 * @throw   RuntimeException
	 *
	 * @see     JFilesystemElement::getInstance
	 *
	 * @since   12.2
	 */
	public function getDirectory($path)
	{
		return JFilesystemElementDirectory::getInstance($path, $this);
	}
}
