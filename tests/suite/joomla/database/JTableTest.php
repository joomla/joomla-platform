<?php
/**
 * @version		$Id: JTableTest.php 20196 2011-01-09 02:40:25Z ian $
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

require_once JPATH_PLATFORM.'/joomla/database/table.php';
require_once 'TestHelpers/JTable-helper-dataset.php';

/**
 * Test class for JTable.
 * Generated by PHPUnit on 2009-10-08 at 22:02:03.
 */
class JTableTest extends PHPUnit_Framework_TestCase {
	/**
	 * @return  array
	 *
	 * @since   11.3
	 */
	public function getGetSourceData()
	{
		return JTableTest_DataSet::getGetSourceTest();
	}

	/**
	 * @var	JTable
	 */
	protected $object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp() {
		/**
		 * JTable is abstract class.  Needs Mock
		 * $this->object = new JTable;
		 */
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown() {
	}

	/**
	 * @todo Implement testGetInstance().
	 */
	public function testGetInstance() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @todo Implement testAddIncludePath().
	 */
	public function testAddIncludePath() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @todo Implement testGetTableName().
	 */
	public function testGetTableName() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @todo Implement testGetKeyName().
	 */
	public function testGetKeyName() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @todo Implement testGetDBO().
	 */
	public function testGetDBO() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @todo Implement testSetDBO().
	 */
	public function testSetDBO() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @todo Implement testSetRules().
	 */
	public function testSetRules() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @todo Implement testGetRules().
	 */
	public function testGetRules() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @todo Implement testReset().
	 */
	public function testReset() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * Method that returns the source of a bind
	 *
	 * @dataProvider  getGetSourceData
	 * @since   11.3
	 */
	public function testGetSource($source, $value)
	{
		$mockTable = $this->getMock('JTable', null, array(), '', false);
		$mockTable->bind($source);
		$this->assertEquals(
			$value,
			$mockTable->getSource(),
			'Line:'.__LINE__.' getSource has not been set to its value when calling bind.'
		);
	}

	/**
	 * @todo Implement testBind().
	 */
	public function testBind() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @todo Implement testLoad().
	 */
	public function testLoad() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @todo Implement testCheck().
	 */
	public function testCheck() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @todo Implement testStore().
	 */
	public function testStore() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @todo Implement testSave().
	 */
	public function testSave() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @todo Implement testDelete().
	 */
	public function testDelete() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @todo Implement testCheckOut().
	 */
	public function testCheckOut() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @todo Implement testCheckIn().
	 */
	public function testCheckIn() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @todo Implement testHit().
	 */
	public function testHit() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @todo Implement testIsCheckedOut().
	 */
	public function testIsCheckedOut() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @todo Implement testGetNextOrder().
	 */
	public function testGetNextOrder() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @todo Implement testReorder().
	 */
	public function testReorder() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @todo Implement testMove().
	 */
	public function testMove() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @todo Implement testPublish().
	 */
	public function testPublish() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @todo Implement testCanDelete().
	 */
	public function testCanDelete() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @todo Implement testToXML().
	 */
	public function testToXML() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}
}
?>
