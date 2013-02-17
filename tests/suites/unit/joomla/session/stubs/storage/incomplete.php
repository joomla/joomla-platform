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
		JSession::addExternalConnector(('incomplete'));
	}
/**
 * Incomplete session handler for PHP
 * An incomplete implementation of a session storage handler
 *
 * @package     Joomla.UnitTest
 *
 * @copyright  Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */
class JSessionStorageIncomplete extends JSessionStorage
{

	/**
	 * The author forgot to add an isSupported method so this will fail
	 *
	 */

}
