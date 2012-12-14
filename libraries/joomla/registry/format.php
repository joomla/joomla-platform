<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Registry
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Abstract Format for JRegistry
 *
 * @package     Joomla.Platform
 * @subpackage  Registry
 * @since       11.1
 */
abstract class JRegistryFormat
{
	/**
	 * @var    array  JRegistryFormat instances container.
	 * @since  11.3
	 */
	protected static $instances = array();

	/**
	 * Returns a reference to a Format object, only creating it
	 * if it doesn't already exist.
	 *
	 * @param   string  $type  The format to load
	 *
	 * @return  JRegistryFormat  Registry format handler
	 *
	 * @since   11.1
	 * @throws  InvalidArgumentException
	 */
	public static function getInstance($type)
	{
		// Sanitize format type.
		$type = strtolower(preg_replace('/[^A-Z0-9_]/i', '', $type));

		// Only instantiate the object if it doesn't already exist.
		if (!isset(self::$instances[$type]))
		{
			// Only load the file the class does not exist.
			$class = 'JRegistryFormat' . $type;

			if (!class_exists($class))
			{
				$path = __DIR__ . '/format/' . $type . '.php';

				if (is_file($path))
				{
					include_once $path;
				}
				else
				{
					throw new InvalidArgumentException('Unable to load format class.', 500);
				}
			}

			self::$instances[$type] = new $class;
		}
		return self::$instances[$type];
	}

	/**
	 * Converts an object into a formatted string.
	 *
	 * @param   object  $object   Data Source Object.
	 * @param   array   $options  An array of options for the formatter.
	 *
	 * @return  string  Formatted string.
	 *
	 * @since   11.1
	 */
	abstract public function objectToString($object, $options = null);

	/**
	 * Converts a formatted string into an object.
	 *
	 * @param   string  $data     Formatted string
	 * @param   array   $options  An array of options for the formatter.
	 *
	 * @return  object  Data Object
	 *
	 * @since   11.1
	 */
	abstract public function stringToObject($data, array $options = array());
}
