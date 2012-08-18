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
	 * set up for testing
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
	 * Tests the JForm::addFieldPath method.
	 *
	 * This method is used to add additional lookup paths for field helpers.
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
	 * Tests the JForm::addFieldPath method.
	 *
	 * This method is used to add additional lookup paths for field helpers.
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
	 * Tests the JForm::addFieldPath method.
	 *
	 * This method is used to add additional lookup paths for field helpers.
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
	 * Tests the JForm::addFieldPath method.
	 *
	 * This method is used to add additional lookup paths for field helpers.
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
	 * Tests the JForm::addFieldPath method.
	 *
	 * This method is used to add additional lookup paths for field helpers.
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
	 * Tests the JForm::addFieldPath method.
	 *
	 * This method is used to add additional lookup paths for field helpers.
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
