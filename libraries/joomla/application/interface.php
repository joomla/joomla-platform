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
 * Joomla Platform Application Interface
 *
 * @package     Joomla.Platform
 * @subpackage  Application
 * @since       12.1
 */
interface JApplicationInterface
{
	/**
	 * Method to execute the application.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function execute();

	/**
	 * Method to close the application.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function close();

	/**
	 * Method to register an application event.
	 *
	 * @param   string    $event    The event name.
	 * @param   callback  $handler  The event callback.
	 *
	 * @return  JApplicationInterface  The application instance to support chaining.
	 *
	 * @since   12.1
	 */
	public function registerEvent($event, $handler);

	/**
	 * Method to trigger an application event.
	 *
	 * @param   string  $event  The event name.
	 * @param   array   $args   The event arguments.
	 *
	 * @return  mixed  An array of results from each function call, or null if no event handlers
	 *                 are defined.
	 *
	 * @since   12.1
	 */
	public function triggerEvent($event, array $args = null);

	/**
	 * Method to get a property of the application or the default value if the property is not set.
	 *
	 * @param   string  $key      The name of the property.
	 * @param   mixed   $default  The default value (optional) if none is set.
	 *
	 * @return  mixed   The value of the configuration.
	 *
	 * @since   12.1
	 */
	public function get($key, $default = null);

	/**
	 * Method to set a property of the application, creating it if it does not already exist.
	 *
	 * @param   string  $key    The name of the property.
	 * @param   mixed   $value  The value of the property to set (optional).
	 *
	 * @return  mixed   The previous value of the property
	 *
	 * @since   12.1
	 */
	public function set($key, $value = null);

	/**
	 * Method to get the application character set.
	 *
	 * @return  string  The character set.
	 *
	 * @since   12.1
	 */
	public function getCharacterSet();

	/**
	 * Method to set the application character set.
	 *
	 * @param   string  $charset  The character set.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function setCharacterSet($charset);

	/**
	 * Method to get the application input.
	 *
	 * @return  JInput  The input object.
	 *
	 * @since   12.1
	 */
	public function getInput();

	/**
	 * Method to set the application input.
	 *
	 * @param   JInput  $input  The input object.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function setInput(JInput $input);
}
