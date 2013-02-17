<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Session
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Memcache session storage handler for PHP
 *
 * @package     Joomla.Platform
 * @subpackage  Session
 * @since       11.1
 */
class JSessionStorageMemcache extends JSessionStorage
{
	/**
	 * Constructor
	 *
	 * @param   array  $options  Optional parameters.
	 *
	 * @since   11.1
	 * @throws  RuntimeException
	 */
	public function __construct($options = array())
	{
		if (!self::isSupported())
		{
			throw new RuntimeException('Memcache Extension is not available', 404);
		}

		$config = JFactory::getConfig();

		// This will be an array of loveliness, must be configured before constructing one!
		// @todo: multiple servers
		$this->_servers = array(
			array(
				'host' => $config->get('memcache_server_host', 'localhost'),
				'port' => $config->get('memcache_server_port', 11211)
			)
		);

		parent::__construct($options);
	}

	/**
	 * Register the functions of this class with PHP's session handler
	 *
	 * @return  void
	 *
	 * @since   12.2
	 */
	public function register()
	{
		// @todo: multiple servers, only first entree works now
		$server = $this->_servers[0]['host'];
		$port = $this->_servers[0]['port'];
		ini_set('session.save_path', $server . ':' . $port );
		ini_set('session.save_handler', 'memcache');
	}

	/**
	 * Test to see if the SessionHandler is available.
	 *
	 * @return boolean  True on success, false otherwise.
	 *
	 * @since   12.1
	 */
	static public function isSupported()
	{
		return (extension_loaded('memcache') && class_exists('Memcache'));
	}
}
