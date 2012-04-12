<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Database
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Joomla Platform Database Factory class
 *
 * @package     Joomla.Platform
 * @subpackage  Database
 * @since       12.1
 */
class JDatabaseFactory
{
	/**
	 * Name of the Database Driver
	 *
	 * @var    string
	 * @since  12.1
	 */
	private static $_name = null;

	/**
	 * Contains the current JDatabaseFactory instance
	 *
	 * @var    JDatabaseFactory
	 * @since  12.1
	 */
	private static $_instance = null;

	/**
	 * Gets an instance of the factory object.
	 *
	 * @param   string  $name     Name of the database driver you'd like to instantiate
	 * @param   array   $options  Parameters to be passed to the database driver
	 *
	 * @return  JDatabaseFactory
	 *
	 * @since   12.1
	 */
	public static function getInstance($name = 'mysql', $options = array())
	{
		return self::$_instance ? self::$_instance : new JDatabaseFactory($name, $options);
	}

	/**
	 * Class constructor.
	 *
	 * @param   string  $name     Name of the database driver you'd like to instantiate
	 * @param   array   $options  Parameters to be passed to the database driver
	 *
	 * @since   11.3
	 */
	public function __construct($name = 'mysql', $options = array())
	{
		$this->name = $name;
		return $this->getDriver($options);
	}

	/**
	 * Method to return a JDatabaseDriver instance based on the given options.  There are three global options and then
	 * the rest are specific to the database driver.  The 'driver' option defines which JDatabaseDriver class is
	 * used for the connection -- the default is 'mysql'.  The 'database' option determines which database is to
	 * be used for the connection.  The 'select' option determines whether the connector should automatically select
	 * the chosen database.
	 *
	 * Instances are unique to the given options and new objects are only created when a unique options array is
	 * passed into the method.  This ensures that we don't end up with unnecessary database connection resources.
	 *
	 * @param   array   $options  Parameters to be passed to the database driver.
	 *
	 * @return  JDatabaseDriver  A database driver object.
	 *
	 * @since   12.1
	 */
	public function getDriver($options = array())
	{
		// Sanitize the database connector options.
		$options['driver'] = preg_replace('/[^A-Z0-9_\.-]/i', '', $this->name);
		$options['database'] = (isset($options['database'])) ? $options['database'] : null;
		$options['select'] = (isset($options['select'])) ? $options['select'] : true;

		// Derive the class name from the driver.
		$class = 'JDatabaseDriver' . ucfirst(strtolower($options['driver']));

		// If the class still doesn't exist we have nothing left to do but throw an exception.  We did our best.
		if (!class_exists($class))
		{
			throw new RuntimeException(sprintf('Unable to load Database Driver: %s', $options['driver']));
		}

		// Create our new JDatabaseDriver connector based on the options given.
		try
		{
			$instance = new $class($options);
		}
		catch (RuntimeException $e)
		{
			throw new RuntimeException(sprintf('Unable to connect to the Database: %s', $e->getMessage()));
		}

		return $instance;
	}

	/**
	 * Gets an exporter class object.
	 *
	 * @return  JDatabaseExporter  An exporter object.
	 *
	 * @since   12.1
	 * @throws  RuntimeException
	 */
	public function getExporter()
	{
		// Derive the class name from the driver.
		$class = 'JDatabaseExporter' . ucfirst(strtolower($this->name));

		// Make sure we have an exporter class for this driver.
		if (!class_exists($class))
		{
			// If it doesn't exist we are at an impasse so throw an exception.
			throw new RuntimeException('Database Exporter not found.');
		}

		$o = new $class;

		if ($this->_instance instanceof JDatabaseDriver)
		{
			$o->setDbo($this->_instance);
		}

		return $o;
	}

	/**
	 * Gets an importer class object.
	 *
	 * @return  JDatabaseImporter  An importer object.
	 *
	 * @since   12.1
	 * @throws  RuntimeException
	 */
	public function getImporter()
	{
		// Derive the class name from the driver.
		$class = 'JDatabaseImporter' . ucfirst(strtolower($this->name));

		// Make sure we have an importer class for this driver.
		if (!class_exists($class))
		{
			// If it doesn't exist we are at an impasse so throw an exception.
			throw new RuntimeException('Database importer not found.');
		}

		$o = new $class;

		if ($this->_instance instanceof JDatabaseDriver)
		{
			$o->setDbo($this->_instance);
		}

		return $o;
	}

	/**
	 * Get the current query object or a new JDatabaseQuery object.
	 *
	 * @return  JDatabaseQuery  The current query object or a new object extending the JDatabaseQuery class.
	 *
	 * @since   12.1
	 * @throws  RuntimeException
	 */
	public function getQuery()
	{
		// Derive the class name from the driver.
		$class = 'JDatabaseQuery' . ucfirst(strtolower($this->name));

		// Make sure we have a query class for this driver.
		if (!class_exists($class))
		{
			// If it doesn't exist we are at an impasse so throw an exception.
			throw new RuntimeException('Database Query class not found');
		}

		return new $class($this->_instance);
	}

	/**
	 * Get a new iterator on the current query.
	 *
	 * @param   string  $column  An option column to use as the iterator key.
	 * @param   string  $class   The class of object that is returned.
	 *
	 * @return  JDatabaseIterator  A new database iterator.
	 *
	 * @since   12.1
	 * @throws  RuntimeException
	 */
	public function getIterator($column = null, $class = 'stdClass')
	{
		// Derive the class name from the driver.
		$iteratorClass = 'JDatabaseIterator' . ucfirst($this->name);

		// Make sure we have an iterator class for this driver.
		if (!class_exists($iteratorClass))
		{
			// If it doesn't exist we are at an impasse so throw an exception.
			throw new RuntimeException(sprintf('class *%s* is not defined', $iteratorClass));
		}

		// Return a new iterator
		return new $iteratorClass($this->execute(), $column, $class);
	}
}
