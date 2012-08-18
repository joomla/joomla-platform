<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Response
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * Test class for JResponseJson.
 *
 * @package			Joomla.UnitTest
 * @subpackage	JResponseJson
 */
class JResponseJsonTest extends TestCase
{
	/**
	 * Set up for testing
	 *
	 * @return void
	 */
	public function setUp()
	{
		$this->saveFactoryState();
	}

	/**
	 * Tear down test
	 *
	 * @return void
	 */
	function tearDown()
	{
		$this->restoreFactoryState();
	}

	/**
	 * Tests a simple success response where only the JResponseJson
	 * class is instantiated and send
	 *
	 * @return void
	 */
	public function testSimpleSuccess()
	{
		ob_start();
		echo new JResponseJson();
		$output = ob_get_clean();

		$response = json_decode($output);

		$this->assertEquals(true, $response->success);
	}

	/**
	 * Tests a success response with data to send back
	 *
	 * @return void
	 */
	public function testSuccessWithData()
	{
		$data = new stdClass();
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
	 */
	public function testFailureWithData()
	{
		$data = new stdClass();
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
	 */
	public function testFailureWithMessages()
	{
		require_once JPATH_PLATFORM.'/legacy/application/application.php';

		$app = new JApplication(array('session' => false));
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
	 * Tests a simple success response where only the JResponseJson
	 * class is instantiated and send, but this time with additional messages
	 *
	 * @return void
	 */
	public function testSuccessWithMessages()
	{
		require_once JPATH_PLATFORM.'/legacy/application/application.php';

		$app = new JApplication(array('session' => false));
		$app->enqueueMessage('This part was successful');
		$app->enqueueMessage('This one was also successful');
		JFactory::$application = $app;

		ob_start();
		echo new JResponseJson();
		$output = ob_get_clean();

		$response = json_decode($output);

		$this->assertEquals(true, $response->success);
		$this->assertEquals('This part was successful', $response->messages->message[0]);
		$this->assertEquals('This one was also successful', $response->messages->message[1]);
	}
}
