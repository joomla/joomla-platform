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
require_once JPATH_PLATFORM . '/joomla/mediawiki/sites.php';

/**
 * Test class for JMediawikiSites.
 */
class JMediawikiSitesTest extends PHPUnit_Framework_TestCase
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
	 * @var    JMediawikiSites  Object under test.
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

		$this->object = new JMediawikiSites($this->options, $this->client);
	}

	/**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
	protected function tearDown()
	{
	}

	/**
     * @covers JMediawikiSites::getSiteInfo
     */
	public function testGetSiteInfo()
	{
		$returnData = new stdClass;
		$returnData->body = $this->sampleString;

		$this->client->expects($this->once())
			->method('get')
			->with('/api.php?action=query&meta=siteinfo&format=xml')
			->will($this->returnValue($returnData));

		$this->assertThat(
			$this->object->getSiteInfo(),
			$this->equalTo(simplexml_load_string($this->sampleString))
		);
	}

	/**
     * @covers JMediawikiSites::getEvents
     */
	public function testGetEvents()
	{
		$returnData = new stdClass;
		$returnData->body = $this->sampleString;

		$this->client->expects($this->once())
			->method('get')
			->with('/api.php?action=query&list=logevents&format=xml')
			->will($this->returnValue($returnData));

		$this->assertThat(
			$this->object->getEvents(),
			$this->equalTo(simplexml_load_string($this->sampleString))
		);
	}

	/**
     * @covers JMediawikiSites::getRecentChanges
     */
	public function testGetRecentChanges()
	{
		$returnData = new stdClass;
		$returnData->body = $this->sampleString;

		$this->client->expects($this->once())
			->method('get')
			->with('/api.php?action=query&list=recentchanges&format=xml')
			->will($this->returnValue($returnData));

		$this->assertThat(
			$this->object->getRecentChanges(),
			$this->equalTo(simplexml_load_string($this->sampleString))
		);
	}

	/**
     * @covers JMediawikiSites::getProtectedTitles
     */
	public function testGetProtectedTitles()
	{
		$returnData = new stdClass;
		$returnData->body = $this->sampleString;

		$this->client->expects($this->once())
			->method('get')
			->with('/api.php?action=query&list=protectedtitles&format=xml')
			->will($this->returnValue($returnData));

		$this->assertThat(
			$this->object->getProtectedTitles(),
			$this->equalTo(simplexml_load_string($this->sampleString))
		);
	}
}
