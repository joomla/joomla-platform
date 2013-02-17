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
		JSession::addExternalConnector(('fakity'));
	}

/**
 * Bogus session handler for PHP
 * This session storage classname was supposed to be changed but the author missed a spot
 *
 * @package     Joomla.UnitTest
 *
 * @copyright  Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */
class JSessionStorageFakityFake extends JSessionStorage
{

}
