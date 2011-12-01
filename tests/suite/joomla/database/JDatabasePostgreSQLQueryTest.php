<?php
/**
 * @version    $Id: JDatabasePostgreSQLQueryTest.php gpongelli $
 * @package    Joomla.UnitTest
 * 
 * @copyright  Copyright (C) 2005 - 2011 Open Source Matters. All rights reserved.
 * @license    GNU General Public License
 */

require_once __DIR__ . '/JDatabasePostgreSQLQueryInspector.php';
require_once JPATH_PLATFORM . '/joomla/database/database/postgresqlquery.php';
require_once JPATH_TESTS . '/includes/JoomlaPostgreSQLTestCase.php';

/**
 * Test class for JDatabasePostgreSQLQuery.
 * 
 * @package     Joomla.UnitTest
 * @subpackage  Database
 * 
 * @since       11.3
 */
class JDatabasePostgreSQLQueryTest extends JoomlaPostgreSQLTestCase
{
	/**
	 * @var  JDatabase  A mock of the JDatabase object for testing purposes.
	 */
	protected $dbo;

	/**
	 * Data for the testNullDate test.
	 *
	 * @return  array
	 *
	 * @since   11.3
	 */
	public function dataTestNullDate()
	{
		return array(
			// quoted, expected
			array(true, "'1970-01-01 00:00:00'"),
			array(false, "1970-01-01 00:00:00"),
		);
	}

	/**
	 * Data for the testNullDate test.
	 *
	 * @return  array
	 *
	 * @since   11.3
	 */
	public function dataTestQuote()
	{
		return array(
			// text, escaped, expected
			array('text', false, '\'text\''),
		);
	}

	/**
	 * Data for the testJoin test.
	 *
	 * @return  array
	 *
	 * @since   11.3
	 */
	public function dataTestJoin()
	{
		return array(
			// $type, $conditions
			array('', 		'b ON b.id = a.id'),
			array('INNER',	'b ON b.id = a.id'),
			array('OUTER',	'b ON b.id = a.id'),
			array('LEFT',	'b ON b.id = a.id'),
			array('RIGHT',	'b ON b.id = a.id'),
		);
	}

	/**
	 * Data for the testLock test.
	 *
	 * @return  array
	 *
	 * @since   11.3
	 */
	public function dataTestLock()
	{
		return array(
			// $table_name, $lock_type
			array('jos_dbtest', 'ACCESS SHARE'),
			array('jos_dbtest',	'ROW SHARE'),
			array('jos_dbtest',	'ROW EXCLUSIVE'),
			array('jos_dbtest',	'SHARE UPDATE EXCLUSIVE'),
			array('jos_dbtest',	'SHARE'),
			array('jos_dbtest',	'SHARE ROW EXCLUSIVE'),
			array('jos_dbtest',	'EXCLUSIVE'),
			array('jos_dbtest',	'ACCESS EXCLUSIVE'),
		);
	}

	/**
	 * A mock callback for the database escape method.
	 *
	 * We use this method to ensure that JDatabaseQuery's escape method uses the
	 * the database object's escape method.
	 *
	 * @param   string  $text  The input text.
	 *
	 * @return  string
	 *
	 * @since   11.3
	 */
	public function getMockEscape($text)
	{
		return "_{$text}_";
	}

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 * 
	 * @return  void
	 */
	protected function setUp()
	{
		$this->dbo = $this->getMockDatabase();

		// Mock the escape method to ensure the API is calling the DBO's escape method.
		$this->assignMockCallbacks(
			$this->dbo,
			array(
				'escape' => array($this, 'getMockEscape'),
			)
		);
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 * 
	 * @return  void
	 */
	protected function tearDown()
	{
	}

	/**
	 * Test for the JDatabaseQueryPostgreSQL::__string method for a 'select' case.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function test__toStringSelect()
	{
		$q = new JDatabasePostgreSQLQueryInspector($this->dbo);

		$q->select('a.id')
			->from('a')
			->innerJoin('b ON b.id = a.id')
			->where('b.id = 1')
			->group('a.id')
				->having('COUNT(a.id) > 3')
			->order('a.id');

		$this->assertThat(
			(string) $q,
			$this->equalTo(
				"\nSELECT a.id" .
				"\nFROM a" .
				"\nINNER JOIN b ON b.id = a.id" .
				"\nWHERE b.id = 1" .
				"\nGROUP BY a.id" .
				"\nHAVING COUNT(a.id) > 3" .
				"\nORDER BY a.id"
			),
			'Tests for correct rendering.'
		);
	}

	/**
	 * Test for the JDatabaseQuery::__string method for a 'update' case.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function test__toStringUpdate()
	{
		$q = new JDatabasePostgreSQLQueryInspector($this->dbo);

		$q->update('#__foo AS a')
			->join('INNER', 'b ON b.id = a.id')
			->set('a.id = 2')
			->where('b.id = 1');

		$this->assertThat(
			(string) $q,
			$this->equalTo(
				"\nUPDATE #__foo AS a" .
				"\nSET a.id = 2" .
				"\nFROM b" .
				"\nWHERE b.id = 1 AND b.id = a.id"
			),
			'Tests for correct rendering.'
		);
	}

	/**
	 * Test for the castAsChar method.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function testCastAsChar()
	{
		$q = new JDatabasePostgreSQLQueryInspector($this->dbo);

		$this->assertThat(
			$q->castAsChar('123'),
			$this->equalTo('123'),
			'The default castAsChar behaviour is to return the input.'
		);

	}

	/**
	 * Test for the charLength method.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function testCharLength()
	{
		$q = new JDatabasePostgreSQLQueryInspector($this->dbo);

		$this->assertThat(
			$q->charLength('a.title'),
			$this->equalTo('CHAR_LENGTH(a.title)')
		);
	}

	/**
	 * Test chaining.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function testChaining()
	{
		$q = $this->dbo->getQuery(true)->select('foo');

		$this->assertThat(
			$q,
			$this->isInstanceOf('JDatabaseQuery')
		);
	}

	/**
	 * Test for the clear method (clearing all types and clauses).
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function testClear_all()
	{
		$properties = array(
			'select',
			'delete',
			'update',
			'insert',
			'from',
			'join',
			'set',
			'where',
			'group',
			'having',
			'order',
			'columns',
			'values',
			'forShare',
			'forUpdate',
			'limit',
			'noWait',
			'offset',
			'returning',
		);

		$q = new JDatabasePostgreSQLQueryInspector($this->dbo);

		// First pass - set the values.
		foreach ($properties as $property)
		{
			$q->$property = $property;
		}

		// Clear the whole query.
		$q->clear();

		// Check that all properties have been cleared
		foreach ($properties as $property)
		{
			$this->assertThat(
				$q->get($property),
				$this->equalTo(null)
			);
		}

		// And check that the type has been cleared.
		$this->assertThat(
			$q->type,
			$this->equalTo(null)
		);
	}

	/**
	 * Test for the clear method (clearing each clause).
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function testClear_clause()
	{
		$clauses = array(
			'from',
			'join',
			'set',
			'where',
			'group',
			'having',
			'order',
			'columns',
			'values',
			/*'forShare',*/
			/*'forUpdate',*/
			/*'limit',*/
			/*'noWait',*/
			/*'offset',*/
			/*'returning',*/
		);

		// Test each clause.
		foreach ($clauses as $clause)
		{
			$q = new JDatabasePostgreSQLQueryInspector($this->dbo);

			// Set the clauses
			foreach ($clauses as $clause2)
			{
				$q->$clause2 = $clause2;
			}

			// Clear the clause.
			$q->clear($clause);

			// Check that clause was cleared.
			$this->assertThat(
				$q->get($clause),
				$this->equalTo(null)
			);

			// Check the state of the other clauses.
			foreach ($clauses as $clause2)
			{
				if ($clause != $clause2)
				{
					$this->assertThat(
						$q->get($clause2),
						$this->equalTo($clause2),
						"Clearing $clause resulted in $clause2 having a value of " . $q->get($clause2) . '.'
					);
				}
			}
		}
	}

	/**
	 * Test for the clear method (clearing each query type).
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function testClear_type()
	{
		$types = array(
			'select',
			'delete',
			'update',
			'insert',
			'forShare',
			'forUpdate',
			'limit',
			'noWait',
			'offset',
			'returning',
		);

		$clauses = array(
			'from',
			'join',
			'set',
			'where',
			'group',
			'having',
			'order',
			'columns',
			'values',
		);

		$q = new JDatabasePostgreSQLQueryInspector($this->dbo);

		// Set the clauses.
		foreach ($clauses as $clause)
		{
			$q->$clause = $clause;
		}

		// Check that all properties have been cleared
		foreach ($types as $type)
		{
			// Set the type.
			$q->$type = $type;

			// Clear the type.
			$q->clear($type);

			// Check the type has been cleared.
			$this->assertThat(
				$q->type,
				$this->equalTo(null)
			);

			$this->assertThat(
				$q->get($type),
				$this->equalTo(null)
			);

			// Now check the claues have not been affected.
			foreach ($clauses as $clause)
			{
				$this->assertThat(
					$q->get($clause),
					$this->equalTo($clause)
				);
			}
		}
	}

	/**
	 * Test for "concatenate" words.
	 * 
	 * @return  void
	 */
	public function testConcatenate()
	{
		$q = new JDatabasePostgreSQLQueryInspector($this->dbo);

		$this->assertThat(
			$q->concatenate(array('foo', 'bar')),
			$this->equalTo('foo || bar'),
			'Tests without separator.'
		);

		$this->assertThat(
			$q->concatenate(array('foo', 'bar'), ' and '),
			$this->equalTo("foo || '_ and _' || bar"),
			'Tests without separator.'
		);
	}

	/**
	 * Test for FROM clause.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function testFrom()
	{
		$q = new JDatabasePostgreSQLQueryInspector($this->dbo);
		$q->from('jos_dbtest');

		$query = new JDatabaseQueryPostgreSQL;
		$query->from('jos_dbtest');

		$this->assertThat(
					$q->get('from'),
					$this->equalTo($query->from)
					);
	}

	/**
	 * Test for GROUP clause.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function testGroup()
	{
		$q = new JDatabasePostgreSQLQueryInspector($this->dbo);
		$q->group('jos_dbtest');

		$query = new JDatabaseQueryPostgreSQL;
		$query->group('jos_dbtest');

		$this->assertThat(
					$q->get('group'),
					$this->equalTo($query->group)
					);
	}

	/**
	 * Test for HAVING clause using a simple condition and with glue for second one.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function testHaving()
	{
		$q = new JDatabasePostgreSQLQueryInspector($this->dbo);
		$q->having('i=3');

		$query = new JDatabaseQueryPostgreSQL;
		$query->having('i=3');

		$this->assertThat(
					$q->get('having'),
					$this->equalTo($query->having)
					);

		/* check glue */
		$q->having('k<>2', 'AND');
		$query->having('k<>2', 'AND');

		$this->assertThat(
					$q->get('having'),
					$this->equalTo($query->having)
					);
	}

	/**
	 * Test for INNER JOIN clause.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function testInnerJoin()
	{
		$q = new JDatabasePostgreSQLQueryInspector($this->dbo);
		$q->innerJoin('b ON b.id = a.id');

		$query = new JDatabaseQueryPostgreSQL;
		$query->innerJoin('b ON b.id = a.id');

		$this->assertThat(
					$q->get('innerJoin'),
					$this->equalTo($query->innerJoin)
					);
	}

	/**
	 * Test for JOIN clause using dataprovider to test all types of join.
	 *
	 * @param   string  $type        Type of JOIN, could be INNER, OUTER, LEFT, RIGHT
	 * @param   string  $conditions  Join condition
	 *
	 * @return  void
	 *
	 * @since   11.3
	 * @dataProvider  dataTestJoin
	 */
	public function testJoin($type, $conditions)
	{
		$q = new JDatabasePostgreSQLQueryInspector($this->dbo);
		$q->join($type, $conditions);

		$query = new JDatabaseQueryPostgreSQL;
		$query->join($type, $conditions);

		$this->assertThat(
					$q->get('join'),
					$this->equalTo($query->join)
					);
	}

	/**
	 * Test for LEFT JOIN clause.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function testLeftJoin()
	{
		$q = new JDatabasePostgreSQLQueryInspector($this->dbo);
		$q->leftJoin('b ON b.id = a.id');

		$query = new JDatabaseQueryPostgreSQL;
		$query->leftJoin('b ON b.id = a.id');

		$this->assertThat(
					$q->get('leftJoin'),
					$this->equalTo($query->leftJoin)
					);
	}

	/**
	 * Tests the quoteName method.
	 *
	 * @param   boolean  $quoted    The value of the quoted argument.
	 * @param   string   $expected  The expected result.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 * @dataProvider  dataTestNullDate
	 */
	public function testNullDate($quoted, $expected)
	{
		$q = new JDatabasePostgreSQLQueryInspector($this->dbo);

		$this->assertThat(
			$q->nullDate($quoted),
			$this->equalTo($expected),
			'The nullDate method should be a proxy for the JDatabase::getNullDate method.'
		);
	}

	/**
	 * Test for ORDER clause.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function testOrder()
	{
		$q = new JDatabasePostgreSQLQueryInspector($this->dbo);
		$q->order('jos_dbtest');

		$query = new JDatabaseQueryPostgreSQL;
		$query->order('jos_dbtest');

		$this->assertThat(
					$q->get('order'),
					$this->equalTo($query->order)
					);
	}

	/**
	 * Test for OUTER JOIN clause.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function testOuterJoin()
	{
		$q = new JDatabasePostgreSQLQueryInspector($this->dbo);
		$q->outerJoin('b ON b.id = a.id');

		$query = new JDatabaseQueryPostgreSQL;
		$query->outerJoin('b ON b.id = a.id');

		$this->assertThat(
					$q->get('outerJoin'),
					$this->equalTo($query->outerJoin)
					);
	}

	/**
	 * Tests the quoteName method.
	 *
	 * @param   boolean  $text      The value to be quoted.
	 * @param   boolean  $escape    True to escape the string, false to leave it unchanged.
	 * @param   string   $expected  The expected result.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 * @dataProvider  dataTestQuote
	 */
	public function testQuote($text, $escape, $expected)
	{
		$q = new JDatabasePostgreSQLQueryInspector($this->dbo);

		$this->assertThat(
			$q->quoteName("test"),
			$this->equalTo('"test"'),
			'The quoteName method should be a proxy for the JDatabase::escape method.'
		);
	}

	/**
	 * Tests the quoteName method.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function testQuoteName()
	{
		$q = new JDatabasePostgreSQLQueryInspector($this->dbo);

		$this->assertThat(
			$q->quoteName('test'),
			$this->equalTo('"test"'),
			'The quoteName method should be a proxy for the JDatabase::escape method.'
		);
	}

	/**
	 * Test for RIGHT JOIN clause.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function testRightJoin()
	{
		$q = new JDatabasePostgreSQLQueryInspector($this->dbo);
		$q->rightJoin('b ON b.id = a.id');

		$query = new JDatabaseQueryPostgreSQL;
		$query->rightJoin('b ON b.id = a.id');

		$this->assertThat(
					$q->get('rightJoin'),
					$this->equalTo($query->rightJoin)
					);
	}

	/**
	 * Test for SELECT clause.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function testSelect()
	{
		$q = new JDatabasePostgreSQLQueryInspector($this->dbo);
		$q->select('jos_dbtest');

		$query = new JDatabaseQueryPostgreSQL;
		$query->select('jos_dbtest');

		$this->assertThat(
					$q->get('select'),
					$this->equalTo($query->select)
					);
	}

	/**
	 * Test for WHERE clause using a simple condition and with glue for second one. 
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function testWhere()
	{
		$q = new JDatabasePostgreSQLQueryInspector($this->dbo);
		$q->where('i=3');

		$query = new JDatabaseQueryPostgreSQL;
		$query->where('i=3');

		$this->assertThat(
					$q->get('where'),
					$this->equalTo($query->where)
					);

		/* check with glue */
		$q->where('f<>7', 'OR');
		$query->where('f<>7', 'OR');

		$this->assertThat(
					$q->get('where'),
					$this->equalTo($query->where)
					);
	}

	/**
	 * Tests the JDatabaseQueryPostgreSQL::escape method.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function testEscape()
	{
		$q = new JDatabasePostgreSQLQueryInspector($this->dbo);

		$this->assertThat(
			$q->escape('foo'),
			$this->equalTo('_foo_')
		);
	}

	/**
	 * Test for FOR UPDATE clause. 
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function testForUpdate ()
	{
		$q = new JDatabasePostgreSQLQueryInspector($this->dbo);
		$q->forUpdate('jos_dbtest');

		$query = new JDatabaseQueryPostgreSQL;
		$query->forUpdate('jos_dbtest');

		$this->assertThat(
					$q->get('forUpdate'),
					$this->equalTo($query->forUpdate)
					);

		/* check with glue */
		$q->forUpdate('jos_assets', ',');
		$query->forUpdate('jos_assets', ',');

		$this->assertThat(
					$q->get('forUpdate'),
					$this->equalTo($query->forUpdate)
					);
	}

	/**
	 * Test for FOR SHARE clause. 
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function testForShare ()
	{
		$q = new JDatabasePostgreSQLQueryInspector($this->dbo);
		$q->forShare('jos_dbtest');

		$query = new JDatabaseQueryPostgreSQL;
		$query->forShare('jos_dbtest');

		$this->assertThat(
					$q->get('forShare'),
					$this->equalTo($query->forShare)
					);

		/* check with glue */
		$q->forShare('jos_assets', ',');
		$query->forShare('jos_assets', ',');

		$this->assertThat(
					$q->get('forShare'),
					$this->equalTo($query->forShare)
					);
	}

	/**
	 * Test for NOWAIT clause. 
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function testNoWait ()
	{
		$q = new JDatabasePostgreSQLQueryInspector($this->dbo);
		$q->noWait();

		$query = new JDatabaseQueryPostgreSQL;
		$query->noWait();

		$this->assertThat(
					$q->get('noWait'),
					$this->equalTo($query->noWait)
					);
	}

	/** 
	 * Test for LIMIT clause. 
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function testLimit()
	{
		$q = new JDatabasePostgreSQLQueryInspector($this->dbo);
		$q->limit('5');

		$query = new JDatabaseQueryPostgreSQL;
		$query->limit('5');

		$this->assertThat(
					$q->get('limit'),
					$this->equalTo($query->limit)
					);
	}

	/** 
	 * Test for OFFSET clause. 
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function testOffset()
	{
		$q = new JDatabasePostgreSQLQueryInspector($this->dbo);
		$q->offset('10');

		$query = new JDatabaseQueryPostgreSQL;
		$query->offset('10');

		$this->assertThat(
					$q->get('offset'),
					$this->equalTo($query->offset)
					);
	}

	/** 
	 * Test for RETURNING clause. 
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function testReturning()
	{
		$q = new JDatabasePostgreSQLQueryInspector($this->dbo);
		$q->returning('id');

		$query = new JDatabaseQueryPostgreSQL;
		$query->returning('id');

		$this->assertThat(
					$q->get('returning'),
					$this->equalTo($query->returning)
					);
	}
}
