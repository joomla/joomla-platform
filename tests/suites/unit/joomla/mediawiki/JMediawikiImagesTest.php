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
require_once JPATH_PLATFORM . '/joomla/mediawiki/images.php';

/**
 * Test class for JMediawikiImages.
 */
class JMediawikiImagesTest extends PHPUnit_Framework_TestCase
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
	 * @var    JMediawikiImages  Object under test.
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

		$this->object = new JMediawikiImages($this->options, $this->client);
	}

	/**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
    */
	protected function tearDown()
	{
	}

	/**
     * @covers JMediawikiImages::getImages
    */
	public function testGetImages()
	{
		$returnData = new stdClass;
		$returnData->body = $this->sampleString;

		$this->client->expects($this->once())
			->method('get')
			->with('/api.php?action=query&prop=images&titles=Main Page&format=xml')
			->will($this->returnValue($returnData));

		$this->assertThat(
			$this->object->getImages(array('Main Page')),
			$this->equalTo(simplexml_load_string($this->sampleString))
		);
	}

	/**
     * @covers JMediawikiImages::getImagesUsed
    */
	public function testGetImagesUsed()
	{
		$returnData = new stdClass;
		$returnData->body = $this->sampleString;

		$this->client->expects($this->once())
			->method('get')
			->with('/api.php?action=query&generator=images&prop=info&titles=Main Page&format=xml')
			->will($this->returnValue($returnData));

		$this->assertThat(
			$this->object->getImagesUsed(array('Main Page')),
			$this->equalTo(simplexml_load_string($this->sampleString))
		);
	}

	/**
     * @covers JMediawikiImages::getImageInfo
    */
	public function testGetImageInfo()
	{
		$returnData = new stdClass;
		$returnData->body = $this->sampleString;

		$this->client->expects($this->once())
			->method('get')
			->with('/api.php?action=query&meta=siteinfo&format=xml')
			->will($this->returnValue($returnData));

		$this->assertThat(
			$this->object->getImageInfo(),
			$this->equalTo(simplexml_load_string($this->sampleString))
		);
	}

	/**
     * @covers JMediawikiImages::enumerateImages
     */
	public function testEnumerateImages()
	{
		$returnData = new stdClass;
		$returnData->body = $this->sampleString;

		$this->client->expects($this->once())
			->method('get')
			->with('/api.php?action=query&list=allimages&format=xml')
			->will($this->returnValue($returnData));

		$this->assertThat(
			$this->object->enumerateImages(),
			$this->equalTo(simplexml_load_string($this->sampleString))
		);
	}
}
