<?php
/**
 * @package     Joomla.Platform
 * @subpackage  FileSystem
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Generic Memory stream handler
 *
 * This class provides a generic memory stream.  It can be used to store/retrieve/manipulate
 * files in memory with the standard PHP filesystem I/O methods.
 *
 * @package     Joomla.Platform
 * @subpackage  FileSystem
 * @since       11.4
 */
class JStreamMemory
{
	/**
	 * Stream position
	 *
	 * @var    integer
	 * @since  11.4
	 */
	protected $position = 0;

	/**
	 * File name
	 *
	 * @var    string
	 * @since  11.4
	 */
	protected $name = null;

	/**
	 * Files hash
	 *
	 * @var    array
	 * @since  11.4
	 */
	protected static $files = array();

	/**
	 * Compute an hash for the file name
	 *
	 * @param   string  $path  The URL that was passed
	 *
	 * @return  string  Hashed file name
	 *
	 * @since   11.4
	 */
	protected static function getName($path)
	{
		return md5($path);
	}

	/**
	 * Function to open file or url
	 *
	 * @param   string   $path          The URL that was passed
	 * @param   string   $mode          Not used.
	 * @param   integer  $options       Not used.
	 * @param   string   &$opened_path  Not used.
	 *
	 * @return  boolean  TRUE on success or FALSE on failure
	 *
	 * @since   11.4
	 * @see     streamWrapper::stream_open
	 */
	public function stream_open($path, $mode, $options, &$opened_path)
	{
		$now = time();
		$this->name = self::getName($path);
		if (strpos($mode, 'r') !== false)
		{
			if (isset(self::$files[$this->name]))
			{
				$this->position = 0;
				self::$files[$this->name]->atime = $now;
				return true;
			}
			else
			{
				return false;
			}
		}
		elseif (strpos($mode, 'a') !== false)
		{
			if (!isset(self::$files[$this->name]))
			{
				self::$files[$this->name] = (object) array('buffer' => '', 'atime' => $now, 'mtime' => $now, 'ctime' => $now);
			}
			$this->position = strlen(self::$files[$this->name]->buffer);
			self::$files[$this->name]->atime = $now;
			self::$files[$this->name]->mtime = $now;
			return true;
		}
		else
		{
			self::$files[$this->name] = (object) array('buffer' => '', 'atime' => $now, 'mtime' => $now, 'ctime' => $now);
			$this->position = 0;
			return true;
		}
	}

	/**
	 * Read stream
	 *
	 * @param   integer  $count  How many bytes of data from the current position should be returned.
	 *
	 * @return  mixed    The data from the stream up to the specified number of bytes (all data if
	 *                   the total number of bytes in the stream is less than $count. Null if
	 *                   the stream is empty.
	 *
	 * @see     streamWrapper::stream_read
	 * @since   11.4
	 */
	public function stream_read($count)
	{
		$ret = substr(self::$files[$this->name]->buffer, $this->position, $count);
		$this->position += strlen($ret);

		return $ret;
	}

	/**
	 * Write stream
	 *
	 * @param   string  $data  The data to write to the stream.
	 *
	 * @return  integer  The number of bytes written
	 *
	 * @see     streamWrapper::stream_write
	 * @since   11.4
	 */
	public function stream_write($data)
	{
		$left = substr(self::$files[$this->name]->buffer, 0, $this->position);
		$right = substr(self::$files[$this->name]->buffer, $this->position + strlen($data));
		self::$files[$this->name]->buffer = $left . $data . $right;
		$this->position += strlen($data);

		return strlen($data);
	}

	/**
	 * Function to get the current position of the stream
	 *
	 * @return  integer    The position of the file pointer.
	 *
	 * @see     streamWrapper::stream_tell
	 * @since   11.4
	 */
	public function stream_tell()
	{
		return $this->position;
	}

	/**
	 * Function to test for end of file pointer
	 *
	 * @return  boolean  True if the pointer is at the end of the stream
	 *
	 * @see     streamWrapper::stream_eof
	 * @since   11.4
	 */
	public function stream_eof()
	{
		return $this->position >= strlen(self::$files[$this->name]->buffer);
	}

	/**
	 * The read write position updates in response to $offset and $whence
	 *
	 * @param   integer  $offset  The offset in bytes
	 * @param   integer  $whence  Position the offset is added to
	 *                            Options are SEEK_SET, SEEK_CUR, and SEEK_END
	 *
	 * @return  boolean  TRUE on success or FALSE on failure.
	 *
	 * @see     streamWrapper::stream_seek
	 * @since   11.4
	 */
	public function stream_seek($offset, $whence)
	{
		switch ($whence)
		{
			case SEEK_SET:
				if ($offset < strlen(self::$files[$this->name]->buffer) && $offset >= 0)
				{
					$this->position = $offset;
					return true;
				}
				else
				{
					return false;
				}
				break;

			case SEEK_CUR:
				if ($offset >= 0)
				{
					$this->position += $offset;
					return true;
				}
				else
				{
					return false;
				}
				break;

			case SEEK_END:
				if (strlen(self::$files[$this->name]->buffer) + $offset >= 0)
				{
					$this->position = strlen(self::$files[$this->name]->buffer) + $offset;
					return true;
				}
				else
				{
					return false;
				}
				break;

			default:
				return false;
		}
	}

	/**
	 * Delete a file
	 *
	 * @param   string  $path  File name
	 *
	 * @return  boolean  TRUE on success or FALSE on failure. 
	 *
	 * @see     streamWrapper::unlink
	 * @since   11.4
	 */
	public function unlink($path)
	{
		$name = self::getName($path);
		if (isset(self::$files[$name]))
		{
			unset(self::$files[$name]);
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Retrieve information about a file resource
	 *
	 * @return  array  Associative array of information
	 *
	 * @see     streamWrapper::stream_stat
	 * @since   11.4
	 */
	public function stream_stat()
	{
		return self::getStat(self::$files[$this->name]);
	}

	/**
	 * Retrieve information about a file resource
	 *
	 * @param   string   $path   File name
	 * @param   integer  $flags  Unused
	 *
	 * @return  mixed    Associative array of information or FALSE on failure
	 *
	 * @see     streamWrapper::url_stat
	 * @since   11.4
	 */
	public function url_stat($path, $flags)
	{
		$name = self::getName($path);
		if (isset(self::$files[$name]))
		{
			return self::getStat(self::$files[$name]);
		}
		else
		{
			return false;
		}
	}

	/**
	 * Retrieve information about a file
	 *
	 * @param   array  $file  File information
	 *
	 * @return  array  Associative array of information
	 *
	 * @since   11.4
	 */
	protected static function getStat($file)
	{
		$size = strlen($file->buffer);
		$blocks = (int) ceil($size / 512);
		return array(
			1 => 0, 2 => 0, 3 => 1, 4 => 0, 5 => 0, 6 => 0, 7 => $size, 8 => $file->atime,
			9 => $file->mtime, 10 => $file->ctime, 11 => 512, 12 => $blocks,
			'ino' => 0, 'mode' => 0, 'nlink' => 1, 'uid' => 0, 'gid' => 0, 'rdev' => 0, 'size' => $size, 'atime' => $file->atime,
			'mtime' => $file->mtime, 'ctime' => $file->ctime, 'blksize' => 512, 'blocks' => $blocks
		);
	}

	/**
	 * Register a protocol implemented by this class
	 *
	 * @param   string  $protocol  The wrapper name to be registered.
	 *
	 * @return  boolean  TRUE on success or FALSE on failure. 
	 */
	public static function register($protocol = 'memory')
	{
		if (!in_array($protocol, stream_get_wrappers()))
		{
			return stream_wrapper_register($protocol, __CLASS__);
		}
		else
		{
			return false;
		}
	}
}
