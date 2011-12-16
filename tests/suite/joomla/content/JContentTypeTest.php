<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Content
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * Joomla Platform Content Type Test Class
 *
 * @package     Joomla.UnitTest
 * @subpackage  Database
 * @since       12.1
 */
class JContentTypeTest extends JoomlaDatabaseTestCase
{
	/**
	 * Test object.
	 *
	 * @var    JContentType
	 * @since  12.1
	 */
	protected $object;

	/**
	 * Gets the data set to be loaded into the database during setup
	 *
	 * @return  xml dataset
	 *
	 * @since   11.1
	 */
	protected function getDataSet()
	{
		return $this->createXMLDataSet(__DIR__.'/stubs/type.xml');
	}

	/**
	 * Setup for testing.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	protected function setUp()
	{
		parent::setup();

		$this->saveFactoryState();

		JFactory::$session = $this->getMockSession();

		// Get the inspector object.
		$this->object = new JContentType;
	}

	/**
	 * @return  void
	 *
	 * @since   12.1
	 */
	protected function tearDown()
	{
		$this->restoreFactoryState();

		parent::teardown();
	}

	/**
	 * Tests the JContentType::__construct() method.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function test__construct()
	{
		$db = $this->getMockDatabase();
		$db->foo = 'bar';

		$user = JFactory::getUser();
		$user->foo = 'bar';

		$object = new JContentType($db, $user);

		$db2 = ReflectionHelper::getValue($object, 'db');
		$user2 = ReflectionHelper::getValue($object, 'user');

		$this->assertThat(
			$db2->foo,
			$this->equalTo('bar'),
			'Checks the database object has been correctly injected.'
		);

		$this->assertThat(
			$user2->foo,
			$this->equalTo('bar'),
			'Checks the user object has been correctly injected.'
		);
	}

	/**
	 * Tests the JContentType::authorise() method.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testAuthorise()
	{
		// Create a user.
		$user = JFactory::getUser();
		$user->id = 42;

		// Set rules.
		$this->object->rules = '{"do":{"-42":1,"42":0}}';

		$this->assertThat(
			$this->object->authorise('do', $user),
			$this->isTrue(),
			'Checks allow.'
		);

		// Add the user to a group.
		$user->groups = array(42);

		$this->assertThat(
			$this->object->authorise('do', $user),
			$this->isFalse(),
			'Checks explicit deny.'
		);
	}

	/**
	 * Tests the JContentType::canCreate() method.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testCanCreate()
	{
		// Create a user for injection.
		$user = JFactory::getUser();
		$user->id = 42;

		$object = new JContentType(null, $user);
		$object->load(1);

		$this->assertThat(
			$object->canCreate(),
			$this->isTrue(),
			'Checks allow.'
		);

		// Create a user for injection.
		$user->groups = array(42);

		$object = new JContentType(null, $user);
		$object->load(1);

		$this->assertThat(
			$object->canCreate(),
			$this->isFalse(),
			'Checks explicit deny.'
		);
	}

	/**
	 * Tests the JContentType::canCreate() method.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 * @expectedException LogicException
	 */
	public function testCanCreateNotLoadedException()
	{
		// Content no loaded will cause an exception.
		$this->object->canCreate();
	}

	/**
	 * Tests the JContentType::checkTableExists() method.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testCheckTableExists()
	{
		$this->object->load(3);

		// Exception will be thrown if table does not exist.
		ReflectionHelper::invoke($this->object, 'validateTableExists');
	}

	/**
	 * Tests the JContentType::checkTableExists() method.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 * @expectedException RuntimeException
	 */
	public function testCheckTableExistsException()
	{
		$this->object->load(2);

		// Exception will be thrown if table does not exist.
		ReflectionHelper::invoke($this->object, 'validateTableExists');
	}

	/**
	 * Tests the JContentType::getRules() method.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testGetRules()
	{
		ReflectionHelper::setValue($this->object, 'properties', array('rules' => '{"foo":"bar"}'));

		$this->assertThat(
			$this->object->rules,
			$this->equalTo(array('foo' => 'bar'))
		);
	}

	/**
	 * Tests the JContentType::setRules() method.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testSetRules()
	{
		$this->object->rules = 'string';
		$properties = ReflectionHelper::getValue($this->object, 'properties');

		$this->assertThat(
			$properties['rules'],
			$this->equalTo('string'),
			'Checks that a string is set.'
		);

		$this->object->rules = array('foo' => 'bar');
		$properties = ReflectionHelper::getValue($this->object, 'properties');

		$this->assertThat(
			$properties['rules'],
			$this->equalTo('{"foo":"bar"}'),
			'Checks that array input is converted to a JSON string.'
		);
	}
}
