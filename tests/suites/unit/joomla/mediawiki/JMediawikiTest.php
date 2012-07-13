<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Client
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

require_once JPATH_PLATFORM.'/joomla/mediawiki/mediawiki.php';

/**
 * Test class for JMediawiki.
 */
class JMediawikiTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var    JRegistry  Options for the Mediawiki object.
	 * @since  12.1
	 */
	protected $options;

	/**
	 * @var    JGithubHttp  Mock client object.
	 * @since  12.1
	 */
	protected $client;

    /**
     * @var JMediawiki Object under test.
     * @since  12.1
     */
    protected $object;

	/**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
	protected function setUp()
	{
		$this->options = new JRegistry;
		$this->client = $this->getMock('JMediawikiHttp', array('get', 'post'));

		$this->object = new JMediawiki($this->options, $this->client);
	}

	/**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
	protected function tearDown()
	{
	}

	/**
	 * Tests the magic __get method - sites
	 * @since  12.1
	 */
	public function test__Getsites()
	{
		$this->assertThat(
			$this->object->sites,
			$this->isInstanceOf('JMediawikiSites')
		);
	}

	/**
	 * Tests the magic __get method - pages
	 * @since  12.1
	 */
	public function test__GetPages()
	{
		$this->assertThat(
			$this->object->pages,
			$this->isInstanceOf('JMediawikiPages')
		);
	}

	/**
	 * Tests the magic __get method - users
	 * @since  12.1
	 */
	public function test__GetUsers()
	{
		$this->assertThat(
			$this->object->users,
			$this->isInstanceOf('JMediawikiUsers')
		);
	}

	/**
	 * Tests the magic __get method - links
	 * @since  12.1
	 */
	public function test__GetLinks()
	{
		$this->assertThat(
			$this->object->links,
			$this->isInstanceOf('JMediawikiLinks')
		);
	}

	/**
	 * Tests the magic __get method - categories
	 * @since  12.1
	 */
	public function test__GetCategories()
	{
		$this->assertThat(
			$this->object->categories,
			$this->isInstanceOf('JMediawikiCategories')
		);
	}

	/**
	 * Tests the magic __get method - images
	 * @since  12.1
	 */
	public function test__GetImages()
	{
		$this->assertThat(
			$this->object->images,
			$this->isInstanceOf('JMediawikiImages')
		);
	}

	/**
	 * Tests the magic __get method - search
	 * @since  12.1
	 */
	public function test__GetSearch()
	{
		$this->assertThat(
			$this->object->search,
			$this->isInstanceOf('JMediawikiSearch')
		);
	}

	/**
	 * Tests the setOption method
	 * @since  12.1
	 */
	public function testSetOption()
	{
		$this->object->setOption('api.url', 'https://example.com/settest');

		$this->assertThat(
			$this->options->get('api.url'),
			$this->equalTo('https://example.com/settest')
		);
	}

	/**
	 * Tests the getOption method
	 * @since  12.1
	 */
	public function testGetOption()
	{
		$this->options->set('api.url', 'https://example.com/gettest');

		$this->assertThat(
			$this->object->getOption('api.url', 'https://example.com/gettest'),
			$this->equalTo('https://example.com/gettest')
		);
	}
}
