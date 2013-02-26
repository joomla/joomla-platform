<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Database
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

require_once JPATH_PLATFORM . '/joomla/database/query.php';

/**
 * Class to expose protected properties and methods in JDatabaseQueryExporter for testing purposes.
 *
 * @package     Joomla.UnitTest
 * @subpackage  Database
 *
 * @since       11.1
 */
class JDatabaseQueryInspector extends JDatabaseQuery
{
	/**
	 * Sets any property from the class.
	 *
	 * @param   string  $property  The name of the class property.
	 * @param   string  $value     The value of the class property.
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	public function __set($property, $value)
	{
		return $this->$property = $value;
	}

	/**
	 * Gets any property from the class.
	 *
	 * @param   string  $property  The name of the class property.
	 *
	 * @return  mixed   The value of the class property.
	 *
	 * @since   11.1
	 */
	public function get($property)
	{
		return $this->$property;
	}

	/**
	 * Generates a Globally Unique Identifier (32 hexadecimal digits separated by hyphens as 8-4-4-4-12).
	 *
	 * Usage:
	 * $query->set('guid = ' . $query->GUID());
	 *
	 * @return  string
	 *
	 * @since   12.3
	 */
	public function GUID()
	{
		return 'GUID()';
	}
}
