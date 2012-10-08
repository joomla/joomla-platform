<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Feed
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * RSS Feed Parser Namespace handler for iTunes.
 *
 * @package     Joomla.Platform
 * @subpackage  Feed
 * @see         http://www.apple.com/itunes/podcasts/specs.html
 * @since       12.1
 */
class JFeedParserRssItunes implements JFeedParserNamespace
{
	/**
	 * Method to handle an element for the feed given that the itunes namespace is present.
	 *
	 * @param   JFeed        $feed  The JFeed object being built from the parsed feed.
	 * @param   JXMLElement  $el    The current XML element object to handle.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function processElementForFeed(JFeed $feed, JXMLElement $el)
	{

	}

	/**
	 * Method to handle the feed entry element for the feed given that the itunes namespace is present.
	 *
	 * @param   JFeedEntry   $entry  The JFeedEntry object being built from the parsed feed entry.
	 * @param   JXMLElement  $el     The current XML element object to handle.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function processElementForFeedEntry(JFeedEntry $entry, JXMLElement $el)
	{

	}
}
