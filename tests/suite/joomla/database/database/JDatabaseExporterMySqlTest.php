<?php
/**
 * @copyright  Copyright (C) 2005 - 2012 Open Source Matters. All rights reserved.
 * @license    GNU General Public License
 */

require_once JPATH_PLATFORM.'/joomla/database/database/mysqlexporter.php';

/**
 * Tests the JDatabaseExporterMySql class.
 *
 * @package    Joomla.UnitTest
 * @subpackage Database
 * @since      11.1
 */
class JDatabaseExporterMySqlTest extends JoomlaTestCase
{
	/**
	 * @var    JDatabaseExporterMySQL
	 * @since  12.1
	 */
	protected $class;

	/**
	 * @var    object  The mocked database object for use by test methods.
	 * @since  11.1
	 */
	protected $dbo = null;

	/**
	 * @var    string  The last query sent to the dbo setQuery method.
	 * @since  11.1
	 */
	protected $lastQuery = '';

	/**
	 * Sets up the testing conditions
	 *
	 * @return  void
	 * @since   11.1
	 */
	public function setup()
	{
		$this->class = new JDatabaseExporterMySQL;

		// Set up the database object mock.
		$this->dbo = $this->getMockDatabase('JDatabaseMySQL');

		$this->dbo->expects(
			$this->any()
		)
		->method('getPrefix')
		->will(
			$this->returnValue(
				'jos_'
			)
		);

		$this->dbo->expects(
			$this->any()
		)
		->method('getTableColumns')
		->will(
			$this->returnValue(
				array(
					(object) array(
						'Field' => 'id',
						'Type' => 'int(11) unsigned',
						'Collation' => null,
						'Null' => 'NO',
						'Key' => 'PRI',
						'Default' => '',
						'Extra' => 'auto_increment',
						'Privileges' => 'select,insert,update,references',
						'Comment' => '',
					),
					(object) array(
						'Field' => 'title',
						'Type' => 'varchar(255)',
						'Collation' => 'utf8_general_ci',
						'Null' => 'NO',
						'Key' => '',
						'Default' => '',
						'Extra' => '',
						'Privileges' => 'select,insert,update,references',
						'Comment' => '',
					),
				)
			)
		);

		$this->dbo->expects(
			$this->any()
		)
		->method('getTableKeys')
		->will(
			$this->returnValue(
				array(
					(object) array(
						'Table' => 'jos_test',
			            'Non_unique' => '0',
			            'Key_name' => 'PRIMARY',
			            'Seq_in_index' => '1',
			            'Column_name' => 'id',
			            'Collation' => 'A',
			            'Cardinality' => '2695',
			            'Sub_part' => '',
			            'Packed' => '',
			            'Null' => '',
			            'Index_type' => 'BTREE',
			            'Comment' => '',
					)
				)
			)
		);

		$this->dbo->expects(
			$this->any()
		)
		->method('loadObjectList')
		->will(
			$this->returnCallback(
				array($this, 'callbackLoadObjectList')
			)
		);
	}

	/**
	 * Callback for the dbo loadObjectList method.
	 *
	 * @return array  An array of results based on the setting of the last query.
	 * @since  11.1
	 */
	public function callbackLoadObjectList()
	{
		return array();
	}

	/**
	 * Test the magic __toString method.
	 *
	 * @return  void
	 * @since   11.1
	 */
	public function test__toString()
	{
		// Set up the export settings.
		$this->class
			->setDbo($this->dbo)
			->from('jos_test')
			->withStructure(true)
			;

		$this->assertThat(
			(string) $this->class,
			$this->equalTo(
'<?xml version="1.0"?>
<mysqldump xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
 <database name="">
  <table_structure name="#__test">
   <field Field="id" Type="int(11) unsigned" Null="NO" Key="PRI" Default="" Extra="auto_increment" Comment="" />
   <field Field="title" Type="varchar(255)" Null="NO" Key="" Default="" Extra="" Comment="" />
   <key Table="#__test" Non_unique="0" Key_name="PRIMARY" Seq_in_index="1" Column_name="id" Collation="A" Null="" Index_type="BTREE" Comment="" />
  </table_structure>
 </database>
</mysqldump>'
			),
			'__toString has not returned the expected result.'
		);

	}

	/**
	 * Tests the asXml method.
	 *
	 * @return void
	 * @since  11.1
	 */
	public function testAsXml()
	{
		$result = $this->class->asXml();

		$this->assertThat(
			$result,
			$this->identicalTo($this->class),
			'asXml must return an object to support chaining.'
		);

		$this->assertThat(
			ReflectionHelper::getValue($this->class, 'asFormat'),
			$this->equalTo('xml'),
			'The asXml method should set the protected asFormat property to "xml".'
		);
	}

	/**
	 * Test the buildXML method.
	 *
	 * @return  void
	 * @since   11.1
	 */
	public function testBuildXml()
	{
		// Set up the export settings.
		$this->class
			->setDbo($this->dbo)
			->from('jos_test')
			->withStructure(true)
			;

		$this->assertThat(
			ReflectionHelper::invoke($this->class, 'buildXml'),
			$this->equalTo(
'<?xml version="1.0"?>
<mysqldump xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
 <database name="">
  <table_structure name="#__test">
   <field Field="id" Type="int(11) unsigned" Null="NO" Key="PRI" Default="" Extra="auto_increment" Comment="" />
   <field Field="title" Type="varchar(255)" Null="NO" Key="" Default="" Extra="" Comment="" />
   <key Table="#__test" Non_unique="0" Key_name="PRIMARY" Seq_in_index="1" Column_name="id" Collation="A" Null="" Index_type="BTREE" Comment="" />
  </table_structure>
 </database>
</mysqldump>'
			),
			'buildXml has not returned the expected result.'
		);
	}

	/**
	 * Tests the buildXmlStructure method.
	 *
	 * @return  void
	 * @since   11.1
	 */
	public function testBuildXmlStructure()
	{
		// Set up the export settings.
		$this->class
			->setDbo($this->dbo)
			->from('jos_test')
			->withStructure(true)
			;

		$this->assertThat(
			ReflectionHelper::invoke($this->class, 'buildXmlStructure'),
			$this->equalTo(
				array(
					'  <table_structure name="#__test">',
					'   <field Field="id" Type="int(11) unsigned" Null="NO" Key="PRI" Default="" Extra="auto_increment" Comment="" />',
					'   <field Field="title" Type="varchar(255)" Null="NO" Key="" Default="" Extra="" Comment="" />',
					'   <key Table="#__test" Non_unique="0" Key_name="PRIMARY" Seq_in_index="1" Column_name="id" Collation="A" Null="" Index_type="BTREE" Comment="" />',
					'  </table_structure>'
				)
			),
			'buildXmlStructure has not returned the expected result.'
		);
	}

	/**
	 * Tests the check method.
	 *
	 * @return void
	 * @since  11.1
	 */
	public function testCheckWithNoDbo()
	{
		try
		{
			$this->class->check();
		}
		catch (Exception $e)
		{
			// Exception expected.
			return;
		}

		$this->fail(
			'Check method should throw exception if DBO not set'
		);
	}

	/**
	 * Tests the check method.
	 *
	 * @return void
	 * @since  11.1
	 */
	public function testCheckWithNoTables()
	{
		$this->class->setDbo($this->dbo);

		try
		{
			$this->class->check();
		}
		catch (Exception $e)
		{
			// Exception expected.
			return;
		}

		$this->fail(
			'Check method should throw exception if DBO not set'
		);
	}

	/**
	 * Tests the check method.
	 *
	 * @return void
	 * @since  11.1
	 */
	public function testCheckWithGoodInput()
	{
		$this->class->setDbo($this->dbo);
		$this->class->from('foobar');

		try
		{
			$result = $this->class->check();

			$this->assertThat(
				$result,
				$this->identicalTo($this->class),
				'check must return an object to support chaining.'
			);
		}
		catch (Exception $e)
		{
			$this->fail(
				'Check method should not throw exception with good setup: '.$e->getMessage()
			);
		}
	}

	/**
	 * Tests the from method with bad input.
	 *
	 * @return void
	 * @since  11.1
	 */
	public function testFromWithBadInput()
	{
		try
		{
			$this->class->from(new stdClass);
		}
		catch (Exception $e)
		{
			// Exception expected.
			return;
		}

		$this->fail(
			'From method should thrown an exception if argument is not a string or array.'
		);
	}

	/**
	 * Tests the from method with expected good inputs.
	 *
	 * @return void
	 * @since  11.1
	 */
	public function testFromWithGoodInput()
	{
		try
		{
			$result = $this->class->from('jos_foobar');

			$this->assertThat(
				$result,
				$this->identicalTo($this->class),
				'from must return an object to support chaining.'
			);

			$this->assertThat(
				ReflectionHelper::getValue($this->class, 'from'),
				$this->equalTo(array('jos_foobar')),
				'The from method should convert a string input to an array.'
			);
		}
		catch (Exception $e)
		{
			$this->fail(
				'From method should not throw exception with good input: '.$e->getMessage()
			);
		}
	}

	/**
	 * Tests the method getGenericTableName method.
	 *
	 * @return  void
	 * @since   11.1
	 */
	public function testGetGenericTableName()
	{
		$this->class->setDbo($this->dbo);

		$this->assertThat(
			ReflectionHelper::invoke($this->class, 'getGenericTableName', 'jos_test'),
			$this->equalTo('#__test'),
			'The testGetGenericTableName should replace the database prefix with #__.'
		);
	}

	/**
	 * Tests the setDbo method with the wrong type of class.
	 *
	 * @return void
	 * @since  11.1
	 */
	public function testSetDboWithBadInput()
	{
		try
		{
			$this->class->setDbo(new stdClass);
		}
		catch (PHPUnit_Framework_Error $e)
		{
			// Expecting the error, so just ignore it.
			return;
		}

		$this->fail(
			'setDbo requires a JDatabaseMySql object and should throw an exception.'
		);
	}

	/**
	 * Tests the setDbo method with the wrong type of class.
	 *
	 * @return void
	 * @since  11.1
	 */
	public function testSetDboWithGoodInput()
	{
		try
		{
			$result = $this->class->setDbo($this->dbo);

			$this->assertThat(
				$result,
				$this->identicalTo($this->class),
				'setDbo must return an object to support chaining.'
			);

		}
		catch (PHPUnit_Framework_Error $e)
		{
			// Unknown error has occurred.
			$this->fail(
				$e->getMessage()
			);
		}
	}

	/**
	 * Tests the withStructure method.
	 *
	 * @return  void
	 * @since   11.1
	 */
	public function testWithStructure()
	{
		$result = $this->class->withStructure();

		$this->assertThat(
			$result,
			$this->identicalTo($this->class),
			'withStructure must return an object to support chaining.'
		);

		$this->assertThat(
			ReflectionHelper::getValue($this->class, 'options')->get('with-structure'),
			$this->isTrue(),
			'The default use of withStructure should result in true.'
		);

		$this->class->withStructure(true);
		$this->assertThat(
			ReflectionHelper::getValue($this->class, 'options')->get('with-structure'),
			$this->isTrue(),
			'The explicit use of withStructure with true should result in true.'
		);

		$this->class->withStructure(false);
		$this->assertThat(
			ReflectionHelper::getValue($this->class, 'options')->get('with-structure'),
			$this->isFalse(),
			'The explicit use of withStructure with false should result in false.'
		);
	}
}
