<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Database
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

require_once JPATH_PLATFORM . '/joomla/log/log.php';
require_once JPATH_PLATFORM . '/joomla/database/iterator.php';
require_once JPATH_PLATFORM . '/joomla/database/iterator/mysql.php';
require_once JPATH_PLATFORM . '/joomla/database/driver.php';
require_once JPATH_PLATFORM . '/joomla/database/driver/mysql.php';
require_once JPATH_PLATFORM . '/joomla/database/query.php';

/**
 * Test class for JDatabaseResults using MySQL engine.
 *
 * @package     Joomla.UnitTest
 * @subpackage  Database
 *
 * @since       12.1
 */
class JDatabaseIteratorMySQLTest extends TestCaseDatabaseMysql
{
	/**
	 * Gets the data set to be loaded into the database during setup
	 *
	 * @return  xml dataset
	 *
	 * @since   12.1
	 */
	protected function getDataSet()
	{
		return $this->createXMLDataSet(dirname(__DIR__) . '/stubs/database.xml');
	}

	/**
	 * Data provider for the testForEach method
	 *
	 * @return  array
	 *
	 * @since   12.1
	 */
	public function casesForEachData()
	{
		return array(
			// Testing 'stdClass' type without specific index, offset or limit
			array(
				'title',
				'#__dbtest',
				null,
				'stdClass',
				0,
				0,
				array(
					(object) array('title' => 'Testing'),
					(object) array('title' => 'Testing2'),
					(object) array('title' => 'Testing3'),
					(object) array('title' => 'Testing4')
				),
				null
			),

			// Testing 'stdClass' type, limit=2 without specific index or offset
			array(
				'title',
				'#__dbtest',
				null,
				'stdClass',
				2,
				0,
				array(
					(object) array('title' => 'Testing'),
					(object) array('title' => 'Testing2')
				),
				null
			),

			// Testing 'stdClass' type, offset=2 without specific index or limit
			array(
				'title',
				'#__dbtest',
				null,
				'stdClass',
				20,
				2,
				array(
					(object) array('title' => 'Testing3'),
					(object) array('title' => 'Testing4')
				),
				null
			),

			// Testing 'stdClass' type, index='title' without specific offset or limit
			array(
				'title, id',
				'#__dbtest',
				'title',
				'stdClass',
				0,
				0,
				array(
					'Testing' => (object) array('title' => 'Testing', 'id' => '1'),
					'Testing2' => (object) array('title' => 'Testing2', 'id' => '2'),
					'Testing3' => (object) array('title' => 'Testing3', 'id' => '3'),
					'Testing4' => (object) array('title' => 'Testing4', 'id' => '4')
				),
				null,
			),

			// Testing 'UnexistingClass' type, index='title' without specific offset or limit
			array(
				'title',
				'#__dbtest',
				'title',
				'UnexistingClass',
				0,
				0,
				array(),
				'InvalidArgumentException',
			),
		);
	}

	/**
	 * Test foreach control
	 *
	 * @param   array           $options    Array of options
	 * @param   string          $select     Fields to select
	 * @param   string          $from       Table to search for
	 * @param   array           $expected   Array of expected results
	 * @param   boolean|string  $exception  Exception thrown
	 *
	 * @return  void
	 *
	 * @dataProvider casesForEachData
	 *
	 * @since   12.1
	 */
	public function testForEach($select, $from, $column, $class, $limit, $offset, $expected, $exception)
	{
		if ($exception)
		{
			$this->setExpectedException($exception);
		}
		self::$driver->setQuery(self::$driver->getQuery(true)->select($select)->from($from)->setLimit($limit, $offset));
		$iterator = new JDatabaseIteratorMysql(self::$driver->execute(), $column, $class);

		// Run the Iterator pattern
		$this->assertThat(
			iterator_to_array($iterator),
			$this->equalTo($expected),
			__LINE__
		);
	}

	/**
	 * Test count
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testCount()
	{
		self::$driver->setQuery(self::$driver->getQuery(true)->select('title')->from('#__dbtest'));
		$this->assertThat(
			count(new JDatabaseIteratorMysql(self::$driver->execute())),
			$this->equalTo(4),
			__LINE__
		);

		self::$driver->setQuery(self::$driver->getQuery(true)->select('title')->from('#__dbtest')->setLimit(2));
		$this->assertThat(
			count(new JDatabaseIteratorMysql(self::$driver->execute())),
			$this->equalTo(2),
			__LINE__
		);

		self::$driver->setQuery(self::$driver->getQuery(true)->select('title')->from('#__dbtest')->setLimit(2,3));
		$this->assertThat(
			count(new JDatabaseIteratorMysql(self::$driver->execute())),
			$this->equalTo(1),
			__LINE__
		);
	}
}
