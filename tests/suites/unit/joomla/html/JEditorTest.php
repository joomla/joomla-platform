<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  HTML
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

require_once __DIR__ . '/JEditorInspector.php';

/**
 * Test class for JEditor.
 *
 * @package     Joomla.UnitTest
 * @subpackage  HTML
 * @since       12.2
 */
class JEditorTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var   object   JEditor
	 *
	 * @since 12.2
	 */
	protected $editor;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return  void
	 */
	protected function setUp()
	{
		$this->editor = new JEditorInspector;

		// Mock editor plugins.
		require_once __DIR__ . '/stubs/fakeEditorPlugin.php';
		require_once __DIR__ . '/stubs/fakeEditorXtdPlugin.php';

		// Inject the mocked plugin list.
		TestReflection::setValue('JPluginHelper', 'plugins', array(
				(object) array(
					'type' => 'editors',
					'name' => 'fake'
				),
				(object) array(
					'type' => 'editors-xtd',
					'name' => 'fake'
				)
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
		// Reset the loaded plugins.
		TestReflection::setValue('JPluginHelper', 'plugins', null);

		parent::tearDown();
	}

	/**
	 * Test the constructor.
	 *
	 * @return  void
	 */
	public function test__construct()
	{
		// Test default constructor called in setUp.
		$this->assertEquals($this->editor->_name, 'none');

		// Test with an other value.
		$object = new JEditor('tinymce');
		$this->assertEquals(TestReflection::getValue($object, '_name'), 'tinymce');
	}

	/**
	 * Test the getInstance() method.
	 *
	 * @return  void
	 */
	public function testGetInstance()
	{
		$instance = $this->editor->getInstance();
		$instance1 = $this->editor->getInstance('tinymce');

		// Check returned values.
		$this->assertInstanceof('JEditor', $instance);
		$this->assertInstanceof('JEditor', $instance1);

		// Check if the instances are correctly stored.
		$instances = $this->editor->instances;
		$this->assertInstanceof('JEditor', $instances[serialize('none')]);
		$this->assertInstanceof('JEditor', $instances[serialize('tinymce')]);
	}

	/**
	 * Test the getState() method.
	 *
	 * @return  void
	 */
	public function testGetState()
	{
		// Test default value.
		$this->assertNull($this->editor->getState());

		// Set an other value and test.
		$this->editor->_state = false;
		$this->assertFalse($this->editor->getState());
	}

	/**
	 * Test the attach() method.
	 *
	 * @return  void
	 */
	public function testAttach()
	{
		// Test an invalid observer.
		$observer = array();
		$this->editor->attach($observer);

		// Check _methods and _observers arguments are empty.
		$this->assertEquals($this->editor->_methods, array());
		$this->assertEquals($this->editor->_observers, array());

		// Test an uncallable observer.
		$observer = array('handler' => 'fakefunction', 'event' => 'onTestEvent');
		$this->editor->attach($observer);

		// Check _methods and _observers arguments are empty.
		$this->assertEquals($this->editor->_methods, array());
		$this->assertEquals($this->editor->_observers, array());

		// Test a callable function observer.
		$observer = array('handler' => 'JEditorEventMockFunction', 'event' => 'onTestEvent');
		$observers = array($observer);

		$this->editor->attach($observer);

		// Check _methods and _observers arguments are correctly set.
		$this->assertEquals($this->editor->_methods, array('ontestevent' => array(0)));
		$this->assertEquals($this->editor->_observers, $observers);

		// Test to attach it twice.
		$observer = array('handler' => 'JEditorEventMockFunction', 'event' => 'onTestEvent');
		$observers = array($observer);

		$returned = $this->editor->attach($observer);

		// Check it returned NULL.
		$this->assertNull($returned);

		// OR Check _methods and _observers arguments are unchanged.
		$this->assertEquals($this->editor->_methods, array('ontestevent' => array(0)));
		$this->assertEquals($this->editor->_observers, $observers);

		// Test an invalid object.
		$observer = new stdClass;

		$this->editor->attach($observer);

		// Check _methods and _observers arguments are unchanged.
		$this->assertEquals($this->editor->_methods, array('ontestevent' => array(0)));
		$this->assertEquals($this->editor->_observers, $observers);

		// Test a valid event object.
		$observer = new JEditorInspector;
		$observers[] = $observer;

		$this->editor->attach($observer);
		$this->assertEquals(
			$this->editor->_methods,
				array('__get' => array(1),
					'ontestevent' => array(0,1),
					'__set' => array(1),
					'__call' => array(1),
					'getinstance' => array(1),
					'getstate' => array(1),
					'attach' => array(1),
					'detach' => array(1),
					'initialise' => array(1),
					'display' => array(1),
					'save' => array(1),
					'getcontent' => array(1),
					'setcontent' => array(1),
					'getbuttons' => array(1),
					'_loadeditor' => array(1)
					)
		);

		$this->assertEquals($this->editor->_observers, $observers);

		// Test to attach it twice.
		$observer = new JEditorInspector;
		$this->editor->attach($observer);
		$this->assertEquals(
			$this->editor->_methods,
				array('__get' => array(1),
					'ontestevent' => array(0,1),
					'__set' => array(1),
					'__call' => array(1),
					'getinstance' => array(1),
					'getstate' => array(1),
					'attach' => array(1),
					'detach' => array(1),
					'initialise' => array(1),
					'display' => array(1),
					'save' => array(1),
					'getcontent' => array(1),
					'setcontent' => array(1),
					'getbuttons' => array(1),
					'_loadeditor' => array(1)
			)
		);

		$this->assertEquals($this->editor->_observers, $observers);
	}

	/**
	 * Test the detach() method.
	 *
	 * @return  void
	 */
	public function testDetach()
	{
		$observer1 = array('handler' => 'fakefunction', 'event' => 'onTestEvent');
		$observer2 = array('handler' => 'JEditorEventMockFunction', 'event' => 'onTestEvent');
		$observer3 = new JEditorInspector;

		$this->editor->attach($observer2);
		$this->editor->attach($observer3);

		// Test detaching a non-attached observer.
		$return = $this->editor->detach($observer1);

		// Check it returns false.
		$this->assertFalse($return);

		// Check _methods and _observers arguments are unchanged.
		$this->assertEquals(
			$this->editor->_methods,
				array('__get' => array(1),
					'ontestevent' => array(0,1),
					'__set' => array(1),
					'__call' => array(1),
					'getinstance' => array(1),
					'getstate' => array(1),
					'attach' => array(1),
					'detach' => array(1),
					'initialise' => array(1),
					'display' => array(1),
					'save' => array(1),
					'getcontent' => array(1),
					'setcontent' => array(1),
					'getbuttons' => array(1),
					'_loadeditor' => array(1)
				)
		);

		$this->assertEquals($this->editor->_observers, array($observer2, $observer3));

		// Test detaching an attached observer (observer2).
		$return = $this->editor->detach($observer2);

		$this->assertTrue($return);

		$this->assertEquals(
			$this->editor->_methods,
				array('__get' => array(1),
					'ontestevent' => array(1 => 1),
					'__set' => array(1),
					'__call' => array(1),
					'getinstance' => array(1),
					'getstate' => array(1),
					'attach' => array(1),
					'detach' => array(1),
					'initialise' => array(1),
					'display' => array(1),
					'save' => array(1),
					'getcontent' => array(1),
					'setcontent' => array(1),
					'getbuttons' => array(1),
					'_loadeditor' => array(1)
				)
		);

		$this->assertEquals($this->editor->_observers, array(1 => $observer3));

		// Test detaching an other attached observer (observer3).
		$return = $this->editor->detach($observer3);

		$this->assertTrue($return);

		$this->assertEquals(
			$this->editor->_methods,
				array('__get' => array(),
					'ontestevent' => array(),
					'__set' => array(),
					'__call' => array(),
					'getinstance' => array(),
					'getstate' => array(),
					'attach' => array(),
					'detach' => array(),
					'initialise' => array(),
					'display' => array(),
					'save' => array(),
					'getcontent' => array(),
					'setcontent' => array(),
					'getbuttons' => array(),
					'_loadeditor' => array()
				)
		);

		// Check _observers are empty.
		$this->assertEquals($this->editor->_observers, array());
	}

	/**
	 * Test the initialise() method.
	 *
	 * @return  void
	 */
	public function testInitialise()
	{
		// Verify it returns null if the 'none' plugin doesn't exist.
		$this->assertNull($this->editor->initialise());

		// Test a valid editor plugin with onInit event specified.
		$plugin = new plgEditorFake($this->editor);
		$this->editor->_editor = $plugin;

		$return = $this->editor->initialise();

		// Check it returned nothing.
		$this->assertEmpty($return);

		// Check JDocument is correctly updated.
		$document = JFactory::getDocument();
		$this->assertContains($plugin->onInit(), $document->_custom);
	}

	/**
	 * Test the save() default returning values.
	 *
	 * @return  void
	 */
	public function testSaveWithoutData()
	{
		// Verify it returns null if the 'none' plugin doesn't exist.
		$this->assertNull($this->editor->save('fake'));
	}

	/**
	 * A data provider for testSave and testGetContent
	 *
	 * @return  array
	 */
	public function casesSaveAndGetContent()
	{
		return array(
			array('fake'),
			array(null)
		);
	}

	/**
	 * Test the save() method.
	 *
	 * @param   string  $editor  Editor name
	 *
	 * @dataProvider casesSaveAndGetContent
	 *
	 * @return  void
	 */
	public function testSave($editor)
	{
		// Set a valid editor plugin.
		$plugin = new plgEditorFake($this->editor);
		$this->editor->_editor = $plugin;
		$this->editor->_name = 'fake';

		$return = $this->editor->save($editor);

		// Check the onSave event is correctly triggered and the param passed.
		$this->assertEquals($return, $plugin->onSave($editor));
	}

	/**
	 * Test getContent() default returning values.
	 *
	 * @return  void
	 */
	public function testGetContentWithoutData()
	{
		// Verify it returns null if the 'none' plugin doesn't exist.
		$this->assertNull($this->editor->getContent('fake'));
	}

	/**
	 * Test the getContent() method.
	 *
	 * @param   string  $editor  Editor name
	 *
	 * @dataProvider casesSaveAndGetContent
	 *
	 * @return  void
	 */
	public function testGetContent($editor)
	{
		// Set a valid editor plugin.
		$plugin = new plgEditorFake($this->editor);
		$this->editor->_editor = $plugin;
		$this->editor->_name = 'fake';

		$return = $this->editor->getContent($editor);

		// Check the onGetContent event is correctly triggered and the param passed.
		$this->assertEquals($return, $plugin->onGetContent($editor));
	}

	/**
	 * Test the setContent() default returning values.
	 *
	 * @return  void
	 */
	public function testSetContentWithoutData()
	{
		// Verify it returns null if the 'none' plugin doesn't exist.
		$this->assertNull($this->editor->setContent('fake', 'somestring'));
	}

	/**
	 * A data provider for testSetContent.
	 *
	 * @return  array
	 */
	public function casesSetContent()
	{
		return array(
			array('fake', '<html><head>test_head</head></html>'),
			array(null, null)
		);
	}

	/**
	 * Test the setContent() method.
	 *
	 * @param   string  $editor  The editor name
	 * @param   string  $html    The content
	 *
	 * @dataProvider casesSetContent
	 *
	 * @return  void
	 */
	public function testSetContent($editor, $html)
	{
		// Set a valid editor plugin.
		$plugin = new plgEditorFake($this->editor);
		$this->editor->_editor = $plugin;
		$this->editor->_name = 'fake';

		$return = $this->editor->setContent($editor, $html);

		// Check the onSetContent event is correctly triggered and the params passed.
		$this->assertEquals($return, $plugin->onSetContent($editor, $html));
	}

	/**
	 * Test the getButtons() default returning values.
	 *
	 * @return  void
	 */
	public function testGetButtonsWithoutData()
	{
		// Test it returns correctly with buttons = false.
		$this->assertEmpty($this->editor->getButtons('fake', false));

		// Test it returns correctly with buttons = array();
		$this->assertEmpty($this->editor->getButtons('fake', array()));
	}

	/**
	 * A data provider for testGetButtons().
	 *
	 * @return  array
	 */
	public function casesGetButtons()
	{
		return array(
			array('not_working', false, array()),
			array('fake', true, array('triggered')),
			array('fake', array('Fake'), array('triggered'))
		);
	}

	/**
	 * Test the getButtons() method.
	 *
	 * @param   string  $editor    The editor name
	 * @param   mixed   $buttons   Buttons displayed or no
	 * @param   mixed   $expected  Expected values
	 *
	 * @dataProvider casesGetButtons
	 *
	 * @return  void
	 */
	public function testGetButtons($editor, $buttons, $expected)
	{
		// Set asset and author.
		$this->editor->asset = 'test_asset';
		$this->editor->author = 'test_author';

		// Test with the dataset.
		$this->assertEquals($this->editor->getButtons($editor, $buttons), $expected);
	}

	/**
	 * Test the display() default returning values.
	 *
	 * @return  void
	 */
	public function testDisplayWithoutData()
	{
		// Verify it returns null if the 'none' plugin doesn't exist.
		$this->assertNull(
			$this->editor->display(
				'test', 'test', 'test',
				'test', 'test', 'test',
				'test', 'test', 'test',
				'test', 'test')
		);
	}

	/**
	 * A data provider for the testDisplay() method.
	 *
	 * @return array
	 */
	public function casesDisplay()
	{
		return array(
			array('fake', 'test', '0', '0', '0', '0', true, 'test0', 'test', 'test', array()),
			array('testarea', 'test_content', '100', '250', '0', '1', false, 'test1', 'testasset', 'testauthor', array()),
			array(null, 'test_content', '100', '250', '0', '1', false, 'test1', 'testasset', 'testauthor', array())
		);
	}

	/**
	 * Test the display() method.
	 *
	 * @dataProvider casesDisplay
	 *
	 * @return  void
	 */
	public function testDisplay($name, $html, $width,$height, $col, $row, $buttons, $id, $asset, $author, $params)
	{
		// Set a valid editor plugin.
		$plugin = new plgEditorFake($this->editor);
		$this->editor->_editor = $plugin;

		$return = $this->editor->display(
			$name, $html, $width, $height, $col, $row, $buttons, $id,
			$asset, $author, $params
		);

		// Check asset and author are correctly set.
		$this->assertEquals($this->editor->asset, $asset);
		$this->assertEquals($this->editor->author, $author);

		// Check the onDisplay event is correctly triggered and the param passed.
		$this->assertEquals(
			$return, $plugin->onDisplay(
				$name, $html, $width, $height, $col, $row, $buttons, $id
			)
		);
	}

	/**
	 * Test the _loadEditor() method.
	 */
	public function test_LoadEditor()
	{
		$this->markTestSkipped('This test cannot be implemented in the platform.
		It could if _loadEditor uses JPluginHelper::importPlugin("editors") instead of importing it with JFile.');
	}
}
