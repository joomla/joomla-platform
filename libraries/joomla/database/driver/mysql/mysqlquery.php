<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Database
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.database.databasequery');

/**
 * Query Element Class.
 *
 * @package		Joomla.Framework
 * @subpackage	Database
 * @since		1.6
 */
class JDatabaseQueryElementMySQL extends JDatabaseQueryElement
{
	/**
	 * Constructor.
	 *
	 * @param	string	$name		The name of the element.
	 * @param	mixed	$elements	String or array.
	 * @param	string	$glue		The glue for elements.
	 *
	 * @return	JDatabaseQueryElementMySQL
	 * @since	1.6
	 */
	public function __construct($name, $elements, $glue = ',')
	{
		parent::__construct($name, $elements, $glue);
	}
}


/**
 * Query Building Class.
 *
 * @package     Joomla.Platform
 * @subpackage  Database
 * @since       11.1
 */
class JDatabaseQueryMySQL extends JDatabaseQuery
{
	/**
	 * Concatenates an array of column names or values.
	 *
	 * @param   array   $values     An array of values to concatenate.
	 * @param   string  $separator  As separator to place between each value.
	 *
	 * @return  string  The concatenated values.
	 *
	 * @since   11.1
	 */
   function concat($values, $separator = null)
   {
		if ($separator) {
			$concat_string = 'CONCAT_WS('.$this->quote($separator);

			foreach($values as $value)
			{
				$concat_string .= ', '.$value;
			}

			return $concat_string.')';
		}
		else {
			return 'CONCAT('.implode(',', $values).')';
		}
	}
	
	
	/**
	 * Add a single column, or array of columns to the SELECT clause of the query.
	 *
	 * Note that you must not mix insert, update, delete and select method calls when building a query.
	 * The select method can, however, be called multiple times in the same query.
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

		if (is_null($this->select)) {
			$this->select = new JDatabaseQueryElementMySQL('SELECT', $columns);
		}
		else {
			$this->select->append($columns);
		}

		return $this;
	}
	
	/**
	 * Add a table name to the DELETE clause of the query.
	 *
	 * Note that you must not mix insert, update, delete and select method calls when building a query.
	 *
	 * @param   string  $table  The name of the table to delete from.
	 *
	 * @return  JDatabaseQuery  Returns this object to allow chaining.
	 *
	 * @since   11.1
	 */ 
	public function delete($table = null)
	{
		$this->type	= 'delete';
		$this->delete	= new JDatabaseQueryElementMySQL('DELETE', null);

		if (!empty($table)) {
			$this->from($table);
		}

		return $this;
	}	
	
	/**
	 * Add a table name to the INSERT clause of the query.
	 *
	 * Note that you must not mix insert, update, delete and select method calls when building a query.
	 *
	 * @param   mixed  $table  The name of the table to insert data into.
	 *
	 * @return  JDatabaseQuery  Returns this object to allow chaining.
	 *
	 * @since   11.1
	 */	 
	public function insert($table)
	{
		$this->type	= 'insert';
		$this->insert	= new JDatabaseQueryElementMySQL('INSERT INTO', $table);

		return $this;
	}	
	
	/**
	 * Add a table name to the UPDATE clause of the query.
	 *
	 * Note that you must not mix insert, update, delete and select method calls when building a query.
	 *
	 * @param   mixed  $tables  A string or array of table names.
	 *
	 * @return  JDatabaseQuery  Returns this object to allow chaining.
	 *
	 * @since   11.1
	 */
	public function update($tables)
	{
		$this->type = 'update';
		$this->update = new JDatabaseQueryElementMySQL('UPDATE', $tables);

		return $this;
	}	
	
	/**
	 * Add a table to the FROM clause of the query.
	 *
	 * Note that while an array of tables can be provided, it is recommended you use explicit joins.
	 *
	 * @param   mixed  $tables  A string or array of table names.
	 *
	 * @return  JDatabaseQuery  Returns this object to allow chaining.
	 *
	 * @since   11.1
	 */
	public function from($tables)
	{
		if (is_null($this->from)) {
			$this->from = new JDatabaseQueryElementMySQL('FROM', $tables);
		}
		else {
			$this->from->append($tables);
		}

		return $this;
	}	
	
	/**
	 * Add a JOIN clause to the query.
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
		if (is_null($this->join)) {
			$this->join = array();
		}
		$this->join[] = new JDatabaseQueryElementMySQL(strtoupper($type) . ' JOIN', $conditions);

		return $this;
	}
	
	/**
	 * Add an INNER JOIN clause to the query.
	 *
	 * @param   string  $conditions  A string or array of conditions.
	 *
	 * @return  JDatabaseQuery  Returns this object to allow chaining.
	 *
	 * @since   11.1
	 */
	public function innerJoin($conditions)
	{
		$this->join('INNER', $conditions);

		return $this;
	}
	
	/**
	 * Add an OUTER JOIN clause to the query.
	 *
	 * @param   string  $conditions  A string or array of conditions.
	 *
	 * @return  JDatabaseQuery  Returns this object to allow chaining.
	 *
	 * @since   11.1
	 */
	public function outerJoin($conditions)
	{
		$this->join('OUTER', $conditions);

		return $this;
	}
	
	/**
	 * Add a LEFT JOIN clause to the query.
	 *
	 * @param   string  $conditions  A string or array of conditions.
	 *
	 * @return  JDatabaseQuery  Returns this object to allow chaining.
	 *
	 * @since   11.1
	 */
	public function leftJoin($conditions)
	{
		$this->join('LEFT', $conditions);

		return $this;
	}
	
	/**
	 * Add a RIGHT JOIN clause to the query.
	 *
	 * @param   string  $conditions  A string or array of conditions.
	 *
	 * @return  JDatabaseQuery  Returns this object to allow chaining.
	 *
	 * @since   11.1
	 */
	public function rightJoin($conditions)
	{
		$this->join('RIGHT', $conditions);

		return $this;
	}
	
	/**
	 * Add a single condition string, or an array of strings to the SET clause of the query.
	 *
	 * @param   mixed   $conditions  A string or array of conditions.
	 * @param   string  $glue        The glue by which to join the condition strings. Defaults to ,.
	 *
	 * @return  JDatabaseQuery  Returns this object to allow chaining.
	 *
	 * @since   11.1
	 */
	public function set($conditions, $glue=',')
	{
		if (is_null($this->set)) {
			$glue = strtoupper($glue);
			$this->set = new JDatabaseQueryElementMySQL('SET', $conditions, "\n\t$glue ");
		}
		else {
			$this->set->append($conditions);
		}

		return $this;
	}
	
	/**
	 * Add a single condition, or an array of conditions to the WHERE clause of the query.
	 *
	 * @param   mixed   $conditions  A string or array of where conditions.
	 * @param   string  $glue        The glue by which to join the conditions. Defaults to AND.
	 *
	 * @return  JDatabaseQuery  Returns this object to allow chaining.
	 *
	 * @since   11.1
	 */
	public function where($conditions, $glue = 'AND')
	{
		if (is_null($this->where)) {
			$glue = strtoupper($glue);
			$this->where = new JDatabaseQueryElementMySQL('WHERE', $conditions, " $glue ");
		}
		else {
			$this->where->append($conditions);
		}

		return $this;
	}
	
	/**
	 * Add a grouping column to the GROUP clause of the query.
	 *
	 * @param   mixed  $columns  A string or array of ordering columns.
	 *
	 * @return  JDatabaseQuery  Returns this object to allow chaining.
	 *
	 * @since   11.1
	 */
 	public function group($columns)
	{
		if (is_null($this->group)) {
			$this->group = new JDatabaseQueryElementMySQL('GROUP BY', $columns);
		}
		else {
			$this->group->append($columns);
		}

		return $this;
	}
	
	/**
	 * A conditions to the HAVING clause of the query.
	 *
	 * @param   mixed   $conditions  A string or array of columns.
	 * @param   string  $glue        The glue by which to join the conditions. Defaults to AND.
	 *
	 * @return  JDatabaseQuery  Returns this object to allow chaining.
	 *
	 * @since   11.1
	 */	 
	public function having($conditions, $glue='AND')
	{
		if (is_null($this->having)) {
			$glue = strtoupper($glue);
			$this->having = new JDatabaseQueryElementMySQL('HAVING', $conditions, " $glue ");
		}
		else {
			$this->having->append($conditions);
		}

		return $this;
	}
	
	/**
	 * Add a ordering column to the ORDER clause of the query.
	 *
	 * @param   mixed  $columns  A string or array of ordering columns.
	 *
	 * @return  JDatabaseQuery  Returns this object to allow chaining.
	 *
	 * @since   11.1
	 */
	public function order($columns)
	{
		if (is_null($this->order)) {
			$this->order = new JDatabaseQueryElementMySQL('ORDER BY', $columns);
		}
		else {
			$this->order->append($columns);
		}

		return $this;
	}
	
	/**
	 * Adds a tuple, or array of tuples that would be used as values for an INSERT INTO statement.
	 *
	 * @param  string  $values  A single tuple, or array of tuples.
	 *
	 * @return  JDatabaseQuerySQLAzure  Returns this object to allow chaining.
	 *
	 * @since   11.1
	 */	 
	public function values($values)
	{
		if (is_null($this->values)) {
			$this->values = new JDatabaseQueryElementMySQL('()', $values, '), (');
		}
		else {
			$this->values->append($values);
		}

		return $this;
	}
	
	/**
	 * Adds a column, or array of column names that would be used for an INSERT INTO statement.
	 *
	 * @param   mixed  $columns  A column name, or array of column names.
	 *
	 * @return  JDatabaseQueryElementMySQL  Returns this object to allow chaining.
	 *
	 * @since   11.1
	 */
	public function columns($columns)
	{
		if (is_null($this->columns)) {
			$this->columns = new JDatabaseQueryElementMySQL('()', $columns);
		}
		else {
			$this->columns->append($columns);
		}

		return $this;
	}
	
	/**
	 * Concatenates an array of column names or values.
	 *
	 * @param   array   $values     An array of values to concatenate.
	 * @param   string  $separator  As separator to place between each value.
	 *
	 * @return  string  The concatenated values.
	 *
	 * @since   11.1
	 */
	public function concatenate($values, $separator = null)
	{
		if ($separator) {
			return 'CONCATENATE('.implode(' || '.$this->quote($separator).' || ', $values).')';
		}
		else{
			return 'CONCATENATE('.implode(' || ', $values).')';
		}
	}

	/**
	 * Gets the current date and time.
	 *
	 * @return  string
	 *
	 * @since   11.1
	 */
	public function currentTimestamp()
	{
		return 'CURRENT_TIMESTAMP()';
	}	
	
	/**
	 * Casts a value to a char.
	 *
	 * Ensure that the value is properly quoted before passing to the method.
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
	 * @param   string  $value  A value.
	 *
	 * @return  string  The required char lenght call.
	 *
	 * @since 11.1
	 */	 
	public function charLength($field)
	{
		return 'CHAR_LENGTH('.$field.')';
	}
	
	/**
	 * Get the length of a string in bytes.
	 *
	 * Note, use 'charLength' to find the number of characters in a string.
	 *
	 * @param   string  $value  The string to measure.
	 *
	 * @return  int
	 *
	 * @since   11.1
	 */
	public function length($value)
	{
		return 'LENGTH('.$value.')';
	}
	
	
	/* NEW FUNCTIONS */
   	
   	/**
   	* @param string $table_name  A string 
   	* 
   	* @return  Drop if exists syntax
   	* @since 11.1
   	*/
   	public function dropIfExists($table_name)
   	{
     	$this->type = 'drop';

      	if (is_null($this->drop)) {
        	$this->drop = new JDatabaseQueryElementMySQL('DROP TABLE IF EXISTS', $table_name);
      	}
      	else {
        	$this->drop->append($table_name);
      	}

      	return $this;
   	}
   
	/**
   	 * Method to lock the database table for writing.
     *
	 * @return	Lock query syntax
	 * @since	11.1
   	 * @todo	from Hooduku project, check for errors	 
	 */
	public function lock($table_name, $lock_type)
	{
		$this->type = 'lock';
		      	
		if (is_null($this->lock)) {
        	$this->lock = new JDatabaseQueryElementMySQL('LOCK TABLES ', $table_name . ' ' . $lock_type);
      	}
      	else {
        	$this->lock->append($table_name);
      	}

      	return $this;
	}

	/**
	 * Method to unlock the database table for writing.
	 *
	 * @return	Unlock query syntax
	 * @since	11.1
     * @todo	from Hooduku project, check for errors
	 */
	public function unlock()
	{
		$this->type = 'unlock';
		
		if (is_null($this->unlock)) {
        	$this->unlock = new JDatabaseQueryElementMySQL('UNLOCK TABLES ', ' ');
      	}

      	return $this;	
	}
}
