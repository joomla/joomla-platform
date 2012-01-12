<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Session
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Database session storage handler for PHP
 *
 * @package     Joomla.Platform
 * @subpackage  Session
 * @see         http://www.php.net/manual/en/function.session-set-save-handler.php
 * @since       11.1
 */
class JSessionStorageDatabase extends JSessionStorage
{
	/**
	 * @var    unknown  No idea what this does.
	 * @since  11.1
	 */
	protected $_data = null;

	/**
	 * Open the SessionHandler backend.
	 *
	 * @param   string  $save_path     The path to the session object.
	 * @param   string  $session_name  The name of the session.
	 *
	 * @return  boolean  True on success, false otherwise.
	 *
	 * @since   11.1
	 */
	public function open($save_path, $session_name)
	{
		return true;
	}

	/**
	 * Close the SessionHandler backend.
	 *
	 * @return  boolean  True on success, false otherwise.
	 *
	 * @since   11.1
	 */
	public function close()
	{
		return true;
	}

	/**
	 * Read the data for a particular session identifier from the SessionHandler backend.
	 *
	 * @param   string  $id  The session identifier.
	 *
	 * @return  string  The session data.
	 *
	 * @since   11.1
	 */
	public function read($id)
	{
		// Get the database connection object and verify its connected.
		$db = JFactory::getDbo();
		if (!$db->connected())
		{
			return false;
		}

		// Get the session data from the database table.
		$query = $db->getQuery(true);
		$query->select($db->quoteName('data'))
			->from($db->quoteName('#__session'))
			->where($db->quoteName('session_id') . ' = ' . $db->quote($id));

		$db->setQuery($query);

		return (string) $db->loadResult();
	}

	/**
	 * Write session data to the SessionHandler backend.
	 *
	 * @param   string  $id    The session identifier.
	 * @param   string  $data  The session data.
	 *
	 * @return  boolean  True on success, false otherwise.
	 *
	 * @since   11.1
	 */
	public function write($id, $data)
	{
		// Get the database connection object and verify its connected.
		$db = JFactory::getDbo();
		if (!$db->connected())
		{
			return false;
		}

		// Try to update the session data in the database table.
		$db->setQuery(
			'UPDATE ' . $db->quoteName('#__session') .
			' SET ' . $db->quoteName('data') . ' = ' . $db->quote($data) . ',' . '	  ' . $db->quoteName('time') . ' = ' . (int) time() .
			' WHERE ' . $db->quoteName('session_id') . ' = ' . $db->quote($id)
		);
		if (!$db->query())
		{
			return false;
		}

		if ($db->getAffectedRows())
		{
			return true;
		}
		else
		{
            // update did not error, so it succeeded but was identical
            if (JDEBUG) {
                jimport('joomla.error.log');
                $log = JLog::getInstance('jcontroller.log.php')->addEntry(
                    array(
                        'comment' => sprintf('Session ID %s update is identical to current record', $id)
                    )
                );
            }
            return true;
		}
	}

	/**
	 * Destroy the data for a particular session identifier in the SessionHandler backend.
	 *
	 * @param   string  $id  The session identifier.
	 *
	 * @return  boolean  True on success, false otherwise.
	 *
	 * @since   11.1
	 */
	public function destroy($id)
	{
		// Get the database connection object and verify its connected.
		$db = JFactory::getDbo();
		if (!$db->connected())
		{
			return false;
		}

		// Remove a session from the database.
		$db->setQuery(
			'DELETE FROM ' . $db->quoteName('#__session') .
			' WHERE ' . $db->quoteName('session_id') . ' = ' . $db->quote($id)
		);

		return (boolean) $db->query();
	}

	/**
	 * Garbage collect stale sessions from the SessionHandler backend.
	 *
	 * @param   integer  $lifetime  The maximum age of a session.
	 *
	 * @return  boolean  True on success, false otherwise.
	 *
	 * @since   11.1
	 */
	public function gc($lifetime = 1440)
	{
		// Get the database connection object and verify its connected.
		$db = JFactory::getDbo();
		if (!$db->connected())
		{
			return false;
		}

		// Determine the timestamp threshold with which to purge old sessions.
		$past = time() - $lifetime;

		// Remove expired sessions from the database.
		$db->setQuery(
			'DELETE FROM ' . $db->quoteName('#__session') .
			' WHERE ' . $db->quoteName('time') . ' < ' . (int) $past
		);

		return (boolean) $db->query();
	}
}
