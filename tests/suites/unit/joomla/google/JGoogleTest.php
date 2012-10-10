<?php
/**
 * @package     Joomla.UnitTest
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

require_once JPATH_PLATFORM . '/joomla/google/google.php';

/**
 * Test class for JGoogle.
 */
class JGoogleTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var    JRegistry  Options for the JOauth2client object.
	 */
	protected $options;

	/**
	 * @var    JHttp  Mock client object.
	 */
	protected $client;

	/**
	 * @var    JInput  The input object to use in retrieving GET/POST data.
	 */
	protected $input;

	/**
	 * @var    JOauth2client  The OAuth client for sending requests to Google.
	 */
	protected $oauth;

	/**
	 * @var    JGoogleAuth  The authentication wrapper for sending requests to Google.
	 */
	protected $auth;

	/**
	 * @var    JGoogle  Object under test.
	 */
	protected $object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->options = new JRegistry;
		$this->client = $this->getMock('JHttpTransportStream', array('request'), array($this->options));
		$this->input = new JInput;
		$this->oauth = new JOauthOauth2client($this->options, $this->client, $this->input);
		$this->auth = new JGoogleAuthOauth2($this->options, $this->oauth);
		$this->object = new JGoogle($this->options, $this->auth);
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
	}

	/**
	 * Tests the magic __get method - data
	 * @group	JGoogle
	 * @return void
	 */
	public function test__GetData()
	{
		$this->options->set('clientid', '1075367716947.apps.googleusercontent.com');
		$this->options->set('redirecturi', 'http://j.aaronschmitz.com/web/calendar-test');
		$this->assertThat(
			$this->object->data('Picasa'),
			$this->isInstanceOf('JGoogleDataPicasa')
		);
	}

	/**
	 * Tests the magic __get method - embed
	 * @group	JGoogle
	 * @return void
	 */
	public function test__GetEmbed()
	{
		$this->assertThat(
			$this->object->embed('Maps'),
			$this->isInstanceOf('JGoogleEmbedMaps')
		);
	}

	/**
	 * Tests the setOption method
	 * @group	JGoogle
	 * @return void
	 */
	public function testSetOption()
	{
		$this->object->setOption('key', 'value');

		$this->assertThat(
			$this->options->get('key'),
			$this->equalTo('value')
		);
	}

	/**
	 * Tests the getOption method
	 * @group	JGoogle
	 * @return void
	 */
	public function testGetOption()
	{
		$this->options->set('key', 'value');

		$this->assertThat(
			$this->object->getOption('key'),
			$this->equalTo('value')
		);
	}
}
