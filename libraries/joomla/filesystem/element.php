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
 * A File System Element handling class
 *
 * @property  int     $owner        The owner id
 * @property  int     $group        The group id
 * @property  int     $permissions  The element permissions
 * @property  string  $path         The path
 * @property  string  $name         The name
 * @property  string  $dirpath      The directory path
 * @property  string  $basename     The base name
 * @property  string  $extension    The extension
 * @property  string  $link         The link
 *
 * @property-write  string  $permissions  The element permissions using a string representation
 *
 * @property-read  bool         $exists             Tell if the element exists.
 * @property-read  bool         $is_dir             Tell if the element is an existing directory
 * @property-read  bool         $is_file            Tell if the element is an existing file
 * @property-read  bool         $is_link            Tell if the element is an existing link
 * @property-read  bool         $is_readable        Tell if the element is writable
 * @property-read  bool         $is_writable        Tell if the element is readable
 * @property-read  int          $access_time        The access time
 * @property-read  int          $change_time        The change time
 * @property-read  int          $modification_time  The modification time
 * @property-read  int          $size               The element size
 * @property-read  string       $fullpath           The full path
 * @property-read  JFilesystem  $system             The file system
 *
 * @package     Joomla.Platform
 * @subpackage  FileSystem
 *
 * @since       12.1
 */
abstract class JFilesystemElement
{
	/**
	 * @var  array  Array of instances
	 *
	 * @since   12.1
	 */
	protected static $instances = array();

	/**
	 * @var  string  Element signature
	 *
	 * @since   12.1
	 */
	private $_signature;

	/**
	 * @var  string  Element path
	 *
	 * @since   12.1
	 */
	private $_path;

	/**
	 * @var  string  Element full path
	 *
	 * @since   12.1
	 */
	private $_fullpath;

	/**
	 * @var  JFilesystem  Element file system
	 *
	 * @since   12.1
	 */
	private $_system;

	/**
	 * @var  bool  Tell if the error are handled
	 */
	private $_errors_handled;

	/**
	 * @var  array  Default context options
	 */
	private $_default_options;

	/**
	 * @var  array  Default context params
	 */
	private $_default_params;

	/**
	 * Constructor
	 *
	 * @param   string       $path       Element path.
	 * @param   JFilesystem  $system     Element file system
	 * @param   string       $signature  Signature
	 *
	 * @since   12.1
	 */
	protected function __construct($path, $fullpath, JFilesystem $system, $signature)
	{
		$this->_path = $path;
		$this->_system = $system;
		$this->_fullpath = $system->prefix . $path;
		$this->_signature = $signature;
	}

	/**
	 * Magic getter.
	 *
	 * @param   string  $property  The property name.
	 *
	 * @return  mixed  The property value.
	 *
	 * @throw   Exception
	 *
	 * @since   12.1
	 */
	public function __get($property)
	{
		switch ($property)
		{
			case 'name':
			case 'basename':
			case 'dirpath':
			case 'extension':
			case 'realpath':
				return $this->{'get' . JStringNormalise::toCamelCase($property)}();
				break;
			case 'exists':
			case 'access_time':
			case 'change_time':
			case 'modification_time':
			case 'group':
			case 'owner':
			case 'permissions':
			case 'size':
			case 'is_file':
			case 'is_dir':
			case 'is_link':
			case 'is_readable':
			case 'is_writable':
			case 'link':
				clearstatcache(true, $this->_fullpath);
				return $this->callHandleError('get' . JStringNormalise::toCamelCase($property));
				break;
			case 'system':
			case 'path':
			case 'fullpath':
				return $this->{'_' . $property};
				break;
			default:
				throw new InvalidArgumentException(sprintf('Undefined property: %s::%s', get_called_class(), $property));
				break;
		}
	}

	/**
	 * Magic setter.
	 *
	 * @param   string  $property  The property name.
	 * @param   mixed   $value     The property value.
	 *
	 * @return  void.
	 *
	 * @throw   Exception
	 *
	 * @link    http://php.net/manual/en/function.clearstatcache.php
	 *
	 * @since   12.1
	 */
	public function __set($property, $value)
	{
		switch ($property)
		{
			case 'group':
			case 'owner':
			case 'permissions':
			case 'link':
			case 'path':
			case 'dirpath':
			case 'name':
			case 'basename':
			case 'extension':
				$return = $this->callHandleError('set' . JStringNormalise::toCamelCase($property), array($value));
				clearstatcache(true, $this->_fullpath);
				return $return;
				break;
			default:
				throw new InvalidArgumentException(sprintf('Undefined property: %s::%s', get_called_class(), $property));
				break;
		}
	}

	/**
	 * Magic call.
	 *
	 * @param   string  $method  The called method.
	 * @param   array   $args    The array of arguments passed to the method.
	 *
	 * @return  mixed  The result returned by the called method.
	 *
	 * @throw   Exception
	 *
	 * @since   12.1
	 */
	public function __call($method, $args)
	{
		throw new InvalidArgumentException(sprintf('Call to undefined method: %s::%s()', get_called_class(), $method));
	}

	/**
	 * To string
	 *
	 * @return  string  Return full path
	 *
	 * @since   12.1
	 */
	public function __toString()
	{
		return $this->_path;
	}

	/**
	 * Call a method while catching all emitted errors.
	 *
	 * @param   string  $method  The called method.
	 * @param   array   $args    The array of arguments passed to the method.
	 *
	 * @return  mixed  The returned value.
	 *
	 * @throw   Exception
	 *
	 * @since   12.1
	 */
	public function callHandleError($method, array $args = array())
	{
		if ($this->_errors_handled)
		{
			// @codeCoverageIgnoreStart
			return call_user_func_array(array($this, $method), $args);

			// @codeCoverageIgnoreEnd
		}
		else
		{
			$this->_errors_handled = true;

			// Transform notices and warning by exceptions
			try
			{
				set_error_handler(array(__CLASS__, 'errorHandler'));
				$return = call_user_func_array(array($this, $method), $args);
				restore_error_handler();
				$this->_errors_handled = false;
				return $return;
			}
			catch (Exception $e)
			{
				restore_error_handler();
				$this->_errors_handled = false;
				throw $e;
			}
		}
	}

	/**
	 * Catch an error and throw an exception.
	 *
	 * @param   integer  $errno    The level of the error raised
	 * @param   string   $errstr   The error message
	 * @param   string   $errfile  The filename that the error was raised in
	 * @param   integer  $errline  The line number the error was raised
	 *
	 * @return  void
	 *
	 * @link    http://php.net/manual/en/function.set-error-handler.php
	 *
	 * @throw   ErrorException
	 */
	public static function errorHandler($errno , $errstr, $errfile, $errline)
	{
		throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
	}

	/**
	 * Get an instance of a element
	 *
	 * @param   string|null  $path    The element path.
	 * @param   JFilesystem  $system  The file system.
	 *
	 * @return  JFilesystemElement  The file system element instance.
	 *
	 * @throw   RuntimeException
	 *
	 * @since   12.1
	 */
	public static function getInstance($path, JFilesystem $system = null)
	{
		// If file system is null, use the default file system
		if (null === $system)
		{
			$system = JFilesystem::getInstance();
		}

		// Set signature
		$path = static::cleanpath($path);
		$signature = md5($path . spl_object_hash($system));

		if (isset(static::$instances[$signature]))
		{
			$element = static::$instances[$signature];
			if (!is_a($element, get_called_class()))
			{
				static::$instances[$signature] = new static($path, $system, $signature);
			}
		}
		else
		{
			static::$instances[$signature] = new static($path, $system, $signature);
		}
		return static::$instances[$signature];
	}

	/**
	 * Returns trailing name component of element
	 *
	 * @return  string  The base name.
	 *
	 * @link    http://php.net/manual/en/function.basename.php
	 *
	 * @since   12.1
	 */
	protected function getName()
	{
		return basename($this->_path);
	}

	/**
	 * Returns the base name of element (name of file without extension)
	 *
	 * @return  string  The base name.
	 *
	 * @link    http://php.net/manual/en/function.basename.php
	 *
	 * @since   12.1
	 */
	protected function getBasename()
	{
		$extension = $this->extension;
		if (empty($extension))
		{
			return basename($this->_path);
		}
		else
		{
			return basename($this->_path, '.' . $extension);
		}
	}

	/**
	 * Returns the directory path of element
	 *
	 * @return  string  The directory path.
	 *
	 * @link    http://php.net/manual/en/function.dirname.php
	 *
	 * @since   12.1
	 */
	protected function getDirpath()
	{
		return dirname($this->_path);
	}

	/**
	 * Returns the extention of element
	 *
	 * @return  string  The extension.
	 *
	 * @link    http://php.net/manual/en/function.pathinfo.php
	 *
	 * @since   12.1
	 */
	protected function getExtension()
	{
		return pathinfo($this->_path, PATHINFO_EXTENSION);
	}

	/**
	 * Returns the real path of element
	 *
	 * @return  string  The real path.
	 *
	 * @link    http://php.net/manual/en/function.realpath.php
	 *
	 * @since   12.1
	 */
	protected function getRealpath()
	{
		return realpath($this->_fullpath);
	}

	/**
	 * Checks whether an element exists
	 *
	 * @return  bool  TRUE if the element exists, FALSE otherwise.
	 *
	 * @link    http://php.net/manual/en/function.file-exists.php
	 *
	 * @since   12.1
	 */
	protected function getExists()
	{
		return file_exists($this->_fullpath);
	}

	/**
	 * Gets last access time of an element
	 *
	 * @return  int|false  Returns the time the element was last accessed, or FALSE on failure. The time is returned as a Unix timestamp.
	 *
	 * @link    http://php.net/manual/en/function.fileatime.php
	 *
	 * @since   12.1
	 */
	protected function getAccessTime()
	{
		return fileatime($this->_fullpath);
	}

	/**
	 * Gets change time of an element
	 *
	 * @return  int|false  Returns the time the element was last changed, or FALSE on failure. The time is returned as a Unix timestamp.
	 *
	 * @link    http://php.net/manual/en/function.filectime.php
	 *
	 * @since   12.1
	 */
	protected function getChangeTime()
	{
		return filectime($this->_fullpath);
	}

	/**
	 * Gets modification time of an element
	 *
	 * @return  int|false  Returns the time the element was last modified, or FALSE on failure. The time is returned as a Unix timestamp.
	 *
	 * @link    http://php.net/manual/en/function.filemtime.php
	 *
	 * @since   12.1
	 */
	protected function getModificationTime()
	{
		return filemtime($this->_fullpath);
	}

	/**
	 * Gets the element group
	 *
	 * @return  int|false  Returns the group ID of the element, or FALSE if an error occurs.
	 *
	 * @link    http://php.net/manual/en/function.filegroup.php
	 *
	 * @since   12.1
	 */
	protected function getGroup()
	{
		return filegroup($this->_fullpath);
	}

	/**
	 * Gets or sets the element owner
	 *
	 * @return  int|false  Returns the owner ID of the element, or FALSE if an error occurs.
	 *
	 * @link    http://php.net/manual/en/function.fileowner.php
	 *
	 * @since   12.1
	 */
	protected function getOwner()
	{
		return fileowner($this->_fullpath);
	}

	/**
	 * Gets the element permissions
	 *
	 * @return  int|false  Returns the permissions of the element, or FALSE if an error occurs.
	 *
	 * @link    http://php.net/manual/en/function.fileperms.php
	 *
	 * @since   12.1
	 */
	protected function getPermissions()
	{
		$return = fileperms($this->_fullpath);
		if ($return === false)
		{
			// @codeCoverageIgnoreStart
			return false;

			// @codeCoverageIgnoreEnd
		}
		else
		{
			return $return & 0777;
		}
	}

	/**
	 * Gets element size
	 *
	 * @return  int|false  Returns the size of the file in bytes, or FALSE if an error occurs.
	 *
	 * @link    http://php.net/manual/en/function.filesize.php
	 *
	 * @since   12.1
	 */
	protected function getSize()
	{
		return filesize($this->_fullpath);
	}

	/**
	 * Tell if the element is an existing directory
	 *
	 * @return  bool  Returns TRUE if the element is an existing directory, FALSE otherwise.
	 *
	 * @link    http://php.net/manual/en/function.is-dir.php
	 *
	 * @since   12.1
	 */
	protected function getIsDir()
	{
		return is_dir($this->_fullpath);
	}

	/**
	 * Tell if the element is an existing file
	 *
	 * @return  bool  Returns TRUE if the element is an existing file, FALSE otherwise.
	 *
	 * @link    http://php.net/manual/en/function.is-file.php
	 *
	 * @since   12.1
	 */
	protected function getIsFile()
	{
		return is_file($this->_fullpath);
	}

	/**
	 * Tell if the element is an existing link
	 *
	 * @return  bool  Returns TRUE if the element is an existing link, FALSE otherwise.
	 *
	 * @link    http://php.net/manual/en/function.is-link.php
	 *
	 * @since   12.1
	 */
	protected function getIsLink()
	{
		return is_link($this->_fullpath);
	}

	/**
	 * Tells whether an element exists and is readable
	 *
	 * @return  bool  Returns TRUE if the element exists and is readable, FALSE otherwise. 
	 *
	 * @link    http://php.net/manual/en/function.is-readable.php
	 *
	 * @since   12.1
	 */
	protected function getIsReadable()
	{
		return is_readable($this->_fullpath);
	}

	/**
	 * Tells whether an element exists and is writable
	 *
	 * @return  bool  Returns TRUE if the element exists and is writable, FALSE otherwise. 
	 *
	 * @link    http://php.net/manual/en/function.is-writable.php
	 *
	 * @since   12.1
	 */
	protected function getIsWritable()
	{
		return is_writable($this->_fullpath);
	}

	/**
	 * Returns the symbolic link
	 *
	 * @return  JFilesystemElement  The symbolink link. 
	 *
	 * @link    http://php.net/manual/en/function.readlink.php
	 *
	 * @since   12.1
	 */
	protected function getLink()
	{
		return readlink($this->_fullpath);
	}

	/**
	 * Sets the element group
	 *
	 * @param   int  $group  The new group ID
	 *
	 * @return  int|false  Returns the group ID of the element, or FALSE if an error occurs.
	 *
	 * @link    http://php.net/manual/en/function.filegroup.php
	 * @link    http://php.net/manual/en/function.chgrp.php
	 *
	 * @since   12.1
	 */
	protected function setGroup($group)
	{
		$return = filegroup($this->_fullpath);
		if (false === $return)
		{
			// @codeCoverageIgnoreStart
			return false;

			// @codeCoverageIgnoreEnd
		}
		else
		{
			if (chgrp($this->_fullpath, $group))
			{
				return $return;
			}
			else
			{
				// @codeCoverageIgnoreStart
				return false;

				// @codeCoverageIgnoreEnd
			}
		}
	}

	/**
	 * Sets the element owner
	 *
	 * @param   int  $owner  The new owner ID
	 *
	 * @return  int|false  Returns the owner ID of the element, or FALSE if an error occurs.
	 *
	 * @link    http://php.net/manual/en/function.fileowner.php
	 * @link    http://php.net/manual/en/function.chown.php
	 *
	 * @since   12.1
	 */
	protected function setOwner($owner)
	{
		$return = fileowner($this->_fullpath);
		if (false === $return)
		{
			// @codeCoverageIgnoreStart
			return false;

			// @codeCoverageIgnoreEnd
		}
		else
		{
			if (chown($this->_fullpath, $owner))
			{
				return $return;
			}
			else
			{
				// @codeCoverageIgnoreStart
				return false;

				// @codeCoverageIgnoreEnd
			}
		}
	}

	/**
	 * Sets the element permissions
	 *
	 * @param   int|string  $permissions  The new permissions
	 *
	 * @return  bool  TRUE on success, or FALSE if an error occurs.
	 *
	 * @link    http://php.net/manual/en/function.fileperms.php
	 * @link    http://php.net/manual/en/function.chmod.php
	 *
	 * @since   12.1
	 */
	protected function setPermissions($permissions)
	{
		return $this->affectPermissions($permissions);
	}

	/**
	 * Sets the element permissions
	 *
	 * @param   int|string  $permissions  The new permissions
	 *
	 * @return  bool  TRUE on success, or FALSE if an error occurs.
	 *
	 * @link    http://php.net/manual/en/function.fileperms.php
	 * @link    http://php.net/manual/en/function.chmod.php
	 *
	 * @since   12.1
	 */
	protected function affectPermissions($permissions)
	{
		clearstatcache(true, $this->_fullpath);
		$mode = fileperms($this->_fullpath);
		if ($mode === false)
		{
			// @codeCoverageIgnoreStart
			return false;

			// @codeCoverageIgnoreEnd
		}
		else
		{
			$mode = $mode & 0777;
			if (is_numeric($permissions))
			{
				$mode = $permissions;
			}
			else
			{
				$operations = explode(',', $permissions);
				foreach ($operations as $operation)
				{
					$mode = static::newPermissions($mode, $operation);
				}
			}
			return chmod($this->_fullpath, $mode);
		}
	}

	/**
	 * Transformation of string permissions into integer ones
	 *
	 * @param   int     $permissions  The actual permissions
	 * @param   string  $operation    The modification operations
	 *
	 * @return  int  The new permissions
	 *
	 * @since   12.1
	 */
	protected static function newPermissions($permissions, $operation)
	{
		if (preg_match('#^([ugoa]+)([\+=-])([rwxX]+|u|g|o|\-)$#', $operation, $matches))
		{
			// Get the targets (0 = 'other', 3 = 'group', 6 = 'user')
			$targets = array();
			if (strpos($matches[1], 'a') !== false)
			{
				$targets = array(0, 3, 6);
			}
			else
			{
				if (strpos($matches[1], 'u') !== false)
				{
					$targets[] = 6;
				}
				if (strpos($matches[1], 'g') !== false)
				{
					$targets[] = 3;
				}
				if (strpos($matches[1], 'o') !== false)
				{
					$targets[] = 0;
				}
			}

			// Get the operator
			$op = $matches[2];

			// Get the value: one of {0,1,2,3,4,5,6,7}
			if ($matches[3] == 'u')
			{
				$values = ($permissions & 0700) >> 6;
			}
			elseif ($matches[3] == 'g')
			{
				$values = ($permissions & 0070) >> 3;
			}
			elseif ($matches[3] == 'o')
			{
				$values = $permissions & 0007;
			}
			else
			{
				$values = 0;
				if (strpos($matches[3], 'r') !== false)
				{
					$values = $values | 4;
				}
				if (strpos($matches[3], 'w') !== false)
				{
					$values = $values | 2;
				}
				if (strpos($matches[3], 'x') !== false)
				{
					$values = $values | 1;
				}
			}

			// Apply operation for each target
			foreach ($targets as $target)
			{
				switch ($op)
				{
					case '=':
						$permissions = $permissions & ~ (7 << $target) | ($values << $target);
						break;
					case '+':
						$permissions = $permissions | ($values << $target);
						break;
					case '-':
						$permissions = $permissions & ~ ($values << $target);
						break;
				}
			}
		}
		else
		{
			throw new InvalidArgumentException(sprintf('The permissions %s are not correct', $operation));
		}
		return $permissions;
	}

	/**
	 * Sets the element link
	 *
	 * @param   string  $link  The linked element
	 *
	 * @return  bool  TRUE on success, or FALSE if an error occurs.
	 *
	 * @link    http://php.net/manual/en/function.symlink.php
	 *
	 * @since   12.1
	 */
	protected function setLink($link)
	{
		return symlink($this->_system->prefix . $link, $this->_fullpath);
	}

	/**
	 * Change path
	 *
	 * @param   string  $path  The path name.
	 *
	 * @return  bool  TRUE on success, or FALSE on failure.
	 *
	 * @since   12.1
	 */
	protected function setPath($path)
	{
		$path = static::cleanpath($path);
		$fullpath = $this->system->prefix . $path;
		if (!$this->exists || rename($this->fullpath, $fullpath, $this->system->context))
		{
			$this->_fullpath = $fullpath;
			$this->_path = $path;
			unset(static::$instances[$this->_signature]);
			$this->_signature = md5($path . spl_object_hash($this->_system));
			static::$instances[$this->_signature] = $this;
			return true;
		}
		else
		{
			// @codeCoverageIgnoreStart
			return false;

			// @codeCoverageIgnoreEnd
		}
	}

	/**
	 * Change directory path
	 *
	 * @param   string  $dirpath  The directory  path name.
	 *
	 * @return  bool  TRUE on success, or FALSE on failure.
	 *
	 * @since   12.1
	 */
	protected function setDirpath($dirpath)
	{
		return $this->setPath($dirpath . '/' . $this->name);
	}

	/**
	 * Change name
	 *
	 * @param   string  $name  The base name.
	 *
	 * @return  bool  TRUE on success, or FALSE on failure.
	 *
	 * @since   12.1
	 */
	protected function setName($name)
	{
		return $this->setPath($this->dirpath . '/' . $name);
	}

	/**
	 * Change basename
	 *
	 * @param   string  $basename  The base name.
	 *
	 * @return  bool  TRUE on success, or FALSE on failure.
	 *
	 * @since   12.1
	 */
	protected function setBasename($basename)
	{
		$extension = $this->extension;
		if (empty($extension))
		{
			return $this->setPath($this->dirpath . '/' . $basename);
		}
		else
		{
			return $this->setPath($this->dirpath . '/' . $basename . '.' . $extension);
		}
	}

	/**
	 * Change extension
	 *
	 * @param   string  $extension  The base name.
	 *
	 * @return  bool  TRUE on success, or FALSE on failure.
	 *
	 * @since   12.1
	 */
	protected function setExtension($extension)
	{
		if (empty($extension))
		{
			return $this->setPath($this->dirpath . '/' . $this->basename);
		}
		else
		{
			return $this->setPath($this->dirpath . '/' . $this->basename . '.' . $extension);
		}
	}

	/**
	 * Clean path
	 *
	 * @param   string  $path  The path.
	 *
	 * @return  void.
	 *
	 * @since   12.1
	 */
	protected static function cleanpath($path)
	{
		// Get absolute path
		$parts = explode('/', str_replace('\\', '/', $path));
		$absolutes = array();
		foreach ($parts as $part)
		{
			switch ($part)
			{
				case '':
					if (empty($absolutes))
					{
						$absolutes[] = '';
					}
					break;
				case '.':
					break;
				case '..':
					if (empty($absolutes) || current($absolutes) == '..')
					{
						$absolutes[] = '..';
					}
					else
					{
						array_pop($absolutes);
					}
					break;
				default:
					$absolutes[] = $part;
			}
		}

		// Get clean path
		return implode('/', $absolutes);
	}
}
