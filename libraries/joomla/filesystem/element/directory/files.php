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
 * Recursive filter directory files iterator
 *
 * @package     Joomla.Platform
 * @subpackage  FileSystem
 *
 * @since       12.1
 */
class JFilesystemElementDirectoryFiles extends RecursiveFilterIterator
{
	/**
	 * @var  string  regex filter
	 *
	 * @since   12.1
	 */
	private $_filter;

	/**
	 * @var  string  regex exclude filter
	 *
	 * @since   12.1
	 */
	private $_exclude;

	/**
	 * @var  string  regex directory filter
	 *
	 * @since   12.1
	 */
	private $_filter_directory;

	/**
	 * @var  string  regex directory exclude filter
	 *
	 * @since   12.1
	 */
	private $_exclude_directory;

	/**
	 * @var  function  function accepting a file
	 *
	 * @since   12.1
	 */
	private $_accept = null;

	/**
	 * @var  array  Array of options
	 *
	 * @since   12.1
	 */
	private $_options;

	/**
	 * Accept the file
	 *
	 * @return  bool  TRUE on success, FALSE on failure
	 *
	 * @since   12.1
	 */
	public function accept()
	{
		if (parent::hasChildren())
		{
			return
				preg_match($this->_filter_directory, $this->getInnerIterator()->current()) &&
				!preg_match($this->_exclude_directory, $this->getInnerIterator()->current());
		}
		else
		{
			return
				preg_match($this->_filter, $this->getInnerIterator()->current()) &&
				!preg_match($this->_exclude, $this->getInnerIterator()->current()) &&
				(!is_callable($this->_accept)
					|| call_user_func_array($this->_accept, array($this->getInnerIterator()->path, parent::key(), $this->getInnerIterator()->system)));
		}
	}

	/**
	 * Recursive iterator getChildren function
	 *
	 * @return  JFilesystemElementDirectoryDirectories  new iterator
	 *
	 * @since   12.1
	 */
	public function getChildren()
	{
		return new static($this->getInnerIterator()->getChildren(), $this->_options);
	}

	/**
	 * Recursive itertator current function
	 *
	 * @return  JFilesystemElementFile  File object
	 *
	 * @since   12.1
	 */
	public function current()
	{
		return $this->getInnerIterator()->system->getFile($this->getInnerIterator()->path . parent::key());
	}

	/**
	 * Constructor
	 *
	 * @param   JFilesystemElementDirectoryContents  $iterator  Directory iterator
	 * @param   array                                $options   Array of options
	 *
	 * @since   12.1
	 */
	public function __construct($iterator, array $options = array())
	{
		$this->_options = $options;
		if (isset($options['filter']))
		{
			$this->_filter = (string) $options['filter'];
		}
		else
		{
			$this->_filter = chr(1) . '.' . chr(1);
		}

		if (isset($options['exclude']))
		{
			$this->_exclude = (string) $options['exclude'];
		}
		else
		{
			$this->_exclude = chr(1) . '^\..' . chr(1);
		}

		if (isset($options['filter_directory']))
		{
			$this->_filter_directory = (string) $options['filter_directory'];
		}
		else
		{
			$this->_filter_directory = chr(1) . '.' . chr(1);
		}

		if (isset($options['exclude_directory']))
		{
			$this->_exclude_directory = (string) $options['exclude_directory'];
		}
		else
		{
			$this->_exclude_directory = chr(1) . '^(\..|CVS|__MACOSX)$' . chr(1);
		}

		if (isset($options['accept']) && is_callable($options['accept']))
		{
			$this->_accept = $options['accept'];
		}
		parent::__construct($iterator);
	}
}
