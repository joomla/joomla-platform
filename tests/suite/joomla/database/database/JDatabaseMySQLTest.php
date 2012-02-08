<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Database
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

require_once JPATH_PLATFORM.'/joomla/log/log.php';
require_once JPATH_PLATFORM.'/joomla/database/database.php';
require_once JPATH_PLATFORM.'/joomla/database/database/mysql.php';
require_once JPATH_PLATFORM.'/joomla/database/query.php';
require_once JPATH_PLATFORM.'/joomla/database/database/mysqlquery.php';

/**
 * Test class for JDatabaseMySQL.
 *
 * @package     Joomla.UnitTest
 * @subpackage  Database
 */
class JDatabaseMySQLTest extends JoomlaDatabaseTestCase
{
	/**
	 * @var  JDatabaseMySQL
	 */
	protected $object;

	/**
	 * Data for the testEscape test.
	 *
	 * @return  array
	 *
	 * @since   11.1
	 */
	public function dataTestEscape()
	{
		return array(
			array("'%_abc123", false, '\\\'%_abc123'),
			array("'%_abc123", true, '\\\'\\%\_abc123'),
		);
	}

	/**
	 * Gets the data set to be loaded into the database during setup
	 *
	 * @return  xml dataset
	 *
	 * @since   11.1
	 */
	protected function getDataSet()
	{
		return $this->createXMLDataSet(__DIR__.'/stubs/database.xml');
	}

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	protected function setUp()
	{
		@include_once JPATH_TESTS . '/config_mysql.php';
		if (class_exists('JMySQLTestConfig')) {
			$config = new JMySQLTestConfig;
		} else {
			$this->markTestSkipped('There is no MySQL test config file present.');
		}
		$this->object = JDatabase::getInstance(
			array(
				'driver' => $config->dbtype,
				'database' => $config->db,
				'host' => $config->host,
				'user' => $config->user,
				'password' => $config->password
			)
		);

		parent::setUp();
	}

	/**
	 * @todo Implement test__destruct().
	 */
	public function test__destruct()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @todo Implement testConnected().
	 */
	public function testConnected()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * Tests the JDatabaseMySQL dropTable method.
	 *
	 * @return  void
	 *
	 * @since   11.4
	 */
	public function testDropTable()
	{
		$this->assertThat(
			$this->object->dropTable('#__bar', true),
			$this->isInstanceOf('JDatabaseMySQL'),
			'The table is dropped if present.'
		);
	}

	/**
	 * Tests the JDatabaseMySQL escape method.
	 *
	 * @param   string   $text   The string to be escaped.
	 * @param   boolean  $extra  Optional parameter to provide extra escaping.
	 *
	 * @return  void
	 *
	 * @since   11.1
	 * @dataProvider  dataTestEscape
	 */
	public function testEscape($text, $extra, $result)
	{
		$this->assertThat(
			$this->object->escape($text, $extra),
			$this->equalTo($result),
			'The string was not escaped properly'
		);
	}

	/**
	 * Test getAffectedRows method.
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	public function testGetAffectedRows()
	{
		$query = $this->object->getQuery(true);
		$query->delete();
		$query->from('jos_dbtest');
		$this->object->setQuery($query);

		$result = $this->object->query();

		$this->assertThat(
			$this->object->getAffectedRows(),
			$this->equalTo(4),
			__LINE__
		);
	}

	/**
	 * @todo Implement testGetCollation().
	 */
	public function testGetCollation()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * Test getExporter method.
	 *
	 * @return  void
	 *
	 * @since   11.4
	 */
	public function testGetExporter()
	{
		$this->assertThat(
			$this->object->getExporter(),
			$this->isInstanceOf('JDatabaseExporterMySQL'),
			'Line:'.__LINE__.' The getExporter method should return the correct exporter.'
		);
	}

	/**
	 * Test getImporter method.
	 *
	 * @return  void
	 *
	 * @since   11.4
	 */
	public function testGetImporter()
	{
		$this->assertThat(
			$this->object->getImporter(),
			$this->isInstanceOf('JDatabaseImporterMySQL'),
			'Line:'.__LINE__.' The getImporter method should return the correct importer.'
		);
	}

	/**
	 * @todo Implement testGetNumRows().
	 */
	public function testGetNumRows()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * Tests the JDatabaseMySQL getTableCreate method.
	 *
	 * @return  void
	 *
	 * @since   11.4
	 */
	public function testGetTableCreate()
	{
		$this->assertThat(
			$this->object->getTableCreate('#__dbtest'),
			$this->isType('array'),
			'The statement to create the table is returned in an array.'
		);
	}

	/**
	 * @todo Implement testGetTableColumns().
	 */
	public function testGetTableColumns()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * Tests the JDatabaseMySQL getTableKeys method.
	 *
	 * @return  void
	 *
	 * @since   11.4
	 */
	public function testGetTableKeys()
	{
		$this->assertThat(
			$this->object->getTableKeys('#__dbtest'),
			$this->isType('array'),
			'The list of keys for the table is returned in an array.'
		);
	}

	/**
	 * Tests the JDatabaseMySQL getTableList method.
	 *
	 * @return  void
	 *
	 * @since   11.4
	 */
	public function testGetTableList()
	{
		$this->assertThat(
			$this->object->getTableList(),
			$this->isType('array'),
			'The list of tables for the database is returned in an array.'
		);
	}

	/**
	 * Test getVersion method.
	 *
	 * @return  void
	 *
	 * @since   11.4
	 */
	public function testGetVersion()
	{
		$this->assertThat(
			strlen($this->object->getVersion()),
			$this->greaterThan(0),
			'Line:'.__LINE__.' The getVersion method should return something without error.'
		);
	}

	/**
	 * @todo Implement testInsertid().
	 */
	public function testInsertid()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * Test loadAssoc method.
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	public function testLoadAssoc()
	{
		$query = $this->object->getQuery(true);
		$query->select('title');
		$query->from('jos_dbtest');
		$this->object->setQuery($query);
		$result = $this->object->loadAssoc();

		$this->assertThat(
			$result,
			$this->equalTo(array('title' => 'Testing')),
			__LINE__
		);
	}

	/**
	 * Test loadAssocList method.
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	public function testLoadAssocList()
	{
		$query = $this->object->getQuery(true);
		$query->select('title');
		$query->from('jos_dbtest');
		$this->object->setQuery($query);
		$result = $this->object->loadAssocList();

		$this->assertThat(
			$result,
			$this->equalTo(array(
				array('title' => 'Testing'),
				array('title' => 'Testing2'),
				array('title' => 'Testing3'),
				array('title' => 'Testing4'),
			)),
			__LINE__
		);
	}

	/**
	 * Test loadColumn method
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	public function testLoadColumn()
	{
		$query = $this->object->getQuery(true);
		$query->select('title');
		$query->from('jos_dbtest');
		$this->object->setQuery($query);
		$result = $this->object->loadColumn();

		$this->assertThat(
			$result,
			$this->equalTo(array('Testing', 'Testing2', 'Testing3', 'Testing4')),
			__LINE__
		);
	}

	/**
	 * @todo Implement testLoadNextObject().
	 */
	public function testLoadNextObject()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @todo Implement testLoadNextRow().
	 */
	public function testLoadNextRow()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * Test loadObject method
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	public function testLoadObject()
	{
		$query = $this->object->getQuery(true);
		$query->select('*');
		$query->from('jos_dbtest');
		$query->where('description='.$this->object->quote('three'));
		$this->object->setQuery($query);
		$result = $this->object->loadObject();

		$objCompare = new stdClass;
		$objCompare->id = 3;
		$objCompare->title = 'Testing3';
		$objCompare->start_date = '1980-04-18 00:00:00';
		$objCompare->description = 'three';

		$this->assertThat(
			$result,
			$this->equalTo($objCompare),
			__LINE__
		);
	}

	/**
	 * Test loadObjectList method
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	public function testLoadObjectList()
	{
		$query = $this->object->getQuery(true);
		$query->select('*');
		$query->from('jos_dbtest');
		$query->order('id');
		$this->object->setQuery($query);
		$result = $this->object->loadObjectList();

		$expected = array();

		$objCompare = new stdClass;
		$objCompare->id = 1;
		$objCompare->title = 'Testing';
		$objCompare->start_date = '1980-04-18 00:00:00';
		$objCompare->description = 'one';

		$expected[] = clone $objCompare;

		$objCompare = new stdClass;
		$objCompare->id = 2;
		$objCompare->title = 'Testing2';
		$objCompare->start_date = '1980-04-18 00:00:00';
		$objCompare->description = 'one';

		$expected[] = clone $objCompare;

		$objCompare = new stdClass;
		$objCompare->id = 3;
		$objCompare->title = 'Testing3';
		$objCompare->start_date = '1980-04-18 00:00:00';
		$objCompare->description = 'three';

		$expected[] = clone $objCompare;

		$objCompare = new stdClass;
		$objCompare->id = 4;
		$objCompare->title = 'Testing4';
		$objCompare->start_date = '1980-04-18 00:00:00';
		$objCompare->description = 'four';

		$expected[] = clone $objCompare;

		$this->assertThat(
			$result,
			$this->equalTo($expected),
			__LINE__
		);
	}

	/**
	 * Test loadResult method
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	public function testLoadResult()
	{
		$query = $this->object->getQuery(true);
		$query->select('id');
		$query->from('jos_dbtest');
		$query->where('title='.$this->object->quote('Testing2'));

		$this->object->setQuery($query);
		$result = $this->object->loadResult();

		$this->assertThat(
			$result,
			$this->equalTo(2),
			__LINE__
		);

	}

	/**
	 * Test loadRow method
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	public function testLoadRow()
	{
		$query = $this->object->getQuery(true);
		$query->select('*');
		$query->from('jos_dbtest');
		$query->where('description='.$this->object->quote('three'));
		$this->object->setQuery($query);
		$result = $this->object->loadRow();

		$expected = array(3, 'Testing3', '1980-04-18 00:00:00', 'three');

		$this->assertThat(
			$result,
			$this->equalTo($expected),
			__LINE__
		);
	}

	/**
	 * Test loadRowList method
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	public function testLoadRowList()
	{
		$query = $this->object->getQuery(true);
		$query->select('*');
		$query->from('jos_dbtest');
		$query->where('description='.$this->object->quote('one'));
		$this->object->setQuery($query);
		$result = $this->object->loadRowList();

		$expected = array(
			array(1, 'Testing', '1980-04-18 00:00:00', 'one'),
			array(2, 'Testing2', '1980-04-18 00:00:00', 'one')
		);

		$this->assertThat(
			$result,
			$this->equalTo($expected),
			__LINE__
		);
	}

	/**
	 * Test the JDatabaseMySQL::query() method
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	public function testQuery()
	{
		$this->object->setQuery("REPLACE INTO `jos_dbtest` SET `id` = 5, `title` = 'testTitle'");

		$this->assertThat(
			$this->object->query(),
			$this->isTrue(),
			__LINE__
		);

		$this->assertThat(
			$this->object->insertid(),
			$this->equalTo(5),
			__LINE__
		);

	}

	/**
	 * @todo Implement testSelect().
	 */
	public function testSelect()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @todo Implement testSetUTF().
	 */
	public function testSetUTF()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * Test Test method - there really isn't a lot to test here, but
	 * this is present for the sake of completeness
	 */
	public function testTest()
	{
		$this->assertThat(
			JDatabaseMySQL::test(),
			$this->isTrue(),
			__LINE__
		);
	}

	/**
	 * @todo Implement testUpdateObject().
	 */
	public function testUpdateObject()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * Data provider for the testForEach method
	 *
	 * @return  array
	 *
	 * @since   11.3
	 */
	public function getForEachData()
	{
		return array(
			// Testing 'assoc' type without specific key, offset or limit
			array(
				'assoc',
				null,
				'title',
				'#__dbtest',
				0,
				0,
				array(
					array('title' => 'Testing'),
					array('title' => 'Testing2'),
					array('title' => 'Testing3'),
					array('title' => 'Testing4')
				)
			),

			// Testing 'assoc' type, offset=2 without specific key, or limit
			array(
				'assoc',
				null,
				'title',
				'#__dbtest',
				2,
				0,
				array(
					array('title' => 'Testing3'),
					array('title' => 'Testing4')
				)
			),

			// Testing 'assoc' type, limit=2 without specific key, or offset
			array(
				'assoc',
				null,
				'title',
				'#__dbtest',
				0,
				2,
				array(
					array('title' => 'Testing'),
					array('title' => 'Testing2')
				)
			),

			// Testing 'assoc' type, key='title' without specific limit, or offset
			array(
				'assoc',
				'title',
				'title, id',
				'#__dbtest',
				0,
				0,
				array(
					'Testing' => array('title' => 'Testing', 'id' => '1'),
					'Testing2' => array('title' => 'Testing2', 'id' => '2'),
					'Testing3' => array('title' => 'Testing3', 'id' => '3'),
					'Testing4' => array('title' => 'Testing4', 'id' => '4')
				)
			),

			// Testing 'stdClass' type without specific key, offset or limit
			array(
				'stdClass',
				null,
				'title',
				'#__dbtest',
				0,
				0,
				array(
					(object) array('title' => 'Testing'),
					(object) array('title' => 'Testing2'),
					(object) array('title' => 'Testing3'),
					(object) array('title' => 'Testing4')
				)
			),

			// Testing 'stdClass' type, key='title' without specific limit, or offset
			array(
				'stdClass',
				'title',
				'title, id',
				'#__dbtest',
				0,
				0,
				array(
					'Testing' => (object) array('title' => 'Testing', 'id' => '1'),
					'Testing2' => (object) array('title' => 'Testing2', 'id' => '2'),
					'Testing3' => (object) array('title' => 'Testing3', 'id' => '3'),
					'Testing4' => (object) array('title' => 'Testing4', 'id' => '4')
				)
			),

			// Testing 'array' type without specific key, offset or limit
			array(
				'array',
				null,
				'title',
				'#__dbtest',
				0,
				0,
				array(
					array(0 => 'Testing'),
					array(0 => 'Testing2'),
					array(0 => 'Testing3'),
					array(0 => 'Testing4')
				)
			),

			// Testing 'array' type, key='title' without specific limit, or offset
			array(
				'array',
				0,
				'title, id',
				'#__dbtest',
				0,
				0,
				array(
					'Testing' => array(0 => 'Testing', 1 => '1'),
					'Testing2' => array(0 => 'Testing2', 1 => '2'),
					'Testing3' => array(0 => 'Testing3', 1 => '3'),
					'Testing4' => array(0 => 'Testing4', 1 => '4')
				)
			),

		);
	}

	/**
	 * Test foreach control
	 *
	 * @covers JDatabase::rewind
	 * @covers JDatabase::current
	 * @covers JDatabase::key
	 * @covers JDatabase::next
	 * @covers JDatabase::valid
	 *
	 * @return  void
	 *
	 * @dataProvider getForEachData
	 *
	 * @since   12.1
	 */
	public function testForEach($type, $key, $select, $from, $offset, $limit, $results)
	{
		$this->object->setType($type)->setKey($key)->setQuery($this->object->getQuery(true)->select($select)->from($from), $offset, $limit);

		// Run the Iterator pattern
		foreach ($this->object as $i => $result)
		{
			$this->assertThat(
				$result,
				$this->equalTo($results[$i]),
				__LINE__
			);
		}

		// Running twice
		foreach ($this->object as $i => $result)
		{
			$this->assertThat(
				$result,
				$this->equalTo($results[$i]),
				__LINE__
			);
		}

		// Test cloning and nested loops
		$dbo2 = clone $this->object;
		foreach ($this->object as $i => $result)
		{
			$this->assertThat(
				$result,
				$this->equalTo($results[$i]),
				__LINE__
			);
			foreach ($dbo2 as $i2 => $result2)
			{
				$this->assertThat(
					$result2,
					$this->equalTo($results[$i2]),
					__LINE__
				);
			}
		}
	}
}
