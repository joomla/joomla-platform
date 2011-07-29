<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Session
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die();

/**
 * WINCACHE session storage handler for PHP
 *
 * @package     Joomla.Platform
 * @subpackage  Session
 * @since       11.1
 * @see http://www.php.net/manual/en/function.session-set-save-handler.php
 */
class JSessionStorageWincache extends JSessionStorage
{

	/**
	 * Constructor
	 *
	 * @param   array  $options optional parameters
	 */
	public function __construct($options = array())
	{
		if (!$this->test())
		{
			return JError::raiseError(404, JText::_('JLIB_SESSION_WINCACHE_EXTENSION_NOT_AVAILABLE'));
		}

		parent::__construct($options);
	}

	/**
	 * Open the SessionHandler backend.
	 *
	 * @param   string  $save_path     The path to the session object.
	 * @param   string  $session_name  The name of the session.
	 *
	 * @return boolean  True on success, false otherwise.
	 */
	public function open($save_path, $session_name)
	{
		return true;
	}

	/**
	 * Close the SessionHandler backend.
	 *
	 * @return boolean  True on success, false otherwise.
	 */
	public function close()
	{
		return true;
	}

	/**
	 * Read the data for a particular session identifier from the
	 * SessionHandler backend.
	 *
	 * @param   string   $id  The session identifier.
	 *
	 * @return  string    The session data.
	 */
	public function read($id)
	{
		$sess_id = 'sess_' . $id;
		return (string) wincache_ucache_get($sess_id);
	}

	/**
	 * Write session data to the SessionHandler backend.
	 *
	 * @param   string   $id            The session identifier.
	 * @param   string   $session_data  The session data.
	 *
	 * @return boolean  True on success, false otherwise.
	 */
	public function write($id, $session_data)
	{
		$sess_id = 'sess_' . $id;
		return wincache_ucache_set($sess_id, $session_data, ini_get("session.gc_maxlifetime"));
	}

	/**
	 * Destroy the data for a particular session identifier in the
	 * SessionHandler backend.
	 *
	 * @param   string   $id  The session identifier.
	 *
	 * @return boolean  True on success, false otherwise.
	 */
	public function destroy($id)
	{
		$sess_id = 'sess_' . $id;
		return wincache_ucache_delete($sess_id);
	}

	/**
	 * Garbage collect stale sessions from the SessionHandler backend.
	 *
	 * @param   integer  $maxlifetime  The maximum age of a session.
	 *
	 * @return boolean  True on success, false otherwise.
	 */
	public function gc($maxlifetime = null)
	{
		return true;
	}

	/**
	 * Test to see if the SessionHandler is available.
	 *
	 * @return boolean  True on success, false otherwise.
	 */
	static public function test()
	{
		return (extension_loaded('wincache') && function_exists('wincache_ucache_get') && !strcmp(ini_get('wincache.ucenabled'), "1"));
	}
}
