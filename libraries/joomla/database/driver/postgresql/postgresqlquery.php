<?php
/**
 * @package		Joomla.Platform
 * @subpackage	Database
 * 
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.database.databasequery');

/**
 * Query Element Class.
 *
 * @package		Joomla.Framework
 * @subpackage	Database
 * @since		11.1
 */
class JDatabaseQueryElementPostgreSQL extends JDatabaseQueryElement
{
	/**
	 * Constructor.
	 *
	 * @param	string	$name		The name of the element.
	 * @param	mixed	$elements	String or array.
	 * @param	string	$glue		The glue for elements.
	 *
	 * @return	JDatabaseQueryElementPostgreSQL
	 * @since	11.1
	 */
	public function __construct($name, $elements, $glue = ',')
	{
		parent::__construct($name, $elements, $glue);
	}
}

/**
 * Query Building Class.
 *
 * @package		Joomla.Framework
 * @subpackage	Database
 * @since		11.1
 */
class JDatabaseQueryPostgreSQL extends JDatabaseQuery
{
	/**
	 * @param	mixed	$columns	A string or an array of field names.
	 *
	 * @return	JDatabaseQueryPostgreSQL	Returns this object to allow chaining.
	 * @since	11.1
	 */
	public function select($columns)
	{
		$this->type = 'select';

		if (is_null($this->select)) {
			$this->select = new JDatabaseQueryElementPostgreSQL('SELECT', $columns);
		}
		else {
			$this->select->append($columns);
		}

		return $this;
	}

	/**
	 * @param	string	$table	The name of the table to delete from.
	 *
	 * @return	JDatabaseQueryPostgreSQL	Returns this object to allow chaining.
	 * @since	11.1
	 */
	public function delete($table = null)
	{
		$this->type	= 'delete';
		$this->delete	= new JDatabaseQueryElementPostgreSQL('DELETE', null);

		if (!empty($table)) {
			$this->from($table);
		}

		return $this;
	}

	/**
	 * @param	mixed	$tables	A string or array of table names.
	 *
	 * @return	JDatabaseQueryPostgreSQL	Returns this object to allow chaining.
	 * @since	11.1
	 */
	public function insert($tables)
	{
		$this->type	= 'insert';
		$this->insert	= new JDatabaseQueryElementPostgreSQL('INSERT INTO', $tables);

		return $this;
	}

	/**
	 * @param	mixed	$tables	A string or array of table names.
	 *
	 * @return	JDatabaseQueryPostgreSQL	Returns this object to allow chaining.
	 * @since	11.1
	 */
	public function update($tables)
	{
		$this->type = 'update';
		$this->update = new JDatabaseQueryElementPostgreSQL('UPDATE', $tables);

		return $this;
	}

	/**
	 * @param	mixed	A string or array of table names.
	 *
	 * @return	JDatabaseQueryPostgreSQL	Returns this object to allow chaining.
	 * @since	11.1
	 */
	public function from($tables)
	{
		if (is_null($this->from)) {
			$this->from = new JDatabaseQueryElementPostgreSQL('FROM', $tables);
		}
		else {
			$this->from->append($tables);
		}

		return $this;
	}

	/**
	 * @param	string	$type
	 * @param	string	$conditions
	 *
	 * @return	JDatabaseQueryPostgreSQL	Returns this object to allow chaining.
	 * @since	11.1
	 */
	public function join($type, $conditions)
	{
		if (is_null($this->join)) {
			$this->join = array();
		}
		$this->join[] = new JDatabaseQueryElementPostgreSQL(strtoupper($type) . ' JOIN', $conditions);

		return $this;
	}

	/**
	 * @param	string	$conditions
	 *
	 * @return	JDatabaseQueryPostgreSQL	Returns this object to allow chaining.
	 * @since	11.1
	 */
	public function innerJoin($conditions)
	{
		$this->join('INNER', $conditions);

		return $this;
	}

	/**
	 * @param	string	$conditions
	 *
	 * @return	JDatabaseQueryPostgreSQL	Returns this object to allow chaining.
	 * @since	11.1
	 */
	public function outerJoin($conditions)
	{
		$this->join('OUTER', $conditions);

		return $this;
	}

	/**
	 * @param	string	$conditions
	 *
	 * @return	JDatabaseQueryPostgreSQL	Returns this object to allow chaining.
	 * @since	11.1
	 */
	public function leftJoin($conditions)
	{
		$this->join('LEFT', $conditions);

		return $this;
	}

	/**
	 * @param	string	$conditions
	 *
	 * @return	JDatabaseQueryPostgreSQL	Returns this object to allow chaining.
	 * @since	11.1
	 */
	public function rightJoin($conditions)
	{
		$this->join('RIGHT', $conditions);

		return $this;
	}

	/**
	 * @param	mixed	$conditions	A string or array of conditions.
	 * @param	string	$glue
	 *
	 * @return	JDatabaseQueryPostgreSQL	Returns this object to allow chaining.
	 * @since	11.1
	 */
	public function set($conditions, $glue=',')
	{
		if (is_null($this->set)) {
			$glue = strtoupper($glue);
			$this->set = new JDatabaseQueryElementPostgreSQL('SET', $conditions, "\n\t$glue ");
		}
		else {
			$this->set->append($conditions);
		}

		return $this;
	}

	/**
	 * @param	mixed	$conditions	A string or array of where conditions.
	 * @param	string	$glue
	 *
	 * @return	JDatabaseQueryPostgreSQL	Returns this object to allow chaining.
	 * @since	11.1
	 */
	public function where($conditions, $glue='AND')
	{
		if (is_null($this->where)) {
			$glue = strtoupper($glue);
			$this->where = new JDatabaseQueryElementPostgreSQL('WHERE', $conditions, " $glue ");
		}
		else {
			$this->where->append($conditions);
		}

		return $this;
	}

	/**
	 * @param	mixed	$columns	A string or array of ordering columns.
	 *
	 * @return	JDatabaseQueryPostgreSQL	Returns this object to allow chaining.
	 * @since	11.1
	 */
	public function group($columns)
	{
		if (is_null($this->group)) {
			$this->group = new JDatabaseQueryElementPostgreSQL('GROUP BY', $columns);
		}
		else {
			$this->group->append($columns);
		}

		return $this;
	}

	/**
	 * @param	mixed	$conditions	A string or array of columns.
	 * @param	string	$glue
	 *
	 * @return	JDatabaseQueryPostgreSQL	Returns this object to allow chaining.
	 * @since	11.1
	 */
	public function having($conditions, $glue='AND')
	{
		if (is_null($this->having)) {
			$glue = strtoupper($glue);
			$this->having = new JDatabaseQueryElementPostgreSQL('HAVING', $conditions, " $glue ");
		}
		else {
			$this->having->append($conditions);
		}

		return $this;
	}

	/**
	 * @param	mixed	$columns	A string or array of ordering columns.
	 *
	 * @return	JDatabaseQueryPostgreSQL	Returns this object to allow chaining.
	 * @since	11.1
	 */
	public function order($columns)
	{
		if (is_null($this->order)) {
			$this->order = new JDatabaseQueryElementPostgreSQL('ORDER BY', $columns);
		}
		else {
			$this->order->append($columns);
		}

		return $this;
	}
   
	/**
	 * @param		string $table_name  A string 
	 * 
	 * @return	Drop if exists syntax
	 * @since		11.1
	 */
	function dropIfExists($table_name)
	{
		$this->type = 'drop';

		if (is_null($this->drop)) {
			$this->drop = new JDatabaseQueryElementPostgreSQL('DROP TABLE IF EXISTS', $table_name);
		}
		else {
			$this->drop->append($table_name);
		}

		return $this;
	}
   
	/**
	 * @param		string $values  A string 
	 * 
	 * @return	JDatabaseQueryPostgreSQL  Returns this object to allow chaining.
	 * @since		11.1
	 */
	function values($values)
	{
		if (is_null($this->values)) {
     		$this->values = new JDatabaseQueryElementPostgreSQL('()', $values, '), (');
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
			$this->columns = new JDatabaseQueryElementPostgreSQL('()', $columns);
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
		return 'NOW()';
	}
   
	/**
	 * @param		string $query A string
	 * 
	 * @return	JDatabaseQueryPostgreSQL  Returns this object to allow chaining.
	 * @since		11.1
	 */
	function auto_increment($query)
	{
		return $query;
	}
   
	/**
	 * Casts a value to a char.
	 *
	 * Ensure that the value is properly quoted before passing to the method.
	 *
	 * @param   string  $value  The value to cast as a char.
	 * 
     * @return	JDatabaseQueryPostgreSQL  Returns this object to allow chaining.
     * @since		11.1
     */
	function castAsChar($field)
    {
		return $field;
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
	function charLength($field)
    {
		return 'CHAR_LENGTH('.$field.')';
	}
   
	/**
	 * @param		string $field
	 * 
	 * @param		string separator
	 * @return	Length function for the field
	 * @since		11.1
	 */
	function length($field)
	{
		return 'LENGTH('.$field.')';
	}
   
   	/**
   	 * Method to lock the database table for writing.
     *
	 * @return	Lock query syntax
	 * @since	11.1
   	 * @todo	from Hooduku project, check for errors	 
	 */
	public function lock($table_name, $lock_type='ACCESS EXCLUSIVE')
	{
		$this->type = 'lock';
		      	
		if (is_null($this->lock)) {
        	$this->lock = new JDatabaseQueryElementPostgreSQL('LOCK TABLE ', $table_name . ' IN ' . $lock_type .' MODE');
      	}
      	else {
        	$this->lock->append($table_name);
      	}

      	return $this;
	}

	/**
	 * Unlock does not exist in PostgreSQL, it is automatically done on commit or rollback
	 *
	 * @return	boolean	True .
	 * @since	11.1
	 */
	public function unlock()  
	{
		return true;
	}
}