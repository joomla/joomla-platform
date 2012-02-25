<?php
/**
 * @version    $Id: JoomlaDatabasePostgresqlTestCase.php gpongelli $
 * @package    Joomla.UnitTest
 * 
 * @copyright  Copyright (C) 2005 - 2011 Open Source Matters. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

require_once JPATH_TESTS . '/includes/JoomlaDatabaseTestCase.php';

/**
 * Test case class for Joomla Unit Testing
 *
 * @package     Joomla.UnitTest
 * @subpackage  Database
 * 
 * @since       11.3
 */
class JoomlaDatabasePostgresqlTestCase extends JoomlaDatabaseTestCase
{
	/**
	 * setupBeforeClass.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public static function setUpBeforeClass()
	{
		jimport('joomla.database.database');
		jimport('joomla.database.database.postgresql');
		jimport('joomla.database.table');

		// Load the config if available.
		if (class_exists('JPostgresqlTestConfig'))
		{
			$config = new JPostgresqlTestConfig;
		}

		if (!is_object(self::$dbo))
		{
			$options = array(
				'driver' => isset($config) ? $config->dbtype : 'postgresql',
				'host' => isset($config) ? $config->host : '127.0.0.1',
				'user' => isset($config) ? $config->user : 'utuser',
				'password' => isset($config) ? $config->password : 'ut1234',
				'database' => isset($config) ? $config->db : 'joomla_ut',
				'prefix' => isset($config) ? $config->dbprefix : 'jos_');

			try
			{
				self::$dbo = JDatabase::getInstance($options);
			}
			catch (RuntimeException $e)
			{
			}

			if (self::$dbo instanceof Exception)
			{
				// Ignore errors
				define('DB_NOT_AVAILABLE', true);
			}
		}

		self::$database = JFactory::$database;
		JFactory::$database = self::$dbo;
	}

	/**
	 * This method is called after the last test of this test class is run.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public static function tearDownAfterClass()
	{
		// JFactory::$database = self::$database;
	}

	/**
	 * Sets the connection to the database
	 *
	 * @return  connection
	 *
	 * @since   11.3
	 */
	protected function getConnection()
	{
		// Load the config if available.
		if (class_exists('JPostgreSQLTestConfig'))
		{
			$config = new JPostgreSQLTestConfig;
		}
		elseif (class_exists('JTestConfig'))
		{
			$config = new JTestConfig;
		}

		$options = array(
			'driver' => ((isset($config)) && ($config->dbtype != 'postgresql')) ? $config->dbtype : 'pgsql',
			'host' => isset($config) ? $config->host : '127.0.0.1',
			'user' => isset($config) ? $config->user : 'utuser',
			'password' => isset($config) ? $config->password : 'ut1234',
			'database' => isset($config) ? $config->db : 'joomla_ut',
			'prefix' => isset($config) ? $config->dbprefix : 'jos_'
		);

		$pdo = new PDO($options['driver'] . ':host=' . $options['host'] . ';dbname=' . $options['database'], $options['user'], $options['password']);

		return $this->createDefaultDBConnection($pdo, $options['database']);
	}

	/**
	 * Gets a mock database object.
	 *
	 * @return  JDatabase
	 *
	 * @since   11.3
	 */
	public function getMockDatabase()
	{
		// Load the real class first otherwise the mock will be used if jimport is called again.
		require_once JPATH_PLATFORM . '/joomla/database/database.php';
		require_once JPATH_PLATFORM . '/joomla/database/database/postgresql.php';

		// Load the mock class builder.
		require_once JPATH_TESTS . '/includes/mocks/JDatabasePostgresqlMock.php';

		return JDatabasePostgresqlMock::create($this);
	}
}
