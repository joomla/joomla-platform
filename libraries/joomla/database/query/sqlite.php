<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Database
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * SQLite Query Building Class.
 *
 * @package     Joomla.Platform
 * @subpackage  Database
 * @since       12.1
 */
class JDatabaseQuerySqlite extends JDatabaseQueryPdo implements JDatabaseQueryPreparable, JDatabaseQueryLimitable
{
	/**
	 * @var integer
	 * @since 12.1
	 */
	protected $limit;

	/**
	 * @var integer
	 * @since 12.1
	 */
	protected $offset;

	/**
	 * @var mixed
	 * @since 12.1
	 */
	protected $bounded = array();

	/**
	 * Method to add a variable to an internal array that will be bound to a prepared SQL statement before query execution. Also
	 * removes a variable that has been bounded from the internal bounded array when the passed in value is null.
	 *
	 * @param   string|integer  $key            The key that will be used in your SQL query to reference the value. Usually of
	 *                                          the form ':key', but can also be an integer.
	 * @param   mixed           &$value         The value that will be bound. The value is passed by reference to support output
	 *                                          parameters such as those possible with stored procedures.
	 * @param   integer         $dataType       Constant corresponding to a SQL datatype.
	 * @param   integer         $length         The length of the variable. Usually required for OUTPUT parameters.
	 * @param   array           $driverOptions  Optional driver options to be used.
	 *
	 * @return  JDatabaseQuery
	 *
	 * @since   12.1
	 */
	public function bind($key = null, &$value = null, $dataType = PDO::PARAM_STR, $length = 0, $driverOptions = array())
	{
		// Case 1: Empty Key (reset $bounded array)
		if (empty($key))
		{
			$this->bounded = array();

			return $this;
		}

		// Case 2: Key Provided, null value (unset key from $bounded array)
		if (is_null($value))
		{
			if (isset($this->bounded[$key]))
			{
				unset($this->bounded[$key]);
			}

			return $this;
		}

		$obj = new stdClass;

		$obj->value = &$value;
		$obj->dataType = $dataType;
		$obj->length = $length;
		$obj->driverOptions = $driverOptions;

		// Case 3: Simply add the Key/Value into the bounded array
		$this->bounded[$key] = $obj;

		return $this;
	}

	/**
	 * Retrieves the bound parameters array when key is null and returns it by reference. If a key is provided then that item is
	 * returned.
	 *
	 * @param   mixed  $key  The bounded variable key to retrieve.
	 *
	 * @return  mixed
	 *
	 * @since   12.1
	 */
	public function &getBounded($key = null)
	{
		if (empty($key))
		{
			return $this->bounded;
		}
		else
		{
			if (isset($this->bounded[$key]))
			{
				return $this->bounded[$key];
			}
		}
	}

	/**
	 * Clear data from the query or a specific clause of the query.
	 *
	 * @param   string  $clause  Optionally, the name of the clause to clear, or nothing to clear the whole query.
	 *
	 * @return  JDatabaseQuery  Returns this object to allow chaining.
	 *
	 * @since   12.1
	 */
	public function clear($clause = null)
	{
		switch ($clause)
		{
			case null:
				$this->bounded = array();
				break;
		}

		parent::clear($clause);

		return $this;
	}

	/**
	 * Method to modify a query already in string format with the needed
	 * additions to make the query limited to a particular number of
	 * results, or start at a particular offset. This method is used
	 * automatically by the __toString() method if it detects that the
	 * query implements the JDatabaseQueryLimitable interface.
	 *
	 * @param   string   $query   The query in string format
	 * @param   integer  $limit   The limit for the result set
	 * @param   integer  $offset  The offset for the result set
	 *
	 * @return  string
	 *
	 * @since   12.1
	 */
	public function processLimit($query, $limit, $offset = 0)
	{
		if ($limit > 0 || $offset > 0)
		{
			$query .= ' LIMIT ' . $offset . ', ' . $limit;
		}

		return $query;
	}

	/**
	 * Sets the offset and limit for the result set, if the database driver supports it.
	 *
	 * Usage:
	 * $query->setLimit(100, 0); (retrieve 100 rows, starting at first record)
	 * $query->setLimit(50, 50); (retrieve 50 rows, starting at 50th record)
	 *
	 * @param   integer  $limit   The limit for the result set
	 * @param   integer  $offset  The offset for the result set
	 *
	 * @return  JDatabaseQuery  Returns this object to allow chaining.
	 *
	 * @since   12.1
	 */
	public function setLimit($limit = 0, $offset = 0)
	{
		$this->limit = (int) $limit;
		$this->offset = (int) $offset;

		return $this;
	}

	/**
	 * Generates a Globally Unique Identifier (32 hexadecimal digits separated by hyphens as 8-4-4-4-12).
	 * Since SQLLite has no native function to generate GUIDs, use the technique from here:
	 * http://comments.gmane.org/gmane.comp.db.sqlite.general/62514
	 *
	 * Usage:
	 * $query->set('guid = ' . $query->GUID());
	 *
	 * @return  string
	 *
	 * @since   12.3
	 */
	public function GUID()
	{
		$parts = array(
			'hex(randomblob(4))', "'-'",
			'hex(randomblob(2))', "'-'",
			"'4'", 'substr(hex(randomblob(2)), 2)', "'-'",
			'substr(\'AB89\', 1 + (abs(random()) % 4) , 1)', 'substr(hex(randomblob(2)), 2)', "'-'",
			'hex(randomblob(6))'
		);

		// Implode using the concatenation operator as glue.
		return implode(' || ', $parts);
	}
}
