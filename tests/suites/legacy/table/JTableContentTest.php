<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Database
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * Test class for JTableMenuType.
 *
 * @package     Joomla.UnitTest
 * @subpackage  Table
 *
 * @since       12.3
 */
class JTableContentTest extends TestCaseDatabase
{
	/**
	 * @var  JTableContent
	 */
	protected $object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return  void
	 */
	protected function setUp()
	{
		parent::setUp();

		// Get the mocks
		$this->saveFactoryState();

		JFactory::$session = $this->getMockSession();

		$this->object = new JTableContent(self::$driver);
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @return  void
	 */
	protected function tearDown()
	{
		$this->restoreFactoryState();

		parent::tearDown();
	}

	/**
	 * Gets the data set to be loaded into the database during setup
	 *
	 * @since   11.4
	 *
	 * @return  CSV database tables
	 */
	protected function getDataSet()
	{
		$dataSet = new PHPUnit_Extensions_Database_DataSet_CsvDataSet(',', "'", '\\');

		$stubpath = JPATH_TESTS . '/suites/unit/joomla/table/stubs';

		$dataSet->addTable('jos_assets', $stubpath . '/jos_assets.csv');
		$dataSet->addTable('jos_categories', __DIR__ . '/stubs/jos_categories.csv');
		$dataSet->addTable('jos_content', __DIR__ . '/stubs/jos_content.csv');

		return $dataSet;
	}

	/**
	 * Test JTableContent::bind
	 *
	 * @todo   Implement testBind().
	 *
	 * @return  void
	 */
	public function testBind()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * Tests JTableContent::check
	 *
	 * @since   11.4
	 *
	 * @return  void
	 */
	public function testCheck()
	{
		$table = $this->object;

		$this->assertThat(
			$table->check(),
			$this->isFalse(),
			'Line: ' . __LINE__ . ' Checking an empty table should fail.'
		);

		$table->title = 'Test Title';
		$this->assertThat(
			$table->check(),
			$this->isTrue(),
			'Line: ' . __LINE__ . ' Checking the table with just the title should pass.'
		);

		$this->assertThat(
			$table->alias,
			$this->equalTo('test-title'),
			'Line: ' . __LINE__ . ' An empty alias should assume the value of the title.'
		);

		$table->introtext = '';
		$this->assertThat(
			$table->check(),
			$this->isTrue(),
			'Line: ' . __LINE__ . ' Checking with an empty introtext should pass.'
		);

		$table->introtext = 'The intro text object.';
		$table->publish_down = '2001-01-01 00:00:00';
		$table->publish_up = JFactory::getDate();

		$this->assertThat(
			$table->check(),
			$this->isTrue(),
			'Line: ' . __LINE__ . ' The check function should now complete without error.'
		);

		$this->assertThat(
			$table->publish_up,
			$this->equalTo('2001-01-01 00:00:00'),
			'Line: ' . __LINE__ . ' The check function should have reversed the previously set publish_up and down times.'
		);
	}

	/**
	 * Tests JTableContent::store
	 *
	 * @since   11.4
	 *
	 * @return  void
	 */
	public function testStore()
	{
		$table = $this->object;

		// Handle updating an existing article
		$table->load('3');
		$originalAlias = $table->alias;
		$table->title = 'New Title';
		$table->alias = 'archive-module';
		$this->assertFalse($table->store(), 'Line: ' . __LINE__ . ' Table store should fail due to a duplicated alias');
		$table->alias = 'article-categories-module';
		$this->assertTrue($table->store(), 'Line: ' . __LINE__ . ' Table store should succeed');
		$table->reset();
		$table->load('3');
		$this->assertEquals('New Title', $table->title, 'Line: ' . __LINE__ . ' Title should be updated');
		$this->assertEquals($originalAlias, $table->alias, 'Line: ' . __LINE__ . ' Alias should be the same as originally set');

		// Store a new article
		$table->load('8');
		$table->id = null;
		$table->title = 'Beginners (Copy)';
		$table->alias = 'beginners-copy';
		$table->created = null;
		$table->created_by = null;
		$this->assertTrue($table->store(), 'Line: ' . __LINE__ . ' Table store should succeed and insert a new record');
	}

	/**
	 * Tests JTableContent::publish
	 *
	 * @since   11.4
	 *
	 * @return  void
	 */
	public function testPublish()
	{
		$table = $this->object;

		// Test with pk's in an array
		$pks = array('18', '31');
		$this->assertTrue($table->publish($pks, '0'), 'Line: ' . __LINE__ . ' Publish with an array of pks should work');
		$table->load('18');
		$this->assertEquals('0', $table->state, 'Line: ' . __LINE__ . ' Id 18 should be unpublished');
		$table->reset();
		$table->load('31');
		$this->assertEquals('0', $table->state, 'Line: ' . __LINE__ . ' Id 31 should be unpublished');
		$table->reset();

		// Test with a single pk
		$this->assertTrue($table->publish('32', '1'), 'Line: ' . __LINE__ . ' Publish with a single pk should work');
		$table->load('32');
		$this->assertEquals('1', $table->state, 'Line: ' . __LINE__ . ' Id 32 should be published');
	}
}
