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
	 * @var    JDatabase  The JDatabase object
	 * @since  12.1
	 */
	protected $dbo;

	/**
	 * @var    JDatabaseQuery|string  The database query
	 * @since  12.1
	 */
	protected $query;

	/**
	 * @var    string  The query type 'array'|'assoc'|className
	 * @since  12.1
	 */
	protected $type;

	/**
	 * @var    string|integer  The key used
	 * @since  12.1
	 */
	protected $key;

	/**
	 * @var    scalar  Current position
	 * @since  12.1
	 */
	protected $position;

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
	 * classic use is
	 *
	 * <code>
	 * $dbo = JFactory::getDbo();
	 * foreach (new JDatabaseIterator($dbo, $dbo->getQuery(true)->select('*')->from('#__content')) as $i => $row)
	 * {
	 *     var_dump($i, $row);
	 * } 
	 * </code>
	 *
	 * @param   JDatabase              $dbo      The database object
	 * @param   JDatabaseQuery|string  $query    The query to execute
	 * @param   array                  $options  An array of options.  Available key are:
	 *                                           'type' for the type of result ('array', 'assoc' or class name). Default is 'array',
	 *                                           'key' for the key used. Default is incremental integers.
	 *
	 * @since   12.1
	 */
	public function __construct(JDatabase $dbo, $query, array $options = array())
	{
		$this->dbo = $dbo;
		$this->query = $query;
		if (isset($options['type']))
		{
			$this->type = $options['type'];
		}
		else
		{
			$this->type = 'array';
		}
		if (isset($options['key']))
		{
			$this->key = $options['key'];
		}
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
		$this->freeResult();
	}

	/**
	 * Rewind the iterator to the first row
	 *
	 * @since   12.1
	 */
	public function rewind()
	{
		// Release the database cursor
		$this->freeResult();

		// Set the query
		$this->dbo->setQuery($this->query);

		// Run the query
		$this->cursor = $this->dbo->query();

		// Initialise the position
		unset($this->position);

		// Get the first row
		$this->next();
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
		// Get the next result
		if ($this->type == 'array')
		{
			$this->result = $this->dbo->fetchArray($this->cursor);
		}
		elseif ($this->type == 'assoc')
		{
			$this->result = $this->dbo->fetchAssoc($this->cursor);
		}
		else
		{
			$this->result = $this->dbo->fetchObject($this->cursor, $this->type);
		}

		// If there is a result
		if ($this->result)
		{
			// Get the next position
			if (isset($this->key))
			{
				// If a key was givern
				if ($this->type == 'array' || $this->type == 'assoc')
				{
					// Get the next position using the result array
					$this->position = $this->result[$this->key];
				}
				else
				{
					// Get the next position using the result object
					$this->position = $this->result->{$this->key};
				}
			}
			elseif (isset($this->position))
			{
				// Increment current position
				$this->position++;
			}
			else
			{
				// Initialise position to 0
				$this->position = 0;
			}
		}
    }

	/**
	 * Checks if the current position is valid
	 *
	 * @since   12.1
	 */
	public function valid()
	{
		// If there is no result, the validation failed
		if ($this->result)
		{
			return true;
		}
		else
		{
			$this->freeResult();
			return false;
		}
    }

	/**
	 * Release the cursor if it is set
	 *
	 * @since   12.1
	 */
	protected function freeResult()
	{
		if (!empty($this->cursor))
		{
			$this->dbo->freeResult($this->cursor);
		}
	}
}

