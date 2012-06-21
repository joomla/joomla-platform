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
 * Filesystem cache driver for the Joomla Platform.
 *
 * @package     Joomla.Platform
 * @subpackage  Cache
 * @since       12.3
 */
class JCacheFile extends JCache
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

		$this->options->def('file.locking', true);

		if (!is_dir($this->options->get('file.path')))
		{
			throw new RuntimeException(sprintf('The base cache path `%s` does not exist.', $this->options->get('file.path')));
		}
		elseif (!is_writable($this->options->get('file.path')))
		{
			throw new RuntimeException(sprintf('The base cache path `%s` is not writable.', $this->options->get('file.path')));
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
		if ($this->exists($key))
		{
			throw new RuntimeException(sprintf('Unable to add cache entry for %s. Entry already exists.', $key));
		}

		$success = (bool) file_put_contents(
			$this->_fetchStreamUri($key),
			serialize($value),
			($this->options->get('file.locking') ? LOCK_EX : null)
		);

		if (!$success)
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
		return is_file($this->_fetchStreamUri($key));
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
		// If the cached data has expired remove it and return.
		if ($this->exists($key) && $this->_isExpired($key))
		{
			try
			{
				$this->delete($key);
			}
			catch (RuntimeException $e)
			{
				throw new RuntimeException(sprintf('Unable to clean expired cache entry for %s.', $key), null, $e);
			}

			return;
		}

		if (!$this->exists($key))
		{
			return;
		}

		$resource = @fopen($this->_fetchStreamUri($key), 'rb');
		if (!$resource)
		{
			throw new RuntimeException(sprintf('Unable to fetch cache entry for %s.  Connot open the resource.', $key));
		}

		// If locking is enabled get a shared lock for reading on the resource.
		if ($this->options->get('file.locking') && !flock($resource, LOCK_SH))
		{
			throw new RuntimeException(sprintf('Unable to fetch cache entry for %s.  Connot obtain a lock.', $key));
		}

		$data = stream_get_contents($resource);

		// If locking is enabled release the lock on the resource.
		if ($this->options->get('file.locking') && !flock($resource, LOCK_UN))
		{
			throw new RuntimeException(sprintf('Unable to fetch cache entry for %s.  Connot release the lock.', $key));
		}

		fclose($resource);

		return unserialize($data);
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
		$success = (bool) unlink($this->_fetchStreamUri($key));

		if (!$success)
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
		$success = (bool) file_put_contents(
			$this->_fetchStreamUri($key),
			serialize($value),
			($this->options->get('file.locking') ? LOCK_EX : null)
		);

		if (!$success)
		{
			throw new RuntimeException(sprintf('Unable to set cache entry for %s.', $value));
		}
	}

	/**
	 * Get the full stream URI for the cache entry.
	 *
	 * @param   string  $key  The storage entry identifier.
	 *
	 * @return  string  The full stream URI for the cache entry.
	 *
	 * @since   12.1
	 */
	private function _fetchStreamUri($key)
	{
		return $this->options->get('file.path') . '/' . $key;
	}

	/**
	 * Check whether or not the cached data by id has expired.
	 *
	 * @param   string  $key  The storage entry identifier.
	 *
	 * @return  boolean  True if the data has expired.
	 *
	 * @since   12.3
	 */
	private function _isExpired($key)
	{
		// Check to see if the cached data has expired.
		if (filemtime($this->_fetchStreamUri($key)) < (time() - $this->options->get('ttl')))
		{
			return true;
		}

		return false;
	}
}
