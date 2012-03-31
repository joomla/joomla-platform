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
	 * Contains the current JDatabaseFactory instance
	 *
	 * @var    JDatabaseFactory
	 * @since  12.1
	 */
	private static $_instance = null;

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
	 * @param   string  $name     Name of the database driver you'd like to instantiate
	 * @param   array   $options  Parameters to be passed to the database driver.
	 *
	 * @return  JDatabaseDriver  A database driver object.
	 *
	 * @since   12.1
	 */
	public function getDriver($name = 'mysql', $options = array())
	{
		// Sanitize the database connector options.
		$options['driver'] = preg_replace('/[^A-Z0-9_\.-]/i', '', $name);
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
	 * @param   string           $name  Name of the driver you want an exporter for.
	 * @param   JDatabaseDriver  $db    Optional JDatabaseDriver instance
	 *
	 * @return  JDatabaseExporter  An exporter object.
	 *
	 * @since   12.1
	 * @throws  RuntimeException
	 */
	public function getExporter($name, JDatabaseDriver $db = null)
	{
		// Derive the class name from the driver.
		$class = 'JDatabaseExporter' . ucfirst(strtolower($name));

		// Make sure we have an exporter class for this driver.
		if (!class_exists($class))
		{
			// If it doesn't exist we are at an impasse so throw an exception.
			throw new RuntimeException('Database Exporter not found.');
		}

		$o = new $class;

		if ($db instanceof JDatabaseDriver)
		{
			$o->setDbo($db);
		}

		return $o;
	}

	/**
	 * Gets an importer class object.
	 *
	 * @param   string           $name  Name of the driver you want an importer for.
	 * @param   JDatabaseDriver  $db    Optional JDatabaseDriver instance
	 *
	 * @return  JDatabaseImporter  An importer object.
	 *
	 * @since   12.1
	 * @throws  RuntimeException
	 */
	public function getImporter($name, JDatabaseDriver $db = null)
	{
		// Derive the class name from the driver.
		$class = 'JDatabaseImporter' . ucfirst(strtolower($name));

		// Make sure we have an importer class for this driver.
		if (!class_exists($class))
		{
			// If it doesn't exist we are at an impasse so throw an exception.
			throw new RuntimeException('Database importer not found.');
		}

		$o = new $class;

		if ($db instanceof JDatabaseDriver)
		{
			$o->setDbo($db);
		}

		return $o;
	}

	/**
	 * Gets an instance of the factory object.
	 *
	 * @return  JDatabaseFactory
	 *
	 * @since   12.1
	 */
	public static function getInstance()
	{
		return self::$_instance ? self::$_instance : new JDatabaseFactory;
	}

	/**
	 * Get the current query object or a new JDatabaseQuery object.
	 *
	 * @param   string           $name  Name of the driver you want an importer for.
	 * @param   JDatabaseDriver  $db    Optional JDatabaseDriver instance
	 *
	 * @return  JDatabaseQuery  The current query object or a new object extending the JDatabaseQuery class.
	 *
	 * @since   12.1
	 * @throws  RuntimeException
	 */
	public function getQuery($name, JDatabaseDriver $db = null)
	{
		// Derive the class name from the driver.
		$class = 'JDatabaseQuery' . ucfirst(strtolower($name));

		// Make sure we have a query class for this driver.
		if (!class_exists($class))
		{
			// If it doesn't exist we are at an impasse so throw an exception.
			throw new RuntimeException('Database Query class not found');
		}

		return new $class($db);
	}

	/**
	 * Static method to get an instance of a JTable class if it can be found in
	 * the table include paths.  To add include paths for searching for JTable
	 * classes @see JTable::addIncludePath().
	 *
	 * @param   string  $type    The type (name) of the JTable class to get an instance of.
	 * @param   string  $prefix  An optional prefix for the table class name.
	 * @param   array   $config  An optional array of configuration values for the JTable object.
	 *
	 * @return  mixed    A JTable object if found or boolean false if one could not be found.
	 *
	 * @since   12.1
	 */
	public function getTable($type, $prefix = 'JTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Gets an instance of a factory object to return on subsequent calls of getInstance.
	 *
	 * @param   JDatabaseFactory  $instance  A JDatabaseFactory object.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public static function setInstance(JDatabaseFactory $instance = null)
	{
		self::$_instance = $instance;
	}
}
