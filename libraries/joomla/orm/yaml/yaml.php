<?php
/**
 * @package     Joomla.Platform
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

JLoader::import('includes.spyc',dirname(__FILE__));

/**
 * JORM YAML
 *
 * Class to work with yaml files
 *
 * @package     Joomla.Platform
 * @subpackage  Yaml
 * @since       11.1
 * @tutorial	Joomla.Platform/jormyaml.cls
 * @link		http://docs.joomla.org/JORMYaml
 */
class JORMYaml
{
	/**
	 * Load YAML string
	 * 
	 * @param unknown_type $string
	 * @since 11.1
	 */
	static function loadstring($string)
	{
		return spyc_load($string);
	}
	
	/**
	 * Load YAML file
	 * 
	 * @param string $filename complete file path
	 * @since 11.1
	 */
	static function loadfile($filename)
	{
		return spyc_load_file($filename);
	}	
	
	/**
	 * Find YAML file
	 * 
	 * Return full path file
	 * 
	 * @param array $path
	 * @param string $filename
	 * @since 11.1
	 */
	static function findfile($path,$filename)
	{
		if ($path = JPath::find(self::addIncludePath(), strtolower($filename).'.yml')) {
			// Import the class file.
			return self::loadfile($path);
		}
		else {
			// If we were unable to find the class file in the YAML include paths, raise a warning and return false.
			JError::raiseWarning(0, JText::sprintf('JORMLIB_YAML_ERROR_NOT_SUPPORTED_FILE_NOT_FOUND', $filename));
			return false;
		}
	}
	
	/**
     * Dump YAML from PHP array statically
     *
     * The dump method, when supplied with an array, will do its best
     * to convert the array into friendly YAML.  Pretty simple.  Feel free to
     * save the returned string as nothing.yaml and pass it around.
     *
     * Oh, and you can decide how big the indent is and what the wordwrap
     * for folding is.  Pretty cool -- just pass in 'false' for either if
     * you want to use the default.
     *
     * Indent's default is 2 spaces, wordwrap's default is 40 characters.  And
     * you can turn off wordwrap by passing in 0.
     *
     * @access public
     * @return string
     * @param array $array PHP array
     * @param int $indent Pass in false to use the default, which is 2
     * @param int $wordwrap Pass in 0 for no wordwrap, false for default (40)
     */
	static function dump($array,$indent = false,$wordwrap = false)
	{
		return Spyc::YAMLDump($array,$indent,$wordwrap);
	}
	
	/**
	 * Add a filesystem path where should search for yaml files.
	 * You may either pass a string or an array of paths.
	 *
	 * @param   mixed  A filesystem path or array of filesystem paths to add.
	 *
	 * @return  array  An array of filesystem paths to find YAML file in.
	 *
	 * @link    http://docs.joomla.org/JORMYaml/addIncludePath
	 * @since   11.1
	 */
	public static function addIncludePath($path = null)
	{
		// Declare the internal paths as a static variable.
		static $_paths;

		// If the internal paths have not been initialised, do so with the base table path.
		if (!isset($_paths)) {
			$_paths = array();
		}

		// Convert the passed path(s) to add to an array.
		settype($path, 'array');

		// If we have new paths to add, do so.
		if (!empty($path) && !in_array($path, $_paths)) {
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