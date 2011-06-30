
<?php
/**
 * @version		$Id:eaccelerator.php 6961 2007-03-15 16:06:53Z tcp $
 * @package		Joomla.Framework
 * @subpackage	Session
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('JPATH_BASE') or die;

/**
 * Memcache session storage handler for PHP
 *
 * -- Inspired in both design and implementation by the Horde memcache handler --
 *
 * @package		Joomla.Framework
 * @subpackage	Session
 * @since		1.5
 * @see http://www.php.net/manual/en/function.session-set-save-handler.php
 */
class JSessionStorageMemcache extends JSessionStorage
{
	/**
	 * Resource for the current memcached connection.
	 *
	 * @var resource
	 */
	var $_db;

	/**
	 * Use compression?
	 *
	 * @var int
	 */
	var $_compress = null;

	/**
	 * Use persistent connections
	 *
	 * @var boolean
	 */
	var $_persistent = false;

	/**
	* Constructor
	*
	* @access protected
	* @param array $options optional parameters
	*/
	function __construct($options = array())
	{
		if (!$this->test()) {
			return JError::raiseError(404, JText::_('JLIB_SESSION_MEMCACHE_EXTENSION_NOT_AVAILABLE'));
		}

		parent::__construct($options);

		$config = JFactory::getConfig();
		$memcache_persist = $config->get('memcache_persist');
		$memcache_compress = $config->get('memcache_compress');
                $memcache_server_host = $config->get('memcache_server_host');
                $memcache_server_port = $config->get('memcache_server_port');

		$this->_compress	= (isset($memcache_compress)) ? $memcache_compress : 0;
		$this->_persistent	= (isset($memcache_persist)) ? $memcache_persist : false;

		// This will be an array of loveliness
                $server[] = array('host'=>$memcache_server_host,'port'=>$memcache_server_port);

		$this->_servers	= $server;
	}

	/**
	 * Open the SessionHandler backend.
	 *
	 * @access public
	 * @param string $save_path	The path to the session object.
	 * @param string $session_name  The name of the session.
	 * @return boolean  True on success, false otherwise.
	 */
	function open($save_path, $session_name)
	{
		$this->_db = new Memcache;
		for ($i=0, $n=count($this->_servers); $i < $n; $i++)
		{
			$server = $this->_servers[$i];
			$this->_db->addServer($server['host'], $server['port'], $this->_persistent);
		}
		return true;
	}

	/**
	 * Close the SessionHandler backend.
	 *
	 * @access public
	 * @return boolean  True on success, false otherwise.
	 */
	function close()
	{
		return $this->_db->close();
	}

	/**
	 * Read the data for a particular session identifier from the
	 * SessionHandler backend.
	 *
	 * @access public
	 * @param string $id  The session identifier.
	 * @return string  The session data.
	 */
	function read($id)
	{
		$sess_id = 'sess_'.$id;
		$this->_setExpire($sess_id);
		return $this->_db->get($sess_id);
	}

	/**
	 * Write session data to the SessionHandler backend.
	 *
	 * @access public
	 * @param string $id			The session identifier.
	 * @param string $session_data  The session data.
	 * @return boolean  True on success, false otherwise.
	 */
	function write($id, $session_data)
	{
		$sess_id = 'sess_'.$id;
		if ($this->_db->get($sess_id.'_expire')) {
			$this->_db->replace($sess_id.'_expire', time());
		} else {
			$this->_db->set($sess_id.'_expire', time(), 0);
		}
		if ($this->_db->get($sess_id)) {
			$this->_db->replace($sess_id, $session_data);
		} else {
			$this->_db->set($sess_id, $session_data, 0);
		}
		return;
	}

	/**
	 * Destroy the data for a particular session identifier in the
	 * SessionHandler backend.
	 *
	 * @access public
	 * @param string $id  The session identifier.
	 * @return boolean  True on success, false otherwise.
	 */
	function destroy($id)
	{
		$sess_id = 'sess_'.$id;
		$this->_db->delete($sess_id.'_expire');
		return $this->_db->delete($sess_id);
	}

	/**
	 * Garbage collect stale sessions from the SessionHandler backend.
	 *
	 *	-- Not Applicable in memcache --
	 *
	 * @access public
	 * @param integer $maxlifetime  The maximum age of a session.
	 * @return boolean  True on success, false otherwise.
	 */
	function gc($maxlifetime = null)
	{
		return true;
	}

	/**
	 * Test to see if the SessionHandler is available.
	 *
	 * @static
	 * @access public
	 * @return boolean  True on success, false otherwise.
	 */
	static function test()
	{
		return (extension_loaded('memcache') && class_exists('Memcache'));
	}

	/**
	 * Set expire time on each call since memcache sets it on cache creation.
	 *
	 * @access private
	 *
	 * @param string  $key		Cache key to expire.
	 * @param integer $lifetime  Lifetime of the data in seconds.
	 */
	function _setExpire($key)
	{
		$lifetime	= ini_get("session.gc_maxlifetime");
		$expire		= $this->_db->get($key.'_expire');

		// set prune period
		if ($expire + $lifetime < time()) {
			$this->_db->delete($key);
			$this->_db->delete($key.'_expire');
		} else {
			$this->_db->replace($key.'_expire', time());
		}
	}
}
