<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Pathway
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * General inspector class for JPathway.
 *
 * @package     Joomla.UnitTest
 * @subpackage  Pathway
 *
 * @since       12.3
 */
class JPathwayInspector extends JPathway
{
	/**
	 * Method for inspecting protected variables.
	 *
	 * @param   string  $name  The name of the class property.
	 *
	 * @return  mixed   The value of the class variable.
	 */
	public function __get($name)
	{
		if (property_exists($this, $name))
		{
			return $this->$name;
		}
		else
		{
			trigger_error('Undefined or private property: ' . __CLASS__ . '::' . $name, E_USER_ERROR);

			return null;
		}
	}

	/**
	 * Sets any property from the class.
	 *
	 * @param   string  $property  The name of the class property.
	 * @param   string  $value     The value of the class property.
	 *
	 * @return  void
	 */
	public function __set($property, $value)
	{
		$this->$property = $value;
	}

	/**
	 * Calls any inaccessible method from the class.
	 *
	 * @param   string  $name        Name of the method to invoke
	 * @param   array   $parameters  Parameters to be handed over to the original method
	 *
	 * @return  mixed   The return value of the method
	 */
	public function __call($name, $parameters = false)
	{
		return call_user_func_array(array($this, $name), $parameters);
	}
}
