<?php
/**
 * @package     Joomla.Legacy
 * @subpackage  Simplepie
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

jimport('simplepie.simplepie');

/**
 * Class to maintain a pathway.
 *
 * The user's navigated path within the application.
 *
 * @package     Joomla.Legacy
 * @subpackage  Simplepie
 * @since       12.2
 */
class JSimplepieFactory
{
	/**
	 * Get a parsed XML Feed Source
	 *
	 * @param   string   $url         Url for feed source.
	 * @param   integer  $cache_time  Time to cache feed for (using internal cache mechanism).
	 *
	 * @return  mixed  SimplePie parsed object on success, false on failure.
	 *
	 * @since   12.2
	 * @deprecated  12.3  Will be dropped without replacement.
	 */
	public static function getFeedParser($url, $cache_time = 0)
	{
		$cache = JFactory::getCache('feed_parser', 'callback');

		if ($cache_time > 0)
		{
			$cache->setLifeTime($cache_time);
		}

		$simplepie = new SimplePie(null, null, 0);

		$simplepie->enable_cache(false);
		$simplepie->set_feed_url($url);
		$simplepie->force_feed(true);

		$contents = $cache->get(array($simplepie, 'init'), null, false, false);

		if ($contents)
		{
			return $simplepie;
		}

		JLog::add(JText::_('JLIB_UTIL_ERROR_LOADING_FEED_DATA'), JLog::WARNING, 'jerror');

		return false;
	}
}
