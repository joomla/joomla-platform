<?php
/**
 * @package     Joomla.Platform
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * JORM Database Query Helper class
 *
 * Helper for query
 *
 * @package     Joomla.Platform
 * @subpackage  Database
 * @since       11.1
 * @tutorial	Joomla.Platform/jormdatabasequeryhelper.cls
 * @link		http://docs.joomla.org/JORMDatabaseQueryHelper
 */
abstract class JORMDatabaseQueryHelper
{
	/**
	 * Instance a Query Helper Class
	 * 
	 * @param JORMDatabaseQueryHelperAbstract $helper
	 * 
	 * @param JORMDatabaseQuery $reference
	 * 
	 * @since  11.1
	 */
	public static function getInstance($helper,$prefix='JOrmDatabaseQueryHelper',JORMDatabaseQuery $reference)
	{
		// Sanitize and prepare the table class name.
		$helper = preg_replace('/[^A-Z0-9_\.-]/i', '', $helper);
		$helper_class = $prefix.ucfirst($helper);
		
		// Only try to load the class if it doesn't already exist.
		if (!class_exists($helper_class))
		{
			// Search for the class file in the helpers include paths.
			jimport('joomla.filesystem.path');

			if ($path = JPath::find(self::addIncludePath(), strtolower($helper).'.php'))
			{
				// Import the class file.
				require_once $path;

				// If we were unable to load the proper class, raise a warning and return false.
				if (!class_exists($helper_class))
				{
					JError::raiseWarning(0, JText::sprintf('JLIB_ORM_DATABASE_QUERY_HELPER_ERROR_CLASS_NOT_FOUND_IN_FILE', $helperClass));
					return false;
				}
			}
			else {
				// If we were unable to find the class file in the JOrmDatabaseQueryHelper include paths, raise a warning and return false.
				JError::raiseWarning(0, JText::sprintf('JLIB_ORM_DATABASE_QUERY_HELPER_ERROR_NOT_SUPPORTED_FILE_NOT_FOUND', $type));
				return false;
			}
		}
		
		// Instantiate a new helper class and return it.
		return new $helper_class($reference);
	}
	
	/**
	 * Add a filesystem path where JOrmDatabaseQuery should search for query helpers class files.
	 * You may either pass a string or an array of paths.
	 *
	 * @param   mixed  A filesystem path or array of filesystem paths to add.
	 *
	 * @return  array  An array of filesystem paths to find query helpers classes in.
	 *
	 * @link    http://docs.joomla.org/JOrmDatabaseQueryHelper/addIncludePath
	 * 
	 * @since   11.1
	 */
	public static function addIncludePath($path = null)
	{
		// Declare the internal paths as a static variable.
		static $_paths;

		// If the internal paths have not been initialised, do so with the base query helpers path.
		if (!isset($_paths))
		{
			$_paths = array(dirname(__FILE__) . '/query/helpers');
		}

		// Convert the passed path(s) to add to an array.
		settype($path, 'array');

		// If we have new paths to add, do so.
		if (!empty($path) && !in_array($path, $_paths))
		{
			// Check and add each individual new path.
			foreach ($path as $dir)
			{
				// Sanitize path.
				$dir = trim($dir);

				// Add to the front of the list so that custom paths are searched first.
				array_unshift($_paths, $dir);
			}
		}

		return $_paths;
	}
}