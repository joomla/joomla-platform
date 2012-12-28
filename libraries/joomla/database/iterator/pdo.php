<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Database
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * PDO database iterator.
 *
 * @package     Joomla.Platform
 * @subpackage  Database
 * @since       12.1
 */
class JDatabaseIteratorPdo extends JDatabaseIterator
{
	/**
	 * Get the number of rows in the result set for the executed SQL given by the cursor.
	 *
	 * @return  integer  The number of rows in the result set.
	 *
	 * @since   12.1
	 * @see     Countable::count()
	 */
	public function count()
	{
		if (!empty($this->cursor) && $this->cursor instanceof \PDOStatement)
		{
			return $this->cursor->rowCount();
		}
		else
		{
			return 0;
		}
	}

	/**
	 * Method to fetch a row from the result set cursor as an object.
	 *
	 * @return  mixed   Either the next row from the result set or false if there are no more rows.
	 *
	 * @since   12.1
	 */
	protected function fetchObject()
	{
		if (!empty($this->cursor) && $this->cursor instanceof \PDOStatement)
		{
			return $this->cursor->fetchObject($this->class);
		}
		else
		{
			return false;
		}
	}

	/**
	 * Method to free up the memory used for the result set.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	protected function freeResult()
	{
		if (!empty($this->cursor) && $this->cursor instanceof \PDOStatement)
		{
			$this->cursor->closeCursor();
		}
	}
}
