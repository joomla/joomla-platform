<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Mediawiki
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

require_once JPATH_PLATFORM . '/joomla/mediawiki/mediawiki.php';
require_once JPATH_PLATFORM . '/joomla/mediawiki/http.php';
require_once JPATH_PLATFORM . '/joomla/mediawiki/search.php';

/**
 * Test class for JMediawikiCategories.
 */
class JMediawikiCategoriesTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var    JRegistry  Options for the Mediawiki object.
	 * @since  12.1
	 */
	protected $options;

	/**
	 * @var    JMediawikiHttp  Mock client object.
	 * @since  12.1
	 */
	protected $client;

	/**
	 * @var    JMediawikiCategories  Object under test.
	 * @since  12.1
	 */
	protected $object;

	/**
	 * @var    string  Sample xml string.
	 * @since  12.1
	 */
	protected $sampleString = '<a><b></b><c></c></a>';

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp()
	{
		$this->options = new JRegistry;
		$this->client = $this->getMock('JMediawikiHttp', array('get', 'post'));

		$this->object = new JMediawikiCategories($this->options, $this->client);
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown()
	{
	}

	/**
	 * @covers JMediawikiCategories::getCategories
	 */
	public function testGetCategories()
	{
		$returnData = new stdClass;
		$returnData->body = $this->sampleString;

		$this->client->expects($this->once())
			->method('get')
			->with('/api.php?action=query&prop=categories&titles=Main Page&format=xml')
			->will($this->returnValue($returnData));

		$this->assertThat(
			$this->object->getCategories(array('Main Page')),
			$this->equalTo(simplexml_load_string($this->sampleString))
		);
	}

	/**
	 * @covers JMediawikiCategories::getCategoriesUsed
	 */
	public function testGetCategoriesUsed()
	{
		$returnData = new stdClass;
		$returnData->body = $this->sampleString;

		$this->client->expects($this->once())
			->method('get')
			->with('/api.php?action=query&generator=categories&prop=info&titles=Main Page&format=xml')
			->will($this->returnValue($returnData));

		$this->assertThat(
			$this->object->getCategoriesUsed(array('Main Page')),
			$this->equalTo(simplexml_load_string($this->sampleString))
		);
	}

	/**
	 * @covers JMediawikiCategories::getCategoriesInfo
	 */
	public function testGetCategoriesInfo()
	{
		$returnData = new stdClass;
		$returnData->body = $this->sampleString;

		$this->client->expects($this->once())
			->method('get')
			->with('/api.php?action=query&prop=categoryinfo&titles=Main Page&format=xml')
			->will($this->returnValue($returnData));

		$this->assertThat(
			$this->object->getCategoriesInfo(array('Main Page')),
			$this->equalTo(simplexml_load_string($this->sampleString))
		);
	}

	/**
     * @covers JMediawikiCategories::enumerateCategories
	 */
	public function testEnumerateCategories()
	{
		$returnData = new stdClass;
		$returnData->body = $this->sampleString;

		$this->client->expects($this->once())
			->method('get')
			->with('/api.php?action=query&list=allcategories&format=xml')
			->will($this->returnValue($returnData));

		$this->assertThat(
			$this->object->enumerateCategories(),
			$this->equalTo(simplexml_load_string($this->sampleString))
		);
	}

	/**
     * @covers JMediawikiCategories::getChangeTags
     */
	public function testGetChangeTags()
	{
		$returnData = new stdClass;
		$returnData->body = $this->sampleString;

		$this->client->expects($this->once())
			->method('get')
			->with('/api.php?action=query&list=tags&format=xml')
			->will($this->returnValue($returnData));

		$this->assertThat(
			$this->object->getChangeTags(),
			$this->equalTo(simplexml_load_string($this->sampleString))
		);
	}
}
