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
	 * @var    integer  The affected row limit for the current SQL statement.
	 * @since  12.1
	 */
	protected $limit = 0;

	/**
	 * @var    integer  The affected row offset to apply for the current SQL statement.
	 * @since  12.1
	 */
	protected $offset = 0;

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
	 * $iterator = new JDatabaseIterator($dbo->getQuery(true)->select('*')->from('#__content'));
	 * foreach ($iterator as $i => $row)
	 * {
	 *     var_dump($i, $row);
	 * } 
	 * </code>
	 *
	 * @param   JDatabaseQuery|string  $query    The query to execute
	 * @param   array                  $options  An array of options.  Available key are:
	 *                                           'dbo' for setting the database connector. Default is JFactory::getDbo()
	 *                                           'type' for the type of result ('array', 'assoc' or class name). Default is 'assoc',
	 *                                           'key' for the key used. Default is incremental integers.
	 *                                           'offset' for the affected row offset to set. Default is 0.
	 *                                           'limit' for the maximum affected rows to set. Default is 0.
	 *
	 * @throw   InvalidArgumentException
	 *
	 * @since   12.1
	 */
	public function __construct($query, array $options = array())
	{
		// Set the query
		$this->query = $query;

		// Set the dbo
		if (isset($options['dbo']))
		{
			if ($options['dbo'] instanceof JDatabase)
			{
				$this->dbo = $options['dbo'];
			}
			else
			{
				throw new InvalidArgumentException('The dbo must be an instance of JDatabase');
			}
		}
		else
		{
			$this->dbo = JFactory::getDbo();
		}

		// Set the type
		if (isset($options['type']))
		{
			if (in_array($options['type'], array('array', 'assoc')) || class_exists($options['type']))
			{
				$this->type = $options['type'];
			}
			else
			{
				throw new InvalidArgumentException("The type must be 'array', 'assoc' or an existing class name");
			}
		}
		else
		{
			$this->type = 'assoc';
		}

		// Set the key
		if (isset($options['key']))
		{
			if ($this->type == 'array')
			{
				if (is_int($options['key']))
				{
					$this->key = $options['key'];
				}
				else
				{
					throw new InvalidArgumentException("The key must be an integer if the type is equal to 'array'");
				}
			}
			else
			{
				if (is_string($options['key']))
				{
					$this->key = $options['key'];
				}
				else
				{
					throw new InvalidArgumentException("The key must be a string if the type is not equal to 'array'");
				}
			}
		}

		// Set the offset
		if (isset($options['offset']))
		{
			if (is_int($options['offset']))
			{
				$this->offset = $options['offset'];
			}
			else
			{
				throw new InvalidArgumentException('The offset must be an integer');
			}
		}
		else
		{
			$this->offset = 0;
		}

		// Set the limit
		if (isset($options['limit']))
		{
			if (is_int($options['limit']))
			{
				$this->limit = $options['limit'];
			}
			else
			{
				throw new InvalidArgumentException('The limit must be an integer');
			}
		}
		else
		{
			$this->limit = 0;
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
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function rewind()
	{
		// Release the database cursor
		$this->freeResult();

		// Set the query
		$this->dbo->setQuery($this->query, $this->offset, $this->limit);

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
	 * @return  mixed  The current row
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
	 * @return  scalar  The current key
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
	 * @return  void
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
	 * @return  bool  TRUE on success, FALSE on failure
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
	 * @return  void
	 *
	 * @since   12.1
	 */
	protected function freeResult()
	{
		if (!empty($this->cursor))
		{
			$this->dbo->freeResult($this->cursor);
			unset($this->cursor);
		}
	}
}
