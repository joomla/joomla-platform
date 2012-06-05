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
 * Recursive directory iterator
 *
 * @package     Joomla.Platform
 * @subpackage  FileSystem
 *
 * @since       12.2
 */
class JFilesystemElementDirectoryContents implements RecursiveIterator
{
	/**
	 * Iterate in depth first mode
	 *
	 * @since   12.2
	 */
	const DEPTH_FIRST = -1;

	/**
	 * Iterate in breadth first mode
	 *
	 * @since   12.2
	 */
	const BREADTH_FIRST = 1;

	/**
	 * @var  string  Original path
	 *
	 * @since   12.2
	 */
	private $_path;

	/**
	 * @var  JFilesystem  File system
	 *
	 * @since   12.2
	 */
	private $_system;

	/**
	 * @var  array  Array of options
	 *
	 * @since   12.2
	 */
	private $_options;

	/**
	 * @var  array  Array of entries
	 *
	 * @since   12.2
	 */
	private $_entries = array();

	/**
	 * @var  function  Compare function
	 *
	 * @since   12.2
	 */
	private $_compare = null;

	/**
	 * @var  int  Either JFilesystemElementDirectoryContents::DEPTH_FIRST|BREADTH_FIRST
	 *
	 * @since   12.2
	 */
	private $_mode = self::BREADTH_FIRST;

	/**
	 * Constructor
	 *
	 * @param   string       $path      Directory path
	 * @param   string       $relative  Relative path
	 * @param   JFilesystem  $system    File system
	 * @param   array        $options   Array of options
	 *
	 * @since   12.2
	 */
	public function __construct($path, $relative, $system, array $options = array())
	{
		$this->_options = $options;
		$this->_path = $path;
		if (isset($options['compare']) && is_callable($options['compare']))
		{
			$this->_compare = $options['compare'];
		}

		if (isset($options['mode']) && ($options['mode'] == self::DEPTH_FIRST || $options['mode'] == self::BREADTH_FIRST))
		{
			$this->_mode = $options['mode'];
		}

		$this->_system = $system;

		$handle = opendir($system->prefix . $path . (empty($relative) ? '' : ('/' . $relative)), $system->context);
		while (false !== ($entry = readdir($handle)))
		{
			if ($entry != '.' && $entry != '..')
			{
				$this->_entries[(empty($relative) ? '' : ($relative . '/')) . $entry] = $entry;
			}
		}
		closedir($handle);
		uksort($this->_entries, array($this, '_compare'));
	}

	/**
	 * Compare 2 directories
	 *
	 * @param   string  $a  First directory
	 * @param   string  $b  Second directory
	 *
	 * @return  int     Negative, 0 or positive
	 *
	 * @since   12.2
	 */
	private function _compare($a, $b)
	{
		if (is_dir($this->_system->prefix . $this->_path . '/' . $a))
		{
			if (is_dir($this->_system->prefix . $this->_path . '/' . $b))
			{
				if (isset($this->_compare))
				{
					$compare = $this->_compare;
					return $compare($this->_path, $a, $b, $this->_system);
				}
				else
				{
					return strcmp(basename($a), basename($b));
				}
			}
			else
			{
				return $this->_mode;
			}
		}
		else
		{
			if (is_dir($this->_system->prefix . $this->_path . '/' . $b))
			{
				return -$this->_mode;
			}
			else
			{
				if (isset($this->_compare))
				{
					$compare = $this->_compare;
					return $compare($this->_path, $a, $b, $this->_system);
				}
				else
				{
					return strcmp(basename($a), basename($b));
				}
			}
		}
	}

	/**
	 * Magic get
	 *
	 * @param   string  $property  Property name
	 *
	 * @return  mixed  Property value
	 *
	 * @throw   InvalidArgumentException
	 *
	 * @since   12.2
	 */
	public function __get($property)
	{
		switch ($property)
		{
			case 'system':
				return $this->_system;
				break;

			case 'path':
				return $this->_path;
				break;

			// @codeCoverageIgnoreStart
			default:
				throw new InvalidArgumentException(sprintf('Undefined property: %s::%s', get_called_class(), $property));
				break;

		}

			// @codeCoverageIgnoreEnd
	}

	/**
	 * Iterator rewind function
	 *
	 * @return  void
	 *
	 * @since   12.2
	 */
	public function rewind()
	{
		reset($this->_entries);
	}

	/**
	 * Iterator key function
	 *
	 * @return  string  Directory pathname
	 *
	 * @since   12.2
	 */
	public function key()
	{
		return key($this->_entries);
	}

	/**
	 * Iterator next function
	 *
	 * @return  void
	 *
	 * @since   12.2
	 */
	public function next()
	{
		next($this->_entries);
	}

	/**
	 * Iterator current function
	 *
	 * @return  string  Directory name
	 *
	 * @since   12.2
	 */
	public function current()
	{
		return current($this->_entries);
	}

	/**
	 * Iterator valid function
	 *
	 * @return  bool  TRUE on success, or FALSE on failure
	 *
	 * @since   12.2
	 */
	public function valid()
	{
		return $this->current() !== false;
	}

	/**
	 * Recursive iterator hasChildren function
	 *
	 * @return  bool  TRUE on success, or FALSE on failure
	 *
	 * @since   12.2
	 */
	public function hasChildren()
	{
		return is_dir($this->_system->prefix . $this->_path . '/' . $this->key());
	}

	/**
	 * Recursive iterator getChildren function
	 *
	 * @return  JFilesystemElementDirectoryContents  new iterator
	 *
	 * @since   12.2
	 */
	public function getChildren()
	{
		return new static($this->_path, $this->key(), $this->_system, $this->_options);
	}
}
