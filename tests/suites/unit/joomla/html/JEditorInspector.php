<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  HTML
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

require_once JPATH_PLATFORM . '/joomla/html/editor.php';

/**
 * Inspector for the JEditor class.
 *
 * @package     Joomla.UnitTest
 * @subpackage  HTML
 * @since       12.2
 */
class JEditorInspector extends JEditor
{
	/**
	 * Method for inspecting protected variables.
	 *
	 * @param   string  $name  The property name
	 *
	 * @return  mixed  The value of the class variable
	 */
	public function __get($name)
	{
		if (property_exists($this, $name))
		{
			if ($name === 'instances')
			{
				return self::$instances;
			}

			return $this->$name;
		}

		else
		{
			trigger_error('Undefined or private property: ' . __CLASS__ . '::' . $name, E_USER_ERROR);
			return null;
		}
	}

	/**
	 * Mock Event Method.
	 *
	 * @param   string  $var1  A string
	 * @param   string  $var2  An other string
	 *
	 * @return  mixed   A value to test against
	 */
	public function onTestEvent($var1 = null, $var2 = null)
	{
		$return = '';

		if (is_string($var1))
		{
			$return .= $var1;
		}

		if (is_string($var2))
		{
			$return .= $var2;
		}

		if (is_array($var1))
		{
			$return .= implode('', $var1);
		}

		return $return;
	}

	/**
	 * Sets any property from the class.
	 *
	 * @param   string  $property  The name of the class property
	 * @param   string  $value     The value of the class property
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
	 * @return  mixed  The return value of the method
	 */
	public function __call($name, $parameters = array())
	{
		return call_user_func_array(array($this, $name), $parameters);
	}

}

function JEditorEventMockFunction()
{
	return 'JEditorEventMockFunction executed';
}
