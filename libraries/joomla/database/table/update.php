<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Database
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die();

jimport('joomla.database.table');

/**
 * Update table
 * Stores updates temporarily
 *
 * @package     Joomla.Platform
 * @subpackage  Table
 * @since       11.1
 */
class JTableUpdate extends JTable
{
	/**
	 * Contructor
	 *
	 * @param   database  &$db  A database connector object
	 *
	 * @return  JTableUpdate
	 *
	 * @since   11.1
	 */
	function __construct(&$db)
	{
		parent::__construct('#__updates', 'update_id', $db);
	}

	/**
	 * Overloaded check function
	 *
	 * @return  boolean  True if the object is ok
	 *
	 * @see     JTable:bind
	 * @since   11.1
	 */
	public function check()
	{
		// check for valid name
		if (trim($this->name) == '' || trim($this->element) == '')
		{
			$this->setError(JText::_('JLIB_DATABASE_ERROR_MUSTCONTAIN_A_TITLE_EXTENSION'));
			return false;
		}
		return true;
	}

	/**
	 * Overloaded bind function
	 *
	 * @param   array  $array   Named array
	 * @param   mixed  $ignore  An optional array or space separated list of properties
	 * to ignore while binding.
	 *
	 * @return  mixed  Null if operation was satisfactory, otherwise returns an error
	 *
	 * @see     JTable:bind
	 * @since   11.1
	 */
	public function bind($array, $ignore = '')
	{
		if (isset($array['params']) && is_array($array['params']))
		{
			$registry = new JRegistry;
			$registry->loadArray($array['params']);
			$array['params'] = (string) $registry;
		}

		if (isset($array['control']) && is_array($array['control']))
		{
			$registry = new JRegistry;
			$registry->loadArray($array['control']);
			$array['control'] = (string) $registry;
		}

		return parent::bind($array, $ignore);
	}

	/**
	 * Method to create and execute a SELECT WHERE query.
	 *
	 * @param   array  $options  Array of options
	 *
	 * @return  JDatabase object
	 *
	 * @since   11.1
	 */
	function find($options = array())
	{
		$dbo = JFactory::getDBO();
		$where = array();
		foreach ($options as $col => $val)
		{
			$where[] = $col . ' = ' . $dbo->Quote($val);
		}
		$query = 'SELECT update_id FROM #__updates WHERE ' . implode(' AND ', $where);
		$dbo->setQuery($query);
		return $dbo->loadResult();
	}
}
