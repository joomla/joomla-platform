<?php
/**
 * @package    Joomla.Test
 *
 * @copyright  Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

/**
 * Abstract test case class for unit testing.
 *
 * @package  Joomla.Test
 * @since    12.1
 */
abstract class TestCase extends PHPUnit_Framework_TestCase
{
	/**
	 * @var         array  The list of errors expected to be encountered during the test.
	 * @deprecated  13.1
	 * @since       12.1
	 */
	protected $expectedErrors;

	/**
	 * @var         array  JError handler state stashed away to be restored later.
	 * @deprecated  13.1
	 * @since       12.1
	 */
	private $_stashedErrorState = array();

	/**
	 * @var    array  Various JFactory static instances stashed away to be restored later.
	 * @since  12.1
	 */
	private $_stashedFactoryState = array(
		'application' => null,
		'config' => null,
		'dates' => null,
		'session' => null,
		'language' => null,
		'document' => null,
		'acl' => null,
		'mailer' => null
	);

	/**
	 * Receives the callback from JError and logs the required error information for the test.
	 *
	 * @param   JException	$error  The JException object from JError
	 *
	 * @return  boolean  To not continue with JError processing
	 *
	 * @deprecated  13.1
	 * @since       12.1
	 */
	public static function errorCallback($error)
	{
		return false;
	}

	/**
	 * Assigns mock callbacks to methods.
	 *
	 * @param   object  $mockObject  The mock object that the callbacks are being assigned to.
	 * @param   array   $array       An array of methods names to mock with callbacks.
	 * This method assumes that the mock callback is named {mock}{method name}.
	 *
	 * @return  void
	 *
	 * @since   12.1
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
	 * Assigns mock values to methods.
	 *
	 * @param   object  $mockObject  The mock object.
	 * @param   array   $array       An associative array of methods to mock with return values:<br />
	 * string (method name) => mixed (return value)
	 *
	 * @return  void
	 *
	 * @since   12.1
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
	 * Callback receives the error from JError and deals with it appropriately
	 * If a test expects a JError to be raised, it should call this setExpectedError first
	 * If you don't call this method first, the test will fail.
	 *
	 * @param   JException  $error
	 *
	 * @return  JException
	 *
	 * @deprecated  13.1
	 * @since       12.1
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
	 * Gets a mock application object.
	 *
	 * @return  JApplication
	 *
	 * @since   12.1
	 */
	public function getMockApplication()
	{
		// Attempt to load the real class first.
		class_exists('JApplication');

		return TestMockApplication::create($this);
	}

	/**
	 * Gets a mock configuration object.
	 *
	 * @return  JConfig
	 *
	 * @since   12.1
	 */
	public function getMockConfig()
	{
		return TestMockConfig::create($this);
	}

	/**
	 * Gets a mock database object.
	 *
	 * @return  JDatabase
	 *
	 * @since   12.1
	 */
	public function getMockDatabase()
	{
		// Attempt to load the real class first.
		class_exists('JDatabaseDriver');

		return TestMockDatabaseDriver::create($this);
	}

	/**
	 * Gets a mock dispatcher object.
	 *
	 * @param   boolean  $defaults  Add default register and trigger methods for testing.
	 *
	 * @return  JDispatcher
	 *
	 * @since   12.1
	 */
	public function getMockDispatcher($defaults = true)
	{
		// Attempt to load the real class first.
		class_exists('JDispatcher');

		return TestMockDispatcher::create($this, $defaults);
	}

	/**
	 * Gets a mock document object.
	 *
	 * @return  JDocument
	 *
	 * @since   12.1
	 */
	public function getMockDocument()
	{
		// Attempt to load the real class first.
		class_exists('JDocument');

		return TestMockDocument::create($this);
	}

	/**
	 * Gets a mock language object.
	 *
	 * @return  JLanguage
	 *
	 * @since   12.1
	 */
	public function getMockLanguage()
	{
		// Attempt to load the real class first.
		class_exists('JLanguage');

		return TestMockLanguage::create($this);
	}

	/**
	 * Gets a mock session object.
	 *
	 * @param   array  $options  An array of key-value options for the JSession mock.
	 * getId : the value to be returned by the mock getId method
	 * get.user.id : the value to assign to the user object id returned by get('user')
	 * get.user.name : the value to assign to the user object name returned by get('user')
	 * get.user.username : the value to assign to the user object username returned by get('user')
	 *
	 * @return  JSession
	 *
	 * @since   12.1
	 */
	public function getMockSession($options = array())
	{
		// Attempt to load the real class first.
		class_exists('JSession');

		return TestMockSession::create($this, $options);
	}

	/**
	 * Gets a mock web object.
	 *
	 * @param   array  $options  A set of options to configure the mock.
	 *
	 * @return  JApplicationWeb
	 *
	 * @since   12.1
	 */
	public function getMockWeb($options = array())
	{
		// Attempt to load the real class first.
		class_exists('JApplicationWeb');

		return TestMockApplicationWeb::create($this, $options);
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
	 * @deprecated  13.1
	 * @since       12.1
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
	 * Sets the JError error handlers.
	 *
	 * @return  void
	 *
	 * @deprecated  13.1
	 * @since       12.1
	 */
	protected function restoreErrorHandlers()
	{
		$this->setErrorhandlers($this->_stashedErrorState);
	}

	/**
	 * Sets the Factory pointers
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	protected function restoreFactoryState()
	{
		JFactory::$application = $this->_stashedFactoryState['application'];
		JFactory::$config = $this->_stashedFactoryState['config'];
		JFactory::$dates = $this->_stashedFactoryState['dates'];
		JFactory::$session = $this->_stashedFactoryState['session'];
		JFactory::$language = $this->_stashedFactoryState['language'];
		JFactory::$document = $this->_stashedFactoryState['document'];
		JFactory::$acl = $this->_stashedFactoryState['acl'];
		JFactory::$mailer = $this->_stashedFactoryState['mailer'];
	}

	/**
	 * Saves the current state of the JError error handlers.
	 *
	 * @return  void
	 *
	 * @deprecated  13.1
	 * @since       12.1
	 */
	protected function saveErrorHandlers()
	{
		$this->_stashedErrorState = array();
		$this->_stashedErrorState[E_NOTICE] = JError::getErrorHandling(E_NOTICE);
		$this->_stashedErrorState[E_WARNING] = JError::getErrorHandling(E_WARNING);
		$this->_stashedErrorState[E_ERROR] = JError::getErrorHandling(E_ERROR);
	}

	/**
	 * Saves the Factory pointers
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	protected function saveFactoryState()
	{
		$this->_stashedFactoryState['application'] = JFactory::$application;
		$this->_stashedFactoryState['config'] = JFactory::$config;
		$this->_stashedFactoryState['dates'] = JFactory::$dates;
		$this->_stashedFactoryState['session'] = JFactory::$session;
		$this->_stashedFactoryState['language'] = JFactory::$language;
		$this->_stashedFactoryState['document'] = JFactory::$document;
		$this->_stashedFactoryState['acl'] = JFactory::$acl;
		$this->_stashedFactoryState['mailer'] = JFactory::$mailer;
	}

	/**
	 * Sets the JError error handlers.
	 *
	 * @param   array  $errorHandlers  araay of values and options to set the handlers
	 *
	 * @return  void
	 *
	 * @since   12.1
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
	 * Sets the JError error handlers to callback mode and points them at the test
	 * logging method.
	 *
	 * @return	void
	 *
	 * @since   12.1
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
		$this->setExpectedError();

		parent::setUp();
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
}
