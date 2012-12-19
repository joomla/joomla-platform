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
		JSession::addExternalConnector(('bogus'));
	}

/**
 * Bogus session handler for PHP
 * This handler is not a JSessionStorage descendant, but the author included the supported check so we will assume it works
 *
 * @package     Joomla.UnitTest
 *
 * @copyright  Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */
class JSessionStorageBogus extends \StdObject
{

	/**
	 * This session storage is active
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
