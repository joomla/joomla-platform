<?php
/**
 * @package    Joomla.UnitTest
 *
 * @copyright  Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

require_once JPATH_PLATFORM . '/joomla/google/google.php';

/**
 * Test class for JGoogleAuthOauth2Test .
 *
 * @package     Joomla.UnitTest
 * @subpackage  Google
 * @since       12.3
 */
class JGoogleAuthOauth2Test extends PHPUnit_Framework_TestCase
{
	/**
	 * @var    JRegistry  Options for the JOAuth2Client object.
	 */
	protected $options;

	/**
	 * @var    JHttp  Mock client object.
	 */
	protected $http;

	/**
	 * @var    JInput  The input object to use in retrieving GET/POST data.
	 */
	protected $input;

	/**
	 * @var    JOAuth2Client  The OAuth client for sending requests to Google.
	 */
	protected $oauth;

	/**
	 * @var    JApplicationWeb  The application object to send HTTP headers for redirects.
	 */
	protected $application;

	/**
	 * @var    JGoogleAuthOauth2  Object under test.
	 */
	protected $object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 * @return void
	 */
	protected function setUp()
	{
		$_SERVER['HTTP_HOST'] = 'mydomain.com';
		$_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0';
		$_SERVER['REQUEST_URI'] = '/index.php';
		$_SERVER['SCRIPT_NAME'] = '/index.php';

		$this->options = new JRegistry;
		$this->http = $this->getMock('JHttp', array('head', 'get', 'delete', 'trace', 'post', 'put', 'patch'), array($this->options));
		$this->input = new JInput;
		$this->application = new JApplicationWebInspector;
		$this->oauth = new JOAuth2Client($this->options, $this->http, $this->input, $this->application);
		$this->object = new JGoogleAuthOauth2($this->options, $this->oauth);
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 * @return void
	 */
	protected function tearDown()
	{
	}

	/**
	 * Tests the auth method
	 *
	 * @group	JGoogle
	 * @return void
	 */
	public function testAuth()
	{
		$this->object->setOption('clientid', '01234567891011.apps.googleusercontent.com');
		$this->object->setOption('scope', array('https://www.googleapis.com/auth/adsense', 'https://www.googleapis.com/auth/calendar'));
		$this->object->setOption('redirecturi', 'http://localhost/oauth');
		$this->object->setOption('sendheaders', true);

		$this->object->authenticate();
		$this->assertEquals(0, $this->application->closed);

		$this->object->setOption('clientsecret', 'jeDs8rKw_jDJW8MMf-ff8ejs');
		$this->input->set('code', '4/wEr_dK8SDkjfpwmc98KejfiwJP-f4wm.kdowmnr82jvmeisjw94mKFIJE48mcEM');
		$this->http->expects($this->once())->method('post')->will($this->returnCallback('jsonGrantOauthCallback'));
		$result = $this->object->authenticate();
		$this->assertEquals('accessvalue', $result['access_token']);
		$this->assertEquals('refreshvalue', $result['refresh_token']);
		$this->assertEquals(3600, $result['expires_in']);
		$this->assertEquals(time(), $result['created'], null, 10);
	}

	/**
	 * Tests the isauth method
	 *
	 * @group	JGoogle
	 * @return void
	 */
	public function testIsAuth()
	{
		$this->assertFalse($this->object->isAuthenticated());

		$token['access_token'] = 'accessvalue';
		$token['refresh_token'] = 'refreshvalue';
		$token['created'] = time();
		$token['expires_in'] = 3600;
		$this->oauth->setToken($token);

		$this->assertTrue($this->object->isAuthenticated());

		$token['created'] = time() - 4000;
		$token['expires_in'] = 3600;
		$this->oauth->setToken($token);

		$this->assertFalse($this->object->isAuthenticated());
	}

	/**
	 * Tests the auth method
	 *
	 * @group	JGoogle
	 * @return void
	 */
	public function testQuery()
	{
		$token['access_token'] = 'accessvalue';
		$token['refresh_token'] = 'refreshvalue';
		$token['created'] = time() - 1800;
		$token['expires_in'] = 3600;
		$this->oauth->setToken($token);

		$this->http->expects($this->once())->method('get')->will($this->returnCallback('getOauthCallback'));
		$result = $this->object->query('https://www.googleapis.com/auth/calendar', array('param' => 'value'), array(), 'get');
		$this->assertEquals($result->body, 'Lorem ipsum dolor sit amet.');
		$this->assertEquals(200, $result->code);

		$this->http->expects($this->once())->method('post')->will($this->returnCallback('queryOauthCallback'));
		$result = $this->object->query('https://www.googleapis.com/auth/calendar', array('param' => 'value'), array(), 'post');
		$this->assertEquals($result->body, 'Lorem ipsum dolor sit amet.');
		$this->assertEquals(200, $result->code);
	}

	/**
	 * Tests the googlize method
	 *
	 * @group	JGoogle
	 * @return void
	 */
	public function testGooglize()
	{
		$this->assertEquals(null, $this->object->getOption('authurl'));
		$this->assertEquals(null, $this->object->getOption('tokenurl'));

		$token['access_token'] = 'accessvalue';
		$token['refresh_token'] = 'refreshvalue';
		$token['created'] = time() - 1800;
		$token['expires_in'] = 3600;
		$this->oauth->setToken($token);

		$this->http->expects($this->once())->method('get')->will($this->returnCallback('getOauthCallback'));
		$result = $this->object->query('https://www.googleapis.com/auth/calendar', array('param' => 'value'), array(), 'get');

		$this->assertEquals('https://accounts.google.com/o/oauth2/auth', $this->object->getOption('authurl'));
		$this->assertEquals('https://accounts.google.com/o/oauth2/token', $this->object->getOption('tokenurl'));
	}

	/**
	 * Tests the setOption method
	 *
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
	 *
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
