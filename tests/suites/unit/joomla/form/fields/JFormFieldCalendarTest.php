<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * Test class for JForm.
 *
 * @package     Joomla.UnitTest
 * @subpackage  Form
 *
 * @since       11.1
 */
class JFormFieldCalendarTest extends TestCase
{
	/**
	 * Sets up dependancies for the test.
	 *
	 * @return void
	 */
	protected function setUp()
	{
		parent::setUp();

		require_once JPATH_PLATFORM . '/joomla/form/fields/calendar.php';
		include_once dirname(__DIR__) . '/inspectors.php';
		$this->saveFactoryState();
	}

	/**
	 * Test...
	 *
	 * @return void
	 */
	protected function tearDown()
	{
		$this->restoreFactoryState();
	}

	/**
	 * Test...
	 *
	 * @return array
	 */
	public function attributeData()
	{

		return array(
			/*
			* Test normal parameters
			*/
			'normal' => array(
				'myCalendarElement',
				'myCalendarId',
				'myValue',
				array(
					'format' => '%m-%Y-%d',
					'size' => '25',
					'maxlength' => '45',
					'class' => 'myClass',
					'readonly' => 'true',
					'disabled' => 'false',
					'onchange' => '',
					'filter' => ''
				),
				array(
					'myValue',
					'myCalendarElement',
					'myCalendarId',
					'%m-%Y-%d',
					array(
						'size' => 25,
						'maxlength' => 45,
						'class' => 'myClass',
						'readonly' => true,
						'disabled' => false,
						'onchange' => ''
					)
				)
			),

			/*
			* Non integer size
			*/
			'nonintsize' => array(
				'myCalendarElement',
				'myCalendarId',
				'myValue',
				array(
					'format' => '%m-%Y-%d',
					'size' => '25est',
					'maxlength' => 'forty five',
					'class' => 'myClass',
					'readonly' => 'true',
					'disabled' => 'false',
					'onchange' => '',
					'filter' => ''
				),
				array(
					'myValue',
					'myCalendarElement',
					'myCalendarId',
					'%m-%Y-%d',
					array(
						'size' => 25,
						'maxlength' => 0,
						'class' => 'myClass',
						'readonly' => true,
						'disabled' => false,
						'onchange' => ''
					)
				)
			),

			/*
			* No format provided
			*/
			'noformat' => array(
				'myCalendarElement',
				'myCalendarId',
				'myValue',
				array(
					'format' => '',
					'size' => '25est',
					'maxlength' => 'forty five',
					'class' => 'myClass',
					'readonly' => 'true',
					'disabled' => 'false',
					'onchange' => '',
					'filter' => ''
				),
				array(
					'myValue',
					'myCalendarElement',
					'myCalendarId',
					'%Y-%m-%d',
					array(
						'size' => 25,
						'maxlength' => 0,
						'class' => 'myClass',
						'readonly' => true,
						'disabled' => false,
						'onchange' => ''
					)
				)
			),

			/*
			* With an onchange value
			*/
			'onchange' => array(
				'myCalendarElement',
				'myCalendarId',
				'myValue',
				array(
					'format' => '',
					'size' => '25est',
					'maxlength' => 'forty five',
					'class' => 'myClass',
					'readonly' => 'true',
					'disabled' => 'false',
					'onchange' => 'This is my onchange value',
					'filter' => ''
				),
				array(
					'myValue',
					'myCalendarElement',
					'myCalendarId',
					'%Y-%m-%d',
					array(
						'size' => 25,
						'maxlength' => 0,
						'class' => 'myClass',
						'readonly' => true,
						'disabled' => false,
						'onchange' => 'This is my onchange value'
					)
				)
			),

			/*
			* With bad readonly value
			*/
			'bad_readonly' => array(
				'myCalendarElement',
				'myCalendarId',
				'myValue',
				array(
					'format' => '',
					'size' => '25est',
					'maxlength' => 'forty five',
					'class' => 'myClass',
					'readonly' => '1',
					'disabled' => 'false',
					'onchange' => 'This is my onchange value',
					'filter' => ''
				),
				array(
					'myValue',
					'myCalendarElement',
					'myCalendarId',
					'%Y-%m-%d',
					array(
						'size' => 25,
						'maxlength' => 0,
						'class' => 'myClass',
						'readonly' => true,
						'disabled' => false,
						'onchange' => 'This is my onchange value'
					)
				)
			),

			/*
			* disabled is true, no class
			*/
			'disabled_no_class' => array(
				'myCalendarElement',
				'myCalendarId',
				'myValue',
				array(
					'format' => '',
					'size' => '25est',
					'maxlength' => 'forty five',
					'class' => '',
					'readonly' => '1',
					'disabled' => 'true',
					'onchange' => 'This is my onchange value',
					'filter' => ''
				),
				array(
					'myValue',
					'myCalendarElement',
					'myCalendarId',
					'%Y-%m-%d',
					array(
						'size' => 25,
						'maxlength' => 0,
						'class' => '',
						'readonly' => true,
						'disabled' => true,
						'onchange' => 'This is my onchange value'
					)
				)
			),

			/*
			* value = 'NOW'
			*/
			'value_is_now' => array(
				'myCalendarElement',
				'myCalendarId',
				'NOW',
				array(
					'format' => '',
					'size' => '25est',
					'maxlength' => 'forty five',
					'class' => '',
					'readonly' => '1',
					'disabled' => 'true',
					'onchange' => 'This is my onchange value',
					'filter' => ''
				),
				array(
					'strftime(\'%Y-%m-%d\')',
					'myCalendarElement',
					'myCalendarId',
					'%Y-%m-%d',
					array(
						'size' => 25,
						'maxlength' => 0,
						'class' => '',
						'readonly' => true,
						'disabled' => true,
						'onchange' => 'This is my onchange value'
					)
				)
			)
		);
	}

	/**
	 * Convert an array into XML for use with simplexml_load_string
	 *
	 * @param   array  $array  The array to convert to an XML string
	 *
	 * @return  object  SimpleXMLElement
	 *
	 * @since   12.3
	 */
	protected function arrayToFieldXml(array $array)
	{
		$xml = '<field ';

		foreach ($array as $key => $value)
		{
			$xml .= $key . '="' . $value . '" ';
		}

		$xml .= '/>';

		return simplexml_load_string($xml);
	}

	/**
	 * Tests various attribute methods - this method does not handle filters
	 *
	 * @param   string   $name                @todo
	 * @param   integer  $id                  @todo
	 * @param   mixed    $value               @todo
	 * @param   mixed    $element             @todo
	 * @param   string   $expectedParameters  @todo
	 *
	 * @dataProvider attributeData
	 *
	 * @return void
	 */
	public function testGetInputAttributes($name, $id, $value, $element, $expectedParameters)
	{
		// We create stubs for config and session/user objects
		$config = new stdClass;

		// Put the stub in place
		JFactory::$config = $config;
		$sessionMock = $this->getMock('sessionMock', array('get'));

		require_once JPATH_PLATFORM . '/joomla/user/user.php';
		$userObject = new JUser;

		$sessionMock->expects($this->any())
			->method('get')
			->with('user')
			->will($this->returnValue($userObject));

		// Put the stub in place
		JFactory::$session = $sessionMock;

		// Include our inspector which will allow us to manipulate and call protected methods and attributes
		require_once __DIR__ . '/inspectors/JFormFieldCalendar.php';
		$calendar = new JFormFieldCalendarInspector;

		if ($expectedParameters[0] == 'strftime(\'%Y-%m-%d\')')
		{
			date_default_timezone_set('UTC');
			$expectedParameters[0] = strftime('%Y-%m-%d');
		}

		// Transform array $element into SimpleXMLElement for the setup method
		if (is_array($element))
		{
			$element = $this->arrayToFieldXml($element);
		}

		// Setup our values from our data set
		$calendar->setup($element, $value);
		$calendar->setProtectedProperty('name', $name);
		$calendar->setProtectedProperty('id', $id);

		// Create the mock to implant into JHtml so that we can check our values
		$mock = $this->getMock('calendarHandler', array('calendar'));

		// Setup the expectation with the values from the dataset
		$mock->expects($this->once())
			->method('calendar')
			->with($expectedParameters[0], $expectedParameters[1], $expectedParameters[2], $expectedParameters[3], $expectedParameters[4]);

		// Register our mock with JHtml
		JHtml::register('calendar', array($mock, 'calendar'));

		// Invoke our method
		$calendar->getInput();

		// Unregister the mock
		JHtml::unregister('jhtml..calendar');
	}

	/**
	 * Test...
	 *
	 * @return void
	 */
	public function testGetInputServer_UTC()
	{
		// We create stubs for config and session/user objects
		$config = new JObject;

		// Put the stub in place
		JFactory::$config = $config;
		$sessionMock = $this->getMock('sessionMock', array('get'));

		require_once JPATH_PLATFORM . '/joomla/user/user.php';
		$userObject = new JUser;

		$sessionMock->expects($this->any())
			->method('get')
			->with('user')
			->will($this->returnValue($userObject));

		// Put the stub in place
		JFactory::$session = $sessionMock;

		$languageMock = $this->getMock('languageMock', array('getTag'));
		$languageMock->expects($this->any())
			->method('getTag')
			->will($this->returnValue('en-GB'));

		// Put the stub in place
		JFactory::$language = $languageMock;

		// Include our inspector which will allow us to manipulate and call protected methods and attributes
		require_once __DIR__ . '/inspectors/JFormFieldCalendar.php';
		$calendar = new JFormFieldCalendarInspector;

		// Setup our values from our data set
		$element = simplexml_load_string('<field
			format="%m-%Y-%d"
			size="25"
			maxlength="45"
			class="myClass"
			readonly="true"
			disabled="false"
			onchange=""
			filter="SERVER_UTC"
		/>');
		$calendar->setup($element, 1269442718);
		$calendar->setProtectedProperty('name', 'myElementName');
		$calendar->setProtectedProperty('id', 'myElementId');

		// -5
		$config->set('offset', 'US/Eastern');

		// -3
		$userObject->setParam('timezone', 'America/Buenos_Aires');

		// Create the mock to implant into JHtml so that we can check our values
		$mock = $this->getMock('calendarHandler', array('calendar'));

		// Setup the expectation with the values from the dataset
		$mock->expects($this->once())
			->method('calendar')
			->with('2010-03-24 10:58:38', 'myElementName', 'myElementId', '%m-%Y-%d',
			array(
				'size' => 25,
				'maxlength' => 45,
				'class' => 'myClass',
				'readonly' => true,
				'disabled' => false,
				'onchange' => ''
			)
		);

		// Register our mock with JHtml
		JHtml::register('calendar', array($mock, 'calendar'));

		// Invoke our method
		$calendar->getInput();

		// Unregister the mock
		JHtml::unregister('jhtml..calendar');
	}

	/**
	 * Test...
	 *
	 * @return void
	 */
	public function testGetInputUser_UTC()
	{
		// We create stubs for config and session/user objects
		$config = new JObject;

		// Put the stub in place
		JFactory::$config = $config;
		$sessionMock = $this->getMock('sessionMock', array('get'));

		require_once JPATH_PLATFORM . '/joomla/user/user.php';
		$userObject = new JUser;

		$sessionMock->expects($this->any())
			->method('get')
			->with('user')
			->will($this->returnValue($userObject));

		// Put the stub in place
		JFactory::$session = $sessionMock;

		$languageMock = $this->getMock('languageMock', array('getTag'));
		$languageMock->expects($this->any())
			->method('getTag')
			->will($this->returnValue('en-GB'));

		// Put the stub in place
		JFactory::$language = $languageMock;

		// Include our inspector which will allow us to manipulate and call protected methods and attributes
		require_once __DIR__ . '/inspectors/JFormFieldCalendar.php';
		$calendar = new JFormFieldCalendarInspector;

		// Setup our values from our data set
		$element = simplexml_load_string('<field
			format="%m-%Y-%d"
			size="25"
			maxlength="45"
			class="myClass"
			readonly="true"
			disabled="false"
			onchange=""
			filter="USER_UTC"
		/>');
		$calendar->setup($element, 1269442718);
		$calendar->setProtectedProperty('name', 'myElementName');
		$calendar->setProtectedProperty('id', 'myElementId');

		// +4
		$config->set('offset', 'Asia/Muscat');

		// We don't set the user param to see if it properly falls back to the server time (as it should)

		// Create the mock to implant into JHtml so that we can check our values
		$mock = $this->getMock('calendarHandler', array('calendar'));

		// Setup the expectation with the values from the dataset
		$mock->expects($this->once())
			->method('calendar')
			->with('2010-03-24 18:58:38', 'myElementName', 'myElementId', '%m-%Y-%d',
			array(
				'size' => 25,
				'maxlength' => 45,
				'class' => 'myClass',
				'readonly' => true,
				'disabled' => false,
				'onchange' => ''
			)
		);

		// Register our mock with JHtml
		JHtml::register('calendar', array($mock, 'calendar'));

		// Invoke our method
		$calendar->getInput();

		// Unregister the mock
		JHtml::unregister('jhtml..calendar');

		// Create the mock to implant into JHtml so that we can check our values
		$mock2 = $this->getMock('calendarHandler', array('calendar'));

		// Setup the expectation with the values from the dataset
		$mock2->expects($this->once())
			->method('calendar')
			->with('2010-03-24 22:58:38', 'myElementName', 'myElementId', '%m-%Y-%d',
			array(
				'size' => 25,
				'maxlength' => 45,
				'class' => 'myClass',
				'readonly' => true,
				'disabled' => false,
				'onchange' => '',
			)
		);

		// -5
		$config->set('offset', 'US/Eastern');

		// +4		// now we set the user param to test it out.
		$userObject->setParam('timezone', 'Asia/Muscat');

		// Register our mock with JHtml
		JHtml::register('calendar', array($mock2, 'calendar'));

		// Invoke our method
		$calendar->getInput();

		// Unregister the mock
		JHtml::unregister('jhtml..calendar');
	}

	/**
	 * Test the getInput method.
	 *
	 * @return void
	 */
	public function testGetInput()
	{
		$form = new JFormInspector('form1');

		$this->assertThat(
			$form->load('<form><field name="calendar" type="calendar" /></form>'),
			$this->isTrue(),
			'Line:' . __LINE__ . ' XML string should load successfully.'
		);

		$field = new JFormFieldCalendar($form);

		$this->assertThat(
			$field->setup($form->getXml()->field, 'value'),
			$this->isTrue(),
			'Line:' . __LINE__ . ' The setup method should return true.'
		);

		$this->markTestIncomplete('Problems encountered in next assertion');

		$this->assertThat(
			strlen($field->input),
			$this->greaterThan(0),
			'Line:' . __LINE__ . ' The getInput method should return something without error.'
		);

		// TODO: Should check all the attributes have come in properly.
	}
}
