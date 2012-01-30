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
 * Database Iterator Class.
 *
 * @package     Joomla.Platform
 * @subpackage  Database
 * @since       12.1
 */
class JDatabaseIterator implements Iterator
{
	/**
	 * @var    JDatabase
	 * @since  12.1
	 */
	protected $dbo;

	/**
	 * @var    JDatabaseQuery|string
	 * @since  12.1
	 */
	protected $query;

	/**
	 * @var    'array'|'assoc'|className
	 * @since  12.1
	 */
	protected $type;

	/**
	 * @var    int  Current position
	 * @since  12.1
	 */
	protected $position = 0;

	/**
	 * @var    mixed  The result set cursor from which to fetch the rows.
	 */
	protected $cursor;

	/**
	 * @var    mixed  The current row.
	 */
	protected $result;

	/**
	 * Constructor.
	 *
	 * @param   JDatabase              $dbo      The database object
	 * @param   JDatabaseQuery|string  $query    The query to execute
	 * @param   string                 $type     The type of result ('array', 'assoc' or class name).
	 *
	 * @since   12.1
	 */
	public function __construct(JDatabase $dbo, $query, $type = 'array')
	{
		$this->dbo = $dbo;
		$this->query = $query;
		$this->type = $type;
	}

	/**
	 * Destructor.
	 *
	 * The destructor releases the database cursor.
	 *
	 * @since   12.1
	 */
	public function __destruct()
	{
		$this->releaseCursor();
	}

	/**
	 * Rewind the iterator to the first row
	 *
	 * @since   12.1
	 */
	public function rewind()
	{
		$this->releaseCursor();
		$this->dbo->setQuery($this->query);
		$this->cursor = $this->dbo->query();
		$this->result = $this->fetch();
		$this->position = 0;
    }

	/**
	 * Return the current row
	 *
	 * @since   12.1
	 */
	public function current()
	{
		return $this->result;
	}

	/**
	 * Return the position of the current row
	 *
	 * @since   12.1
	 */
	public function key()
	{
		return $this->position;
	}

	/**
	 * Move forward to next row
	 *
	 * @since   12.1
	 */
	public function next()
	{
		$this->result = $this->fetch();
		$this->position++;
    }

	/**
	 * Checks if the current position is valid
	 *
	 * @since   12.1
	 */
	public function valid()
	{
		if (!$this->result)
		{
			$this->releaseCursor();
			return false;
		}
		else
		{
			return true;
		}
    }

	/**
	 * Return the next row
	 *
	 * @since   12.1
	 */
	protected function fetch()
	{
		if ($this->type == 'array')
		{
			return $this->dbo->fetchArray($this->cursor);
		}
		elseif ($this->type == 'assoc')
		{
			return $this->dbo->fetchAssoc($this->cursor);
		}
		else
		{
			return $this->dbo->fetchObject($this->cursor, $this->type);
		}
	}

	/**
	 * Release the cursor if it is set
	 *
	 * @since   12.1
	 */
	protected function releaseCursor()
	{
		if (isset($this->cursor))
		{
			$this->dbo->freeResult($this->cursor);
		}
	}
}

