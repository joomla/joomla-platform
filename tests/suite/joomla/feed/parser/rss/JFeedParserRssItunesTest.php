<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Feed
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * Test class for JFeedParserAtom.
 *
 * @package     Joomla.UnitTest
 * @subpackage  Feed
 * @since       12.1
 */
class JFeedParserRssItunesTest extends JoomlaTestCase
{
	/**
	 * Setup for testing.
	 *
	 * @return  void
	 *
	 * @see     PHPUnit_Framework_TestCase::setUp()
	 * @since   12.1
	 */
	protected function setUp()
	{
		parent::setUp();

		// Create the namespace object.
		$this->object = new JFeedParserRssItunes;
	}

	/**
	 * Tear down any fixtures.
	 *
	 * @return  void
	 *
	 * @see     PHPUnit_Framework_TestCase::tearDown()
	 * @since   12.1
	 */
	protected function tearDown()
	{
		$this->object = null;

		parent::tearDown();
	}

	/**
	 * Tests JFeedParserRssItunes::processElementForFeed()
	 *
	 * @return void
	 *
	 * @since 12.1
	 *
	 * @covers  JFeedParserRssItunes::processElementForFeed
	 */
	public function testProcessElementForFeed()
	{
		// Setup the inputs.
		$el   = new JXMLElement('<id>http://domain.com/path/to/resource</id>');
		$feed = new JFeed;

		$this->object->processElementForFeed($feed, $el);
	}

	/**
	 * Tests JFeedParserRssItunes::processElementForFeedEntry()
	 *
	 * @return void
	 *
	 * @since 12.1
	 *
	 * @covers  JFeedParserRssItunes::processElementForFeedEntry
	 */
	public function testProcessElementForFeedEntry()
	{
		// Setup the inputs.
		$el   = new JXMLElement('<id>http://domain.com/path/to/resource</id>');
		$feedEntry = new JFeedEntry;

		$this->object->processElementForFeedEntry($feedEntry, $el);
	}
}
