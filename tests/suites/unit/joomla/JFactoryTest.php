<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Utilities
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
require_once JPATH_PLATFORM . '/joomla/factory.php';

/**
 * Tests for JDate class.
 *
 * @package     Joomla.UnitTest
 * @subpackage  Utilities
 * @since       11.3
 */
class JFactoryTest extends TestCase
{
	/**
	 * Sets up the fixture.
	 *
	 * This method is called before a test is executed.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function setUp()
	{
		parent::setUp();

		$this->saveFactoryState();
	}

	/**
	 * Tears down the fixture.
	 *
	 * This method is called after a test is executed.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	function tearDown()
	{
		$this->restoreFactoryState();

		parent::tearDown();
	}

	/**
	 * Tests the JFactory::getApplicatiom method.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 * @covers  JFactory::getApplicatiom
	 * @todo    Implement testGetApplication().
	 */
	function testGetApplication()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
				'This test has not been implemented yet.'
		);
	}

	/**
	 * Tests the JFactory::getConfig method.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 * @covers  JFactory::getConfig
	 * @covers  JFactory::createConfig
	 */
	function testGetConfig()
	{
		// Temporarily override the config cache in JFactory.
		$temp = JFactory::$config;
		JFactory::$config = null;

		$this->assertInstanceOf(
			'JRegistry',
			JFactory::getConfig(JPATH_TESTS.'/config.php'),
			'Line: '.__LINE__
		);

		JFactory::$config = $temp;
	}

	/**
	 * Tests the JFactory::getLangauge method.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 * @covers  JFactory::getLangauge
	 * @covers  JFactory::createLanguage
	 * @todo    Implement testGetLanguage().
	 */
	function testGetLanguage()
	{
		$this->assertInstanceOf(
			'JLanguage',
			JFactory::getLanguage(),
			'Line: '.__LINE__
		);

		$this->markTestIncomplete(
				'This test has not been implemented completely yet.'
		);
	}
	
	/**
	 * Tests the JFactory::getDocument method.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 * @covers  JFactory::getDocument
	 * @covers  JFactory::createDocument
	 * @todo    Implement testGetDocument().
	 */
	function testGetDocument()
	{
		$this->assertInstanceOf(
			'JDocument',
			JFactory::getDocument(),
			'Line: '.__LINE__
		);

		$this->markTestIncomplete(
				'This test has not been implemented completely yet.'
		);
	}

	/**
	 * Tests the JFactory::getCache method.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 * @covers  JFactory::getCache
	 * @todo    Implement testGetCache().
	 */
	function testGetCache()
	{
		$this->assertInstanceOf(
			'JCacheController',
			JFactory::getCache(),
			'Line: '.__LINE__
		);

		$this->assertInstanceOf(
			'JCacheControllerCallback',
			JFactory::getCache(),
			'Line: '.__LINE__
		);

		$this->assertInstanceOf(
			'JCacheControllerView',
			JFactory::getCache('', 'view', null),
			'Line: '.__LINE__
		);

		$this->markTestIncomplete(
				'This test has not been implemented completely yet.'
		);
	}

	/**
	 * Tests the JFactory::getACL method.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 * @covers  JFactory::getACL
	 */
	function testGetACL()
	{
		$this->assertInstanceOf(
			'JAccess',
			JFactory::getACL(),
			'Line: '.__LINE__
		);
	}

	/**
	 * Tests the JFactory::getURI method.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 * @covers  JFactory::getURI
	 */
	function testGetUri()
	{
		$this->assertInstanceOf(
			'JUri',
			JFactory::getURI('http://www.joomla.org'),
			'Line: '.__LINE__
		);
	}

	/**
	 * Tests the JFactory::getDate method.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 * @covers  JFactory::getDate
	 */
	public function testGetDate()
	{
		JFactory::$language = $this->getMockLanguage();

		$date = JFactory::getDate('2001-01-01 01:01:01');

		$this->assertThat(
			(string) $date,
			$this->equalTo('2001-01-01 01:01:01'),
			'Tests that a date passed in comes back unchanged.'
		);

		$date = JFactory::getDate('now');
		sleep(2);
		$date2 = JFactory::getDate('now');

		$this->assertThat(
			$date2,
			$this->equalTo($date),
			'Tests that the cache for the same time is working.'
		);

		$tz = 'Etc/GMT+0';
		$date = JFactory::getDate('2001-01-01 01:01:01', $tz);

		$this->assertThat(
			(string) $date,
			$this->equalTo('2001-01-01 01:01:01'),
			'Tests that a date passed in with UTC timezone string comes back unchanged.'
		);

		$tz = new DateTimeZone('Etc/GMT+0');
		$date = JFactory::getDate('2001-01-01 01:01:01', $tz);

		$this->assertThat(
			(string) $date,
			$this->equalTo('2001-01-01 01:01:01'),
			'Tests that a date passed in with UTC timezone comes back unchanged.'
		);
	}
}
