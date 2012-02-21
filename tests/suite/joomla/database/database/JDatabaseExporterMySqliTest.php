<?php
/**
 * @copyright  Copyright (C) 2005 - 2012 Open Source Matters. All rights reserved.
 * @license    GNU General Public License
 */

// Must include the mysqli database class so the mock inherits it's class lineage.
require_once JPATH_PLATFORM.'/joomla/database/database/mysqli.php';
require_once JPATH_PLATFORM.'/joomla/database/database/mysqliexporter.php';

/**
 * Tests the JDatabaseMySqlExporter class.
 *
 * @package    Joomla.UnitTest
 * @subpackage Database
 * @since      11.1
 */
class JDatabaseExporterMySQLiTest extends JoomlaTestCase
{
	/**
	 * @var    JDatabaseExporterMySQLi
	 * @since  12.1
	 */
	protected $class;

	/**
	 * @var    object  The mocked database object for use by test methods.
	 * @since  11.1
	 */
	protected $dbo = null;

	/**
	 * Sets up the testing conditions
	 *
	 * @return  void
	 * @since   11.1
	 */
	public function setup()
	{
		// Initialise the class object to test.
		$this->class = new JDatabaseExporterMySqli;

		// Set up the database object mock.
		$this->dbo = $this->getMockDatabase('JDatabaseMySqli');
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
		$this->class	= new JDatabaseExporterMySqli;
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
}
