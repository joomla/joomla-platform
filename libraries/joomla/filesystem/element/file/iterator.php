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
 * A File iterator class
 *
 * @package     Joomla.Platform
 * @subpackage  FileSystem
 *
 * @since       12.1
 */
class JFilesystemElementFileIterator implements Iterator
{
	/**
	 * @var  string  The accessor used for iteration
	 */
	private $_name;

	/**
	 * @var  array  The args used for reading
	 */
	private $_args;

	/**
	 * @var  int  The count used for iterating
	 */
	private $_count = 0;

	/**
	 * Constructor
	 *
	 * @param   string  $name  The accessor name.
	 * @param   array   $args  The file system.
	 *
	 * @since   12.1
	 */
	public function __construct($name, array $args)
	{
		$this->_name = $name;
		$this->_args = $args;
	}

	/**
	 * Return the current data
	 *
	 * @return  mixed  The current data
	 *
	 * @since   12.1
	 */
	public function current()
	{
		return $this->_current;
	}

	/**
	 * Return the current key
	 *
	 * @return  int  The current key
	 *
	 * @since   12.1
	 */
	public function key()
	{
		return $this->_count++;
	}

	/**
	 * Move forward to next element
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function next()
	{
		$this->_current = JFilesystemAccessor::read($this->_name, $this->_args);
	}

	/**
	 * Rewind the Iterator to the first element
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function rewind()
	{
		$this->_args[0]->valid = true;
		$this->next();
	}

	/**
	 * Checks if current position is valid
	 *
	 * @return  bool  Tells if the current position is valid
	 *
	 * @since   12.1
	 */
	public function valid()
	{
		return $this->_args[0]->valid;
	}
}
