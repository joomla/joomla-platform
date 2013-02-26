<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Database
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

require_once JPATH_PLATFORM . '/joomla/database/query/postgresql.php';

/**
 * Class to expose protected properties and methods in JDatabaseQuery for testing purposes.
 *
 * @package     Joomla.UnitTest
 * @subpackage  Database
 *
 * @since       11.1
 */
class JDatabaseQueryPostgresqlInspector extends JDatabaseQueryPostgresql
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
}
