<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Content
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// Load test mocks.
require_once __DIR__.'/mocks/helper.php';

/**
 * Joomla Unit Test Class for JContentFactory
 *
 * @package     Joomla.UnitTest
 * @subpackage  Content
 * @since       12.1
 */
class JContentFactoryTest extends JoomlaTestCase
{
	/**
	 * The factory instance.
	 *
	 * @var    JContentFactory
	 * @since  12.1
	 */
	protected $factory;

	/**
	 * Method to set up the tests.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function setUp()
	{
		parent::setUp();

		$this->saveFactoryState();

		// Create a new content factory.
		$this->factory = new JContentFactory('TPrefix', $this->getMockDatabase(), $this->getMockWeb(), new JUser);
	}

	/**
	 * Method to tear down the tests.
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
	 * Test the JContentFactory::__construct method.
	 */
	public function test__construct()
	{
		JFactory::$application = 'factory app';
		JFactory::$database = 'factory db';

		$factory = new JContentFactory('TPrefix', null, null, new JUser);

		$ref = new ReflectionClass($factory);

		$app = $ref->getProperty('app');
		$app->setAccessible(true);

		$this->assertThat(
			$app->getValue($factory),
			$this->equalTo('factory app'),
			'Checks that the app was given a default value.'
		);

		$db = $ref->getProperty('db');
		$db->setAccessible(true);

		$this->assertThat(
			$db->getValue($factory),
			$this->equalTo('factory db'),
			'Checks that the db was given a default value.'
		);
	}

	/**
	 * Method to test that a call to JContentFactory::getInstance() will return a
	 * JContentFactory object.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testGetInstance()
	{
		$this->assertInstanceOf('JContentFactory', JContentFactory::getInstance('TPrefix', $this->getMockDatabase(), $this->getMockWeb(), new JUser));
	}

	/**
	 * Method to test that a call to JContentFactory::getContent will return a
	 * JContent object.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testGetContent()
	{
		// Create a mock type.
		$type = new JContentType($this->getMockDatabase(), new JUser);
		$type->bind(
			array(
				'type_id'	=> 1,
				'title'		=> 'Test Type',
				'alias'		=> 'tctype'
			)
		);

		// Get a mock helper.
		$helper = JContentHelperMock::create($this);
		$helper->expects($this->any())->method('getTypes')->will($this->returnValue(array('tctype' => $type)));

		// Get the content object using the mock helper.
		$this->assertInstanceOf('JContent', $this->factory->getContent('TCType', $helper));
	}

	/**
	 * Method to test that a call to JContentFactory::getContent will throw an
	 * exception when trying to get an invalid content type.
	 *
	 * @expectedException  InvalidArgumentException
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testGetContentWithInvalidContentType()
	{
		// Get a mock helper.
		$helper = JContentHelperMock::create($this);
		$helper->expects($this->any())->method('getTypes')->will($this->returnValue(array()));

		// Get the content object using the mock helper.
		$this->assertInstanceOf('JContent', $this->factory->getContent('TType', $helper));
	}

	/**
	 * Method to test that JContentFactory::getForm works as expected.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testGetForm()
	{
		// Check that an exception is thrown when the form file cannot be loaded.
		try
		{
			// Get the form object.
			$this->assertInstanceOf('JForm', $this->factory->getForm('tctype'));

			// Fail the test.
			$this->fail('Expected exception');
		}
		catch (RuntimeException $error)
		{
			// Reset the forms.
			ReflectionHelper::setValue('JForm', 'forms', array());
		}

		// Add the form directory.
		JForm::addFormPath(__DIR__ . '/stubs/form');

		// Get the form object.
		$form = $this->factory->getForm('tctype');

		// Check that we got the correct form.
		$this->assertInstanceOf('JForm', $form);

		// Check that we have both forms loaded.
		$this->assertInstanceOf('JFormField', $form->getField('title'));
		$this->assertInstanceOf('JFormField', $form->getField('tctype'));
	}

	/**
	 * Method to test that a call to JContentFactory::getHelper will return a
	 * JContentHelper object.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testGetHelper()
	{
		// Get the controller.
		$this->assertInstanceOf('JContentHelper', $this->factory->getHelper());
	}

	/**
	 * Method to test that a call to JContentFactory::getType will return a
	 * JContentType object.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testGetType()
	{
		// Get the controller.
		$this->assertInstanceOf('JContentType', $this->factory->getType('TType'));
	}

	/**
	 * Method to test that a call to JContentFactory::getContentClass() will determine
	 * the most appropriate class based on the available classes.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testGetContentClass()
	{
		// With no other classes defined, the most appropriate class is JContent.
		$this->assertEquals('JContent', $this->factory->getContentClass('TType'));

		// When a prefix class is defined, the most appropriate class is TPrefixContent.
		$this->getMockBuilder('stdClass')->setMockClassName('TPrefixContent')->getMock();
		$this->assertEquals('TPrefixContent', $this->factory->getContentClass('TType'));

		// When a type class is defined, the most appropriate class is JContentTType.
		$this->getMockBuilder('stdClass')->setMockClassName('JContentTType')->getMock();
		$this->assertEquals('JContentTType', $this->factory->getContentClass('TType'));

		// When a prefix and typed class is defined, the most appropriate class is TPrefixContentTType.
		$this->getMockBuilder('stdClass')->setMockClassName('TPrefixContentTType')->getMock();
		$this->assertEquals('TPrefixContentTType', $this->factory->getContentClass('TType'));
	}

	/**
	 * Method to test that a call to JContentFactory::getHelperClass() will determine
	 * the most appropriate class based on the available classes.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testGetHelperClass()
	{
		// With no other classes defined, the most appropriate class is JContentHelper.
		$this->assertEquals('JContentHelper', $this->factory->getHelperClass());

		// When a prefix class is defined, the most appropriate class is TPrefixHelper.
		$this->getMockBuilder('stdClass')->setMockClassName('TPrefixHelper')->getMock();
		$this->assertEquals('TPrefixHelper', $this->factory->getHelperClass());
	}

	/**
	 * Method to test that a call to JContentFactory::getTypeClass() will determine
	 * the most appropriate class based on the available classes.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testGetTypeClass()
	{
		// With no other classes defined, the most appropriate class is JContentType.
		$this->assertEquals('JContentType', $this->factory->getTypeClass('TType', 'TFormat'));

		// When a prefix class is defined, the most appropriate class is TPrefixType.
		$this->getMockBuilder('stdClass')->setMockClassName('TPrefixType')->getMock();
		$this->assertEquals('TPrefixType', $this->factory->getTypeClass('TType', 'TFormat'));

		// When a type class is defined, the most appropriate class is JContentTypeTType.
		$this->getMockBuilder('stdClass')->setMockClassName('JContentTypeTType')->getMock();
		$this->assertEquals('JContentTypeTType', $this->factory->getTypeClass('TType', 'TFormat'));

		// When a prefix and typed class is defined, the most appropriate class is TPrefixTypeTType.
		$this->getMockBuilder('stdClass')->setMockClassName('TPrefixTypeTType')->getMock();
		$this->assertEquals('TPrefixTypeTType', $this->factory->getTypeClass('TType', 'TFormat'));
	}
}
