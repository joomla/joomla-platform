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
 * OAuth Client class for the Joomla Platform
 *
 * @package     Joomla.Platform
 * @subpackage  OAuth1
 * @since       12.3
 */
class JOAuth1Client
{
	/**
	 * @var    JOAuth1TableClient  JTable object for persisting the client object.
	 * @since  12.3
	 */
	private $_table;

	/**
	 * Object constructor.
	 *
	 * @param   JOAuth1TableClient  $table       The JTable object to use when persisting the object.
	 * @param   array               $properties  A set of properties with which to prime the object.
	 *
	 * @codeCoverageIgnore
	 * @since   12.3
	 */
	public function __construct(JOAuth1TableClient $table = null, array $properties = null)
	{
		// Setup the table object.
		$this->_table = $table ? $table : JTable::getInstance('Client', 'JOAuth1Table');

		// Iterate over any input properties and bind them to the object.
		if ($properties)
		{
			foreach ($properties as $k => $v)
			{
				$this->_table->$k = $v;
			}
		}
	}

	/**
	 * Method to get a property value.
	 *
	 * @param   string  $p  The name of the property for which to return the value.
	 *
	 * @return  mixed  The property value for the given property name.
	 *
	 * @since   12.3
	 */
	public function __get($p)
	{
		if (isset($this->_table->$p))
		{
			return $this->_table->$p;
		}
	}

	/**
	 * Method to set a value for a property.
	 *
	 * @param   string  $p  The name of the property for which to set the value.
	 * @param   mixed   $v  The property value to set.
	 *
	 * @return  void
	 *
	 * @since   12.3
	 */
	public function __set($p, $v)
	{
		if (isset($this->_table->$p))
		{
			$this->_table->$p = $v;
		}
	}

	/**
	 * Method to create the client in the database.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   12.3
	 */
	public function create()
	{
		// Can't insert something that already has an ID.
		if ($this->_table->client_id)
		{
			return false;
		}

		// Ensure we don't have an id to insert... use the auto-incrementor instead.
		$this->_table->client_id = null;

		return $this->_table->store();
	}

	/**
	 * Method to delete the client from the database.
	 *
	 * @return  void
	 *
	 * @since   12.3
	 */
	public function delete()
	{
		$this->_table->delete();
	}

	/**
	 * Method to load a client by id.
	 *
	 * @param   integer  $clientId  The id of the client to load.
	 *
	 * @return  void
	 *
	 * @since   12.3
	 */
	public function load($clientId)
	{
		$this->_table->load($clientId);
	}

	/**
	 * Method to load a client by key.
	 *
	 * @param   string  $key  The key of the client to load.
	 *
	 * @return  void
	 *
	 * @since   12.3
	 */
	public function loadByKey($key)
	{
		$this->_table->loadByKey($key);
	}

	/**
	 * Method to update the client in the database.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   12.3
	 */
	public function update()
	{
		if (!$this->_table->client_id)
		{
			return false;
		}

		return $this->_table->store();
	}
}
