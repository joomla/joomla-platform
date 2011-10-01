<?php
/**
 * @package    Joomla.UnitTest
 *
 * @copyright  Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

require_once dirname(__FILE__).'/JoomlaTestCase.php';

/**
 * Test case class for Joomla Unit Testing
 *
 * @package  Joomla.UnitTest
 * @since    11.1
 */
class JoomlaPostgreSQLTestCase extends JoomlaTestCase
{
	/**
	 * Gets a mock database object.
	 *
	 * @return  object
	 *
	 * @since   11.3
	 */
	protected function getMockDatabase()
	{
		// Load the real class first otherwise the mock will be used if jimport is called again.
		require_once JPATH_PLATFORM.'/joomla/database/database.php';
		
		require_once JPATH_TESTS.'/suite/joomla/database/JDatabasePostgreSQLMock.php';

		return JDatabasePostgreSQLMock::create($this);
	}
}
