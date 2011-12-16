<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Cache
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Joomla Platform Cacheable Class
 *
 * @package     Joomla.Platform
 * @subpackage  Cache
 * @since       12.1
 */
abstract class JCacheObject
{
	/**
	 * Internal memory based cache array of data.
	 *
	 * @var    array
	 * @since  12.1
	 */
	protected $cache = array();

	/**
	 * The persistent cache group.
	 *
	 * @var    string
	 * @since  12.1
	 */
	protected $cacheGroup = 'JCacheObject';

	/**
	 * Method to get a store id based on the state.
	 *
	 * @param   string  $id  An identifier string to generate the store id.
	 *
	 * @return  string  A store id.
	 *
	 * @since   12.1
	 */
	protected function getStoreId($id = '')
	{
		return md5(get_class($this) . ':' . $id);
	}

	/**
	 * Method to retrieve data from cache.
	 *
	 * @param   string   $id          The cache store id.
	 * @param   boolean  $persistent  Flag to enable the use of persistent cache.
	 *
	 * @return  mixed  The cached data.
	 *
	 * @since   12.1
	 */
	protected function retrieve($id, $persistent = true)
	{
		$data = null;

		// Use the internal cache if possible.
		if (isset($this->cache[$id]))
		{
			return $this->cache[$id];
		}

		// Use the persistent cache if appropriate.
		if ($persistent)
		{
			$data = JFactory::getCache($this->cacheGroup, 'output')->get($id);
			$data = $data ? unserialize($data) : null;
		}

		// Store the data in internal cache.
		if ($data)
		{
			$this->cache[$id] = $data;
		}

		return $data;
	}

	/**
	 * Method to store data in cache.
	 *
	 * @param   string   $id          The cache store id.
	 * @param   mixed    $data        The data to cache.
	 * @param   boolean  $persistent  Flag to enable the use of persistent cache.
	 *
	 * @return  mixed  The cached data.
	 *
	 * @since   12.1
	 */
	protected function store($id, $data, $persistent = true)
	{
		// Store the data in internal cache.
		$this->cache[$id] = $data;

		// Store the data in persistent cache if appropriate.
		if ($persistent)
		{
			JFactory::getCache($this->cacheGroup, 'output')->store(serialize($data), $id);
		}

		return $data;
	}
}
