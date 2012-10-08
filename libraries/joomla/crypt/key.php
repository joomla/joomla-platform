<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Crypt
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Encryption key object for the Joomla Platform.
 *
 * @property-read  string  $type  The key type.
 *
 * @package     Joomla.Platform
 * @subpackage  Crypt
 * @since       12.1
 */
class JCryptKey
{
	/**
	 * @var    string  The private key.
	 * @since  12.1
	 */
	public $private;

	/**
	 * @var    string  The public key.
	 * @since  12.1
	 */
	public $public;

	/**
	 * @var    string  The key type.
	 * @since  12.1
	 */
	protected $type;

	/**
	 * Constructor.
	 *
	 * @param   string  $type     The key type.
	 * @param   string  $private  The private key.
	 * @param   string  $public   The public key.
	 *
	 * @since   12.1
	 */
	public function __construct($type, $private = null, $public = null)
	{
		// Set the key type.
		$this->type = (string) $type;

		// Set the optional public/private key strings.
		$this->private = isset($private) ? (string) $private : null;
		$this->public  = isset($public) ? (string) $public : null;
	}

	/**
	 * Magic method to return some protected property values.
	 *
	 * @param   string  $name  The name of the property to return.
	 *
	 * @return  mixed
	 *
	 * @since   12.1
	 */
	public function __get($name)
	{
		if ($name == 'type')
		{
			return $this->type;
		}

		// In dynamic methods we have to do the error handling ourself.
		$trace = debug_backtrace();
		trigger_error(
			'Undefined property via __get(): ' . $name . ' in ' . $trace[0]['file'] . ' on line ' . $trace[0]['line'],
			E_USER_NOTICE
		);
		return null;
	}
}
