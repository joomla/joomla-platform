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
	 * Method to get a property of the object or the default value if the property is not set.
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
	 * Method to set a property of the object, creating it if it does not already exist.
	 *
	 * @param   string  $key    The name of the property.
	 * @param   mixed   $value  The value of the property to set (optional).
	 *
	 * @return  mixed   The previous value of the property
	 *
	 * @since   12.1
	 */
	public function set($key, $value = null);
}
