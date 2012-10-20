<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Event
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * A class containing an inner listeners priority queue
 * that can be iterated multiple times.
 * One instance of JEventListenerQueue is used per Event in the Dispatcher.
 *
 * @package     Joomla.Platform
 * @subpackage  Event
 */
class JEventListenerQueue implements IteratorAggregate, Countable
{
	/**
	 * The inner listeners priority queue.
	 *
	 * @var  SplPriorityQueue
	 */
	protected $queue;

	/**
	 * A copy of the listeners contained in the queue
	 * that is used used when detaching them to
	 * recreate the queue or to see if the queue contains
	 * a given listener.
	 *
	 * @var  SplObjectStorage
	 */
	protected $storage;

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		$this->queue = new SplPriorityQueue;
		$this->storage = new SplObjectStorage;
	}

	/**
	 * Attach a listener with the given priority.
	 *
	 * @param   object    $listener  The listener.
	 * @param   integer   $priority  The listener priority.
	 *
	 * @return  JEventListenerQueue  This method is chainable.
	 */
	public function attach($listener, $priority = 0)
	{
		// If the listener is not already attached.
		if (!$this->storage->contains($listener))
		{
			// Attach it to the storage.
			$this->storage->attach($listener, $priority);

			// Add it in the queue.
			$this->queue->insert($listener, $priority);
		}

		return $this;
	}

	/**
	 * Detach a listener from the queue.
	 *
	 * @param   object  $listener  The listener.
	 *
	 * @return  JEventListenerQueue  This method is chainable.
	 */
	public function detach($listener)
	{
		// If the observer exists in the the storage.
		if ($this->storage->contains($listener))
		{
			// Delete it from the storage.
			$this->storage->detach($listener);

			// Rewind the storage.
			$this->storage->rewind();

			// Reset the queue and re-add all the elements.
			$this->queue = new SplPriorityQueue;

			foreach ($this->storage as $listener)
			{
				$priority = $this->storage->getInfo();
				$this->queue->insert($listener, $priority);
			}
		}

		return $this;
	}

	/**
	 * Check if the listener exists in the queue.
	 *
	 * @param   object  $listener  The listener.
	 *
	 * @return  boolean  True if it exists, false otherwise.
	 */
	public function contains($listener)
	{
		return $this->storage->contains($listener);
	}

	/**
	 * Get all listeners contained in this queue.
	 * The returned array order matches the order in which
	 * the listeners will be called when triggering the event.
	 *
	 * @return  array  The listeners.
	 */
	public function getAll()
	{
		$listeners = array();

		// Get a clone of the queue.
		$queue = $this->getIterator();

		foreach ($queue as $listener)
		{
			$listeners[] = $listener;
		}

		return $listeners;
	}

	/**
	 * Get the inner queue with its cursor on top of the heap.
	 *
	 * @return  SPLPriorityQueue  The inner queue.
	 */
	public function getIterator()
	{
		// SPLPriorityQueue queue is a heap.
		$queue = clone $this->queue;

		$queue->top();

		return $queue;
	}

	/**
	 * Count the number of listeners in the queue.
	 *
	 * @return  integer  The number of listeners in the queue.
	 */
	public function count()
	{
		return count($this->queue);
	}
}
