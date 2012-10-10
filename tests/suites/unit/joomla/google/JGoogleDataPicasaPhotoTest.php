<?php
/**
 * @package    Joomla.UnitTest
 *
 * @copyright  Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

require_once JPATH_PLATFORM . '/joomla/google/data/picasa.php';

/**
 * Test class for JGoogleDataPicasa.
 *
 * @package     Joomla.UnitTest
 * @subpackage  Google
 * @since       12.2
 */
class JGoogleDataPicasaPhotoTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var    JRegistry  Options for the JOauthV2client object.
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
	 * @var    JOauthV2client  The OAuth client for sending requests to Google.
	 */
	protected $oauth;

	/**
	 * @var    JGoogleAuthOauth2  The Google OAuth client for sending requests.
	 */
	protected $auth;

	/**
	 * @var    string  The XML data for the album.
	 */
	protected $xml;

	/**
	 * @var    JGoogleDataPicasaPhoto  Object under test.
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
		$this->options = new JRegistry;
		$this->http = $this->getMock('JOauthHttp', array('head', 'get', 'delete', 'trace', 'post', 'put', 'patch'), array($this->options));
		$this->input = new JInput;
		$this->oauth = new JOauthV2client($this->options, $this->http, $this->input);
		$this->auth = new JGoogleAuthOauth2($this->options, $this->oauth);
        $this->xml = new SimpleXMLElement(JFile::read(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'photo.txt'));
		$this->object = new JGoogleDataPicasaPhoto($this->xml, $this->options, $this->auth);

		$this->object->setOption('clientid', '01234567891011.apps.googleusercontent.com');
		$this->object->setOption('clientsecret', 'jeDs8rKw_jDJW8MMf-ff8ejs');
		$this->object->setOption('redirecturi', 'http://localhost/oauth');

		$token['access_token'] = 'accessvalue';
		$token['refresh_token'] = 'refreshvalue';
		$token['created'] = time() - 1800;
		$token['expires_in'] = 3600;
		$this->oauth->setToken($token);
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
		$this->assertEquals($this->auth->auth(), $this->object->auth());
	}

	/**
	 * Tests the isauth method
	 *
	 * @group	JGoogle
	 * @return void
	 */
	public function testIsAuth()
	{
		$this->assertEquals($this->auth->isAuth(), $this->object->authenticated());
	}

	/**
	 * Tests the delete method
	 *
	 * @group	JGoogle
	 * @return void
	 */
	public function testDelete()
	{
		$this->http->expects($this->once())->method('delete')->will($this->returnCallback('emptyPicasaCallback'));
		$results = $this->object->delete();
		$this->assertEquals($results, true);
	}

	/**
	 * Tests the getLink method
	 *
	 * @group	JGoogle
	 * @return void
	 */
	public function testGetLink()
	{
		$link = $this->object->getLink();
		$this->assertEquals($link, 'https://picasaweb.google.com/data/entry/api/user/12345678901234567890/albumid/0123456789012345678/photoid/12345678901234567890');
		$link = $this->object->getLink('self');
		$this->assertEquals($link, 'https://picasaweb.google.com/data/entry/api/user/12345678901234567890/albumid/0123456789012345678/photoid/12345678901234567890');
	}

	/**
	 * Tests the getURL method
	 *
	 * @group	JGoogle
	 * @return void
	 */
	public function testGetURL()
	{
		$url = $this->object->getURL();
		$this->assertEquals($url, 'https://lh3.googleusercontent.com/-VQfLCrQyGuw/UAYBmwBJZ3I/AAAAAAAAF-k/8y_1iBPJcdQ/Photo2.jpg');
	}

	/**
	 * Tests the getThumbnails method
	 *
	 * @group	JGoogle
	 * @return void
	 */
	public function testGetThumbnails()
	{
		$thumbs = $this->object->getThumbnails();
        $valid[72] = array('url' => 'https://lh3.googleusercontent.com/sdgfdfgsdgf/werewr/aswertrt/vdfderer/s72/Photo2.jpg', 'w' => 72, 'h' => 54);
        $valid[144] = array('url' => 'https://lh3.googleusercontent.com/sdgfdfgsdgf/werewr/aswertrt/vdfderer/s144/Photo2.jpg', 'w' => 144, 'h' => 108);
        $valid[288] = array('url' => 'https://lh3.googleusercontent.com/sdgfdfgsdgf/werewr/aswertrt/vdfderer/s288/Photo2.jpg', 'w' => 288, 'h' => 216);
		$this->assertEquals($thumbs, $valid);
	}

	/**
	 * Tests the getTitle method
	 *
	 * @group	JGoogle
	 * @return void
	 */
	public function testGetTitle()
	{
		$title = $this->object->getTitle();
		$this->assertEquals($title, 'Photo2.jpg');
	}

	/**
	 * Tests the getSummary method
	 *
	 * @group	JGoogle
	 * @return void
	 */
	public function testGetSummary()
	{
		$summary = $this->object->getSummary();
		$this->assertEquals($summary, 'Summary');
	}

	/**
	 * Tests the getAccess method
	 *
	 * @group	JGoogle
	 * @return void
	 */
	public function testGetAccess()
	{
		$access = $this->object->getAccess();
		$this->assertEquals($access, 'only_you');
	}

	/**
	 * Tests the getTime method
	 *
	 * @group	JGoogle
	 * @return void
	 */
	public function testGetTime()
	{
		$time = $this->object->getTime();
		$this->assertEquals($time, 1328140800000);
	}

	/**
	 * Tests the getSize method
	 *
	 * @group	JGoogle
	 * @return void
	 */
	public function testGetSize()
	{
		$size = $this->object->getSize();
		$this->assertEquals($size, 648818);
	}

	/**
	 * Tests the getHeight method
	 *
	 * @group	JGoogle
	 * @return void
	 */
	public function testGetHeight()
	{
		$height = $this->object->getHeight();
		$this->assertEquals($height, 1536);
	}

	/**
	 * Tests the getTime method
	 *
	 * @group	JGoogle
	 * @return void
	 */
	public function testGetWidth()
	{
		$width = $this->object->getWidth();
		$this->assertEquals($width, 2048);
	}

	/**
	 * Tests the setTitle method
	 *
	 * @group	JGoogle
	 * @return void
	 */
	public function testSetTitle()
	{
		$title = $this->object->setTitle('New Title')->getTitle();
		$this->assertEquals($title, 'New Title');
	}

	/**
	 * Tests the setSummary method
	 *
	 * @group	JGoogle
	 * @return void
	 */
	public function testSetSummary()
	{
		$summary = $this->object->setSummary('New Summary')->getSummary();
		$this->assertEquals($summary, 'New Summary');
	}

	/**
	 * Tests the setAccess method
	 *
	 * @group	JGoogle
	 * @return void
	 */
	public function testSetAccess()
	{
		$access = $this->object->setAccess('public')->getAccess();
		$this->assertEquals($access, 'public');
	}

	/**
	 * Tests the getTime method
	 *
	 * @group	JGoogle
	 * @return void
	 */
	public function testSetTime()
	{
		$time = $this->object->setTime('FIX')->getTime();
		$this->assertEquals($time, 0);
	}

	/**
	 * Tests the save method
	 *
	 * @group	JGoogle
	 * @return void
	 */
	public function testSave()
	{
		$this->http->expects($this->exactly(2))->method('put')->will($this->returnCallback('XMLDataPicasaPhotoCallback'));
		$this->object->setTitle('New Title');
        $this->object->save();
        $this->object->save(true);
	}

	/**
	 * Tests the refresh method
	 *
	 * @group	JGoogle
	 * @return void
	 */
	public function testRefresh()
	{
		$this->http->expects($this->once())->method('get')->will($this->returnCallback('XMLPicasaPhotoCallback'));
		$result = $this->object->refresh();
        $this->assertEquals(get_class($result), 'JGoogleDataPicasaPhoto');
	}
}

/**
 * Dummy method
 *
 * @param   string   $url      Path to the resource.
 * @param   array    $headers  An array of name-value pairs to include in the header of the request.
 * @param   integer  $timeout  Read timeout in seconds.
 *
 * @return  JHttpResponse
 *
 * @since   12.2
 */
function XMLPicasaPhotoCallback($url, array $headers = null, $timeout = null)
{
    $response->code = 200;
	$response->headers = array('Content-Type' => 'text/html');
	$response->body = JFile::read(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'photo.txt');

	return $response;
}

/**
 * Dummy method
 *
 * @param   string   $url      Path to the resource.
 * @param   mixed    $data     Either an associative array or a string to be sent with the request.
 * @param   array    $headers  An array of name-value pairs to include in the header of the request.
 * @param   integer  $timeout  Read timeout in seconds.
 *
 * @return  JHttpResponse
 *
 * @since   12.2
 */
function XMLDataPicasaPhotoCallback($url, $data, array $headers = null, $timeout = null)
{
    PHPUnit_Framework_TestCase::assertContains('<title>New Title</title>', $data);
    
    $response->code = 200;
	$response->headers = array('Content-Type' => 'text/html');
	$response->body = $data;

	return $response;
}
