<?php
	/**
	 * @package    Joomla.UnitTest
	 *
	 * @copyright  Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
	 * @license    GNU General Public License version 2 or later; see LICENSE
	 */

	// Register myself as a session storage class
	if (method_exists('JSession', 'addExternalConnector'))
	{
		JSession::addExternalConnector(('fake'));
	}

/**
 * Fake session handler for PHP
 * This is a valid session storage class which passes it's internal check
 *
 *
 * @package     Joomla.UnitTest
 *
 * @copyright  Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */
class JSessionStorageFake extends JSessionStorage
{


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
