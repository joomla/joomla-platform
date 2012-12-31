<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Openstreetmap
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * Test class for JOpenstreetmapInfo.
 *
 * @package     Joomla.UnitTest
 * @subpackage  Openstreetmap
 *
 * @since       12.3
 */
class JOpenstreetmapUserTest extends TestCase
{
	/**
	 * @var    JRegistry  Options for the Openstreetmap object.
	 * @since  12.3
	 */
	protected $options;

	/**
	 * @var    JHttp  Mock client object.
	 * @since  12.3
	 */
	protected $client;

	/**
	 * @var    JInput The input object to use in retrieving GET/POST data.
	 * @since  12.3
	 */
	protected $input;

	/**
	 * @var    JOpenstreetmapUser Object under test.
	 * @since  12.3
	 */
	protected $object;

	/**
	 * @var    JOpenstreetmapOauth  Authentication object for the Openstreetmap object.
	 * @since  12.3
	 */
	protected $oauth;

	/**
	 * @var    string  Sample XML.
	 * @since  12.3
	 */
	protected $sampleXml = <<<XML
<?xml version='1.0'?>
<osm></osm>
XML;

	/**
	 * @var    string  Sample XML error message.
	* @since  12.3
	*/
	protected $errorString = <<<XML
<?xml version='1.0'?>
<osm>ERROR</osm>
XML;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	* This method is called before a test is executed.
	*
	* @access protected
	*
	* @return void
	*/
	protected function setUp()
	{
		$_SERVER['HTTP_HOST'] = 'example.com';
		$_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0';
		$_SERVER['REQUEST_URI'] = '/index.php';
		$_SERVER['SCRIPT_NAME'] = '/index.php';

		$key = "app_key";
		$secret = "app_secret";

		$access_token = array('key' => 'token_key', 'secret' => 'token_secret');

		$this->options = new JRegistry;
		$this->input = new JInput;
		$this->client = $this->getMock('JHttp', array('get', 'post', 'delete', 'put'));
		$this->oauth = new JOpenstreetmapOauth($this->options, $this->client, $this->input);
		$this->oauth->setToken($access_token);

		$this->object = new JOpenstreetmapUser($this->options, $this->client, $this->oauth);

		$this->options->set('consumer_key', $key);
		$this->options->set('consumer_secret', $secret);
		$this->options->set('sendheaders', true);
	}

	/**
	 * Tests the getDetails method
	 *
	 * @return  void
	 *
	 * @since   12.3
	 */
	public function testGetDetails()
	{
		$returnData = new stdClass;
		$returnData->code = 200;
		$returnData->body = $this->sampleXml;

		$path = 'user/details';

		$this->client->expects($this->once())
		->method('get')
		->with($path)
		->will($this->returnValue($returnData));

		$this->assertThat(
				$this->object->getDetails(),
				$this->equalTo($this->sampleXml)
		);
	}

	/**
	 * Tests the getDetails method - failure
	 *
	 * @return  void
	 *
	 * @since   12.3
	 * @expectedException DomainException
	 */
	public function testGetDetailsFailure()
	{
		$returnData = new stdClass;
		$returnData->code = 500;
		$returnData->body = $this->errorString;

		$path = 'user/details';

		$this->client->expects($this->once())
		->method('get')
		->with($path)
		->will($this->returnValue($returnData));

		$this->object->getDetails();
	}

	/**
	 * Tests the getPreferences method
	 *
	 * @return  void
	 *
	 * @since   12.3
	 */
	public function testGetPreferences()
	{

		$returnData = new stdClass;
		$returnData->code = 200;
		$returnData->body = $this->sampleXml;

		$path = 'user/preferences';

		$this->client->expects($this->once())
		->method('get')
		->with($path)
		->will($this->returnValue($returnData));

		$this->assertThat(
				$this->object->getPreferences(),
				$this->equalTo($this->sampleXml)
		);
	}

	/**
	 * Tests the getPreferences method - failure
	 *
	 * @return  void
	 *
	 * @since   12.3
	 * @expectedException DomainException
	 */
	public function testGetPreferencesFailure()
	{

		$returnData = new stdClass;
		$returnData->code = 500;
		$returnData->body = $this->errorString;

		$path = 'user/preferences';

		$this->client->expects($this->once())
		->method('get')
		->with($path)
		->will($this->returnValue($returnData));

		$this->object->getPreferences();
	}

	/**
	 * Tests the replacePreferences method
	 *
	 * @return  void
	 *
	 * @since   12.3
	 */
	public function testReplacePreferences()
	{

		$preferences = array("A" => "a");

		$returnData = new stdClass;
		$returnData->code = 200;
		$returnData->body = $this->sampleXml;

		$path = 'user/preferences';

		$this->client->expects($this->once())
		->method('put')
		->with($path)
		->will($this->returnValue($returnData));

		$this->assertThat(
				$this->object->replacePreferences($preferences),
				$this->equalTo($this->sampleXml)
		);
	}

	/**
	 * Tests the replacePreferences method - failure
	 *
	 * @return  void
	 *
	 * @since   12.3
	 * @expectedException DomainException
	 */
	public function testReplacePreferencesFailure()
	{

		$preferences = array("A" => "a");

		$returnData = new stdClass;
		$returnData->code = 500;
		$returnData->body = $this->errorString;

		$path = 'user/preferences';

		$this->client->expects($this->once())
		->method('put')
		->with($path)
		->will($this->returnValue($returnData));

		$this->object->replacePreferences($preferences);
	}

	/**
	 * Tests the changePreference method
	 *
	 * @return  void
	 *
	 * @since   12.3
	 */
	public function testChangePreference()
	{

		$key = "A";
		$preference = "a";

		$returnData = new stdClass;
		$returnData->code = 200;
		$returnData->body = $this->sampleXml;

		$path = 'user/preferences/' . $key;

		$this->client->expects($this->once())
		->method('put')
		->with($path)
		->will($this->returnValue($returnData));

		$this->assertThat(
				$this->object->changePreference($key, $preference),
				$this->equalTo($this->sampleXml)
		);
	}

	/**
	 * Tests the changePreference method - failure
	 *
	 * @return  void
	 *
	 * @since   12.3
	 * @expectedException DomainException
	 */
	public function testChangePreferenceFailure()
	{

		$key = "A";
		$preference = "a";

		$returnData = new stdClass;
		$returnData->code = 500;
		$returnData->body = $this->errorString;

		$path = 'user/preferences/' . $key;

		$this->client->expects($this->once())
		->method('put')
		->with($path)
		->will($this->returnValue($returnData));

		$this->object->changePreference($key, $preference);
	}
}
