<?php
/**
 * @version		$Id: JDatabasePostgreSQLTest.php 20196 2011-01-09 02:40:25Z Gabriele Pongelli $
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

require_once JPATH_PLATFORM.'/joomla/log/log.php';
require_once JPATH_PLATFORM.'/joomla/database/database.php';
require_once JPATH_PLATFORM.'/joomla/database/database/postgresql.php';
require_once JPATH_PLATFORM.'/joomla/database/databasequery.php';
require_once JPATH_PLATFORM.'/joomla/database/database/postgresqlquery.php';

/**
 * Test class for JDatabasePostgreSQL.
 */
class JDatabasePostgreSQLTest extends JoomlaDatabaseTestCase
{
	/**
	 * @var  JDatabasePostgreSQL
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
			/* ' will be escaped and become '' */
			array("'%_abc123", false, '\'\'%_abc123'),
			array("'%_abc123", true, '\'\'\%\_abc123'),
			/* ' and \ will be escaped: the first become '', the latter \\ */
			array("\'%_abc123", false, '\\\\\'\'%_abc123'),
			array("\'%_abc123", true, '\\\\\'\'\%\_abc123'),
		);
	}

	/**
	 * Data for the testTransactionRollback test.
	 *
	 * @return  array
	 *
	 * @since   11.1
	 */
	public function dataTestTransactionRollback()
	{
		return array(
			array ( null , 0 ),
			array ( 'transactionSavepoint' , 1 )
		);
	}
	
	/**
	 * Data for the getCreateDbQuery test.
	 *
	 * @return  array
	 *
	 * @since   11.1
	 */
	public function dataGetCreateDbQuery()
	{
		$obj = new stdClass;
		$obj->db_user = 'testName';
		$obj->db_name = 'testDb';
		
		return array(
				array( $obj, null )
			);
	}

	/**
	 * Data for the TestReplacePrefix test.
	 *
	 * @return  array
	 *
	 * @since   11.3
	 */
	public function dataTestReplacePrefix()
	{
		return array(
			/* no prefix inside, no change */
			array('SELECT * FROM table', '#__', 'SELECT * FROM table'),
			/* the prefix inside double quote has to be changed */
			array('SELECT * FROM "#__table"', '#__', 'SELECT * FROM "jos_table"'),
			/* the prefix inside single quote hasn't to be changed */
			array('SELECT * FROM \'#__table\'', '#__', 'SELECT * FROM \'#__table\''),
			/* mixed quote case */
			array('SELECT * FROM \'#__table\', "#__tableSecond"', '#__', 'SELECT * FROM \'#__table\', "jos_tableSecond"'),
			/* the prefix used in sequence name (single quote) has to be changed */
			array('SELECT * FROM currval(\'#__table_id_seq\'::regclass)', '#__', 'SELECT * FROM currval(\'jos_table_id_seq\'::regclass)'),
			/* using another prefix */
			array('SELECT * FROM "#!-_table"', '#!-_', 'SELECT * FROM "jos_table"'),
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
		return $this->createXMLDataSet(dirname(__FILE__).'/TestStubs/database.xml');
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
		@include_once JPATH_TESTS . '/config_pgsql.php';
		if (class_exists('JPostgreSQLTestConfig')) {
			$config = new JPostgreSQLTestConfig;
		} else {
			$this->markTestSkipped('There is no PostgreSQL test config file present.');
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
	 * Tear down function used to clean inserted data or insert back deleted data.
	 * 
	 */
	protected function tearDown()
	{
		parent::tearDown();
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
	 * Check if connected() method returns true.
	 */
	public function testConnected()
	{
		$this->assertThat(
			$this->object->connected(),
			$this->equalTo(true),
			'Not connected to database'
		);
	}

	/**
	 * Tests the JDatabasePostgreSQL escape method.
	 *
	 * @param   string  $text   The string to be escaped.
	 * @param   bool    $extra  Optional parameter to provide extra escaping.
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
	 * @todo Implement testExplain().
	 */
	public function testExplain()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
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
	 * Tests the JDatabasePostgreSQL getCollation method.
	 * 
	 * @return  void
	 *
	 * @since   11.1
	 */
	public function testGetCollation()
	{
		$this->assertThat(
			$this->object->getCollation(),
			$this->equalTo("it_IT.UTF-8"),
			__LINE__
		);
	}

	/**
	 * @todo Implement testGetEscaped().
	 */
	public function testGetEscaped()
	{
		// TODO Check that this method proxies to "escape".

		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * Tests the JDatabasePostgreSQL getNumRows method.
	 * 
	 * @return  void
	 *
	 * @since   11.1
	 */
	public function testGetNumRows()
	{	
		$query = $this->object->getQuery(true);
		$query->select('*');
		$query->from('jos_dbtest');
		$query->where('description='.$this->object->quote('one'));
		$this->object->setQuery($query);
		
		$res = $this->object->query();
		
		$this->assertThat(
			$this->object->getNumRows( $res ),
			$this->equalTo( 2 ),
			__LINE__
		);
	}

	/**
	 * @todo Implement testGetTableCreate().
	 */
	public function testGetTableCreate()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @todo Implement testGetTableFields().
	 */
	public function testGetTableFields()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * Tests the JDatabasePostgreSQL getTableList method.
	 * 
	 * @return  void
	 *
	 * @since   11.1
	 */
	public function testGetTableList()
	{
		$expected = array( "0" => "jos_assets", "1" => "jos_categories", "2" => "jos_content", 
					"3" => "jos_core_log_searches",	"4" => "jos_extensions", 				"5" => "jos_languages",
					"6" => "jos_log_entries",		"7" => "jos_menu", 						"8" => "jos_menu_types", 
					"9" => "jos_modules",			"10" => "jos_modules_menu",				"11" => "jos_schemas", 
					"12" => "jos_session",			"13" => "jos_updates",					"14" => "jos_update_categories",	
					"15" => "jos_update_sites",		"16" => "jos_update_sites_extensions",	"17" => "jos_usergroups",
		 		 	"18" => "jos_users",			"19" => "jos_user_profiles", 			"20" => "jos_user_usergroup_map",
					"21" => "jos_viewlevels",		"22" => "jos_dbtest" );
		
		$this->assertThat(
			$this->object->getTableList(),
			$this->equalTo($expected),
			__LINE__
		);
	}

	/**
	 * Tests the JDatabasePostgreSQL getVersion method.
	 * 
	 * @return  void
	 *
	 * @since   11.1.
	 */
	public function testGetVersion()
	{
		$this->assertThat(
			$this->object->getVersion(),
			$this->equalTo("9.0.4"),
			__LINE__
		);
	}

	/**
	 * Tests the JDatabasePostgreSQL hasUTF method.
	 * 
	 * @return  void
	 *
	 * @since   11.1
	 */
	public function testHasUTF()
	{
		$this->assertThat(
			$this->object->hasUTF(),
			$this->isTrue(),
			__LINE__
		);
	}

	/**
	 * Tests the JDatabasePostgreSQL insertId method.
	 * 
	 * @return  void
	 *
	 * @since   11.1
	 */
	public function testInsertid()
	{
		/* REPLACE is not present in PostgreSQL */
		$query = $this->object->getQuery(true);
		$query->delete();
		$query->from('jos_dbtest')->where('id=5');
		$this->object->setQuery($query);
		$result = $this->object->query();
		
		/* increment the sequence automatically with INSERT INTO, 
		 * first insert to have a common starting point */
		$query = $this->object->getQuery(true);		
		$query->insert('jos_dbtest')
			  ->columns('title,start_date,description')
			  ->values("'testTitle','1970-01-01','testDescription'");
		$this->object->setQuery($query);
		$this->object->query();
		
		/* get the current sequence value */
		$actualVal = $this->object->getQuery(true);
		$actualVal->select("currval('jos_dbtest_id_seq'::regclass)");
	  	$this->object->setQuery( $actualVal );
	  	$idActualVal = $this->object->loadRow();
		
		/* insert again, then call insertid() */
		$secondInsertQuery = $this->object->getQuery(true);		
		$secondInsertQuery->insert('jos_dbtest')
			  	 ->columns('title,start_date,description')
			  	 ->values("'testTitle2nd','1971-01-01','testDescription2nd'");
		$this->object->setQuery($secondInsertQuery);
		$this->object->query();
		
		/* get insertid of last INSERT INTO */
		$insertIdArray = $this->object->insertid();
		
		/* check if first sequence val +1 is equal to last sequence val */
		$this->assertThat(
			$insertIdArray[0],
			$this->equalTo($idActualVal[0]+1),
			__LINE__
		);
	}

	/**
	 * @todo Implement testInsertObject().
	 */
	public function testInsertObject()
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
	 * Test loadResultArray method
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	public function testLoadResultArray()
	{		
		$query = $this->object->getQuery(true);
		$query->select('Title');
		$query->from('jos_dbtest');
		$query->where('description='.$this->object->quote('one'));

		$this->object->setQuery($query);
		$result = $this->object->loadResultArray();

		$expected = array( 0 => 'Testing', 1 => 'Testing2' );
		
		$this->assertThat(
			$result,
			$this->equalTo($expected),
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
	 * Test the JDatabasePostgreSQL::query() method
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	public function testQuery()
	{
		/* REPLACE is not present in PostgreSQL */
		$query = $this->object->getQuery(true);
		$query->delete();
		$query->from('jos_dbtest')->where('id=5');
		$this->object->setQuery($query);
		$result = $this->object->query();
		
		
		$query = $this->object->getQuery(true);		
		$query->insert('jos_dbtest')
			  ->columns('id,title,start_date,description')
			  ->values("5, 'testTitle','1970-01-01','testDescription'")
			  ->returning('id');
		
		$this->object->setQuery($query);
		$arr = $this->object->loadResult();
		
		
		$this->assertThat(
			$arr,
			$this->equalTo(5),
			__LINE__
		);
	}

	/**
	 * @todo Implement testQueryBatch().
	 */
	public function testQueryBatch()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * Tests the JDatabasePostgreSQL select method.
	 * 
	 * @return  void
	 *
	 * @since   11.1
	 */
	public function testSelect()
	{
		/* it's not possible to select a database, already done during connection, return true */
		$this->assertThat(
			$this->object->select(),
			$this->isTrue(),
			__LINE__
		);
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
			JDatabasePostgreSQL::test(),
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
	 * Tests the JDatabasePostgreSQL transactionCommit method.
	 * 
	 * @return  void
	 *
	 * @since   11.1
	 */
	public function testTransactionCommit()
	{		
		$this->object->transactionStart();
		$queryIns = $this->object->getQuery(true);		
		$queryIns->insert('jos_dbtest')
			  	 ->columns('id,title,start_date,description')
			  	 ->values("6, 'testTitle','1970-01-01','testDescription'");
		
		$this->object->setQuery($queryIns);
		$arr = $this->object->query();
		
		$this->object->transactionCommit();
		
		/* check if value is present */
		$queryCheck = $this->object->getQuery(true);		
		$queryCheck->select('*')
				 ->from('jos_dbtest')
				 ->where('id=6');
		$this->object->setQuery($queryCheck);		 
		$result = $this->object->loadRow();

		$expected = array(6, 'testTitle', '1970-01-01 00:00:00','testDescription') ;
		
		$this->assertThat(
			$result,
			$this->equalTo($expected),
			__LINE__
		);
	}

	/**
	 * Tests the JDatabasePostgreSQL transactionRollback method, 
	 * with and without savepoint.
	 * 
	 * @return  void
	 *
	 * @since   11.1
	 * @dataProvider dataTestTransactionRollback
	 */
	public function testTransactionRollback ( $toSavepoint, $tupleCount )
	{
		$this->object->transactionStart();
		
		/* try to insert this tuple, inserted only when savepoint != null */
		$queryIns = $this->object->getQuery(true);		
		$queryIns->insert('jos_dbtest')
			  	 ->columns('id,title,start_date,description')
			  	 ->values("7, 'testRollback','1970-01-01','testRollbackSp'");
		$this->object->setQuery($queryIns);
		$arr = $this->object->query();
		
		/* create savepoint only if is passed by data provider */
		if ( !is_null($toSavepoint) )
		{
			$this->object->transactionSavepoint($toSavepoint);
		}
		
		/* try to insert this tuple, always rolled back */
		$queryIns = $this->object->getQuery(true);		
		$queryIns->insert('jos_dbtest')
			  	 ->columns('id,title,start_date,description')
			  	 ->values("8, 'testRollback','1972-01-01','testRollbackSp'");
		$this->object->setQuery($queryIns);
		$arr = $this->object->query();
		
		$this->object->transactionRollback( $toSavepoint ); 
		
		/* release savepoint and commit only if a savepoint exists */
		if ( !is_null($toSavepoint) )
		{
			$this->object->releaseTransactionSavepoint($toSavepoint);
			$this->object->transactionCommit();
		}
		
				
		/* find how many rows have description='testRollbackSp' :
		 *   - 0 if a savepoint doesn't exist 
		 *   - 1 if a savepoint exists 
		 */
		$queryCheck = $this->object->getQuery(true);		
		$queryCheck->select('*')
				 ->from('jos_dbtest')
				 ->where("description='testRollbackSp'");
		$this->object->setQuery($queryCheck);		 
		$result = $this->object->loadRowList();
		
		$this->assertThat(
			count($result),
			$this->equalTo( $tupleCount ),
			__LINE__
		);
	}

	/**
	 * Tests the JDatabasePostgreSQL transactionStart method.
	 * 
	 * @return  void
	 *
	 * @since   11.1
	 */
	public function testTransactionStart()
	{
		$this->object->transactionRollback( );
		$this->object->transactionStart();
		$queryIns = $this->object->getQuery(true);		
		$queryIns->insert('jos_dbtest')
			  	 ->columns('id,title,start_date,description')
			  	 ->values("6, 'testTitle','1970-01-01','testDescription'");
		
		$this->object->setQuery($queryIns);
		$arr = $this->object->query();
		
		/* check if is present an exclusive lock, it means a transaction is running */
		$queryCheck = $this->object->getQuery(true);		
		$queryCheck->select('*')
			  	   ->from('pg_catalog.pg_locks')
			  	   ->where('transactionid NOTNULL');
  	   	$this->object->setQuery($queryCheck);
		$result = $this->object->loadAssocList();

		$this->assertThat(
			count($result),
			$this->equalTo(1),
			__LINE__
		);
	}
	
	/**
	 * @todo Implement testReleaseTransactionSavepoint().
	 */
	public function testReleaseTransactionSavepoint( /*$savepointName*/ )
	{
		$this->markTestSkipped('This command is tested inside testTransactionRollback.');
	}

	/**
	 * Tests the JDatabasePostgreSQL replacePrefix method.
	 * 
	 * @param   stringToReplace	$text	The string in which replace the prefix.
	 * @param   prefix			$text   The prefix.
	 * @param   expected		$text   The string expected.
	 *
	 * @return  void
	 *
	 * @since   11.1
	 * @dataProvider  dataTestReplacePrefix
	 */
	public function testReplacePrefix( $stringToReplace, $prefix, $expected )
	{
		//$this->markTestSkipped('This command is tested inside testTransactionRollback.');
		
		
		$result = $this->object->replacePrefix($stringToReplace, $prefix);
		
		$this->assertThat(
			$result,
			$this->equalTo($expected),
			__LINE__
		);
	}

	/**
	 * @todo Implement testTransactionSavepoint().
	 */
	public function testTransactionSavepoint( /*$savepointName*/ )
	{
		$this->markTestSkipped('This command is tested inside testTransactionRollback.');
	}
	
	/**
	 * Tests the JDatabasePostgreSQL getCreateDbQuery method.
	 * 
	 * @return  void
	 *
	 * @dataProvider dataGetCreateDbQuery
	 */
	public function testGetCreateDbQuery( $options, $utf )
	{
		//$this->markTestSkipped('This command is tested inside testCreateDatabase.');

		$expected = 'CREATE DATABASE ' . $options->db_name . ' OWNER ' . $options->db_user ;

		$result = $this->object->getCreateDbQuery($options, $utf);
		
		$this->assertThat(
			$result,
			$this->equalTo($expected),
			__LINE__
		);
	}
	
}