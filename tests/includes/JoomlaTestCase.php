<?php
/**
 * @package    Joomla.UnitTest
 *
 * @copyright  Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

/**
 * Test case class for Joomla Unit Testing
 *
 * @package  Joomla.UnitTest
 * @since    11.1
 */
abstract class JoomlaTestCase extends PHPUnit_Framework_TestCase
{
	/**
	 * @var    array  The saved factory state.
	 * @since  11.1
	 */
	protected $savedFactoryState = array(
		'application' => null,
		'config' => null,
		'dates' => null,
		'session' => null,
		'language' => null,
		'document' => null,
		'acl' => null,
		'database' => null,
		'mailer' => null
	);

	/**
	 * @var    array
	 * @since  11.1
	 */
	protected $savedErrorState;

	/**
	 * @var    actualError
	 * @since  11.1
	 */
	protected static $actualError;

	/**
	 * @var    errors
	 * @since  11.1
	 */
	protected $expectedErrors = null;

	/**
	 * Assigns mock values to methods.
	 *
	 * @param   object  $mockObject  The mock object.
	 * @param   array   $array       An associative array of methods to mock with return values:<br />
	 *                               string (method name) => mixed (return value)
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function assignMockReturns($mockObject, $array)
	{
		foreach ($array as $method => $return)
		{
			$mockObject->expects($this->any())
				->method($method)
				->will($this->returnValue($return));
		}
	}

	/**
	 * Assigns mock callbacks to methods.
	 *
	 * @param   object  $mockObject  The mock object that the callbacks are being assigned to.
	 * @param   array   $array       An array of methods names to mock with callbacks.
	 *                               This method assumes that the mock callback is named {mock}{method name}.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function assignMockCallbacks($mockObject, $array)
	{
		foreach ($array as $index => $method)
		{
			if (is_array($method))
			{
				$methodName = $index;
				$callback = $method;
			}
			else
			{
				$methodName = $method;
				$callback = array(get_called_class(), 'mock' . $method);
			}

			$mockObject->expects($this->any())
				->method($methodName)
				->will($this->returnCallback($callback));
		}
	}

	/**
	 * Saves the current state of the JError error handlers.
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	protected function saveErrorHandlers()
	{
		$this->savedErrorState = array();
		$this->savedErrorState[E_NOTICE] = JError::getErrorHandling(E_NOTICE);
		$this->savedErrorState[E_WARNING] = JError::getErrorHandling(E_WARNING);
		$this->savedErrorState[E_ERROR] = JError::getErrorHandling(E_ERROR);
	}

	/**
	 * Sets the JError error handlers.
	 *
	 * @param   array	array of values and options to set the handlers
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	protected function setErrorHandlers($errorHandlers)
	{
		$mode = null;
		$options = null;

		foreach ($errorHandlers as $type => $params)
		{
			$mode = $params['mode'];
			if (isset($params['options']))
			{
				JError::setErrorHandling($type, $mode, $params['options']);
			}
			else
			{
				JError::setErrorHandling($type, $mode);
			}
		}
	}

	/**
	 * Sets the JError error handlers to callback mode and points them at the test logging method.
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	protected function setErrorCallback($testName)
	{
		$callbackHandlers = array(
			E_NOTICE => array('mode' => 'callback', 'options' => array($testName, 'errorCallback')),
			E_WARNING => array('mode' => 'callback', 'options' => array($testName, 'errorCallback')),
			E_ERROR => array('mode' => 'callback', 'options' => array($testName, 'errorCallback'))
		);
		$this->setErrorHandlers($callbackHandlers);
	}

	/**
	 * Overrides the parent setup method.
	 *
	 * @return  void
	 *
	 * @see     PHPUnit_Framework_TestCase::setUp()
	 * @since   11.1
	 */
	protected function setUp()
	{
		parent::setUp();
		$this->setExpectedError();
	}

	/**
	 * Overrides the parent tearDown method.
	 *
	 * @return  void
	 *
	 * @see     PHPUnit_Framework_TestCase::tearDown()
	 * @since   11.1
	 */
	protected function tearDown()
	{
		if (is_array($this->expectedErrors) && !empty($this->expectedErrors))
		{
			$this->fail('An expected error was not raised.');
		}

		JError::setErrorHandling(E_NOTICE, 'ignore');
		JError::setErrorHandling(E_WARNING, 'ignore');
		JError::setErrorHandling(E_ERROR, 'ignore');

		parent::tearDown();
	}

	/**
	 * Receives the callback from JError and logs the required error information for the test.
	 *
	 * @param   Exception	$error  The Exception object from JError
	 *
	 * @return  boolean  To not continue with JError processing
	 *
	 * @since   11.1
	 */
	static function errorCallback($error)
	{
	}

	/**
	 * Callback receives the error from JError and deals with it appropriately
	 * If a test expects a JError to be raised, it should call this setExpectedError first
	 * If you don't call this method first, the test will fail.
	 *
	 * @param   unsure  $error
	 *
	 * @return  unsure
	 *
	 * @since   11.1
	 */
	public function expectedErrorCallback($error)
	{
		foreach ($this->expectedErrors as $key => $err)
		{
			$thisError = true;

			foreach ($err as $prop => $value)
			{
				if ($error->get($prop) !== $value)
				{
					$thisError = false;
				}
			}

			if ($thisError)
			{
				unset($this->expectedErrors[$key]);
				return $error;
			}

		}

		$this->fail('An unexpected error occurred - ' . $error->get('message'));

		return $error;
	}

	/**
	 * Tells the unit tests that a method or action you are about to attempt
	 * is expected to result in JError::raiseSomething being called.
	 *
	 * If you don't call this method first, the test will fail.
	 * If you call this method during your test and the error does not occur, then your test
	 * will also fail because we assume you were testing to see that an error did occur when it was
	 * supposed to.
	 *
	 * If passed without argument, the array is initialized if it hsn't been already
	 *
	 * @param   mixed  $error
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	public function setExpectedError($error = null)
	{
		if (!is_array($this->expectedErrors))
		{
			$this->expectedErrors = array();
			JError::setErrorHandling(E_NOTICE, 'callback', array($this, 'expectedErrorCallback'));
			JError::setErrorHandling(E_WARNING, 'callback', array($this, 'expectedErrorCallback'));
			JError::setErrorHandling(E_ERROR, 'callback', array($this, 'expectedErrorCallback'));
		}

		if (!is_null($error))
		{
			$this->expectedErrors[] = $error;
		}
	}

	/**
	 * Saves the Factory pointers
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	protected function saveFactoryState()
	{
		$this->savedFactoryState['application'] = JFactory::$application;
		$this->savedFactoryState['config'] = JFactory::$config;
		$this->savedFactoryState['dates'] = JFactory::$dates;
		$this->savedFactoryState['session'] = JFactory::$session;
		$this->savedFactoryState['language'] = JFactory::$language;
		$this->savedFactoryState['document'] = JFactory::$document;
		$this->savedFactoryState['acl'] = JFactory::$acl;
		$this->savedFactoryState['database'] = JFactory::$database;
		$this->savedFactoryState['mailer'] = JFactory::$mailer;
	}

	/**
	 * Sets the Factory pointers
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	protected function restoreFactoryState()
	{
		JFactory::$application = $this->savedFactoryState['application'];
		JFactory::$config = $this->savedFactoryState['config'];
		JFactory::$dates = $this->savedFactoryState['dates'];
		JFactory::$session = $this->savedFactoryState['session'];
		JFactory::$language = $this->savedFactoryState['language'];
		JFactory::$document = $this->savedFactoryState['document'];
		JFactory::$acl = $this->savedFactoryState['acl'];
		JFactory::$database = $this->savedFactoryState['database'];
		JFactory::$mailer = $this->savedFactoryState['mailer'];
	}

	/**
	 * Gets a mock application object.
	 *
	 * @return  JApplication
	 *
	 * @since   11.3
	 */
	public function getMockApplication()
	{
		// Load the real class first otherwise the mock will be used if jimport is called again.
		require_once JPATH_PLATFORM . '/joomla/application/application.php';

		// Load the mock class builder.
		require_once JPATH_TESTS . '/includes/mocks/JApplicationMock.php';

		return JApplicationGlobalMock::create($this);
	}

	/**
	 * Gets a mock configuration object.
	 *
	 * @return  JConfig
	 *
	 * @since   11.3
	 */
	public function getMockConfig()
	{
		// Load the mock class builder.
		require_once JPATH_TESTS . '/includes/mocks/JConfigMock.php';

		return JConfigGlobalMock::create($this);
	}

	/**
	 * Gets a mock database object.
	 *
	 * @return  JDatabase
	 *
	 * @since   11.3
	 */
	public function getMockDatabase()
	{
		// Load the real class first otherwise the mock will be used if jimport is called again.
		require_once JPATH_PLATFORM . '/joomla/database/database.php';

		// Load the mock class builder.
		require_once JPATH_TESTS . '/includes/mocks/JDatabaseMock.php';

		return JDatabaseGlobalMock::create($this);
	}

	/**
	 * Gets a mock dispatcher object.
	 *
	 * @param   boolean  $defaults  Add default register and trigger methods for testing.
	 *
	 * @return  JDispatcher
	 *
	 * @since   11.3
	 */
	public function getMockDispatcher($defaults = true)
	{
		// Load the real class first otherwise the mock will be used if jimport is called again.
		require_once JPATH_PLATFORM . '/joomla/event/dispatcher.php';

		// Load the mock class builder.
		require_once JPATH_TESTS . '/includes/mocks/JDispatcherMock.php';

		return JDispatcherGlobalMock::create($this, $defaults);
	}

	/**
	 * Gets a mock document object.
	 *
	 * @return  JDocument
	 *
	 * @since   11.3
	 */
	public function getMockDocument()
	{
		// Load the real class first otherwise the mock will be used if jimport is called again.
		require_once JPATH_PLATFORM . '/joomla/document/document.php';

		// Load the mock class builder.
		require_once JPATH_TESTS . '/includes/mocks/JDocumentMock.php';

		return JDocumentGlobalMock::create($this);
	}

	/**
	 * Gets a mock language object.
	 *
	 * @return  JLanguage
	 *
	 * @since   11.3
	 */
	public function getMockLanguage()
	{
		// Load the real class first otherwise the mock will be used if jimport is called again.
		require_once JPATH_PLATFORM . '/joomla/language/language.php';

		// Load the mock class builder.
		require_once JPATH_TESTS . '/includes/mocks/JLanguageMock.php';

		return JLanguageGlobalMock::create($this);
	}

	/**
	 * Gets a mock session object.
	 *
	 * @param   array  $options  An array of key-value options for the JSession mock.
	 *                           getId : the value to be returned by the mock getId method
	 *                           get.user.id : the value to assign to the user object id returned by get('user')
	 *                           get.user.name : the value to assign to the user object name returned by get('user')
	 *                           get.user.username : the value to assign to the user object username returned by get('user')
	 *
	 * @return  JSession
	 *
	 * @since   11.3
	 */
	public function getMockSession($options = array())
	{
		// Load the real class first otherwise the mock will be used if jimport is called again.
		require_once JPATH_PLATFORM . '/joomla/session/session.php';

		// Load the mock class builder.
		require_once JPATH_TESTS . '/includes/mocks/JSessionMock.php';

		return JSessionGlobalMock::create($this, $options);
	}

	/**
	 * Gets a mock web object.
	 *
	 * @return  JWeb
	 *
	 * @since   12.1
	 */
	public function getMockWeb()
	{
		// Load the real class first otherwise the mock will be used if jimport is called again.
		require_once JPATH_PLATFORM . '/joomla/application/web.php';

		// Load the mock class builder.
		require_once JPATH_TESTS . '/includes/mocks/JWebMock.php';

		return JWebGlobalMock::create($this);
	}
}
