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
 * Joomla Cache output type object
 *
 * @package     Joomla.Platform
 * @subpackage  Cache
 * @since       11.1
 */
class JCacheControllerOutput extends JCacheController
{
	/**
	 * @since   12.1
	 */
	protected $id;

	/**
	 * @since   12.1
	 */
	protected $group;

	/**
	 * @since   12.1
	 */
	protected $locktest = null;

	/**
	 * Start the cache
	 *
	 * @param   string  $id     The cache data id
	 * @param   string  $group  The cache data group
	 *
	 * @return  boolean  True if the cache is hit (false else)
	 *
	 * @since   11.1
	 */
	public function start($id, $group = null)
	{
		// If we have data in cache use that.
		$data = $this->cache->get($id, $group);

		$this->locktest = new stdClass;
		$this->locktest->locked = null;
		$this->locktest->locklooped = null;

		if ($data === false)
		{
			$this->locktest = $this->cache->lock($id, $group);
			if ($this->locktest->locked == true && $this->locktest->locklooped == true)
			{
				$data = $this->cache->get($id, $group);
			}
		}

		if ($data !== false)
		{
			$data = unserialize(trim($data));
			echo $data;
			if ($this->locktest->locked == true)
			{
				$this->cache->unlock($id, $group);
			}
			return true;
		}
		else
		{
			// Nothing in cache... let's start the output buffer and start collecting data for next time.
			if ($this->locktest->locked == false)
			{
				$this->locktest = $this->cache->lock($id, $group);
			}
			ob_start();
			ob_implicit_flush(false);

			// Set id and group placeholders
			$this->id = $id;
			$this->group = $group;

			return false;
		}
	}

	/**
	 * Stop the cache buffer and store the cached data
	 *
	 * @return  boolean  True if cache stored
	 *
	 * @since   11.1
	 */
	public function end()
	{
		// Get data from output buffer and echo it
		$data = ob_get_contents();
		ob_end_clean();
		echo $data;

		// Get id and group and reset them placeholders
		$id = $this->id;
		$group = $this->group;
		$this->id = null;
		$this->group = null;

		// Get the storage handler and store the cached data
		$ret = $this->cache->store(serialize($data), $id, $group);

		if ($this->locktest->locked == true)
		{
			$this->cache->unlock($id, $group);
		}

		return $ret;
	}
}
