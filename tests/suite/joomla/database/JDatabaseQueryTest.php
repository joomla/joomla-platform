<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Database
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters. All rights reserved.
 * @license     GNU General Public License
 */

require_once __DIR__.'/JDatabaseQueryInspector.php';

require_once JPATH_PLATFORM.'/joomla/database/database/mysqliquery.php';

/**
 * Test class for JDatabaseQuery.
 *
 * @package     Joomla.UnitTest
 * @subpackage  Database
 * @since       11.1
 */
class JDatabaseQueryTest extends JoomlaTestCase
{
	/**
	 * @var    JDatabase  A mock of the JDatabase object for testing purposes.
	 * @since  11.1
	 */
	protected $dbo;

	/**
	 * Data for the testNullDate test.
	 *
	 * @return  array
	 *
	 * @since   11.1
	 */
	public function dataTestNullDate()
	{
		return array(
			// quoted, expected
			array(true, "'0000-00-00 00:00:00'"),
			array(false, "0000-00-00 00:00:00"),
		);
	}

	/**
	 * Data for the testNullDate test.
	 *
	 * @return  array
	 *
	 * @since   11.1
	 */
	public function dataTestQuote()
	{
		return array(
			// text, escaped, expected
			array('text', false, "'text'"),
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
	 * Sets up the fixture.
	 *
	 * This method is called before a test is executed.
	 *
	 * @since  11.1
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
	 * Test for the JDatabaseQuery::__call method.
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	public function test__call()
	{
		$q = new JDatabaseQueryInspector($this->dbo);

		$this->assertThat(
			$q->e('foo'),
			$this->equalTo($q->escape('foo')),
			'Tests the e alias of escape.'
		);

		$this->assertThat(
			$q->q('foo'),
			$this->equalTo($q->quote('foo')),
			'Tests the q alias of quote.'
		);

		$this->assertThat(
			$q->qn('foo'),
			$this->equalTo($q->quoteName('foo')),
			'Tests the qn alias of quoteName.'
		);

		$this->assertThat(
			$q->foo(),
			$this->isNull(),
			'Tests for an unknown method.'
		);
	}

	/**
	 * Test for the JDatabaseQuery::__string method for a 'select' case.
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	public function test__toStringSelect()
	{
		$q = new JDatabaseQueryInspector($this->dbo);

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
	 * Test for the castAsChar method.
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	public function testCastAsChar()
	{
		$q = new JDatabaseQueryInspector($this->dbo);

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
	 * @since   11.1
	 */
	public function testCharLength()
	{
		$q = new JDatabaseQueryInspector($this->dbo);

		$this->assertThat(
			$q->charLength('a.title'),
			$this->equalTo('CHAR_LENGTH(a.title)')
		);
	}

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
	 * @since   11.1
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
			'truncate'
		);

		$q = new JDatabaseQueryInspector($this->dbo);

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
	 * @since   11.1
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
		);


		// Test each clause.
		foreach ($clauses as $clause)
		{
			$q = new JDatabaseQueryInspector($this->dbo);

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
				if ($clause != $clause2) {
					$this->assertThat(
						$q->get($clause2),
						$this->equalTo($clause2),
						"Clearing $clause resulted in $clause2 having a value of ".$q->get($clause2).'.'
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
	 * @since   11.1
	 */
	public function testClear_type()
	{
		$types = array(
			'select',
			'delete',
			'update',
			'insert',
			'truncate'
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

		$q = new JDatabaseQueryInspector($this->dbo);

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
	 * Tests the JDatabaseQuery::columns method.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function testColumns()
	{
		$q = new JDatabaseQueryInspector($this->dbo);

		$this->assertThat(
			$q->columns('foo'),
			$this->identicalTo($q),
			'Tests chaining.'
		);

		$this->assertThat(
			trim($q->columns),
			$this->equalTo('(foo)'),
			'Tests rendered value.'
		);

		// Add another column.
		$q->columns('bar');

		$this->assertThat(
			trim($q->columns),
			$this->equalTo('(foo,bar)'),
			'Tests rendered value after second use.'
		);
	}

	/**
	 * Tests the JDatabaseQuery::concatenate method.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function testConcatenate()
	{
		$q = new JDatabaseQueryInspector($this->dbo);

		$this->assertThat(
			$q->concatenate(array('foo', 'bar')),
			$this->equalTo('CONCATENATE(foo || bar)'),
			'Tests without separator.'
		);

		$this->assertThat(
			$q->concatenate(array('foo', 'bar'), ' and '),
			$this->equalTo("CONCATENATE(foo || '_ and _' || bar)"),
			'Tests without separator.'
		);
	}

	/**
	 * Tests the JDatabaseQuery::currentTimestamp method.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function testCurrentTimestamp()
	{
		$q = new JDatabaseQueryInspector($this->dbo);

		$this->assertThat(
			$q->currentTimestamp(),
			$this->equalTo('CURRENT_TIMESTAMP()')
		);
	}

	/**
	 * Tests the JDatabaseQuery::dateFormat method.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function testDateFormat()
	{
		$q = new JDatabaseQueryInspector($this->dbo);

		$this->assertThat(
			$q->dateFormat(),
			$this->equalTo('Y-m-d H:i:s')
		);
	}

	/**
	 * Tests the JDatabaseQuery::dateFormat method for an expected exception.
	 *
	 * @return  void
	 *
	 * @expectedException  JDatabaseException
	 * @since   11.3
	 */
	public function testDateFormatException()
	{
		$q = new JDatabaseQueryInspector($this->dbo);

		// Override the internal database for testing.
		$q->db = new stdClass;

		$q->dateFormat();
	}

	/**
	 * Tests the JDatabaseQuery::delete method.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function testDelete()
	{
		$q = new JDatabaseQueryInspector($this->dbo);

		$this->assertThat(
			$q->delete('#__foo'),
			$this->identicalTo($q),
			'Tests chaining.'
		);

		$this->assertThat(
			$q->type,
			$this->equalTo('delete'),
			'Tests the type property is set correctly.'
		);

		$this->assertThat(
			trim($q->delete),
			$this->equalTo('DELETE'),
			'Tests the delete element is set correctly.'
		);

		$this->assertThat(
			trim($q->from),
			$this->equalTo('FROM #__foo'),
			'Tests the from element is set correctly.'
		);
	}

	/**
	 * Tests the JDatabaseQuery::dump method.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function testDump()
	{
		$q = new JDatabaseQueryInspector($this->dbo);

		$q->select('*')
			->from('#__foo');

		$this->assertThat(
			$q->dump(),
			$this->equalTo(
				'<pre class="jdatabasequery">' .
				"\nSELECT *\nFROM foo" .
				'</pre>'
			),
			'Tests that the dump method replaces the prefix correctly.'
		);
	}

	/**
	 * Tests the JDatabaseQuery::escape method.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function testEscape()
	{
		$q = new JDatabaseQueryInspector($this->dbo);

		$this->assertThat(
			$q->escape('foo'),
			$this->equalTo('_foo_')
		);
	}

	/**
	 * Tests the JDatabaseQuery::escape method for an expected exception.
	 *
	 * @return  void
	 *
	 * @expectedException  JDatabaseException
	 * @since   11.3
	 */
	public function testEscapeException()
	{
		$q = new JDatabaseQueryInspector($this->dbo);

		// Override the internal database for testing.
		$q->db = new stdClass;

		$q->escape('foo');
	}

	/**
	 * Tests the JDatabaseQuery::from method.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function testFrom()
	{
		$q = new JDatabaseQueryInspector($this->dbo);

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
	 * Tests the JDatabaseQuery::group method.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function testGroup()
		{
		$q = new JDatabaseQueryInspector($this->dbo);

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
	 * Tests the JDatabaseQuery::having method.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function testHaving()
	{
		$q = new JDatabaseQueryInspector($this->dbo);

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
	 * Tests the JDatabaseQuery::innerJoin method.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function testInnerJoin()
	{
		$q1 = new JDatabaseQueryInspector($this->dbo);
		$q2 = new JDatabaseQueryInspector($this->dbo);
		$condition = 'foo ON foo.id = bar.id';

		$this->assertThat(
			$q1->innerJoin($condition),
			$this->identicalTo($q1),
			'Tests chaining.'
		);

		$q2->join('INNER', $condition);

		$this->assertThat(
			$q1->join,
			$this->equalTo($q2->join),
			'Tests that innerJoin is an alias for join.'
		);
	}

	/**
	 * Tests the JDatabaseQuery::insert method.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function testInsert()
	{
		$q = new JDatabaseQueryInspector($this->dbo);

		$this->assertThat(
			$q->insert('#__foo'),
			$this->identicalTo($q),
			'Tests chaining.'
		);

		$this->assertThat(
			$q->type,
			$this->equalTo('insert'),
			'Tests the type property is set correctly.'
		);

		$this->assertThat(
			trim($q->insert),
			$this->equalTo('INSERT INTO #__foo'),
			'Tests the delete element is set correctly.'
		);
	}

	/**
	 * Tests the JDatabaseQuery::join method.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function testJoin()
	{
		$q = new JDatabaseQueryInspector($this->dbo);

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
	 * Tests the JDatabaseQuery::leftJoin method.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function testLeftJoin()
	{
		$q1 = new JDatabaseQueryInspector($this->dbo);
		$q2 = new JDatabaseQueryInspector($this->dbo);
		$condition = 'foo ON foo.id = bar.id';

		$this->assertThat(
			$q1->leftJoin($condition),
			$this->identicalTo($q1),
			'Tests chaining.'
		);

		$q2->join('LEFT', $condition);

		$this->assertThat(
			$q1->join,
			$this->equalTo($q2->join),
			'Tests that leftJoin is an alias for join.'
		);
	}

	/**
	 * Tests the JDatabaseQuery::length method.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function testLength()
	{
		$q = new JDatabaseQueryInspector($this->dbo);

		$this->assertThat(
			trim($q->length('foo')),
			$this->equalTo('LENGTH(foo)'),
			'Tests method renders correctly.'
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
	 * @since   11.1
	 * @dataProvider  dataTestNullDate
	 */
	public function testNullDate($quoted, $expected)
	{
		$q = new JDatabaseQueryInspector($this->dbo);

		$this->assertThat(
			$q->nullDate($quoted),
			$this->equalTo($expected),
			'The nullDate method should be a proxy for the JDatabase::getNullDate method.'
		);
	}

	/**
	 * Tests the JDatabaseQuery::nullDate method for an expected exception.
	 *
	 * @return  void
	 *
	 * @expectedException  JDatabaseException
	 * @since   11.3
	 */
	public function testNullDateException()
	{
		$q = new JDatabaseQueryInspector($this->dbo);

		// Override the internal database for testing.
		$q->db = new stdClass;

		$q->nullDate();
	}

	/**
	 * Tests the JDatabaseQuery::order method.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function testOrder()
		{
		$q = new JDatabaseQueryInspector($this->dbo);

		$this->assertThat(
			$q->order('foo'),
			$this->identicalTo($q),
			'Tests chaining.'
		);

		$this->assertThat(
			trim($q->order),
			$this->equalTo('ORDER BY foo'),
			'Tests rendered value.'
		);

		// Add another column.
		$q->order('bar');

		$this->assertThat(
			trim($q->order),
			$this->equalTo('ORDER BY foo,bar'),
			'Tests rendered value after second use.'
		);

		$q->order(
			array(
				'goo', 'car'
			)
		);

		$this->assertThat(
			trim($q->order),
			$this->equalTo('ORDER BY foo,bar,goo,car'),
			'Tests array input.'
		);
	}

	/**
	 * Tests the JDatabaseQuery::outerJoin method.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function testOuterJoin()
	{
		$q1 = new JDatabaseQueryInspector($this->dbo);
		$q2 = new JDatabaseQueryInspector($this->dbo);
		$condition = 'foo ON foo.id = bar.id';

		$this->assertThat(
			$q1->outerJoin($condition),
			$this->identicalTo($q1),
			'Tests chaining.'
		);

		$q2->join('OUTER', $condition);

		$this->assertThat(
			$q1->join,
			$this->equalTo($q2->join),
			'Tests that outerJoin is an alias for join.'
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
	 * @since   11.1
	 * @dataProvider  dataTestQuote
	 */
	public function testQuote($text, $escape, $expected)
	{
		$q = new JDatabaseQueryInspector($this->dbo);

		$this->assertThat(
			$q->quoteName("test"),
			$this->equalTo("`test`"),
			'The quoteName method should be a proxy for the JDatabase::escape method.'
		);
	}

	/**
	 * Tests the JDatabaseQuery::nullDate method for an expected exception.
	 *
	 * @return  void
	 *
	 * @expectedException  JDatabaseException
	 * @since   11.3
	 */
	public function testQuoteException()
	{
		$q = new JDatabaseQueryInspector($this->dbo);

		// Override the internal database for testing.
		$q->db = new stdClass;

		$q->quote('foo');
	}

	/**
	 * Tests the quoteName method.
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	public function testQuoteName()
	{
		$q = new JDatabaseQueryInspector($this->dbo);

		$this->assertThat(
			$q->quoteName("test"),
			$this->equalTo("`test`"),
			'The quoteName method should be a proxy for the JDatabase::escape method.'
		);
	}

	/**
	 * Tests the JDatabaseQuery::quoteName method for an expected exception.
	 *
	 * @return  void
	 *
	 * @expectedException  JDatabaseException
	 * @since   11.3
	 */
	public function testQuoteNameException()
	{
		$q = new JDatabaseQueryInspector($this->dbo);

		// Override the internal database for testing.
		$q->db = new stdClass;

		$q->quoteName('foo');
	}

	/**
	 * Tests the JDatabaseQuery::rightJoin method.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function testRightJoin()
	{
		$q1 = new JDatabaseQueryInspector($this->dbo);
		$q2 = new JDatabaseQueryInspector($this->dbo);
		$condition = 'foo ON foo.id = bar.id';

		$this->assertThat(
			$q1->rightJoin($condition),
			$this->identicalTo($q1),
			'Tests chaining.'
		);

		$q2->join('RIGHT', $condition);

		$this->assertThat(
			$q1->join,
			$this->equalTo($q2->join),
			'Tests that rightJoin is an alias for join.'
		);
	}

	/**
	 * Tests the JDatabaseQuery::select method.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function testSelect()
	{
		$q = new JDatabaseQueryInspector($this->dbo);

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
	* Tests the JDatabaseQuery::set method.
	*
	* @return  void
	*
	* @since   11.3
	*/
	public function testSet()
	{
		$q = new JDatabaseQueryInspector($this->dbo);

		$this->assertThat(
			$q->set('foo = 1'),
			$this->identicalTo($q),
			'Tests chaining.'
		);

		$this->assertThat(
			trim($q->set),
			$this->identicalTo('SET foo = 1'),
			'Tests set with a string.'
		);

		// Clear the set.
		$q->set = null;
		$q->set(
			array(
				'foo = 1',
				'bar = 2',
			)
		);

		$this->assertThat(
			trim($q->set),
			$this->identicalTo("SET foo = 1\n\t, bar = 2"),
			'Tests set with an array.'
		);

		// Clear the set.
		$q->set = null;
		$q->set(
			array(
				'foo = 1',
				'bar = 2',
			),
			';'
		);

		$this->assertThat(
			trim($q->set),
			$this->identicalTo("SET foo = 1\n\t; bar = 2"),
			'Tests set with an array and glue.'
		);
	}

	/**
	 * Tests the JDatabaseQuery::truncate method.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function testTruncate()
	{
		$q = new JDatabaseQueryInspector($this->dbo);

		$this->assertThat(
			$q->truncate('#__foo'),
			$this->identicalTo($q),
			'Tests chaining.'
		);

		$this->assertThat(
			$q->type,
			$this->equalTo('truncate'),
			'Tests the type property is set correctly.'
		);

		$this->assertThat(
			trim($q->truncate),
			$this->equalTo('TRUNCATE TABLE #__foo'),
			'Tests the truncate element is set correctly.'
		);
	}

	/**
	 * Tests the JDatabaseQuery::update method.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function testUpdate()
	{
		$q = new JDatabaseQueryInspector($this->dbo);

		$this->assertThat(
			$q->update('#__foo'),
			$this->identicalTo($q),
			'Tests chaining.'
		);

		$this->assertThat(
			$q->type,
			$this->equalTo('update'),
			'Tests the type property is set correctly.'
		);

		$this->assertThat(
			trim($q->update),
			$this->equalTo('UPDATE #__foo'),
			'Tests the update element is set correctly.'
		);
	}

	/**
	 * Tests the JDatabaseQuery::values method.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function testValues()
	{
		$q = new JDatabaseQueryInspector($this->dbo);

		$this->assertThat(
			$q->values('1,2,3'),
			$this->identicalTo($q),
			'Tests chaining.'
		);

		$this->assertThat(
			trim($q->values),
			$this->equalTo('(1,2,3)'),
			'Tests rendered value.'
		);

		// Add another column.
		$q->values(
			array(
				'4,5,6',
				'7,8,9',
			)
		);

		$this->assertThat(
			trim($q->values),
			$this->equalTo('(1,2,3),(4,5,6),(7,8,9)'),
			'Tests rendered value after second use and array input.'
		);
	}

	/**
	 * Tests the JDatabaseQuery::where method.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function testWhere()
	{
		$q = new JDatabaseQueryInspector($this->dbo);

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
	* Tests the JDatabaseQuery::__clone method properly clones an array.
	*
	* @return  void
	*
	* @since   11.3
	*/
	public function test__clone_array()
	{
		$baseElement = new JDatabaseQueryInspector($this->getMockDatabase());

		$baseElement->testArray = array();

		$cloneElement = clone($baseElement);

		$baseElement->testArray[] = 'test';

		$this->assertFalse($baseElement === $cloneElement);
		$this->assertTrue(count($cloneElement->testArray) == 0);
	}

	/**
	 * Tests the JDatabaseQuery::__clone method properly clones an object.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function test__clone_object()
	{
		$baseElement = new JDatabaseQueryInspector($this->getMockDatabase());

		$baseElement->testObject = new stdClass;

		$cloneElement = clone($baseElement);

		$this->assertFalse($baseElement === $cloneElement);

		$this->assertFalse($baseElement->testObject === $cloneElement->testObject);
	}
}
