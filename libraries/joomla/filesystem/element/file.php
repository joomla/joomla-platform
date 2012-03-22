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
 * A File handling class
 *
 * @property  string|null  $mode      The file opening mode.
 * @property  int          $position  The file position.
 *
 * @property-write  string|array|resource  $contents  The file content.
 *
 * @property-read  bool                         $use_include_path  Tells if the file system use include path.
 * @property-read  bool                         $opened            Tells if the file is opened.
 * @property-read  bool                         $eof               Tells if the file is at end-of-file.
 * @property-read  ressource                    $handle            The file handle.
 * @property-read  string                       $contents          The file content.
 * @property-read  JFilesystemElementDirectory  $directory         The directory.
 *
 * @method          JFilesystemElementFile open(string $mode)                         open the file
 * @method          JFilesystemElementFile close()                                    close the file
 * @method                           mixed readXXX(...)                               read content using accessor
 * @method                       int|FALSE writeXXX(...)                              write content using accessor
 * @method                            bool flush()                                    flush the file
 * @method                            bool truncate(int $size)                        truncate the file
 *
 * @method                            bool create()                                   create the file
 * @method                            bool delete()                                   delete the file
 *
 * @method                       int|FALSE copy(JFilesystemElement $dest)             copy the file
 * @method                       int|FALSE copyFromFile(JFilesystemElementFile $src)  copy from a file
 *
 * @method                           mixed pullXXX(...)                               pull content using accessor
 * @method                       int|FALSE pushXXX(...)                               push content using accessor
 * @method  JFilesystemElementFileIterator iterateXXX(...)                            iterate content using accessor
 *
 * @method                        resource prependFilter(string, int, mixed)          prepend a filter
 * @method                        resource appendFilter(string, int, mixed)           prepend a filter
 * @method                            bool removeFilter(resource)                     remove a filter
 *
 * @package     Joomla.Platform
 * @subpackage  FileSystem
 *
 * @since       12.1
 */
class JFilesystemElementFile extends JFilesystemElement
{
	/**
	 * @var  bool  Used by iterators on file
	 */
	public $valid = true;

	/**
	 * @var  resource|null  The file handler or null if it not opened
	 */
	private $_handle;

	/**
	 * @var  bool  Tell the file system to use include path
	 */
	private $_use_include_path;

	/**
	 * @var  string  The file opening mode
	 */
	private $_mode;

	/**
	 * Constructor
	 *
	 * @param   string       $path       Element path.
	 * @param   JFilesystem  $system     Element file system
	 * @param   string       $signature  Signature
	 *
	 * @since   12.1
	 */
	protected function __construct($path, JFilesystem $system, $signature)
	{
		$fullpath = $system->prefix . $path;
		if (file_exists($fullpath) && is_dir($fullpath))
		{
			throw new RuntimeException(
				sprintf('%s is already a directory', $fullpath)
			);
		}
		parent::__construct($path, $fullpath, $system, $signature);
	}

	/**
	 * Destructor
	 *
	 * @since   12.1
	 */
	public function __destruct()
	{
		// @codeCoverageIgnoreStart
		if (null !== $this->_handle)
		{
			$this->callHandleError('close');
		}
		// @codeCoverageIgnoreEnd
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
			case 'mode':
			case 'use_include_path':
			case 'handle':
				return $this->{'_' . $property};
				break;
			case 'opened':
				return $this->_handle !== null;
				break;
			case 'directory':
				return $this->system->getDirectory($this->dirpath);
				break;
			case 'eof':
			case 'position':
				return $this->callHandleError('get' . JStringNormalise::toCamelCase($property));
				break;
			case 'contents':
				return $this->callHandleError('pull', array('Contents', array($this)));
				break;
			default:
				return parent::__get($property);
				break;
		}
	}

	/**
	 * Magic setter.
	 *
	 * @param   string  $property  The property name.
	 * @param   mixed   $value     The property value.
	 *
	 * @return  void  
	 *
	 * @throw   Exception
	 *
	 * @since   12.1
	 */
	public function __set($property, $value)
	{
		switch ($property)
		{
			case 'mode':
				return $this->callHandleError('open', array($value));
				break;
			case 'position':
				return $this->callHandleError('set' . JStringNormalise::toCamelCase($property), array($value));
				break;
			case 'contents':
				return $this->callHandleError('push', array('Contents', array($this, $value)));
				break;
			default:
				return parent::__set($property, $value);
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
		switch ($method)
		{
			case 'open':
			case 'close':
			case 'flush':
			case 'truncate':
			case 'copy':
			case 'copyFromFile':
			case 'delete':
			case 'create':
			case 'appendFilter':
			case 'prependFilter':
			case 'removeFilter':
				return $this->callHandleError($method, $args);
				break;
			default:
				$parts = JString::splitCamelCase($method);
				switch ($parts[0])
				{
					case 'read':
					case 'write':
					case 'pull':
					case 'push':
					case 'iterate':
						array_unshift($args, $this);
						return $this->callHandleError($parts[0], array(implode(array_slice($parts, 1)), $args));
						break;
					default:
						return parent::__call($method, $args);
						break;
				}
				break;
		}
	}

	/**
	 * Get an instance of a element
	 *
	 * @param   string|null  $path              The file path.
	 * @param   JFilesystem  $system            The file system.
	 * @param   string|null  $mode              The file opening mode.
	 * @param   bool         $use_include_path  TRUE if you want to search for the file in the include_path. 
	 *
	 * @return  JFilesystemElement  The file system element instance.
	 *
	 * @throw   RuntimeException
	 *
	 * @since   12.1
	 */
	public static function getInstance($path, JFilesystem $system = null, $mode = null, $use_include_path = false)
	{
		return parent::getInstance($path, $system)->initialise($mode, $use_include_path);
	}

	/**
	 * Initialise the object
	 *
	 * @param   string|null  $mode              The file opening mode.
	 * @param   bool         $use_include_path  TRUE if you want to search for the file in the include_path. 
	 *
	 * @return  JFilesystemElementFile  $this for chaining
	 *
	 * @since   12.1
	 */
	protected function initialise($mode = null, $use_include_path = false)
	{
		$this->_use_include_path = (bool) $use_include_path;
		return $this->callHandleError('open', array($mode));
	}

	/**
	 * Open the file
	 *
	 * @param   string|null  $mode  The opening mode
	 *
	 * @return  JFilesystemElementFile  $this for chaining
	 *
	 * @link    http://php.net/manual/en/function.fopen.php
	 * @link    http://php.net/manual/en/function.fclose.php
	 *
	 * @since   12.1
	 */
	protected function open($mode)
	{
		// Handle new mode
		if ($mode !== $this->_mode)
		{
			// Close the file if already opened
			if (null !== $this->_handle)
			{
				$this->close();
			}

			// Change the mode
			$this->_mode = $mode;

			// Open the file
			if (null !== $mode)
			{
				$this->_handle = fopen($this->fullpath, $mode, $this->_use_include_path, $this->system->context);
			}
		}

		// Return $this for chaining
		return $this;
	}

	/**
	 * Close the file
	 *
	 * @return  JFilesystemElementFile  $this for chaining
	 *
	 * @link    http://php.net/manual/en/function.fclose.php
	 *
	 * @since   12.1
	 */
	protected function close()
	{
		fclose($this->_handle);
		$this->_handle = null;
		$this->_mode = null;
		return $this;
	}

	/**
	 * Flushes the output to the file
	 *
	 * @return  bool  TRUE on success or FALSE on failure.
	 *
	 * @link    http://php.net/manual/en/function.fflush.php
	 *
	 * @since   12.1
	 */
	protected function flush()
	{
		return fflush($this->_handle);
	}

	/**
	 * Read contents from the file
	 *
	 * @param   string  $name  The reader name.
	 * @param   array   $args  Array of args
	 *
	 * @return  mixed|NULL|FALSE  The data read, or NULL or FALSE on failure.
	 *
	 * @see    JFilesystemAccessor::read
	 *
	 * @since   12.1
	 */
	protected function read($name, $args)
	{
		return JFilesystemAccessor::read($name, $args);
	}

	/**
	 * Write contents to the file
	 *
	 * @param   string  $name  The writer name.
	 * @param   array   $args  Array of args
	 *
	 * @return  int|FALSE  The number of bytes written, or FALSE on failure.
	 *
	 * @see    JFilesystemAccessor::write
	 *
	 * @since   12.1
	 */
	protected function write($name, $args)
	{
		return JFilesystemAccessor::write($name, $args);
	}

	/**
	 * Pull the contents from the file
	 *
	 * @param   string  $name  The puller name.
	 * @param   array   $args  Array of args
	 *
	 * @return  mixed|NULL|FALSE  The contents, or NULL or FALSE on failure.
	 *
	 * @see    JFilesystemAccessor::pull
	 *
	 * @since   12.1
	 */
	protected function pull($name, $args)
	{
		return JFilesystemAccessor::pull($name, $args);
	}

	/**
	 * Push the contents to the file
	 *
	 * @param   string  $name  The pusher name.
	 * @param   array   $args  Array of args
	 *
	 * @return  int|FALSE  The number of bytes that were written to the file, or FALSE on failure.
	 *
	 * @see    JFilesystemAccessor::push
	 *
	 * @since   12.1
	 */
	protected function push($name, $args)
	{
		return JFilesystemAccessor::push($name, $args);
	}

	/**
	 * Return a new file iterator
	 *
	 * @param   string  $name  The iterator name.
	 * @param   array   $args  Array of args
	 *
	 * @return  JFilesystemElementFileIterator
	 *
	 * @since   12.1
	 */
	protected function iterate($name, $args)
	{
		return new JFilesystemElementFileIterator($name, $args);
	}

	/**
	 * Truncates the file to a given length
	 *
	 * @param   int  $size  The truncated size
	 *
	 * @return  bool  TRUE on success, or FALSE on failure.
	 *
	 * @link    http://php.net/manual/en/function.ftruncate.php
	 *
	 * @since   12.1
	 */
	protected function truncate($size)
	{
		return ftruncate($this->_handle, $size);
	}

	/**
	 * Tests for end-of-file on a file pointer
	 *
	 * @return  bool  TRUE on success or FALSE on failure.
	 *
	 * @link    http://php.net/manual/en/function.feof.php
	 *
	 * @since   12.1
	 */
	protected function getEof()
	{
		return feof($this->_handle);
	}

	/**
	 * Returns the current position of the file read/write pointer
	 *
	 * @return  int  The current position, or FALSE on failure.
	 *
	 * @link    http://php.net/manual/en/function.ftell.php
	 *
	 * @since   12.1
	 */
	protected function getPosition()
	{
		return ftell($this->_handle);
	}

	/**
	 * Sets the current position of the file read/write pointer
	 *
	 * @param   int  $offset  The offset.
	 * @param   int  $whence  Whence values are:
	 *                        SEEK_SET - Set position equal to offset bytes.
	 *                        SEEK_CUR - Set position to current location plus offset.
	 *                        SEEK_END - Set position to end-of-file plus offset.
	 *
	 * @return  int|FALSE  The current position, or FALSE on failure.
	 *
	 * @link    http://php.net/manual/en/function.fseek.php
	 *
	 * @since   12.1
	 */
	protected function setPosition($offset, $whence = SEEK_SET)
	{
		return fseek($this->_handle, $offset, $whence);
	}

	/**
	 * Copy a file
	 *
	 * @param   JFilesystemElement  $dest  The destination.
	 *
	 * @return  int|FALSE  The number of bytes that were written to the file, or FALSE on failure.
	 *
	 * @since   12.1
	 */
	protected function copy(JFilesystemElement $dest)
	{
		return $dest->copyFromFile($this);
	}

	/**
	 * Copy from a file
	 *
	 * @param   JFilesystemElementFile  $src  The source file.
	 *
	 * @return  int|FALSE  The number of bytes that were written to the file, or FALSE on failure.
	 *
	 * @link    http://php.net/manual/en/function.stream-copy-to-stream.php
	 *
	 * @since   12.1
	 */
	protected function copyFromFile(JFilesystemElementFile $src)
	{
		$this->open('w');
		$src->open('r');
		$return = stream_copy_to_stream($src->_handle, $this->_handle);
		$this->close();
		$src->close();
		return $return;
	}

	/**
	 * Delete a file
	 *
	 * @return  bool  TRUE on success or FALSE on failure.
	 *
	 * @link    http://php.net/manual/en/function.delete.php
	 *
	 * @since   12.1
	 */
	protected function delete()
	{
		return unlink($this->fullpath);
	}

	/**
	 * Create a file
	 *
	 * @return  bool  TRUE on success or FALSE on failure.
	 *
	 * @throw   RuntimeException
	 *
	 * @link    http://php.net/manual/en/function.touch.php
	 *
	 * @since   12.1
	 */
	protected function create()
	{
		if (!$this->exists)
		{
			$this->directory->create();
			$this->open('w')->close();
		}
		return $this;
	}

	/**
	 * Prepend a filter to a stream
	 *
	 * @param   string  $filtername  The filter name.
	 * @param   int     $read_write  STREAM_FILTER_READ, STREAM_FILTER_WRITE, and/or STREAM_FILTER_ALL to override the default behavior.
	 * @param   mixed   $params      Additional parameters.
	 *
	 * @return  bool  TRUE on success or FALSE on failure.
	 *
	 * @link    http://.php.net/manual/en/function.stream-filter-prepend.php
	 *
	 * @since   12.1
	 */
	protected function prependFilter($filtername, $read_write = 0, $params = null)
	{
		$resource = stream_filter_prepend($this->_handle, $filtername, $read_write, $params);
		return $resource;
	}

	/**
	 * Append a filter to a stream
	 *
	 * @param   string  $filtername  The filter name.
	 * @param   int     $read_write  STREAM_FILTER_READ, STREAM_FILTER_WRITE, and/or STREAM_FILTER_ALL to override the default behavior.
	 * @param   mixed   $params      Additional parameters.
	 *
	 * @return  bool  TRUE on success or FALSE on failure.
	 *
	 * @link    http://.php.net/manual/en/function.stream-filter-append.php
	 *
	 * @since   12.1
	 */
	protected function appendFilter($filtername, $read_write = 0, $params = null)
	{
		$resource = stream_filter_append($this->_handle, $filtername, $read_write, $params);
		return $resource;
	}

	/**
	 * Remove a filter from a stream
	 *
	 * @param   resource  $stream_filter  The stream filter to be removed.
	 *
	 * @return  bool  TRUE on success or FALSE on failure.
	 *
	 * @link    http://.php.net/manual/en/function.stream-filter-remove.php
	 *
	 * @since   12.1
	 */
	protected function removeFilter($stream_filter)
	{
		return stream_filter_remove($stream_filter);
	}
}
