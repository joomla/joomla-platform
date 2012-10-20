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
 * Class managing the registration of events and their listeners
 * and the triggering of events.
 *
 * @package     Joomla.Platform
 * @subpackage  Event
 */
class JEventDispatcher
{
	/**
	 * An array of registered JEvent objects.
	 *
	 * @var  array
	 */
	protected $events = array();

	/**
	 * An array of registered listeners containing
	 * the event names as keys and JEventListenerQueue objects as values.
	 *
	 * @var  array
	 */
	protected $listeners = array();

	/**
	 * Stores the singleton instance of the dispatcher.
	 *
	 * @var    JEventDispatcher
	 * @since  11.3
	 */
	protected static $instance = null;

	/**
	 * Returns the global Event Dispatcher object, only creating it
	 * if it doesn't already exist.
	 *
	 * @return  JEventDispatcher  The EventDispatcher object.
	 *
	 * @since   11.1
	 */
	public static function getInstance()
	{
		if (self::$instance === null)
		{
			self::$instance = new static;
		}

		return self::$instance;
	}

	/**
	 * Register an event to the dispatcher.
	 * This is useful when you want to register a custom event
	 * and override the default created event object.
	 *
	 * It is also possible to do so when triggering the event, see JEventDispatcher::trigger.
	 *
	 * @param   JEvent|string  $event  The event object or name.
	 * @param   boolean        $reset  True to reset and existing event with the same name.
	 *
	 * @return  JEventDispatcher  This method is chainable.
	 */
	public function registerEvent(JEvent $event, $reset = false)
	{
		// If the event does not exist already or a reset flag is set.
		if (!isset($this->events[$event->getName()]) || $reset)
		{
			// Register the event.
			$this->events[$event->getName()] = $event;
		}

		return $this;
	}

	/**
	 * Unregister an event from the dispatcher.
	 * It will cause all its listeners to be unregistered.
	 *
	 * @param   JEvent|string  $event  The event object or name.
	 *
	 * @return  JEventDispatcher  This method is chainable.
	 */
	public function unregisterEvent($event)
	{
		if ($event instanceof JEvent)
		{
			$event = $event->getName();
		}

		// Unregister the event.
		if (isset($this->events[$event]))
		{
			unset($this->events[$event]);
		}

		// Unregister all listeners.
		if (isset($this->listeners[$event]))
		{
			unset($this->listeners[$event]);
		}

		return $this;
	}

	/**
	 * Register a listener to the Dispatcher.
	 *
	 * @param   array    $events      An array of event names the listener wants to listen to.
	 * @param   object   $listener    The event listener (can be any object or closure).
	 * @param   array    $priorities  An array containing the event names as key and the corresponding
	 *                                listener priority for that event as value.
	 *
	 * @return  JEventDispatcher  This method is chainable.
	 *
	 * @throws  InvalidArgumentException
	 */
	public function registerListener(array $events, $listener, array $priorities = array())
	{
		// If the listener is an object.
		if (is_object($listener))
		{
			// We deal with a closure.
			if ($listener instanceof Closure)
			{
				// Get the event he wants to register to.
				if (isset($events[0]))
				{
					$eventName = $events[0];
				}

				else
				{
					throw new InvalidArgumentException('Invalid event name specified for the closure listener.');
				}

				// If we have no listeners for this event.
				if (!isset($this->listeners[$eventName]))
				{
					// Create an empty queue.
					$this->listeners[$eventName] = new JEventListenerQueue;
				}

				// If a priority is specified.
				$priority = isset($priorities[$eventName]) ? $priorities[$eventName] : 0;

				// Add the listener to the queue with its priority.
				$this->listeners[$eventName]->attach($listener, $priority);
			}

			else
			{
				// Get all method names matching the specified event names.
				$events = array_intersect($events, get_class_methods($listener));

				foreach ($events as $eventName)
				{
					// If we have no listeners for this event.
					if (!isset($this->listeners[$eventName]))
					{
						// Create an empty queue.
						$this->listeners[$eventName] = new JEventListenerQueue;
					}

					// If a priority is specified.
					$priority = isset($priorities[$eventName]) ? $priorities[$eventName] : 0;

					// Add the listener to the queue with its priority.
					$this->listeners[$eventName]->attach($listener, $priority);
				}
			}
		}

		else
		{
			throw new InvalidArgumentException('Invalid specified event listener.');
		}

		return $this;
	}

	/**
	 * Unregister a listener from the dispatcher.
	 *
	 * @param   object  $listener  The event listener (can be any object (including closures)).
	 * @param   array   $events    An array containing the event names to detach the listener from.
	 *                             If not specified, the listener will be unregistered from all events he is listening to.
	 *
	 * @return  JEventDispatcher  This method is chainable.
	 *
	 * @throws  InvalidArgumentException
	 */
	public function unregisterListener($listener, array $events = array())
	{
		if (is_object($listener))
		{
			// We deal with a closure.
			if ($listener instanceof Closure)
			{
				// If an event is specified, unregister it, otherwise do nothing.
				if (isset($events[0]))
				{
					$eventName = $events[0];

					// Detach the listener.
					if (isset($this->listeners[$eventName]))
					{
						$this->listeners[$eventName]->detach($listener);
					}
				}
			}

			// We deal with a 'normal' object.
			else
			{
				// Get the object methods.
				$methods = get_class_methods($listener);

				// If no event is specified.
				if (empty($events))
				{
					// Assume we want to unregister it from all events.
					$events = $methods;
				}

				else
				{
					// Otherwise consider the specified events.
					$events = array_intersect($methods, $events);
				}

				// Iterate the event names.
				foreach ($events as $event)
				{
					// Deatch the listener.
					if (isset($this->listeners[$event]))
					{
						$this->listeners[$event]->detach($listener);
					}
				}
			}
		}

		else
		{
			throw new InvalidArgumentException('Invalid listener type.');
		}

		return $this;
	}

	/**
	 * Get the registered listeners for the given event.
	 *
	 * @param   JEvent|string  $event  The event object or name.
	 *
	 * @return  mixed  An array of listeners or false if no listener.
	 */
	public function getListeners($event)
	{
		if ($event instanceof JEvent)
		{
			$event = $event->getName();
		}

		if (isset($this->listeners[$event]))
		{
			return $this->listeners[$event]->getAll();
		}

		return false;
	}

	/**
	 * Get the number of listeners for a given event.
	 *
	 * @param   JEvent|string  $event  The event object or name.
	 *
	 * @return  integer  The number of listeners.
	 */
	public function countListeners($event)
	{
		if ($event instanceof JEvent)
		{
			$event = $event->getName();
		}

		if (isset($this->listeners[$event]))
		{
			return count($this->listeners[$event]);
		}

		return 0;
	}

	/**
	 * Check if a listener is registered.
	 *
	 * @param   object         $listener  The event listener.
	 * @param   JEvent|string  $event     The event object or name.
	 *                                    If not specified, it will check if this listener is registered for any event.
	 *
	 * @return  boolean  True if the listener is registered, false otherwise.
	 */
	public function hasListener($listener, $event = null)
	{
		// If an event is specified.
		if ($event)
		{
			if ($event instanceof JEvent)
			{
				$event = $event->getName();
			}

			// If there is any listener for that event.
			if (isset($this->listeners[$event]))
			{
				return $this->listeners[$event]->contains($listener);
			}
		}

		else
		{
			// Iterate all listeners.
			foreach ($this->listeners as $queue)
			{
				if ($queue->contains($listener))
				{
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Triggers the specified event.
	 * All listeners will be called in a queue according to their priorty
	 * and the event object passed as parameter.
	 *
	 * If you pass an event object in this method, it will override the default event object
	 * or any registered event with the same name.
	 *
	 * @param   JEvent|string  $event  The event to trigger.
	 *
	 * @return  JEvent  The event after being passed through all listeners.
	 */
	public function triggerEvent($event)
	{
		// If the event is not an instance of JEvent.
		if (!$event instanceof JEvent)
		{
			// Take a previously registered event with its name if any.
			if (isset($this->events[$event]))
			{
				$event = $this->events[$event];
			}

			// Otherwise create a default event with its name.
			else
			{
				$event = new JEvent($event);
			}
		}

		// If any listener is registered for this event.
		if (isset($this->listeners[$event->getName()]))
		{
			// Iterate the registered listeners.
			foreach ($this->listeners[$event->getName()] as $listener)
			{
				// If the event propagation is not stopped.
				if (!$event->isStopped())
				{
					if ($listener instanceof Closure)
					{
						call_user_func($listener, $event);
					}

					else
					{
						call_user_func(array($listener, $event->getName()), $event);
					}
				}
			}
		}

		return $event;
	}
}
