<?php
/**
 * @package     Joomla.Platform
 * @subpackage  OAuth1
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * OAuth1 Client Table
 *
 * @package     Joomla.Platform
 * @subpackage  OAuth1
 * @since       12.3
 */
class JOAuth1TableCredentials extends JTable
{
	/**
	 * Constructor
	 *
	 * @param   JDatabaseDriver  $db  Database driver object.
	 *
	 * @since   12.3
	 */
	public function __construct($db)
	{
		parent::__construct('#__oauth_credentials', 'credentials_id', $db);
	}

	/**
	 * Delete expired credentials.
	 *
	 * @return  void
	 *
	 * @since   12.3
	 */
	public function clean()
	{
		// Build the query to delete the rows from the database.
		$query = $this->_db->getQuery(true);
		$query->delete('#__oauth_credentials')
			->where(array('expiration_date < ' . time(), 'expiration_date > 0'), 'AND')
			->where(array('temporary_expiration_date < ' . time(), 'temporary_expiration_date > 0'), 'AND');

		// Set and execute the query.
		$this->_db->setQuery($query);
		$this->_db->execute();
	}

	/**
	 * Load the credentials by key.
	 *
	 * @param   string  $key  The key for which to load the credentials.
	 *
	 * @return  void
	 *
	 * @since 12.3
	 */
	public function loadByKey($key)
	{
		// Build the query to load the row from the database.
		$query = $this->_db->getQuery(true);
		$query->select('*')
		->from('#__oauth_credentials')
		->where($this->_db->quoteName('key') . ' = ' . $this->_db->quote($key));

		// Set and execute the query.
		$this->_db->setQuery($query);
		$properties = $this->_db->loadAssoc();

		// Iterate over any the loaded properties and bind them to the object.
		if ($properties)
		{
			foreach ($properties as $k => $v)
			{
				$this->$k = $v;
			}
		}
	}
}
