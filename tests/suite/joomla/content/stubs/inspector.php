<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Content
 * @copyright   Copyright 2011 eBay, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

/**
 * Inspector JContentHelperTest class.
 *
 * @package     Joomla.UnitTest
 * @subpackage  Content
 * @since       12.1
 */
class JContentInspector extends JContent
{
	/**
	 * Method to test that using magic property access to properties will only invoke
	 * the getter one time. We test this by using the property multiple times which
	 * calls a getter that tracks and returns how many times it has been called.
	 *
	 * @return  integer  The number of times the method has been called.
	 *
	 * @since   12.1
	 */
	protected function getCacheTest()
	{
		static $calls = null;

		$calls++;

		return $calls;
	}

	/**
	 * Allows the tester to access a protected property.
	 *
	 * Note, we can't use a magic getter here because the class uses that method.
	 *
	 * @param   string  $name  The name of the class property.
	 *
	 * @return  mixed  The value of the property.
	 *
	 * @since   12.1
	 */
	public function getClassProperty($name)
	{
		return $this->$name;
	}

	/**
	 * Allows the tester to set a protected property.
	 *
	 * Note, we can't use a magic setter here because the class uses that method.
	 *
	 * @param   string  $name  The name of the class property.
	 *
	 * @return  mixed  The value of the property.
	 *
	 * @since   12.1
	 */
	public function setClassProperty($name, $value)
	{
		return $this->$name = $value;
	}

	/**
	 * Dummy custom property setter used by the bind method.
	 *
	 * @param   mixed  $value
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function setMoo($value)
	{
		$this->moo = "*$value*";
	}
}