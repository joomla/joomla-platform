<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Database
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Query Element Class.
 *
 * @package     Joomla.Platform
 * @subpackage  Database
 * @since       11.1
 */
class JDatabaseQueryElement
{
	/**
	 * @var    string  The name of the element.
	 * @since  11.1
	 */
	protected $name = null;

	/**
	 * @var    array  An array of elements.
	 * @since  11.1
	 */
	protected $elements = null;

	/**
	 * @var    string  Glue piece.
	 * @since  11.1
	 */
	protected $glue = null;

	/**
	 * Constructor.
	 *
	 * @param   string  $name      The name of the element.
	 * @param   mixed   $elements  String or array.
	 * @param   string  $glue      The glue for elements.
	 *
	 * @since   11.1
	 */
	public function __construct($name, $elements, $glue = ',')
	{
		$this->elements = array();
		$this->name = $name;
		$this->glue = $glue;

		$this->append($elements);
	}

	/**
	 * Magic function to convert the query element to a string.
	 *
	 * @return  string
	 *
	 * @since   11.1
	 */
	public function __toString()
	{
		if (substr($this->name, -2) == '()')
		{
			return PHP_EOL . substr($this->name, 0, -2) . '(' . implode($this->glue, $this->elements) . ')';
		}
		else
		{
			return PHP_EOL . $this->name . ' ' . implode($this->glue, $this->elements);
		}
	}

	/**
	 * Appends element parts to the internal list.
	 *
	 * @param   mixed  $elements  String or array.
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	public function append($elements)
	{
		if (is_array($elements))
		{
			$this->elements = array_merge($this->elements, $elements);
		}
		else
		{
			$this->elements = array_merge($this->elements, array($elements));
		}
	}

	/**
	 * Gets the elements of this element.
	 *
	 * @return  string
	 *
	 * @since   11.1
	 */
	public function getElements()
	{
		return $this->elements;
	}
}

/**
 * Query Building Class.
 *
 * @package     Joomla.Platform
 * @subpackage  Database
 * @since       11.1
 */
abstract class JDatabaseQuery
{
	/**
	 * @var    resource  The database connection resource.
	 * @since  11.1
	 */
	protected $db;

	/**
	 * @var    string  The SQL query (if a direct query string was provided).
	 * @since  11.4
	 */
	protected $sql;

	/**
	 * @var    string  The query type.
	 * @since  11.1
	 */
	protected $type = '';

	/**
	 * @var    string  The query element for a generic query (type = null).
	 * @since  11.1
	 */
	protected $element;

	/**
	 * @var    object  The select element.
	 * @since  11.1
	 */
	protected $select;

	/**
	 * @var    object  The delete element.
	 * @since  11.1
	 */
	protected $delete;

	/**
	 * @var    object  The update element.
	 * @since  11.1
	 */
	protected $update;

	/**
	 * @var    object  The insert element.
	 * @since  11.1
	 */
	protected $insert;

	/**
	 * @var    object  The from element.
	 * @since  11.1
	 */
	protected $from;

	/**
	 * @var    object  The join element.
	 * @since  11.1
	 */
	protected $join;

	/**
	 * @var    object  The set element.
	 * @since  11.1
	 */
	protected $set;

	/**
	 * @var    object  The where element.
	 * @since  11.1
	 */
	protected $where;

	/**
	 * @var    object  The group by element.
	 * @since  11.1
	 */
	protected $group;

	/**
	 * @var    object  The having element.
	 * @since  11.1
	 */
	protected $having;

	/**
	 * @var    object  The column list for an INSERT statement.
	 * @since  11.1
	 */
	protected $columns;

	/**
	 * @var    object  The values list for an INSERT statement.
	 * @since  11.1
	 */
	protected $values;

	/**
	 * @var    object  The order element.
	 * @since  11.1
	 */
	protected $order;

	/**
	 * @var    object  The call element.
	 * @since  11.4
	 */
	protected $call;

	/**
	 * @var    object  The exec element.
	 * @since  11.4
	 */
	protected $exec;

	/**
	 * Magic method to provide method alias support for quote() and quoteName().
	 *
	 * @param   string  $method  The called method.
	 * @param   array   $args    The array of arguments passed to the method.
	 *
	 * @return  string  The aliased method's return value or null.
	 *
	 * @since   11.1
	 */
	public function __call($method, $args)
	{
		if (empty($args))
		{
			return;
		}

		switch ($method)
		{
			case 'q':
				return $this->quote($args[0], isset($args[1]) ? $args[1] : true);
				break;

			case 'qn':
				return $this->quoteName($args[0]);
				break;

			case 'e':
				return $this->escape($args[0], isset($args[1]) ? $args[1] : false);
				break;
		}
	}

	/**
	 * Class constructor.
	 *
	 * @param   JDatabase  $db  The database connector resource.
	 *
	 * @since   11.1
	 */
	public function __construct(JDatabase $db = null)
	{
		$this->db = $db;
	}

	/**
	 * Magic function to convert the query to a string.
	 *
	 * @return  string	The completed query.
	 *
	 * @since   11.1
	 */
	public function __toString()
	{
		$query = '';

		if ($this->sql)
		{
			return $this->sql;
		}

		switch ($this->type)
		{
			case 'element':
				$query .= (string) $this->element;
				break;

			case 'select':
				$query .= (string) $this->select;
				$query .= (string) $this->from;
				if ($this->join)
				{
					// special case for joins
					foreach ($this->join as $join)
					{
						$query .= (string) $join;
					}
				}

				if ($this->where)
				{
					$query .= (string) $this->where;
				}

				if ($this->group)
				{
					$query .= (string) $this->group;
				}

				if ($this->having)
				{
					$query .= (string) $this->having;
				}

				if ($this->order)
				{
					$query .= (string) $this->order;
				}

				break;

			case 'delete':
				$query .= (string) $this->delete;
				$query .= (string) $this->from;

				if ($this->join)
				{
					// special case for joins
					foreach ($this->join as $join)
					{
						$query .= (string) $join;
					}
				}

				if ($this->where)
				{
					$query .= (string) $this->where;
				}

				break;

			case 'update':
				$query .= (string) $this->update;
				$query .= (string) $this->set;

				if ($this->where)
				{
					$query .= (string) $this->where;
				}

				break;

			case 'insert':
				$query .= (string) $this->insert;

				// Set method
				if ($this->set)
				{
					$query .= (string) $this->set;
				}
				// Columns-Values method
				elseif ($this->values)
				{
					if ($this->columns)
					{
						$query .= (string) $this->columns;
					}

					$query .= ' VALUES ';
					$query .= (string) $this->values;
				}

				break;

			case 'call':
				$query .= (string) $this->call;
				break;

			case 'exec':
				$query .= (string) $this->exec;
				break;
		}

		if ($this instanceof JDatabaseQueryLimitable)
		{
			$query = $this->processLimit($query, $this->limit, $this->offset);
		}

		return $query;
	}

	/**
	 * Magic function to get protected variable value
	 *
	 * @param   string  $name  The name of the variable.
	 *
	 * @return  mixed
	 *
	 * @since   11.1
	 */
	public function __get($name)
	{
		return isset($this->$name) ? $this->$name : null;
	}

	/**
	 * Add a single column, or array of columns to the CALL clause of the query.
	 *
	 * Note that you must not mix insert, update, delete and select method calls when building a query.
	 * The call method can, however, be called multiple times in the same query.
	 *
	 * Usage:
	 * $query->call('a.*')->call('b.id');
	 * $query->call(array('a.*', 'b.id'));
	 *
	 * @param   mixed  $columns  A string or an array of field names.
	 *
	 * @return  JDatabaseQuery  Returns this object to allow chaining.
	 *
	 * @since   11.4
	 */
	public function call($columns)
	{
		$this->type = 'call';

		if (is_null($this->call))
		{
			$this->call = new JDatabaseQueryElement('CALL', $columns);
		}
		else
		{
			$this->call->append($columns);
		}

		return $this;
	}

	/**
	 * Casts a value to a char.
	 *
	 * Ensure that the value is properly quoted before passing to the method.
	 *
	 * Usage:
	 * $query->select($query->castAsChar('a'));
	 *
	 * @param   string  $value  The value to cast as a char.
	 *
	 * @return  string  Returns the cast value.
	 *
	 * @since   11.1
	 */
	public function castAsChar($value)
	{
		return $value;
	}

	/**
	 * Gets the number of characters in a string.
	 *
	 * Note, use 'length' to find the number of bytes in a string.
	 *
	 * Usage:
	 * $query->select($query->charLength('a'));
	 *
	 * @param   string  $field  A value.
	 *
	 * @return  string  The required char length call.
	 *
	 * @since 11.1
	 */
	public function charLength($field)
	{
		return 'CHAR_LENGTH(' . $field . ')';
	}

	/**
	 * Clear data from the query or a specific clause of the query.
	 *
	 * @param   string  $clause  Optionally, the name of the clause to clear, or nothing to clear the whole query.
	 *
	 * @return  JDatabaseQuery  Returns this object to allow chaining.
	 *
	 * @since   11.1
	 */
	public function clear($clause = null)
	{
		$this->sql = null;

		switch ($clause)
		{
			case 'select':
				$this->select = null;
				$this->type = null;
				break;

			case 'delete':
				$this->delete = null;
				$this->type = null;
				break;

			case 'update':
				$this->update = null;
				$this->type = null;
				break;

			case 'insert':
				$this->insert = null;
				$this->type = null;
				break;

			case 'from':
				$this->from = null;
				break;

			case 'join':
				$this->join = null;
				break;

			case 'set':
				$this->set = null;
				break;

			case 'where':
				$this->where = null;
				break;

			case 'group':
				$this->group = null;
				break;

			case 'having':
				$this->having = null;
				break;

			case 'order':
				$this->order = null;
				break;

			case 'columns':
				$this->columns = null;
				break;

			case 'values':
				$this->values = null;
				break;

			case 'exec':
				$this->exec = null;
				$this->type = null;
				break;

			case 'call':
				$this->call = null;
				$this->type = null;
				break;

			case 'limit':
				$this->offset = 0;
				$this->limit = 0;
				break;

			case null:
				$this->type = null;
				$this->select = null;
				$this->delete = null;
				$this->update = null;
				$this->insert = null;
				$this->from = null;
				$this->join = null;
				$this->set = null;
				$this->where = null;
				$this->group = null;
				$this->having = null;
				$this->order = null;
				$this->columns = null;
				$this->values = null;
				$this->exec = null;
				$this->call = null;
				$this->offset = 0;
				$this->limit = 0;
				break;
		}

		return $this;
	}

	/**
	 * Adds a column, or array of column names that would be used for an INSERT INTO statement.
	 *
	 * @param   mixed  $columns  A column name, or array of column names.
	 *
	 * @return  JDatabaseQuery  Returns this object to allow chaining.
	 *
	 * @since   11.1
	 */
	function columns($columns)
	{
		if (is_null($this->columns))
		{
			$this->columns = new JDatabaseQueryElement('()', $columns);
		}
		else
		{
			$this->columns->append($columns);
		}

		return $this;
	}

	/**
	 * Concatenates an array of column names or values.
	 *
	 * Usage:
	 * $query->select($query->concatenate(array('a', 'b')));
	 *
	 * @param   array   $values     An array of values to concatenate.
	 * @param   string  $separator  As separator to place between each value.
	 *
	 * @return  string  The concatenated values.
	 *
	 * @since   11.1
	 */
	function concatenate($values, $separator = null)
	{
		if ($separator)
		{
			return 'CONCATENATE(' . implode(' || ' . $this->quote($separator) . ' || ', $values) . ')';
		}
		else
		{
			return 'CONCATENATE(' . implode(' || ', $values) . ')';
		}
	}

	/**
	 * Gets the current date and time.
	 *
	 * Usage:
	 * $query->where('published_up < '.$query->currentTimestamp());
	 *
	 * @return  string
	 *
	 * @since   11.1
	 */
	function currentTimestamp()
	{
		return 'CURRENT_TIMESTAMP()';
	}

	/**
	 * Returns a PHP date() function compliant date format for the database driver.
	 *
	 * This method is provided for use where the query object is passed to a function for modification.
	 * If you have direct access to the database object, it is recommended you use the getDateFormat method directly.
	 *
	 * @return  string  The format string.
	 *
	 * @since   11.1
	 */
	public function dateFormat()
	{
		if (!($this->db instanceof JDatabase))
		{
			throw new RuntimeException('JLIB_DATABASE_ERROR_INVALID_DB_OBJECT');
		}

		return $this->db->getDateFormat();
	}

	/**
	 * Creates a formatted dump of the query for debugging purposes.
	 *
	 * Usage:
	 * echo $query->dump();
	 *
	 * @return  string
	 *
	 * @since   11.3
	 */
	public function dump()
	{
		return '<pre class="jdatabasequery">' . str_replace('#__', $this->db->getPrefix(), $this) . '</pre>';
	}

	/**
	 * Add a table name to the DELETE clause of the query.
	 *
	 * Note that you must not mix insert, update, delete and select method calls when building a query.
	 *
	 * Usage:
	 * $query->delete('#__a')->where('id = 1');
	 *
	 * @param   string  $table  The name of the table to delete from.
	 *
	 * @return  JDatabaseQuery  Returns this object to allow chaining.
	 *
	 * @since   11.1
	 */
	public function delete($table = null)
	{
		$this->type = 'delete';
		$this->delete = new JDatabaseQueryElement('DELETE', null);

		if (!empty($table))
		{
			$this->from($table);
		}

		return $this;
	}

	/**
	 * Method to escape a string for usage in an SQL statement.
	 *
	 * This method is provided for use where the query object is passed to a function for modification.
	 * If you have direct access to the database object, it is recommended you use the escape method directly.
	 *
	 * Note that 'e' is an alias for this method as it is in JDatabase.
	 *
	 * @param   string   $text   The string to be escaped.
	 * @param   boolean  $extra  Optional parameter to provide extra escaping.
	 *
	 * @return  string  The escaped string.
	 *
	 * @since   11.1
	 * @throws  DatabaseError if the internal db property is not a valid object.
	 */
	public function escape($text, $extra = false)
	{
		if (!($this->db instanceof JDatabase))
		{
			throw new RuntimeException('JLIB_DATABASE_ERROR_INVALID_DB_OBJECT');
		}

		return $this->db->escape($text, $extra);
	}

	/**
	 * Add a single column, or array of columns to the EXEC clause of the query.
	 *
	 * Note that you must not mix insert, update, delete and select method calls when building a query.
	 * The exec method can, however, be called multiple times in the same query.
	 *
	 * Usage:
	 * $query->exec('a.*')->exec('b.id');
	 * $query->exec(array('a.*', 'b.id'));
	 *
	 * @param   mixed  $columns  A string or an array of field names.
	 *
	 * @return  JDatabaseQuery  Returns this object to allow chaining.
	 *
	 * @since   11.4
	 */
	public function exec($columns)
	{
		$this->type = 'exec';

		if (is_null($this->exec))
		{
			$this->exec = new JDatabaseQueryElement('EXEC', $columns);
		}
		else
		{
			$this->exec->append($columns);
		}

		return $this;
	}

	/**
	 * Add a table to the FROM clause of the query.
	 *
	 * Note that while an array of tables can be provided, it is recommended you use explicit joins.
	 *
	 * Usage:
	 * $query->select('*')->from('#__a');
	 *
	 * @param   mixed  $tables  A string or array of table names.
	 *
	 * @return  JDatabaseQuery  Returns this object to allow chaining.
	 *
	 * @since   11.1
	 */
	public function from($tables)
	{
		if (is_null($this->from))
		{
			$this->from = new JDatabaseQueryElement('FROM', $tables);
		}
		else
		{
			$this->from->append($tables);
		}

		return $this;
	}

	/**
	 * Add a grouping column to the GROUP clause of the query.
	 *
	 * Usage:
	 * $query->group('id');
	 *
	 * @param   mixed  $columns  A string or array of ordering columns.
	 *
	 * @return  JDatabaseQuery  Returns this object to allow chaining.
	 *
	 * @since   11.1
	 */
	public function group($columns)
	{
		if (is_null($this->group))
		{
			$this->group = new JDatabaseQueryElement('GROUP BY', $columns);
		}
		else
		{
			$this->group->append($columns);
		}

		return $this;
	}

	/**
	 * A conditions to the HAVING clause of the query.
	 *
	 * Usage:
	 * $query->group('id')->having('COUNT(id) > 5');
	 *
	 * @param   mixed   $conditions  A string or array of columns.
	 * @param   string  $glue        The glue by which to join the conditions. Defaults to AND.
	 *
	 * @return  JDatabaseQuery  Returns this object to allow chaining.
	 *
	 * @since   11.1
	 */
	public function having($conditions, $glue = 'AND')
	{
		if (is_null($this->having))
		{
			$glue = strtoupper($glue);
			$this->having = new JDatabaseQueryElement('HAVING', $conditions, " $glue ");
		}
		else
		{
			$this->having->append($conditions);
		}

		return $this;
	}

	/**
	 * Add an INNER JOIN clause to the query.
	 *
	 * Usage:
	 * $query->innerJoin('b ON b.id = a.id')->innerJoin('c ON c.id = b.id');
	 *
	 * @param   string  $condition  The join condition.
	 *
	 * @return  JDatabaseQuery  Returns this object to allow chaining.
	 *
	 * @since   11.1
	 */
	public function innerJoin($condition)
	{
		$this->join('INNER', $condition);

		return $this;
	}

	/**
	 * Add a table name to the INSERT clause of the query.
	 *
	 * Note that you must not mix insert, update, delete and select method calls when building a query.
	 *
	 * Usage:
	 * $query->insert('#__a')->set('id = 1');
	 * $query->insert('#__a)->columns('id, title')->values('1,2')->values->('3,4');
	 * $query->insert('#__a)->columns('id, title')->values(array('1,2', '3,4'));
	 *
	 * @param   mixed  $table  The name of the table to insert data into.
	 *
	 * @return  JDatabaseQuery  Returns this object to allow chaining.
	 *
	 * @since   11.1
	 */
	public function insert($table)
	{
		$this->type = 'insert';
		$this->insert = new JDatabaseQueryElement('INSERT INTO', $table);

		return $this;
	}

	/**
	 * Add a JOIN clause to the query.
	 *
	 * Usage:
	 * $query->join('INNER', 'b ON b.id = a.id);
	 *
	 * @param   string  $type        The type of join. This string is prepended to the JOIN keyword.
	 * @param   string  $conditions  A string or array of conditions.
	 *
	 * @return  JDatabaseQuery  Returns this object to allow chaining.
	 *
	 * @since   11.1
	 */
	public function join($type, $conditions)
	{
		if (is_null($this->join))
		{
			$this->join = array();
		}
		$this->join[] = new JDatabaseQueryElement(strtoupper($type) . ' JOIN', $conditions);

		return $this;
	}

	/**
	 * Add a LEFT JOIN clause to the query.
	 *
	 * Usage:
	 * $query->leftJoin('b ON b.id = a.id')->leftJoin('c ON c.id = b.id');
	 *
	 * @param   string  $condition  The join condition.
	 *
	 * @return  JDatabaseQuery  Returns this object to allow chaining.
	 *
	 * @since   11.1
	 */
	public function leftJoin($condition)
	{
		$this->join('LEFT', $condition);

		return $this;
	}

	/**
	 * Get the length of a string in bytes.
	 *
	 * Note, use 'charLength' to find the number of characters in a string.
	 *
	 * Usage:
	 * query->where($query->length('a').' > 3');
	 *
	 * @param   string  $value  The string to measure.
	 *
	 * @return  int
	 *
	 * @since   11.1
	 */
	function length($value)
	{
		return 'LENGTH(' . $value . ')';
	}

	/**
	 * Get the null or zero representation of a timestamp for the database driver.
	 *
	 * This method is provided for use where the query object is passed to a function for modification.
	 * If you have direct access to the database object, it is recommended you use the nullDate method directly.
	 *
	 * Usage:
	 * $query->where('modified_date <> '.$query->nullDate());
	 *
	 * @param   boolean  $quoted  Optionally wraps the null date in database quotes (true by default).
	 *
	 * @return  string  Null or zero representation of a timestamp.
	 *
	 * @since   11.1
	 */
	public function nullDate($quoted = true)
	{
		if (!($this->db instanceof JDatabase))
		{
			throw new RuntimeException('JLIB_DATABASE_ERROR_INVALID_DB_OBJECT');
		}

		$result = $this->db->getNullDate($quoted);

		if ($quoted)
		{
			return $this->db->quote($result);
		}

		return $result;
	}

	/**
	 * Add a ordering column to the ORDER clause of the query.
	 *
	 * Usage:
	 * $query->order('foo')->order('bar');
	 * $query->order(array('foo','bar'));
	 *
	 * @param   mixed  $columns  A string or array of ordering columns.
	 *
	 * @return  JDatabaseQuery  Returns this object to allow chaining.
	 *
	 * @since   11.1
	 */
	public function order($columns)
	{
		if (is_null($this->order))
		{
			$this->order = new JDatabaseQueryElement('ORDER BY', $columns);
		}
		else
		{
			$this->order->append($columns);
		}

		return $this;
	}

	/**
	 * Add an OUTER JOIN clause to the query.
	 *
	 * Usage:
	 * $query->outerJoin('b ON b.id = a.id')->outerJoin('c ON c.id = b.id');
	 *
	 * @param   string  $condition  The join condition.
	 *
	 * @return  JDatabaseQuery  Returns this object to allow chaining.
	 *
	 * @since   11.1
	 */
	public function outerJoin($condition)
	{
		$this->join('OUTER', $condition);

		return $this;
	}

	/**
	 * Method to quote and optionally escape a string to database requirements for insertion into the database.
	 *
	 * This method is provided for use where the query object is passed to a function for modification.
	 * If you have direct access to the database object, it is recommended you use the quote method directly.
	 *
	 * Note that 'q' is an alias for this method as it is in JDatabase.
	 *
	 * Usage:
	 * $query->quote('fulltext');
	 * $query->q('fulltext');
	 *
	 * @param   string   $text    The string to quote.
	 * @param   boolean  $escape  True to escape the string, false to leave it unchanged.
	 *
	 * @return  string  The quoted input string.
	 *
	 * @since   11.1
	 * @throws  DatabaseError if the internal db property is not a valid object.
	 */
	public function quote($text, $escape = true)
	{
		if (!($this->db instanceof JDatabase))
		{
			throw new RuntimeException('JLIB_DATABASE_ERROR_INVALID_DB_OBJECT');
		}

		return $this->db->quote(($escape ? $this->db->escape($text) : $text));
	}

	/**
	 * Wrap an SQL statement identifier name such as column, table or database names in quotes to prevent injection
	 * risks and reserved word conflicts.
	 *
	 * This method is provided for use where the query object is passed to a function for modification.
	 * If you have direct access to the database object, it is recommended you use the quoteName method directly.
	 *
	 * Note that 'qn' is an alias for this method as it is in JDatabase.
	 *
	 * Usage:
	 * $query->quoteName('#__a');
	 * $query->qn('#__a');
	 *
	 * @param   string  $name  The identifier name to wrap in quotes.
	 *
	 * @return  string  The quote wrapped name.
	 *
	 * @since   11.1
	 * @throws  DatabaseError if the internal db property is not a valid object.
	 */
	public function quoteName($name)
	{
		if (!($this->db instanceof JDatabase))
		{
			throw new RuntimeException('JLIB_DATABASE_ERROR_INVALID_DB_OBJECT');
		}

		return $this->db->quoteName($name);
	}

	/**
	 * Add a RIGHT JOIN clause to the query.
	 *
	 * Usage:
	 * $query->rightJoin('b ON b.id = a.id')->rightJoin('c ON c.id = b.id');
	 *
	 * @param   string  $condition  The join condition.
	 *
	 * @return  JDatabaseQuery  Returns this object to allow chaining.
	 *
	 * @since   11.1
	 */
	public function rightJoin($condition)
	{
		$this->join('RIGHT', $condition);

		return $this;
	}

	/**
	 * Add a single column, or array of columns to the SELECT clause of the query.
	 *
	 * Note that you must not mix insert, update, delete and select method calls when building a query.
	 * The select method can, however, be called multiple times in the same query.
	 *
	 * Usage:
	 * $query->select('a.*')->select('b.id');
	 * $query->select(array('a.*', 'b.id'));
	 *
	 * @param   mixed  $columns  A string or an array of field names.
	 *
	 * @return  JDatabaseQuery  Returns this object to allow chaining.
	 *
	 * @since   11.1
	 */
	public function select($columns)
	{
		$this->type = 'select';

		if (is_null($this->select))
		{
			$this->select = new JDatabaseQueryElement('SELECT', $columns);
		}
		else
		{
			$this->select->append($columns);
		}

		return $this;
	}

	/**
	 * Add a single condition string, or an array of strings to the SET clause of the query.
	 *
	 * Usage:
	 * $query->set('a = 1')->set('b = 2');
	 * $query->set(array('a = 1', 'b = 2');
	 *
	 * @param   mixed   $conditions  A string or array of string conditions.
	 * @param   string  $glue        The glue by which to join the condition strings. Defaults to ,.
	 *                               Note that the glue is set on first use and cannot be changed.
	 *
	 * @return  JDatabaseQuery  Returns this object to allow chaining.
	 *
	 * @since   11.1
	 */
	public function set($conditions, $glue = ',')
	{
		if (is_null($this->set))
		{
			$glue = strtoupper($glue);
			$this->set = new JDatabaseQueryElement('SET', $conditions, "\n\t$glue ");
		}
		else
		{
			$this->set->append($conditions);
		}

		return $this;
	}

	/**
	 * Allows a direct query to be provided to the database
	 * driver's setQuery() method, but still allow queries
	 * to have bounded variables.
	 *
	 * Usage:
	 * $query->setQuery('select * from #__users');
	 *
	 * @param   mixed   $sql     An SQL Query
	 *
	 * @return  JDatabaseQuery  Returns this object to allow chaining.
	 *
	 * @since   11.1
	 */
	public function setQuery($sql)
	{
		$this->sql = $sql;

		return $this;
	}

	/**
	 * Add a table name to the UPDATE clause of the query.
	 *
	 * Note that you must not mix insert, update, delete and select method calls when building a query.
	 *
	 * Usage:
	 * $query->update('#__foo')->set(...);
	 *
	 * @param   string  $table  A table to update.
	 *
	 * @return  JDatabaseQuery  Returns this object to allow chaining.
	 *
	 * @since   11.1
	 */
	public function update($table)
	{
		$this->type = 'update';
		$this->update = new JDatabaseQueryElement('UPDATE', $table);

		return $this;
	}

	/**
	 * Adds a tuple, or array of tuples that would be used as values for an INSERT INTO statement.
	 *
	 * Usage:
	 * $query->values('1,2,3')->values('4,5,6');
	 * $query->values(array('1,2,3', '4,5,6'));
	 *
	 * @param   string  $values  A single tuple, or array of tuples.
	 *
	 * @return  JDatabaseQuery  Returns this object to allow chaining.
	 *
	 * @since   11.1
	 */
	function values($values)
	{
		if (is_null($this->values))
		{
			$this->values = new JDatabaseQueryElement('()', $values, '),(');
		}
		else
		{
			$this->values->append($values);
		}

		return $this;
	}

	/**
	 * Add a single condition, or an array of conditions to the WHERE clause of the query.
	 *
	 * Usage:
	 * $query->where('a = 1')->where('b = 2');
	 * $query->where(array('a = 1', 'b = 2'));
	 *
	 * @param   mixed   $conditions  A string or array of where conditions.
	 * @param   string  $glue        The glue by which to join the conditions. Defaults to AND.
	 *                               Note that the glue is set on first use and cannot be changed.
	 *
	 * @return  JDatabaseQuery  Returns this object to allow chaining.
	 *
	 * @since   11.1
	 */
	public function where($conditions, $glue = 'AND')
	{
		if (is_null($this->where))
		{
			$glue = strtoupper($glue);
			$this->where = new JDatabaseQueryElement('WHERE', $conditions, " $glue ");
		}
		else
		{
			$this->where->append($conditions);
		}

		return $this;
	}
}
