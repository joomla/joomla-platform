<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Cache
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * APC cache driver for the Joomla Platform.
 *
 * @package     Joomla.Platform
 * @subpackage  Cache
 * @since       12.3
 */
class JCacheApc extends JCache
{
	/**
	 * Constructor.
	 *
	 * @param   JRegistry  $options  Caching options object.
	 *
	 * @since   12.3
	 * @throws  RuntimeException
	 */
	public function __construct(JRegistry $options = null)
	{
		parent::__construct($options);

		if (!extension_loaded('apc') || !is_callable('apc_fetch'))
		{
			throw new RuntimeException('APC not supported.');
		}
	}

	/**
	 * Method to add a storage entry.
	 *
	 * @param   string   $key    The storage entry identifier.
	 * @param   mixed    $value  The data to be stored.
	 * @param   integer  $ttl    The number of seconds before the stored data expires.
	 *
	 * @return  void
	 *
	 * @since   12.3
	 * @throws  RuntimeException
	 */
	protected function add($key, $value, $ttl)
	{
		if (!apc_add($key, $value, $ttl))
		{
			throw new RuntimeException(sprintf('Unable to add cache entry for %s.', $key));
		}
	}

	/**
	 * Method to determine whether a storage entry has been set for a key.
	 *
	 * @param   string  $key  The storage entry identifier.
	 *
	 * @return  boolean
	 *
	 * @since   12.3
	 */
	protected function exists($key)
	{
		return apc_exists($key);
	}

	/**
	 * Method to get a storage entry value from a key.
	 *
	 * @param   string  $key  The storage entry identifier.
	 *
	 * @return  mixed
	 *
	 * @since   12.3
	 * @throws  RuntimeException
	 */
	protected function fetch($key)
	{
		$success = true;

		$data = apc_fetch($key, $success);

		if (!$success)
		{
			throw new RuntimeException(sprintf('Unable to fetch cache entry for %s.', $key));
		}

		return $data;
	}

	/**
	 * Method to remove a storage entry for a key.
	 *
	 * @param   string  $key  The storage entry identifier.
	 *
	 * @return  void
	 *
	 * @since   12.3
	 * @throws  RuntimeException
	 */
	protected function delete($key)
	{
		if (!apc_delete($key))
		{
			throw new RuntimeException(sprintf('Unable to remove cache entry for %s.', $key));
		}
	}

	/**
	 * Method to set a value for a storage entry.
	 *
	 * @param   string   $key    The storage entry identifier.
	 * @param   mixed    $value  The data to be stored.
	 * @param   integer  $ttl    The number of seconds before the stored data expires.
	 *
	 * @return  void
	 *
	 * @since   12.3
	 * @throws  RuntimeException
	 */
	protected function set($key, $value, $ttl)
	{
		if (!apc_store($key, $value, $ttl))
		{
			throw new RuntimeException(sprintf('Unable to set cache entry for %s.', $key));
		}
	}
}
