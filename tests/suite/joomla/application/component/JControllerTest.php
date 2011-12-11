<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Application
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

require_once JPATH_PLATFORM . '/joomla/application/component/controller.php';
require_once JPATH_PLATFORM . '/joomla/environment/request.php';

/**
 * Test class for JController.
 *
 * @package		Joomla.UnitTest
 * @subpackage  Application
 */
class JControllerTest extends PHPUnit_Framework_TestCase
{
	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp()
	{
		if (!defined('JPATH_COMPONENT')) {
			define('JPATH_COMPONENT', JPATH_BASE.'/components/com_foobar');
//			define('JPATH_COMPONENT', __DIR__ . '/_testcomponents/com_foobar');
		}

		include_once 'JControllerInspector.php';

		$this->object = new JControllerInspector;
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown()
	{
	}

	/**
	 * Test JController::__construct
	 *
	 * @since	1.6
	 */
	public function test__construct()
	{
		$controller = new TestTestController;
		$this->assertThat(
			$controller->getTasks(),
			$this->equalTo(
				array(
					'task5', 'task1', 'task2', 'display'
				)
			),
			'Line:' . __LINE__ . ' The available tasks should be the public tasks in _all_ the derived classes after controller plus "display".'
		);
	}

	/**
	 * Test JController::addModelPath
	 *
	 * @since	1.6
	 */
	public function testAddModelPath()
	{
		// Include JModel as this method is a proxy for JModel::addIncludePath
		require_once JPATH_PLATFORM . '/joomla/application/component/model.php';

		$path = JPath::clean(JPATH_ROOT . '/addmodelpath');
		JController::addModelPath($path);

		// The default path is the class file folder/forms
		$valid = JPATH_PLATFORM . '/joomla/form/fields';

		$this->assertThat(
			in_array($path, JModel::addIncludePath()),
			$this->isTrue(),
			'Line:' . __LINE__ . ' The path should be added to the JModel paths.'
		);
	}

	/**
	 * Test JController::createFileName
	 *
	 * @since	1.6
	 */
	public function testCreateFileName()
	{
		$this->assertEquals(
			JControllerInspector::createFileName('foo'),
			'',
			'Line:'.__LINE__.' The view filename is not correct.'
		);
		$this->assertEquals(
			JControllerInspector::createFileName('view', array('name' => 'myview')),
			'myview/view.php',
			'Line:'.__LINE__.' The view filename is not correct.'
		);
		$this->assertEquals(
			JControllerInspector::createFileName('view', array('name' => 'myview', 'type' => 'html')),
			'myview/view.html.php',
			'Line:'.__LINE__.' The view filename is not correct.'
		);
		$this->assertEquals(
			JControllerInspector::createFileName('view', array('name' => 'myview', 'type' => 'mytype')),
			'myview/view.mytype.php',
			'Line:'.__LINE__.' The view filename is not correct.'
		);
		$this->assertEquals(
			JControllerInspector::createFileName('controller', array('name' => 'mycontroller')),
			'mycontroller.php',
			'Line:'.__LINE__.' The controller filename is not correct.'
		);
		$this->assertEquals(
			JControllerInspector::createFileName('controller', array('name' => 'mycontroller', 'format' => 'html')),
			'mycontroller.php',
			'Line:'.__LINE__.' The controller filename is not correct.'
		);
		$this->assertEquals(
			JControllerInspector::createFileName('controller', array('name' => 'mycontroller', 'format' => 'myformat')),
			'mycontroller.myformat.php',
			'Line:'.__LINE__.' The controller filename is not correct.'
		);
	}

	/**
	 * Test JController::addPath
	 *
	 * Note that addPath call JPath::check which will exit if the path is out of bounds.
	 * If execution halts for some reason, a bad path could be the culprit.
	 *
	 * @since	1.6
	 */
	public function testAddPath()
	{
		$controller = new JControllerInspector;

		$path = JPATH_ROOT . '//foobar';
		$controller->addPath('test', $path);
		$paths = $controller->paths;

		$this->assertThat(
			isset($paths['test']),
			$this->isTrue(),
			'Line:' . __LINE__ . ' The path type should be set.'
		);

		$this->assertThat(
			is_array($paths['test']),
			$this->isTrue(),
			'Line:' . __LINE__ . ' The path type should be an array.'
		);

		$this->assertThat(
			str_replace(DIRECTORY_SEPARATOR, '/', $paths['test'][0]),
			$this->equalTo(str_replace(DIRECTORY_SEPARATOR, '/', JPATH_ROOT . '/foobar/')),
			'Line:' . __LINE__ . ' The path type should be present, clean and with a trailing slash.'
		);
	}

	/**
	 * Test JController::addViewPath
	 */
	public function testAddViewPath()
	{
		$controller = new JControllerInspector;

		$path = JPATH_ROOT . '/views';
		$controller->addViewPath($path);
		$paths = $controller->paths;

		$this->assertThat(
			isset($paths['view']),
			$this->isTrue(),
			'Line:' . __LINE__ . ' The path type should be set.'
		);

		$this->assertThat(
			is_array($paths['view']),
			$this->isTrue(),
			'Line:' . __LINE__ . ' The path type should be an array.'
		);

		$this->assertThat(
			str_replace(DIRECTORY_SEPARATOR, '/', $paths['view'][0]),
			$this->equalTo(str_replace(DIRECTORY_SEPARATOR, '/', JPATH_ROOT . '/views/')),
			'Line:' . __LINE__ . ' The path type should be present, clean and with a trailing slash.'
		);
	}

	/**
	 * Test JController::authorize
	 */
	public function testAuthorize()
	{
		$this->markTestSkipped('This method is deprecated.');
	}

	/**
	 * Test JController::createModel
	 */
	public function testCreateModel()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * Test JController::createView
	 */
	public function testCreateView()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * Test JController::display
	 */
	public function testDisplay()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * Test JController::execute
	 */
	public function testExecute()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * Test JController::getInstance
	 */
	public function testGetInstance()
	{
/*		$this->assertThat(
			(JController::getInstance('Foobar', array('base_path' => __DIR__ . '/_testcomponents/com_foobar')) instanceof FoobarController),
			$this->isTrue()
		);
*/
		// Remove the following lines when you implement this test.
		$controller = $this->getMock('JController', null, array(), '', false);
		$className = get_class($controller);
		$_SERVER['REQUEST_METHOD'] = 'get';
		JRequest::setVar('format', 'json');
		try
		{
			$className::getInstance('MyPrefix', array('base_path' => __DIR__ . '/_data/component1'));
		}
		catch (Exception $e)
		{
			$this->assertEquals(
				$e->getMessage(),
				'JLIB_APPLICATION_ERROR_INVALID_CONTROLLER_CLASS',
				'Line:'.__LINE__.' File _data/component1/controller.json.php must be found.'				
			);
		}
		JRequest::setVar('format', 'xml');
		try
		{
			$className::getInstance('MyPrefix', array('base_path' => __DIR__ . '/_data/component1'));
		}
		catch (Exception $e)
		{
			$this->assertEquals(
				$e->getMessage(),
				'JLIB_APPLICATION_ERROR_INVALID_CONTROLLER',
				'Line:'.__LINE__.' File _data/component1/controller.xml.php and _data/component1/controller.php must not be found.'				
			);
		}
		JRequest::setVar('format', 'xml');
		try
		{
			$className::getInstance('MyPrefix', array('base_path' => __DIR__ . '/_data/component2'));
		}
		catch (Exception $e)
		{
			$this->assertEquals(
				$e->getMessage(),
				'JLIB_APPLICATION_ERROR_INVALID_CONTROLLER_CLASS',
				'Line:'.__LINE__.' File _data/component2/controller.php must be found.'				
			);
		}
		$this->markTestIncomplete('This test is not been complete yet.');
	}

	/**
	 * Tests JController::getInstance for exception handling.
	 *
	 * @return  void
	 *
	 * @expectedException  InvalidArgumentException
	 * @since   11.3
	 */
	public function testGetInstanceException()
	{
		JController::getInstance('not-found');
	}

	/**
	 * Test JController::getModel
	 */
	public function testGetModel()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * Test JController::getName
	 */
	public function testGetName()
	{
		$this->assertThat(
			$this->object->getName(),
			$this->equalTo('j')
		);

		$this->object->name = 'inspector';

		$this->assertThat(
			$this->object->getName(),
			$this->equalTo('inspector')
		);
	}

	/**
	 * Test JController::getTask().
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function testGetTask()
	{
		$this->assertThat(
			$this->object->get('task'),
			$this->equalTo(null)
		);

		$this->object->set('task', 'test');

		$this->assertThat(
			$this->object->get('task'),
			$this->equalTo('test')
		);
	}

	/**
	 * Test JController::getTasks
	 */
	public function testGetTasks()
	{
		$controller = new TestController;

		$this->assertThat(
			$controller->getTasks(),
			$this->equalTo(
				array(
					'task1', 'task2', 'display'
				)
			),
			'Line:' . __LINE__ . ' The available tasks should be the public tasks in the derived controller plus "display".'
		);
	}

	/**
	 * Test JController::getView
	 */
	public function testGetView()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * Test JController::redirect
	 */
	public function testRedirect()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * Test JController::registerDefaultTask
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function testRegisterDefaultTask()
	{
		$controller = new TestController;

		$controller->registerDefaultTask('task1');
		$this->assertEquals(
			'task1',
			$controller->taskMap['__default'],
			'Line:' . __LINE__ . ' The task has not been registered.'
		);

		$controller->registerDefaultTask('task3');
		$this->assertNotEquals(
			'task3',
			$controller->taskMap['__default'],
			'Line:' . __LINE__ . ' The task has been registered.'
		);
	}

	/**
	 * Test JController::registerTask
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function testRegisterTask()
	{
		$controller = new JControllerInspector;

		$controller->registerTask('task1', 'display');
		$this->assertArrayHasKey(
			'task1',
			$controller->taskMap,
			'Line:' . __LINE__ . ' The task has not been registered.'
		);

		$controller->registerTask('task2', 'unknown');
		$this->assertThat(
			$controller->taskMap,
			$this->logicalNot(
				$this->arrayHasKey('task2')
          	),
			'Line:' . __LINE__ . ' The task has been registered.'
        );
	}

	/**
	 * Test JController::setAccessControl
	 */
	public function testSetAccessControl()
	{
		$this->markTestSkipped('This method is deprecated.');
	}

	/**
	 * Test JController::setMessage
	 */
	public function testSetMessage()
	{
		$controller = new JControllerInspector;
		$controller->setMessage('Hello World');

		$this->assertEquals($controller->message, 'Hello World',
							'Line:' . __LINE__ . ' The message text does not equal with previuosly set one'
		);

		$this->assertEquals($controller->messageType, 'message',
							'Line:' . __LINE__ . ' Default message type should be "message"'
		);

		$controller->setMessage('Morning Universe', 'notice');

		$this->assertEquals($controller->message, 'Morning Universe',
							'Line:' . __LINE__ . ' The message text does not equal with previuosly set one'
		);

		$this->assertEquals($controller->messageType, 'notice',
							'Line:' . __LINE__ . ' The message type does not equal with previuosly set one'
		);
	}

	/**
	 * Test JController::setPath
	 */
	public function testSetPath()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * Test JController::setRedirect
	 */
	public function testSetRedirect()
	{
		$controller = new JControllerInspector;

		// Set the URL only
		$controller->setRedirect('index.php?option=com_foobar');

		$this->assertEquals(
			$controller->redirect,
			'index.php?option=com_foobar',
			'Line:' . __LINE__ . ' The redirect address does not equal with passed one'
		);

		$this->assertNull(
			$controller->message,
			'Line:' . __LINE__ . ' The message is not set, so it should be null'
		);

		$this->assertEquals(
			$controller->messageType,
			'message',
			'Line:' . __LINE__ . ' Default message type should be "message"'
		);

		// Set the URL and message
		$controller->setRedirect('index.php?option=com_foobar', 'Hello World');

		$this->assertEquals(
			$controller->redirect,
			'index.php?option=com_foobar',
			'Line:' . __LINE__ . ' The redirect address does not equal with passed one'
		);

		$this->assertEquals(
			$controller->message,
			'Hello World',
			'Line:' . __LINE__ . ' The message text does not equal with passed one'
		);

		$this->assertEquals(
			$controller->messageType,
			'message',
			'Line:' . __LINE__ . ' Default message type should be "message"'
		);

		// URL, message and message type
		$controller->setRedirect('index.php?option=com_foobar', 'Morning Universe', 'notice');

		$this->assertEquals(
			$controller->redirect,
			'index.php?option=com_foobar',
			'Line:' . __LINE__ . ' The redirect address does not equal with passed one'
		);

		$this->assertEquals(
			$controller->message,
			'Morning Universe',
			'Line:' . __LINE__ . ' The message text does not equal with passed one'
		);

		$this->assertEquals(
			$controller->messageType,
			'notice',
			'Line:' . __LINE__ . ' The message type does not equal with passed one'
		);

		// With previously set message
		// URL
		$controller->setMessage('Hi all');
		$controller->setRedirect('index.php?option=com_foobar');

		$this->assertEquals(
			$controller->redirect,
			'index.php?option=com_foobar',
			'Line:' . __LINE__ . ' The redirect address does not equal with passed one'
		);

		$this->assertEquals(
			$controller->message,
			'Hi all',
			'Line:' . __LINE__ . ' The message text does not equal with previously set one'
		);

		$this->assertEquals(
			$controller->messageType,
			'message',
			'Line:' . __LINE__ . ' Default message type should be "message"'
		);

		// URL and message
		$controller->setMessage('Hi all');
		$controller->setRedirect('index.php?option=com_foobar', 'Bye all');

		$this->assertEquals(
			$controller->redirect,
			'index.php?option=com_foobar',
			'Line:' . __LINE__ . ' The redirect address does not equal with passed one'
		);

		$this->assertEquals(
			$controller->message,
			'Bye all',
			'Line:' . __LINE__ . ' The message text should be overridden'
		);

		$this->assertEquals(
			$controller->messageType,
			'message',
			'Line:' . __LINE__ . ' Default message type should be "message"'
		);

		// URL, message and message type
		$controller->setMessage('Hi all');
		$controller->setRedirect('index.php?option=com_foobar', 'Bye all', 'notice');

		$this->assertEquals(
			$controller->redirect,
			'index.php?option=com_foobar',
			'Line:' . __LINE__ . ' The redirect address does not equal with passed one'
		);

		$this->assertEquals(
			$controller->message,
			'Bye all',
			'Line:' . __LINE__ . ' The message text should be overridden'
		);

		$this->assertEquals(
			$controller->messageType,
			'notice',
			'Line:' . __LINE__ . ' The message type should be overridden'
		);

		// URL and message type
		$controller->setMessage('Hi all');
		$controller->setRedirect('index.php?option=com_foobar', null, 'notice');

		$this->assertEquals(
			$controller->redirect,
			'index.php?option=com_foobar',
			'Line:' . __LINE__ . ' The redirect address does not equal with passed one'
		);

		$this->assertEquals(
			$controller->message,
			'Hi all',
			'Line:' . __LINE__ . ' The message text should not be overridden'
		);

		$this->assertEquals(
			$controller->messageType,
			'notice',
			'Line:' . __LINE__ . ' The message type should be overridden'
		);

		// With previously set message and message type
		// URL
		$controller->setMessage('Hello folks', 'notice');
		$controller->setRedirect('index.php?option=com_foobar');

		$this->assertEquals(
			$controller->redirect,
			'index.php?option=com_foobar',
			'Line:' . __LINE__ . ' The redirect address does not equal with passed one'
		);

		$this->assertEquals(
			$controller->message,
			'Hello folks',
			'Line:' . __LINE__ . ' The message text does not equal with previously set one'
		);

		$this->assertEquals(
			$controller->messageType,
			'notice',
			'Line:' . __LINE__ . ' The message type does not equal with previously set one'
		);

		// URL and message
		$controller->setMessage('Hello folks', 'notice');
		$controller->setRedirect('index.php?option=com_foobar', 'Bye, Folks');

		$this->assertEquals(
		$controller->redirect,
			'index.php?option=com_foobar',
			'Line:' . __LINE__ . ' The redirect address does not equal with passed one'
		);

		$this->assertEquals(
			$controller->message,
			'Bye, Folks',
			'Line:' . __LINE__ . ' The message text should be overridden'
		);

		$this->assertEquals(
			$controller->messageType,
			'notice',
			'Line:' . __LINE__ . ' The message type does not equal with previously set one'
		);

		// URL, message and message type
		$controller->setMessage('Hello folks', 'notice');
		$controller->setRedirect('index.php?option=com_foobar', 'Bye, folks', 'notice');

		$this->assertEquals(
			$controller->redirect,
			'index.php?option=com_foobar',
			'Line:' . __LINE__ . ' The redirect address does not equal with passed one'
		);

		$this->assertEquals(
			$controller->message,
			'Bye, folks',
			'Line:' . __LINE__ . ' The message text should be overridden'
		);

		$this->assertEquals(
			$controller->messageType,
			'notice',
			'Line:' . __LINE__ . ' The message type should be overridden'
		);

		// URL and message type
		$controller->setMessage('Folks?', 'notice');
		$controller->setRedirect('index.php?option=com_foobar', null, 'question');

		$this->assertEquals(
			$controller->redirect,
			'index.php?option=com_foobar',
			'Line:' . __LINE__ . ' The redirect address does not equal with passed one'
		);

		$this->assertEquals(
			$controller->message,
			'Folks?',
			'Line:' . __LINE__ . ' The message text should not be overridden'
		);

		$this->assertEquals(
			$controller->messageType,
			'question',
			'Line:' . __LINE__ . ' The message type should be overridden'
		);
	}
}
