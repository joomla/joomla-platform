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
 * Joomla! Caching Class
 *
 * @package     Joomla.Platform
 * @subpackage  Cache
 * @since       12.3
 */
abstract class JCache
{
	/**
	 * @var    array  An array of key/value pairs to be used as a runtime cache.
	 * @since  12.3
	 */
	static protected $runtime = array();

	/**
	 * @var    JRegistry  The options for the cache object.
	 * @since  12.3
	 */
	protected $options;

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
		// Set the options object.
		$this->options = $options ? $options : new JRegistry;

		$this->options->def('ttl', 900);
		$this->options->def('runtime', true);
	}

	/**
	 * Get an option from the JCache instance.
	 *
	 * @param   string  $key  The name of the option to get.
	 *
	 * @return  mixed  The option value.
	 *
	 * @since   12.3
	 */
	public function getOption($key)
	{
		return $this->options->get($key);
	}

	/**
	 * Set an option for the JCache instance.
	 *
	 * @param   string  $key    The name of the option to set.
	 * @param   mixed   $value  The option value to set.
	 *
	 * @return  JCache  This object for method chaining.
	 *
	 * @since   12.3
	 */
	public function setOption($key, $value)
	{
		$this->options->set($key, $value);

		return $this;
	}

	/**
	 * Get cached data by id.  If the cached data has expired then the cached data will be removed
	 * and false will be returned.
	 *
	 * @param   string   $cacheId       The cache data id.
	 * @param   boolean  $checkRuntime  True to check runtime cache first.
	 *
	 * @return  mixed  Cached data string if it exists.
	 *
	 * @since   12.3
	 * @throws  RuntimeException
	 */
	public function get($cacheId, $checkRuntime = true)
	{
		if ($checkRuntime && isset(self::$runtime[$cacheId]) && $this->options->get('runtime'))
		{
			return self::$runtime[$cacheId];
		}

		$data = $this->fetch($cacheId);

		if ($this->options->get('runtime'))
		{
			self::$runtime[$cacheId] = $data;
		}

		return $data;
	}

	/**
	 * Store the cached data by id.
	 *
	 * @param   string  $cacheId  The cache data id
	 * @param   mixed   $data     The data to store
	 *
	 * @return  JCache  This object for method chaining.
	 *
	 * @since   12.3
	 * @throws  RuntimeException
	 */
	public function store($cacheId, $data)
	{
		if ($this->exists($cacheId))
		{
			$this->set($cacheId, $data, $this->options->get('ttl'));
		}
		else
		{
			$this->add($cacheId, $data, $this->options->get('ttl'));
		}

		if ($this->options->get('runtime'))
		{
			self::$runtime[$cacheId] = $data;
		}

		return $this;
	}

	/**
	 * Remove a cached data entry by id.
	 *
	 * @param   string  $cacheId  The cache data id.
	 *
	 * @return  JCache  This object for method chaining.
	 *
	 * @since   12.3
	 * @throws  RuntimeException
	 */
	public function remove($cacheId)
	{
		$this->delete($cacheId);

		if ($this->options->get('runtime'))
		{
			unset(self::$runtime[$cacheId]);
		}

		return $this;
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
	abstract protected function add($key, $value, $ttl);

	/**
	 * Method to determine whether a storage entry has been set for a key.
	 *
	 * @param   string  $key  The storage entry identifier.
	 *
	 * @return  boolean
	 *
	 * @since   12.3
	 */
	abstract protected function exists($key);

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
	abstract protected function fetch($key);

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
	abstract protected function delete($key);

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
	abstract protected function set($key, $value, $ttl);
}
