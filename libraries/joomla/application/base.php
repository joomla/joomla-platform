<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Application
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Joomla Platform Base Application Class
 *
 * @package     Joomla.Platform
 * @subpackage  Application
 * @since       12.1
 */
abstract class JApplicationBase
{
	/**
	 * The application dispatcher object.
	 *
	 * @var    JEventDispatcher
	 * @since  12.1
	 */
	protected $dispatcher;

	/**
	 * The application identity object.
	 *
	 * @var    JUser
	 * @since  12.1
	 */
	protected $identity;

	/**
	 * The application input object.
	 *
	 * @var    JInput
	 * @since  12.1
	 */
	public $input = null;

	/**
	 * Method to close the application.
	 *
	 * @param   integer  $code  The exit code (optional; default is 0).
	 *
	 * @return  void
	 *
	 * @codeCoverageIgnore
	 * @since   12.1
	 */
	public function close($code = 0)
	{
		exit($code);
	}

	/**
	 * Get the application identity.
	 *
	 * @return  mixed  A JUser object or null.
	 *
	 * @since   12.1
	 */
	public function getIdentity()
	{
		return $this->identity;
	}

	/**
	 * Method to load a PHP configuration class file based on convention and return the instantiated data object.  You
	 * will extend this method in child classes to provide configuration data from whatever data source is relevant
	 * for your specific application.
	 *
	 * @param   string  $file   The path and filename of the configuration file. If not provided, configuration.php
	 *                          in JPATH_BASE will be used.
	 * @param   string  $class  The class name to instantiate.
	 *
	 * @return  mixed   Either an array or object to be loaded into the configuration object.
	 *
	 * @since   13.1
	 */
	abstract protected function fetchConfigurationData($file = '', $class = 'JConfig');

	/**
	 * Load an object or array into the application configuration object.
	 *
	 * @param   mixed  $data  Either an array or object to be loaded into the configuration object.
	 *
	 * @return  JApplicationBase  Instance of $this to allow chaining.
	 *
	 * @since   13.1
	 */
	public function loadConfiguration($data)
	{
		// Load the data into the configuration object.
		if (is_array($data))
		{
			$this->config->loadArray($data);
		}
		elseif (is_object($data))
		{
			$this->config->loadObject($data);
		}

		return $this;
	}

	/**
	 * Returns a property of the object or the default value if the property is not set.
	 *
	 * @param   string  $key      The name of the property.
	 * @param   mixed   $default  The default value (optional) if none is set.
	 *
	 * @return  mixed   The value of the configuration.
	 *
	 * @since   13.1
	 */
	public function get($key, $default = null)
	{
		return $this->config->get($key, $default);
	}

	/**
	 * Modifies a property of the object, creating it if it does not already exist.
	 *
	 * @param   string  $key    The name of the property.
	 * @param   mixed   $value  The value of the property to set (optional).
	 *
	 * @return  mixed   Previous value of the property
	 *
	 * @since   13.1
	 */
	public function set($key, $value = null)
	{
		$previous = $this->config->get($key);
		$this->config->set($key, $value);

		return $previous;
	}

	/**
	 * Registers a handler to a particular event group.
	 *
	 * @param   string    $event    The event name.
	 * @param   callable  $handler  The handler, a function or an instance of a event object.
	 *
	 * @return  JApplicationBase  The application to allow chaining.
	 *
	 * @since   12.1
	 */
	public function registerEvent($event, $handler)
	{
		if ($this->dispatcher instanceof JEventDispatcher)
		{
			$this->dispatcher->register($event, $handler);
		}

		return $this;
	}

	/**
	 * Calls all handlers associated with an event group.
	 *
	 * @param   string  $event  The event name.
	 * @param   array   $args   An array of arguments (optional).
	 *
	 * @return  array   An array of results from each function call, or null if no dispatcher is defined.
	 *
	 * @since   12.1
	 */
	public function triggerEvent($event, array $args = null)
	{
		if ($this->dispatcher instanceof JEventDispatcher)
		{
			return $this->dispatcher->trigger($event, $args);
		}

		return null;
	}

	/**
	 * Allows the application to load a custom or default dispatcher.
	 *
	 * The logic and options for creating this object are adequately generic for default cases
	 * but for many applications it will make sense to override this method and create event
	 * dispatchers, if required, based on more specific needs.
	 *
	 * @param   JEventDispatcher  $dispatcher  An optional dispatcher object. If omitted, the factory dispatcher is created.
	 *
	 * @return  JApplicationBase This method is chainable.
	 *
	 * @since   12.1
	 */
	public function loadDispatcher(JEventDispatcher $dispatcher = null)
	{
		$this->dispatcher = ($dispatcher === null) ? JEventDispatcher::getInstance() : $dispatcher;

		return $this;
	}

	/**
	 * Allows the application to load a custom or default identity.
	 *
	 * The logic and options for creating this object are adequately generic for default cases
	 * but for many applications it will make sense to override this method and create an identity,
	 * if required, based on more specific needs.
	 *
	 * @param   JUser  $identity  An optional identity object. If omitted, the factory user is created.
	 *
	 * @return  JApplicationBase This method is chainable.
	 *
	 * @since   12.1
	 */
	public function loadIdentity(JUser $identity = null)
	{
		$this->identity = ($identity === null) ? JFactory::getUser() : $identity;

		return $this;
	}
}
