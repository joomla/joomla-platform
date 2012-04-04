<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Base
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

/**
 * Adapter Class
 * Retains common adapter pattern functions
 * Class harvested from joomla.installer.installer
 *
 * @package     Joomla.Platform
 * @subpackage  Base
 * @since       11.1
 */
class JAdapter extends JObject
{
	/**
	 * Associative array of adapters
	 *
	 * @var    array
	 * @since  12.1
	 */
	protected $adapters = array();

	/**
	 * Adapter Folder
	 * @var    string
	 * @since  12.1
	 */
	protected $adapterfolder = 'adapters';

	/**
	 * @var    string	Adapter Class Prefix
	 * @since  12.1
	 */
	protected $classprefix = 'J';

	/**
	 * Base Path for the adapter instance
	 *
	 * @var    string
	 * @since  12.1
	 */
	protected $basepath = null;

	/**
	 * Database Connector Object
	 *
	 * @var    JDatabase
	 * @since  12.1
	 */
	protected $db;

	/**
	 * Constructor
	 *
	 * @param   string  $basepath       Base Path of the adapters
	 * @param   string  $classprefix    Class prefix of adapters
	 * @param   string  $adapterfolder  Name of folder to append to base path
	 *
	 * @since   11.1
	 */
	public function __construct($basepath, $classprefix = null, $adapterfolder = null)
	{
		$this->basepath = $basepath;
		$this->classprefix = $classprefix ? $classprefix : 'J';
		$this->adapterfolder = $adapterfolder ? $adapterfolder : 'adapters';

		$this->db = JFactory::getDBO();
	}

	/**
	 * Get the database connector object
	 *
	 * @return  JDatabase  Database connector object
	 *
	 * @since   11.1
	 */
	public function getDBO()
	{
		return $this->db;
	}

	/**
	 * Set an adapter by name
	 *
	 * @param   string  $name      Adapter name
	 * @param   object  &$adapter  Adapter object
	 * @param   array   $options   Adapter options
	 *
	 * @return  boolean  True if successful
	 *
	 * @since   11.1
	 */
	public function setAdapter($name, &$adapter = null, $options = array())
	{
		if (!is_object($adapter))
		{
			$fullpath = $this->basepath . '/' . $this->adapterfolder . '/' . strtolower($name) . '.php';

			if (!file_exists($fullpath))
			{
				return false;
			}

			// Try to load the adapter object
			require_once $fullpath;

			$class = $this->classprefix . ucfirst($name);
			if (!class_exists($class))
			{
				return false;
			}

			$adapter = new $class($this, $this->db, $options);
		}

		$this->adapters[$name] = &$adapter;

		return true;
	}

	/**
	 * Return an adapter.
	 *
	 * @param   string  $name     Name of adapter to return
	 * @param   array   $options  Adapter options
	 *
	 * @return  object  Adapter of type 'name' or false
	 *
	 * @since   11.1
	 */
	public function getAdapter($name, $options = array())
	{
		if (!array_key_exists($name, $this->adapters))
		{
			if (!$this->setAdapter($name, $options))
			{
				$false = false;

				return $false;
			}
		}

		return $this->adapters[$name];
	}

	/**
	 * Loads all adapters.
	 *
	 * @param   array  $options  Adapter options
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	public function loadAllAdapters($options = array())
	{
		$list = JFolder::files($this->basepath . '/' . $this->adapterfolder);

		foreach ($list as $filename)
		{
			if (JFile::getExt($filename) == 'php')
			{
				// Try to load the adapter object
				require_once $this->basepath . '/' . $this->adapterfolder . '/' . $filename;

				$name = JFile::stripExt($filename);
				$class = $this->classprefix . ucfirst($name);

				if (!class_exists($class))
				{
					// Skip to next one
					continue;
				}

				$adapter = new $class($this, $this->db, $options);
				$this->adapters[$name] = clone $adapter;
			}
		}
	}
}
