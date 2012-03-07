<?php
/**
 * @version    $Id: JDatabasePostgresqlQueryTest.php gpongelli $
 * @package    Joomla.UnitTest
 * 
 * @copyright  Copyright (C) 2005 - 2011 Open Source Matters. All rights reserved.
 * @license    GNU General Public License
 */

require_once __DIR__ . '/JDatabasePostgresqlQueryInspector.php';
require_once JPATH_PLATFORM . '/joomla/database/query/postgresql.php';
require_once JPATH_TESTS . '/includes/JoomlaTestCase.php';

/**
 * Test class for JDatabasePostgresqlQuery.
 * 
 * @package     Joomla.UnitTest
 * @subpackage  Database
 * 
 * @since       11.3
 */
class JDatabasePostgresqlQueryTest extends JoomlaTestCase
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
			// Quoted, expected
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
			// Text, escaped, expected
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
		$this->dbo = $this->getMockDatabase('JDatabasePostgresqlMock');

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
	 * Test for the JDatabaseQueryPostgresql::__string method for a 'select' case.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function test__toStringSelect()
	{
		$q = new JDatabasePostgresqlQueryInspector($this->dbo);

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
		$q = new JDatabasePostgresqlQueryInspector($this->dbo);

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
	 * Test for year extraction from date.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function test__toStringYear()
	{
		$q = new JDatabasePostgresqlQueryInspector($this->dbo);

		$q->select($q->year($q->quoteName('col')))->from('table');

		$this->assertThat(
					(string) $q,
					$this->equalTo("\nSELECT EXTRACT (YEAR FROM \"col\")\nFROM table")
		);
	}

	/**
	 * Test for month extraction from date.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function test__toStringMonth()
	{
		$q = new JDatabasePostgresqlQueryInspector($this->dbo);

		$q->select($q->month($q->quoteName('col')))->from('table');

		$this->assertThat(
					(string) $q,
					$this->equalTo("\nSELECT EXTRACT (MONTH FROM \"col\")\nFROM table")
		);
	}

	/**
	 * Test for day extraction from date.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function test__toStringDay()
	{
		$q = new JDatabasePostgresqlQueryInspector($this->dbo);

		$q->select($q->day($q->quoteName('col')))->from('table');

		$this->assertThat(
					(string) $q,
					$this->equalTo("\nSELECT EXTRACT (DAY FROM \"col\")\nFROM table")
		);
	}

	/**
	 * Test for hour extraction from date.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function test__toStringHour()
	{
		$q = new JDatabasePostgresqlQueryInspector($this->dbo);

		$q->select($q->hour($q->quoteName('col')))->from('table');

		$this->assertThat(
					(string) $q,
					$this->equalTo("\nSELECT EXTRACT (HOUR FROM \"col\")\nFROM table")
		);
	}

	/**
	 * Test for minute extraction from date.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function test__toStringMinute()
	{
		$q = new JDatabasePostgresqlQueryInspector($this->dbo);

		$q->select($q->minute($q->quoteName('col')))->from('table');

		$this->assertThat(
					(string) $q,
					$this->equalTo("\nSELECT EXTRACT (MINUTE FROM \"col\")\nFROM table")
		);
	}

	/**
	 * Test for seconds extraction from date.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function test__toStringSecond()
	{
		$q = new JDatabasePostgresqlQueryInspector($this->dbo);

		$q->select($q->second($q->quoteName('col')))->from('table');

		$this->assertThat(
					(string) $q,
					$this->equalTo("\nSELECT EXTRACT (SECOND FROM \"col\")\nFROM table")
		);
	}

	/**
	 * Test for INSERT INTO clause with subquery.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function test__toStringInsert_subquery()
	{
		$q = new JDatabasePostgresqlQueryInspector($this->dbo);
		$subq = new JDatabasePostgresqlQueryInspector($this->dbo);
		$subq->select('col2')->where('a=1');

		$q->insert('table')->columns('col')->values($subq);

		$this->assertThat(
					(string) $q,
					$this->equalTo("\nINSERT INTO table\n(col)\n(\nSELECT col2\nWHERE a=1)")
		);

		$q->clear();
		$q->insert('table')->columns('col')->values('3');
		$this->assertThat(
					(string) $q,
					$this->equalTo("\nINSERT INTO table\n(col) VALUES \n(3)")
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
		$q = new JDatabasePostgresqlQueryInspector($this->dbo);

		$this->assertThat(
			$q->castAsChar('123'),
			$this->equalTo('123::text'),
			'The default castAsChar behaviour is quote the input.'
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
		$q = new JDatabasePostgresqlQueryInspector($this->dbo);

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

		$q = new JDatabasePostgresqlQueryInspector($this->dbo);

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
			'forShare',
			'forUpdate',
			'limit',
			'noWait',
			'offset',
			'returning',
		);

		// Test each clause.
		foreach ($clauses as $clause)
		{
			$q = new JDatabasePostgresqlQueryInspector($this->dbo);

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
						"Clearing '$clause' resulted in '$clause2' having a value of " . $q->get($clause2) . '.'
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

		$q = new JDatabasePostgresqlQueryInspector($this->dbo);

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
		$q = new JDatabasePostgresqlQueryInspector($this->dbo);

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
		$q = new JDatabasePostgresqlQueryInspector($this->dbo);

		$this->assertThat(
			$q->from('#__foo'),
			$this->identicalTo($q),
			'Tests chaining.'
		);

		$this->assertThat(
			trim($q->from),
			$this->equalTo('FROM #__foo'),
			'Tests rendered value.'
		);

		// Add another column.
		$q->from('#__bar');

		$this->assertThat(
			trim($q->from),
			$this->equalTo('FROM #__foo,#__bar'),
			'Tests rendered value after second use.'
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
		$q = new JDatabasePostgresqlQueryInspector($this->dbo);

		$this->assertThat(
			$q->group('foo'),
			$this->identicalTo($q),
			'Tests chaining.'
		);

		$this->assertThat(
			trim($q->group),
			$this->equalTo('GROUP BY foo'),
			'Tests rendered value.'
		);

		// Add another column.
		$q->group('bar');

		$this->assertThat(
			trim($q->group),
			$this->equalTo('GROUP BY foo,bar'),
			'Tests rendered value after second use.'
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
		$q = new JDatabasePostgresqlQueryInspector($this->dbo);

		$this->assertThat(
			$q->having('COUNT(foo) > 1'),
			$this->identicalTo($q),
			'Tests chaining.'
		);

		$this->assertThat(
			trim($q->having),
			$this->equalTo('HAVING COUNT(foo) > 1'),
			'Tests rendered value.'
		);

		// Add another column.
		$q->having('COUNT(bar) > 2');

		$this->assertThat(
			trim($q->having),
			$this->equalTo('HAVING COUNT(foo) > 1 AND COUNT(bar) > 2'),
			'Tests rendered value after second use.'
		);

		// Reset the field to test the glue.
		$q->having = null;
		$q->having('COUNT(foo) > 1', 'OR');
		$q->having('COUNT(bar) > 2');

		$this->assertThat(
			trim($q->having),
			$this->equalTo('HAVING COUNT(foo) > 1 OR COUNT(bar) > 2'),
			'Tests rendered value with OR glue.'
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
		$q = new JDatabasePostgresqlQueryInspector($this->dbo);
		$q2 = new JDatabasePostgresqlQueryInspector($this->dbo);
		$condition = 'foo ON foo.id = bar.id';

		$this->assertThat(
			$q->innerJoin($condition),
			$this->identicalTo($q),
			'Tests chaining.'
		);

		$q2->join('INNER', $condition);

		$this->assertThat(
			$q->join,
			$this->equalTo($q2->join),
			'Tests that innerJoin is an alias for join.'
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
		$q = new JDatabasePostgresqlQueryInspector($this->dbo);

		$this->assertThat(
			$q->join('INNER', 'foo ON foo.id = bar.id'),
			$this->identicalTo($q),
			'Tests chaining.'
		);

		$this->assertThat(
			trim($q->join[0]),
			$this->equalTo('INNER JOIN foo ON foo.id = bar.id'),
			'Tests that first join renders correctly.'
		);

		$q->join('OUTER', 'goo ON goo.id = car.id');

		$this->assertThat(
			trim($q->join[1]),
			$this->equalTo('OUTER JOIN goo ON goo.id = car.id'),
			'Tests that second join renders correctly.'
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
		$q = new JDatabasePostgresqlQueryInspector($this->dbo);
		$q2 = new JDatabasePostgresqlQueryInspector($this->dbo);
		$condition = 'foo ON foo.id = bar.id';

		$this->assertThat(
			$q->leftJoin($condition),
			$this->identicalTo($q),
			'Tests chaining.'
		);

		$q2->join('LEFT', $condition);

		$this->assertThat(
			$q->join,
			$this->equalTo($q2->join),
			'Tests that innerJoin is an alias for join.'
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
		$q = new JDatabasePostgresqlQueryInspector($this->dbo);

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
		$q = new JDatabasePostgresqlQueryInspector($this->dbo);

		$this->assertThat(
			$q->order('column'),
			$this->identicalTo($q),
			'Tests chaining.'
		);

		$this->assertThat(
			trim($q->order),
			$this->equalTo('ORDER BY column'),
			'Tests rendered value.'
		);

		$q->order('col2');
		$this->assertThat(
			trim($q->order),
			$this->equalTo('ORDER BY column,col2'),
			'Tests rendered value.'
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
		$q = new JDatabasePostgresqlQueryInspector($this->dbo);
		$q2 = new JDatabasePostgresqlQueryInspector($this->dbo);
		$condition = 'foo ON foo.id = bar.id';

		$this->assertThat(
			$q->outerJoin($condition),
			$this->identicalTo($q),
			'Tests chaining.'
		);

		$q2->join('OUTER', $condition);

		$this->assertThat(
			$q->join,
			$this->equalTo($q2->join),
			'Tests that innerJoin is an alias for join.'
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
		$q = new JDatabasePostgresqlQueryInspector($this->dbo);

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
		$q = new JDatabasePostgresqlQueryInspector($this->dbo);

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
		$q = new JDatabasePostgresqlQueryInspector($this->dbo);
		$q2 = new JDatabasePostgresqlQueryInspector($this->dbo);
		$condition = 'foo ON foo.id = bar.id';

		$this->assertThat(
			$q->rightJoin($condition),
			$this->identicalTo($q),
			'Tests chaining.'
		);

		$q2->join('RIGHT', $condition);

		$this->assertThat(
			$q->join,
			$this->equalTo($q2->join),
			'Tests that innerJoin is an alias for join.'
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
		$q = new JDatabasePostgresqlQueryInspector($this->dbo);

		$this->assertThat(
			$q->select('foo'),
			$this->identicalTo($q),
			'Tests chaining.'
		);

		$this->assertThat(
			$q->type,
			$this->equalTo('select'),
			'Tests the type property is set correctly.'
		);

		$this->assertThat(
			trim($q->select),
			$this->equalTo('SELECT foo'),
			'Tests the select element is set correctly.'
		);

		$q->select('bar');

		$this->assertThat(
			trim($q->select),
			$this->equalTo('SELECT foo,bar'),
			'Tests the second use appends correctly.'
		);

		$q->select(
			array(
				'goo', 'car'
			)
		);

		$this->assertThat(
			trim($q->select),
			$this->equalTo('SELECT foo,bar,goo,car'),
			'Tests the second use appends correctly.'
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
		$q = new JDatabasePostgresqlQueryInspector($this->dbo);
		$this->assertThat(
			$q->where('foo = 1'),
			$this->identicalTo($q),
			'Tests chaining.'
		);

		$this->assertThat(
			trim($q->where),
			$this->equalTo('WHERE foo = 1'),
			'Tests rendered value.'
		);

		// Add another column.
		$q->where(
			array(
				'bar = 2',
				'goo = 3',
			)
		);

		$this->assertThat(
			trim($q->where),
			$this->equalTo('WHERE foo = 1 AND bar = 2 AND goo = 3'),
			'Tests rendered value after second use and array input.'
		);

		// Clear the where
		$q->where = null;
		$q->where(
			array(
				'bar = 2',
				'goo = 3',
			),
			'OR'
		);

		$this->assertThat(
			trim($q->where),
			$this->equalTo('WHERE bar = 2 OR goo = 3'),
			'Tests rendered value with glue.'
		);
	}

	/**
	 * Tests the JDatabaseQueryPostgresql::escape method.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function testEscape()
	{
		$q = new JDatabasePostgresqlQueryInspector($this->dbo);

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
		$q = new JDatabasePostgresqlQueryInspector($this->dbo);

		$this->assertThat(
			$q->forUpdate('#__foo'),
			$this->identicalTo($q),
			'Tests chaining.'
		);

		$this->assertThat(
			trim($q->forUpdate),
			$this->equalTo('FOR UPDATE OF #__foo'),
			'Tests rendered value.'
		);

		$q->forUpdate('#__bar');
		$this->assertThat(
			trim($q->forUpdate),
			$this->equalTo('FOR UPDATE OF #__foo, #__bar'),
			'Tests rendered value.'
		);

		// Testing glue
		$q->forUpdate = null;
		$q->forUpdate('#__foo', ';');
		$q->forUpdate('#__bar');
		$this->assertThat(
			trim($q->forUpdate),
			$this->equalTo('FOR UPDATE OF #__foo; #__bar'),
			'Tests rendered value.'
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
		$q = new JDatabasePostgresqlQueryInspector($this->dbo);

		$this->assertThat(
			$q->forShare('#__foo'),
			$this->identicalTo($q),
			'Tests chaining.'
		);

		$this->assertThat(
			trim($q->forShare),
			$this->equalTo('FOR SHARE OF #__foo'),
			'Tests rendered value.'
		);

		$q->forShare('#__bar');
		$this->assertThat(
			trim($q->forShare),
			$this->equalTo('FOR SHARE OF #__foo, #__bar'),
			'Tests rendered value.'
		);

		// Testing glue
		$q->forShare = null;
		$q->forShare('#__foo', ';');
		$q->forShare('#__bar');
		$this->assertThat(
			trim($q->forShare),
			$this->equalTo('FOR SHARE OF #__foo; #__bar'),
			'Tests rendered value.'
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
		$q = new JDatabasePostgresqlQueryInspector($this->dbo);

		$this->assertThat(
			$q->noWait(),
			$this->identicalTo($q),
			'Tests chaining.'
		);

		$this->assertThat(
			trim($q->noWait),
			$this->equalTo('NOWAIT'),
			'Tests rendered value.'
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
		$q = new JDatabasePostgresqlQueryInspector($this->dbo);

		$this->assertThat(
			$q->limit('5'),
			$this->identicalTo($q),
			'Tests chaining.'
		);

		$this->assertThat(
			trim($q->limit),
			$this->equalTo('LIMIT 5'),
			'Tests rendered value.'
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
		$q = new JDatabasePostgresqlQueryInspector($this->dbo);

		$this->assertThat(
			$q->offset('10'),
			$this->identicalTo($q),
			'Tests chaining.'
		);

		$this->assertThat(
			trim($q->offset),
			$this->equalTo('OFFSET 10'),
			'Tests rendered value.'
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
		$q = new JDatabasePostgresqlQueryInspector($this->dbo);

		$this->assertThat(
			$q->returning('id'),
			$this->identicalTo($q),
			'Tests chaining.'
		);

		$this->assertThat(
			trim($q->returning),
			$this->equalTo('RETURNING id'),
			'Tests rendered value.'
		);
	}
}
