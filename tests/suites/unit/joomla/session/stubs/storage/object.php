<?php
	/**
	 * @package    Joomla.UnitTest
	 *
	 * @copyright  Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
	 * @license    GNU General Public License version 2 or later; see LICENSE
	 */


	// For future use

/**
 * Object session handler for PHP
 * This is a valid session storage class where the developer using it can get an instance of it, but may not know the classname
 *
 *
 * @package     Joomla.UnitTest
 *
 * @copyright  Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */
class JSessionStorageObject extends JSessionStorage
{

	/**
	 * Constructor
	 *
	 * @param   array  $options  Optional parameters.
	 *
	 * @since   11.1
	 */
	public function __construct($options = array())
	{
		return $this;
	}

	/**
	 * Test to see if the SessionHandler is available.
	 *
	 * @return boolean  True on success, false otherwise.
	 *
	 * @since   12.1
	 */
	static public function isSupported()
	{
		return true;
	}

}

	// Create an object for testing
	$testObject = new JSessionStorageObject();

	// Register the object a session storage class
	if (method_exists('JSession', 'addExternalConnector'))
	{
		JSession::addExternalConnector(($testObject));
	}

	// Delete the object as no longer relevant
	unset($testObject);