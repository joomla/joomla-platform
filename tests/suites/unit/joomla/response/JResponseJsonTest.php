<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Response
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

require_once __DIR__ . '/stubs/mock.application.php';

/**
 * Test class for JResponseJson.
 *
 * @package     Joomla.UnitTest
 * @subpackage  JResponseJson
 * @since       12.2
 */
class JResponseJsonTest extends TestCase
{
	/**
	 * Set up for testing
	 *
	 * @return void
	 *
	 * @since  12.2
	 */
	public function setUp()
	{
		$this->app = JFactory::$application;
		JFactory::$application = null;
	}

	/**
	 * Tear down test
	 *
	 * @return void
	 *
	 * @since  12.2
	 */
	public function tearDown()
	{
		JFactory::$application = $this->app;
	}

	/**
	 * Tests a simple success response where only the JResponseJson
	 * class is instantiated and send
	 *
	 * @return void
	 *
	 * @since  12.2
	 */
	public function testSimpleSuccess()
	{
		ob_start();
		echo new JResponseJson;
		$output = ob_get_clean();

		$response = json_decode($output);

		$this->assertEquals(true, $response->success);
	}

	/**
	 * Tests a success response with data to send back
	 *
	 * @return void
	 *
	 * @since  12.2
	 */
	public function testSuccessWithData()
	{
		$data = new stdClass;
		$data->value 		= 5;
		$data->average	= 7.9;

		ob_start();
		echo new JResponseJson($data);
		$output = ob_get_clean();

		$response = json_decode($output);

		$this->assertEquals(true, $response->success);
		$this->assertEquals(5, $response->data->value);
		$this->assertEquals(7.9, $response->data->average);
	}

	/**
	 * Tests a response indicating an error where an exception
	 * is passed into the object in order to set 'success' to false.
	 *
	 * The message of the exception is automatically sent back in 'message'.
	 *
	 * @return void
	 *
	 * @since  12.2
	 */
	public function testFailureWithException()
	{
		ob_start();
		echo new JResponseJson(new Exception('This and that went wrong'));
		$output = ob_get_clean();

		$response = json_decode($output);

		$this->assertEquals(false, $response->success);
		$this->assertEquals('This and that went wrong', $response->message);
	}

	/**
	 * Tests a response indicating an error where the third argument
	 * is used to set 'success' to false and the second to set the message
	 *
	 * This way data can also be send back using the first argument.
	 *
	 * @return void
	 *
	 * @since  12.2
	 */
	public function testFailureWithData()
	{
		$data = new stdClass;
		$data->value		= 6;
		$data->average	= 8.9;

		ob_start();
		echo new JResponseJson($data, 'Something went wrong', true);
		$output = ob_get_clean();

		$response = json_decode($output);

		$this->assertEquals(false, $response->success);
		$this->assertEquals('Something went wrong', $response->message);
		$this->assertEquals(6, $response->data->value);
		$this->assertEquals(8.9, $response->data->average);
	}

	/**
	 * Tests a response indicating an error where more messages
	 * are sent back besides the main response message of the exception
	 *
	 * @return void
	 *
	 * @since  12.2
	 */
	public function testFailureWithMessages()
	{
		$app = new JApplicationResponseJsonMock;
		$app->enqueueMessage('This part was successful');
		$app->enqueueMessage('You should not do that', 'warning');
		JFactory::$application = $app;

		ob_start();
		echo new JResponseJson(new Exception('A major error occured'));
		$output = ob_get_clean();

		$response = json_decode($output);

		$this->assertEquals(false, $response->success);
		$this->assertEquals('A major error occured', $response->message);
		$this->assertEquals('This part was successful', $response->messages->message[0]);
		$this->assertEquals('You should not do that', $response->messages->warning[0]);
	}

	/**
	 * Tests a response indicating an error where messages
	 * of the message queue should be ignored
	 *
	 * Note: The third parameter $error will be ignored
	 * if an exception is used for indicating an error
	 *
	 * @return void
	 *
	 * @since  12.2
	 */
	public function testFailureWithIgnoreMessages()
	{
		$app = new JApplicationResponseJsonMock;
		$app->enqueueMessage('This part was successful');
		$app->enqueueMessage('You should not do that', 'warning');
		JFactory::$application = $app;

		ob_start();
		echo new JResponseJson(new Exception('A major error occured'), null, false, true);
		$output = ob_get_clean();

		$response = json_decode($output);

		$this->assertEquals(false, $response->success);
		$this->assertEquals('A major error occured', $response->message);
		$this->assertEquals(null, $response->messages);
	}

	/**
	 * Tests a simple success response where only the JResponseJson
	 * class is instantiated and send, but this time with additional messages
	 *
	 * @return void
	 *
	 * @since  12.2
	 */
	public function testSuccessWithMessages()
	{
		$app = new JApplicationResponseJsonMock;
		$app->enqueueMessage('This part was successful');
		$app->enqueueMessage('This one was also successful');
		JFactory::$application = $app;

		ob_start();
		echo new JResponseJson;
		$output = ob_get_clean();

		$response = json_decode($output);

		$this->assertEquals(true, $response->success);
		$this->assertEquals('This part was successful', $response->messages->message[0]);
		$this->assertEquals('This one was also successful', $response->messages->message[1]);
	}
}
