<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Data
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

JLoader::register('Landsat', __DIR__ . '/stubs/landsat.php');
JLoader::register('Landsat6', __DIR__ . '/stubs/landsat6.php');

/**
 * Tests for the JContentHelperTest class.
 *
 * @package     Joomla.UnitTest
 * @subpackage  Data
 * @since       12.3
 */
class JDataMapperTest extends TestCase
{
	/**
	 * @var    JDataMapper
	 * @since  12.3
	 */
	protected $_instance;

	/**
	 * Tests the __construct method.
	 *
	 * @return  void
	 *
	 * @covers  JDataMapper::__construct
	 * @since   12.3
	 */
	public function test__construct()
	{
		$initData = TestReflection::getValue($this->_instance, 'data');
		$this->assertEquals(5, count($initData), 'Check initialise was called.');
	}

	/**
	 * Tests the create method.
	 *
	 * @return  void
	 *
	 * @covers  JDataMapper::create
	 * @since   12.3
	 */
	public function testCreate()
	{
		$input = new JData(array('fail' => false));

		$this->assertThat(
			$this->_instance->create($input),
			$this->equalTo($input),
			'Checks the return value.'
		);
	}

	/**
	 * Tests the create method for an expected exception.
	 *
	 * @return  void
	 *
	 * @covers            JDataMapper::create
	 * @since             12.3
	 * @expectedException UnexpectedValueException
	 */
	public function testCreate_exception1()
	{
		$instance = new Landsat6;
		$instance->create(new JData);
	}

	/**
	 * Tests the delete method.
	 *
	 * @return  void
	 *
	 * @covers  JDataMapper::delete
	 * @since   12.3
	 */
	public function testDelete()
	{
		$this->_instance->delete(1);

		$this->assertEquals(array(1), $this->_instance->deleted);
	}

	/**
	 * Tests the find method.
	 *
	 * @return  void
	 *
	 * @covers  JDataMapper::find
	 * @since   12.3
	 */
	public function testFind()
	{
		$this->assertEquals(3, count($this->_instance->find(null)), 'Check find all.');
		$this->assertEquals(2, count($this->_instance->find(null, null, 0, 2)), 'Check find with a limit.');
	}

	/**
	 * Tests the find method for an expected exception.
	 *
	 * @return  void
	 *
	 * @covers             JDataMapper::find
	 * @since              12.3
	 * @expectedException  UnexpectedValueException
	 */
	public function testFind_exception()
	{
		$instance = new Landsat6;
		$instance->find(null);
	}

	/**
	 * Tests the findOne method.
	 *
	 * @return  void
	 *
	 * @covers  JDataMapper::findOne
	 * @since   12.3
	 */
	public function testFindOne()
	{
		$findOne = $this->_instance->findOne(null);
		$this->assertInstanceOf('JData', $findOne);
		$this->assertEquals('Landsat 1', $findOne->name);
		$this->assertNull($this->_instance->findOne(6), 'Check findOne where no results are returned.');
	}

	/**
	 * Tests the Update method.
	 *
	 * @return  void
	 *
	 * @covers  JDataMapper::update
	 * @since   12.3
	 */
	public function testUpdate()
	{
		$input = new JData(array('id' => 1, 'name' => 'Landsat 1A'));

		$updated = $this->_instance->update($input);
		$this->assertInstanceOf('JData', $updated, 'Checks the return value is a JData.');
		$this->assertTrue($updated->updated);

		$input = new JDataSet(
			array(
				new JData(array('id' => 1, 'updated' => false)),
				new JData(array('id' => 2, 'updated' => false))
			)
		);

		$updated = $this->_instance->update($input);
		$this->assertInstanceOf('JDataSet', $updated, 'Checks the return value is a JDataSet.');
		$this->assertCount(2, $updated);
	}

	/**
	 * Tests the update method for an expected exception.
	 *
	 * @return  void
	 *
	 * @covers            JDataMapper::update
	 * @since             12.3
	 * @expectedException UnexpectedValueException
	 */
	public function testUpdate_exception1()
	{
		$instance = new Landsat6;
		$instance->update(new JData);
	}

	/**
	 * Setup the tests.
	 *
	 * @return  void
	 *
	 * @since   11.4
	 */
	protected function setUp()
	{
		parent::setUp();

		$this->_instance = new Landsat;
	}
}
