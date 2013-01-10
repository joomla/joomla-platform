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
 * Class representing an Event.
 * The event can contain arguments and its listeners can manipulate them.
 * Additionnaly, its propagation can be stopped.
 *
 * @package     Joomla.Platform
 * @subpackage  Event
 * @since       13.1
 */
class JEvent implements Serializable, Countable
{
	/**
	 * The event name.
	 *
	 * @var    string
	 * @since  13.1
	 */
	protected $name;

	/**
	 * The event arguments.
	 *
	 * @var    array
	 * @since  13.1
	 */
	protected $arguments;

	/**
	 * A flag to see if the event propagation
	 * is stopped.
	 *
	 * @var    boolean
	 * @since  13.1
	 */
	protected $stopped = false;

	/**
	 * Constructor.
	 *
	 * @param   string  $name       The event name.
	 * @param   array   $arguments  The event arguments.
	 *
	 * @since   13.1
	 */
	public function __construct($name, array $arguments = array())
	{
		$this->name = $name;
		$this->arguments = $arguments;
	}

	/**
	 * Get the event name.
	 *
	 * @return  string  The event name.
	 *
	 * @since   13.1
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Get an event argument.
	 *
	 * @param   string  $name     The argument name.
	 * @param   mixed   $default  The default value if not found.
	 *
	 * @return  mixed  The argument value or the default value if not found.
	 *
	 * @since   13.1
	 */
	public function getArgument($name, $default = null)
	{
		if (isset($this->arguments[$name]))
		{
			return $this->arguments[$name];
		}

		return $default;
	}

	/**
	 * Check if the event has the given argument.
	 *
	 * @param   string  $name  The argument name.
	 *
	 * @return  boolean  True if the event has the specified argument,
	 *                   false otherwise.
	 *
	 * @since   13.1
	 */
	public function hasArgument($name)
	{
		return isset($this->arguments[$name]);
	}

	/**
	 * Set the value of an argument.
	 *
	 * @param   string  $name   The argument name.
	 * @param   mixed   $value  The argument value.
	 *
	 * @return  JEvent  This method is chainable.
	 *
	 * @since   13.1
	 */
	public function setArgument($name, $value)
	{
		$this->arguments[$name] = $value;

		return $this;
	}

	/**
	 * Get the event arguments.
	 *
	 * @return  array  The event arguments.
	 *
	 * @since   13.1
	 */
	public function getArguments()
	{
		return $this->arguments;
	}

	/**
	 * Set the event arguments.
	 *
	 * @param   array  $arguments  The event arguments.
	 *
	 * @return  JEvent  This method is chainable.
	 *
	 * @since   13.1
	 */
	public function setArguments(array $arguments)
	{
		$this->arguments = $arguments;

		return $this;
	}

	/**
	 * Count the number of arguments.
	 *
	 * @return  integer  The number of arguments.
	 *
	 * @since   13.1
	 */
	public function count()
	{
		return count($this->arguments);
	}

	/**
	 * Stop the event propagation.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 */
	public function stopPropagation()
	{
		$this->stopped = true;
	}

	/**
	 * Check if the event propagation is stopped.
	 *
	 * @return  boolean  True if stopped, false otherwise.
	 *
	 * @since   13.1
	 */
	public function isStopped()
	{
		return true === $this->stopped;
	}

	/**
	 * Serialize the event.
	 *
	 * @return  string  The serialized event.
	 *
	 * @since   13.1
	 */
	public function serialize()
	{
		return serialize(array($this->name, $this->arguments, $this->stopped));
	}

	/**
	 * Unserialize the event.
	 *
	 * @param   string  $serialized  The serialized event.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 */
	public function unserialize($serialized)
	{
		list($this->name, $this->arguments, $this->stopped) = unserialize($serialized);
	}
}
