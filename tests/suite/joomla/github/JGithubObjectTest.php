<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Client
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

require_once JPATH_PLATFORM . '/joomla/github/http.php';
require_once JPATH_PLATFORM . '/joomla/github/object.php';
require_once __DIR__ . '/stubs/JGithubObjectMock.php';

/**
 * Test class for JGithub.
 */
class JGithubObjectTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var    JRegistry  Options for the GitHub object.
	 * @since  11.4
	 */
	protected $options;

	/**
	 * @var    JGithubHttp  Mock client object.
	 * @since  11.4
	 */
	protected $client;

	/**
	 * @var    JGithubIssues  Object under test.
	 * @since  11.4
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
		$this->client = $this->getMock('JGithubHttp', array('get', 'post', 'delete', 'patch', 'put'));

		$this->object = new JGithubObjectMock($this->options, $this->client);
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

	public function fetchUrlData()
	{
		return array(
			'Standard github - no pagination data' => array('https://api.github.com', '/gists', 0, 0, 'https://api.github.com/gists'),
			'Enterprise github - no pagination data' => array('https://mygithub.com', '/gists', 0, 0, 'https://mygithub.com/gists'),
			'Standard github - page 3' => array('https://api.github.com', '/gists', 3, 0, 'https://api.github.com/gists?page=3'),
			'Enterprise github - page 3, 50 per page' => array('https://mygithub.com', '/gists', 3, 50, 'https://mygithub.com/gists?page=3&per_page=50'),
		);
	}

	/**
	 * Tests the fetchUrl method
	 * @dataProvider fetchUrlData
	 */
	public function testFetchUrl($apiUrl, $path, $page, $limit, $expected)
	{
		$this->options->set('api.url', $apiUrl);

		$this->assertThat(
			$this->object->fetchUrl($path, $page, $limit),
			$this->equalTo($expected)
		);
	}
}
