<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Object
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * Derived JObject class for testing.
 *
 * @package     Joomla.UnitTest
 * @subpackage  Object
 * @since       12.1
 */
class JObjectBuran extends JObject
{
	public $rocket = false;

	/**
	 * Method to set the test_value.
	 *
	 * @param   string  $value  The test value.
	 *
	 * @return  JObject  Chainable.
	 *
	 * @since   12.3
	 */
	protected function setTestValue($value)
	{
		// Set the property as uppercase.
		return strtoupper($value);
	}
}
