<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Content
 *
 * @copyright   Copyright 2011 eBay, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

require_once __DIR__ . '/mocks/factory.php';
require_once __DIR__ . '/stubs/ucm.php';

/**
 * Tests for the JContentHelperTest class.
 *
 * @package     Joomla.UnitTest
 * @subpackage  Content
 * @since       12.1
 */
class JContentHelperTest extends JoomlaDatabaseTestCase
{
	/**
	 * Copy of the target object to test.
	 *
	 * @var    JContentHelperInspector
	 * @since  12.1
	 */
	protected $helper;

	/**
     * Returns the test dataset.
     *
     * @return  PHPUnit_Extensions_Database_DataSet_IDataSet
     *
     * @since   12.1
	 */
	protected function getDataSet()
	{
		return $this->createXMLDataSet(__DIR__.'/stubs/type.xml');
	}

	/**
	 * Sets up the fixture.
	 *
	 * This method is called before a test is executed.
	 *
	 * @return  void
	 *
     * @since   12.1
	 */
	public function setUp()
	{
		parent::setUp();

		$this->saveFactoryState();

		JFactory::$session = $this->getMockSession();

		$factory = JContentFactoryMock::create($this);

		$this->helper = new JContentHelper($factory);
	}

	/**
	 * Tears down the fixture.
	 *
	 * This method is called after a test is executed.
	 *
	 * @return  void
	 *
     * @since   12.1
	 */
	public function tearDown()
	{
		$this->restoreFactoryState();

		parent::tearDown();
	}

	/**
	 * Tests the JContentHelper::getStoreId() method.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testGetStoreId()
	{
		// Create a new helper object exactly the same way as the original.
		$newFactory = JContentFactoryMock::create($this);
		$newHelper = new JContentHelper($newFactory);

		// Access this protected method by reflection.
		$storeId1 = ReflectionHelper::invoke($this->helper, 'getStoreId');
		$storeId2 = ReflectionHelper::invoke($newHelper, 'getStoreId');

		// Verify different store IDs for different objects.
		$this->assertNotEquals(
			$storeId1,
			$storeId2,
			'The store IDs for different objects should be unique.'
		);

		// Verify identical store IDs for the same object.
		$this->assertEquals(
			$storeId2,
			ReflectionHelper::invoke($newHelper, 'getStoreId'),
			'The store IDs for the same object should be identical.'
		);

		// Verify different store IDs for the same object with different input values.
		$this->assertNotEquals(
			$storeId2,
			ReflectionHelper::invoke($newHelper, 'getStoreId', 'foo'),
			'The store IDs for the same object with different input should be different.'
		);
	}

	/**
	 * Tests the JContentHelper::getTypes() method.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testGetTypes()
	{
		$types = $this->helper->getTypes();

		$this->assertThat(
			array_keys($types),
			$this->equalTo(array('inspector', 'inspector-2', 'inspector-3')),
			'Check that the two test types are loaded.'
		);

		$this->assertThat(
			$types['inspector'],
			$this->isInstanceOf('JContentType'),
			'Check that the first element is a JContentType.'
		);
	}

	/**
	 * Tests the JContentHelper::getTypes() method for cached values.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testGetTypesFromCache()
	{
		$types = $this->helper->getTypes();

		// Set the helper's db connection to null so that it cannot query the database, thus must pull from cache.
		ReflectionHelper::setValue($this->helper, 'db', null);

		$types2 = $this->helper->getTypes();

		$this->assertEquals($types, $types2, 'Check that the same values are gotten from the JContentHelper::getTypes() method.');
	}
}