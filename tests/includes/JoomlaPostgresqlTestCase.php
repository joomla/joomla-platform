<?php
/**
 * @version    $Id: JDatabasePostgresqlTestCase.php gpongelli $
 * @package    Joomla.UnitTest
 * 
 * @copyright  Copyright (C) 2005 - 2011 Open Source Matters. All rights reserved.
 * @license    GNU General Public License
 */

require_once dirname(__FILE__) . '/JoomlaTestCase.php';

/**
 * Test case class for Joomla Unit Testing
 *
 * @package     Joomla.UnitTest
 * @subpackage  Database
 * 
 * @since       11.3
 */
class JoomlaPostgresqlTestCase extends JoomlaTestCase
{
	/**
	 * Gets a mock database object.
	 *
	 * @return  object
	 *
	 * @since   11.3
	 */
	public function getMockDatabase()
	{
		// Load the real class first otherwise the mock will be used if jimport is called again.
		require_once JPATH_PLATFORM . '/joomla/database/database.php';

		// Load the mock class builder.
		require_once JPATH_TESTS . '/includes/mocks/JDatabasePostgresqlMock.php';

		return JDatabasePostgresqlMock::create($this);
	}
}
