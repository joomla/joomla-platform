<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Authorisation
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Factory pattern to create objects in the access package.
 *
 * @package     Joomla.Platform
 * @subpackage  Authorisation
 * @since       12.1
 */
class JAuthorisationFactory
{
	/**
	 * A set instance to use when getInstance is called.
	 *
	 * @var    JAuthorisationFactory
	 * @since  12.1
	 */
	private static $_instance = null;

	/**
	 * Gets and instance of a JAuthorisationAuthoriser object.
	 *
	 * This method assumes that the class exists.
	 * Register your own classes using JLoader::register or JLoader::discover.
	 *
	 * @param   string  $name  The optional name of the authoriser (defaults to 'Default' if not provided).
	 *
	 * @return  JAuthorisationAuthoriser
	 *
	 * @since   12.1
	 * @throws  UnexpectedValueException if the authoriser class determined from $name cannot be found.
	 */
	public function getAuthoriser($name = '')
	{
		if (empty($name))
		{
			$name = 'Default';
		}

		$className = 'JAuthorisationAuthoriser' . ucfirst(strtolower($name));

		if (class_exists($className))
		{
			return new $className;
		}

		throw new UnexpectedValueException(JText::sprintf('JAUTHORISATION_INVALID_AUTHORISOR_NAME', $name), 0);
	}

	/**
	 * Gets an instance of the factory object.
	 *
	 * @return  JAuthorisationFactory
	 *
	 * @since   12.1
	 */
	public static function getInstance()
	{
		return self::$_instance ? self::$_instance : new JAuthorisationFactory;
	}

	/**
	 * Sets an instance of a factory object to return on subsequent calls of getInstance.
	 *
	 * @param   JAuthorisationFactory  $instance  A JAuthorisationFactory object.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public static function setInstance(JAuthorisationFactory $instance = null)
	{
		self::$_instance = $instance;
	}
}
