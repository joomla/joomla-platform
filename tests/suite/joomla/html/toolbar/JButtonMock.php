<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  HTML
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

jimport('joomla.html.toolbar.button');

/**
 * General mock class for JButton.
 *
 * @package Joomla.UnitTest
 * @subpackage HTML
 * @since 11.3
 */
class JButtonMock extends JButton
{
	/**
	 * Button type
	 *
	 * @var    string
	 */
	protected $_name = 'Mock';
	
	/**
	 * Get the button CSS Id
	 *
	 * @return  string  Button CSS Id
	 *
	 * @since   11.3
	 */
	public function fetchId()
	{
		return 'mock-id';
	}
	
	/**
	 * Fetch the HTML for the button
	 *
	 * @return  string   HTML string for the button
	 *
	 * @since   11.3
	 */
	public function fetchButton()
	{
		return 'mock-button';
	}
	
	/**
	* Method for inspecting protected variables.
	*
	* @return mixed The value of the class variable.
	*/
	public function __get($name)
	{
		if (property_exists($this, $name)) {
			return $this->$name;
		} else {
			trigger_error('Undefined or private property: ' . __CLASS__.'::'.$name, E_USER_ERROR);
			return null;
		}
	}
}