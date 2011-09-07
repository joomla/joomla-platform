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
 * Query Building Class.
 *
 * @package		Joomla.Framework
 * @subpackage	Database
 * @since		11.1
 */
class JDatabaseQueryPostgreSQL extends JDatabaseQuery
{
	/**
	 * @var    object  The FOR UPDATE element used in "FOR UPDATE" and "FOR SHARE" lock
	 * @since  11.1
	 */
	protected $forUpdate = null;
	
	/**
	 * @var    object  The LIMIT element
	 * @since  11.1
	 */
	protected $limit = null;
	
	/**
	 * @var    object  The COMMIT element
	 * @since  11.1
	 */	
	protected $commit = null;

	/**
	 * @var    object  The ROLLBACK element
	 * @since  11.1
	 */
	protected $rollback = null;
	
	/**
	 * @var    object  The SAVEPOINT element
	 * @since  11.1
	 */
	protected $savepoint = null;
	
	/**
	 * @var    object  The RELEASE SAVEPOINT element
	 * @since  11.1
	 */	
	protected $releaseSavepoint = null;
	
	/**
	 * @var    object  The START TRANSACTION element
	 * @since  11.1
	 */	
	protected $startTransaction = null;
	
	/**
	 * @var    object  The RETURNING element of INSERT INTO
	 * @since  11.1
	 */	
	protected $returning = null;
	
	
	/**
	 * Magic function to convert the query to a string, only for postgresql specific query
	 *
	 * @return  string	The completed query.
	 *
	 * @since   11.1
	 */
	public function __toString()
	{
		$query = '';

		switch ($this->type)
		{
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
				
				if ($this->limit)
				{
					$query .= (string) $this->limit;
				}
				
				if ($this->forUpdate)
				{
					$query .= (string) $this->forUpdate;
				}

				break;

			case 'insert':
				$query .= (string) $this->insert;

				if ($this->values)
				{
					if ($this->columns)
					{
						$query .= (string) $this->columns;
					}

					$query .= ' VALUES ';
					$query .= (string) $this->values;
					
					if ($this->returning)
					{
						$query .= (string) $this->returning;
					}
				}

				break;
				
			case 'lock':
				$query .= (string) $this->lock;
				break;
				
			case 'startTransaction':
				$query .= (string) $this->startTransaction;
				break;
			
			case 'commit':
				$query .= (string) $this->commit;
				break;
				
			case 'rollback':
				$query .= (string) $this->rollback;
				break;
				
			case 'releaseSavepoint':
				$query .= (string) $this->releaseSavepoint;
				break;
				
			case 'savepoint':
				$query .= (string) $this->savepoint;
				break;
				
			default:
				parent::__toString();
				break;

		}

		return $query;
	}
	
	/**
	 * @param	mixed	$columns	A string or an array of field names.
	 *
	 * @return	JDatabaseQueryPostgreSQL	Returns this object to allow chaining.
	 * @since	11.1
	 */
	/*  SAME in JDatabQuery
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
	}*/

	/**
	 * @param	string	$table	The name of the table to delete from.
	 *
	 * @return	JDatabaseQueryPostgreSQL	Returns this object to allow chaining.
	 * @since	11.1
	 */
	/* SAME in JDatabQuery
	public function delete($table = null)
	{
		$this->type	= 'delete';
		$this->delete	= new JDatabaseQueryElementPostgreSQL('DELETE', null);

		if (!empty($table)) {
			$this->from($table);
		}

		return $this;
	}*/

	/**
	 * @param	mixed	$tables	A string or array of table names.
	 *
	 * @return	JDatabaseQueryPostgreSQL	Returns this object to allow chaining.
	 * @since	11.1
	 */
	/* SAME in JDatabQuery
	public function insert($tables)
	{
		$this->type	= 'insert';
		$this->insert	= new JDatabaseQueryElementPostgreSQL('INSERT INTO', $tables);

		return $this;
	}*/

	/**
	 * @param	mixed	$tables	A string or array of table names.
	 *
	 * @return	JDatabaseQueryPostgreSQL	Returns this object to allow chaining.
	 * @since	11.1
	 */
	/* SAME in JDatabQuery
	public function update($tables)
	{
		$this->type = 'update';
		$this->update = new JDatabaseQueryElementPostgreSQL('UPDATE', $tables);

		return $this;
	} */

	/**
	 * @param	mixed	A string or array of table names.
	 *
	 * @return	JDatabaseQueryPostgreSQL	Returns this object to allow chaining.
	 * @since	11.1
	 */
	/* SAME in JDatabQuery
	public function from($tables)
	{
		if (is_null($this->from)) {
			$this->from = new JDatabaseQueryElementPostgreSQL('FROM', $tables);
		}
		else {
			$this->from->append($tables);
		}

		return $this;
	} */

	/**
	 * @param	string	$type
	 * @param	string	$conditions
	 *
	 * @return	JDatabaseQueryPostgreSQL	Returns this object to allow chaining.
	 * @since	11.1
	 */
	/* SAME in JDatabQuery
	public function join($type, $conditions)
	{
		if (is_null($this->join)) {
			$this->join = array();
		}
		$this->join[] = new JDatabaseQueryElementPostgreSQL(strtoupper($type) . ' JOIN', $conditions);

		return $this;
	} */

	/**
	 * @param	string	$conditions
	 *
	 * @return	JDatabaseQueryPostgreSQL	Returns this object to allow chaining.
	 * @since	11.1
	 */
	/* SAME in JDatabQuery
	public function innerJoin($conditions)
	{
		$this->join('INNER', $conditions);

		return $this;
	} */

	/**
	 * @param	string	$conditions
	 *
	 * @return	JDatabaseQueryPostgreSQL	Returns this object to allow chaining.
	 * @since	11.1
	 */
	/*SAME in JDatabQuery
	public function outerJoin($conditions)
	{
		$this->join('OUTER', $conditions);

		return $this;
	}*/

	/**
	 * @param	string	$conditions
	 *
	 * @return	JDatabaseQueryPostgreSQL	Returns this object to allow chaining.
	 * @since	11.1
	 */
	/* SAME in JDatabQuery
	public function leftJoin($conditions)
	{
		$this->join('LEFT', $conditions);

		return $this;
	} */

	/**
	 * @param	string	$conditions
	 *
	 * @return	JDatabaseQueryPostgreSQL	Returns this object to allow chaining.
	 * @since	11.1
	 */
	/* SAME in JDatabQuery
	public function rightJoin($conditions)
	{
		$this->join('RIGHT', $conditions);

		return $this;
	}*/

	/**
	 * @param	mixed	$conditions	A string or array of conditions.
	 * @param	string	$glue
	 *
	 * @return	JDatabaseQueryPostgreSQL	Returns this object to allow chaining.
	 * @since	11.1
	 */
	/* SAME in JDatabQuery
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
	}*/

	/**
	 * @param	mixed	$conditions	A string or array of where conditions.
	 * @param	string	$glue
	 *
	 * @return	JDatabaseQueryPostgreSQL	Returns this object to allow chaining.
	 * @since	11.1
	 */
	/* SAME in JDatabQuery
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
	}*/

	/**
	 * @param	mixed	$columns	A string or array of ordering columns.
	 *
	 * @return	JDatabaseQueryPostgreSQL	Returns this object to allow chaining.
	 * @since	11.1
	 */
	/* SAME in JDatabQuery
	public function group($columns)
	{
		if (is_null($this->group)) {
			$this->group = new JDatabaseQueryElementPostgreSQL('GROUP BY', $columns);
		}
		else {
			$this->group->append($columns);
		}

		return $this;
	}*/

	/**
	 * @param	mixed	$conditions	A string or array of columns.
	 * @param	string	$glue
	 *
	 * @return	JDatabaseQueryPostgreSQL	Returns this object to allow chaining.
	 * @since	11.1
	 */
	/* SAME in JDatabQuery
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
	}*/

	/**
	 * @param	mixed	$columns	A string or array of ordering columns.
	 *
	 * @return	JDatabaseQueryPostgreSQL	Returns this object to allow chaining.
	 * @since	11.1
	 */
	/* SAME in JDatabQuery
	public function order($columns)
	{
		if (is_null($this->order)) {
			$this->order = new JDatabaseQueryElementPostgreSQL('ORDER BY', $columns);
		}
		else {
			$this->order->append($columns);
		}

		return $this;
	}*/
   
	/**
	 * @param		string $values  A string 
	 * 
	 * @return	JDatabaseQueryPostgreSQL  Returns this object to allow chaining.
	 * @since		11.1
	 */
	/* SAME in JDatabQuery
	function values($values)
	{
		if (is_null($this->values)) {
     		$this->values = new JDatabaseQueryElementPostgreSQL('()', $values, '), (');
		}
		else {
			$this->values->append($values);
		}

		return $this;
	}*/
   
	/**
	 * Adds a column, or array of column names that would be used for an INSERT INTO statement.
	 *
	 * @param   mixed  $columns  A column name, or array of column names.
	 *
	 * @return  JDatabaseQueryElementMySQL  Returns this object to allow chaining.
	 *
	 * @since   11.1
	 */
	/* SAME in JDatabQuery
	public function columns($columns)
	{
		if (is_null($this->columns)) {
			$this->columns = new JDatabaseQueryElementPostgreSQL('()', $columns);
		}
		else {
			$this->columns->append($columns);
		}

		return $this;
	}*/
	
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
	/* SAME in JDatabQuery
	public function concatenate($values, $separator = null)
	{
		if ($separator) {
			return 'CONCATENATE('.implode(' || '.$this->quote($separator).' || ', $values).')';
		}
		else{
			return 'CONCATENATE('.implode(' || ', $values).')';
		}
	} */
	
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
	 * Casts a value to a char.
	 *
	 * Ensure that the value is properly quoted before passing to the method.
	 *
	 * @param   string  $value  The value to cast as a char.
	 * 
     * @return	JDatabaseQueryPostgreSQL  Returns this object to allow chaining.
     * @since		11.1
     */
	/* SAME in JDatabQuery
	function castAsChar($field)
    {
		return $field;
	} */
   
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
	/* SAME in JDatabQuery 
	function charLength($field)
    {
		return 'CHAR_LENGTH('.$field.')';
	}*/
   
	/**
	 * @param		string $field
	 * 
	 * @param		string separator
	 * @return	Length function for the field
	 * @since		11.1
	 */
	/* SAME in JDatabQuery
	function length($field)
	{
		return 'LENGTH('.$field.')';
	}*/
   
	/**
	 * Sets the lock on select's output row
	 * 
	 * @param		string	$table_name		The table to lock
	 * @param		boolean	$updateOrShare	Choose the row level lock mode, false for UPDATE or true for SHARE
	 * @param		boolean	$noWait			Choose if use the NOWAIT option
	 * @since		11.1
	 */
	public function forUpdate ($table_name, $updateOrShare = false, $noWait = false)
	{
		$this->type = 'forUpdate';
		
		if ( is_null($this->forUpdate) ) {
			$updateClause = ($updateOrShare) ? ' SHARE ' : ' UPDATE ' ;
			$waitClause = ($noWait) ? ' NOWAIT' : '' ;
			$this->forUpdate = new JDatabaseQueryElement('FOR', $updateClause . ' OF ' . $table_name . $waitClause );
		}
		else {
			$this->forUpdate->append( ' OF ' . $table_name );
		}	
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
        	$this->lock = new JDatabaseQueryElement('LOCK TABLE', " $table_name IN $lock_type MODE");
      	}
      	else {
        	$this->lock->append( " $table_name IN $lock_type MODE" );
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
	
	/**
	 * Set the OFFSET and LIMIT clause to the query
	 * 
	 * @param   int  $limit		An int of how many row will be returned
	 * @param   int  $offset	An int for skipping row
	 *
	 * @return  JDatabaseQuery  Returns this object to allow chaining.
	 *
	 * @since   11.1
	 */
	public function limit( $limit = 0, $offset = 0 )
	{
		$this->type = 'limit';
		      	
		if (is_null($this->limit)) {
        	$this->limit = new JDatabaseQueryElement('LIMIT', (int)$limit. ' OFFSET '. (int)$offset );
      	}

      	return $this;
	}
	
	
	
	
	/*  transaction moved here so it's possible to do
	 * 	1) $query1 = $this->getQuery();
	 * 	   $query1->startTransaction( params );
	 * 	   $this->setTransactionQuery($query1);
	 * 
	 *  2) $query2 = $this->getQuery(true); //obtain new query
	 * 	   $query2->select()->fom()...
	 * 			...
	 * 	   $this->setTransactionQuery($query2);
	 * 
	 * 	3) repeat point 2 many times (with rollback too)
	 * 
	 * 	4) $query3 = $this->getQuery(true); //obtain new query
	 * 	   $query3->commit();
	 * 	   $this->setTransactionQuery($query3);
	 * 
	 *  then transactionQuery() to execute the queries
	 * */
	
	
	/**
	 * Method to commit a transaction.
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	public function commit() //was transactionCommit
	{
		$this->type = 'commit';

		if (is_null($this->commit)) {
			$this->commit = new JDatabaseQueryElement('COMMIT', '');
		}
		
		return $this;
		
		//$this->setQuery('COMMIT');
		//$this->query();
	}
	
	/**
	 * Method to roll back a transaction.
	 *
	 * @param   string	The savepoint to rollback to, else rollback all transaction
	 * @return  void
	 *
	 * @since   11.1
	 */
	public function rollback( $savepointName = null ) //was transactionRollback
	{
		$this->type = 'rollback';

		if (is_null($this->rollback)) {
			$savepoint = (isset($savepointName)) ? ' TO SAVEPOINT ' . $this->escape($savepointName) : '';
			$this->rollback = new JDatabaseQueryElement('ROLLBACK', $savepoint);
		}
		
		return $this;
		
		/*$savepoint = (isset($savepointName)) ? ' TO SAVEPOINT ' . $this->escape($savepointName) : '';
		$this->setQuery('ROLLBACK' . $savepoint );
		$this->query();*/
	}
	
	/**
	 * Method to create a savepoint.
	 *
	 * @param	string	Savepoint's name to create
	 * @return  void
	 *
	 * @since   11.1
	 */
	public function transactionSavepoint( $savepointName ) //was createTransactionSavepoint
	{
		$this->type = 'savepoint';

		if (is_null($this->savepoint)) {
			$this->savepoint = new JDatabaseQueryElement('SAVEPOINT', $this->escape($savepoint));
		}
		else {
			$this->savepoint->append( $this->escape($savepoint) );
		}
		
		return $this;
		
		/*$this->setQuery('SAVEPOINT ' . $this->escape($savepointName) );
		$this->query();*/
	}
	
	/**
	 * Method to release a savepoint.
	 *
	 * @param   string	Savepoint's name to release 
	 * @return  void
	 *
	 * @since   11.1
	 */
	public function releaseTransactionSavepoint( $savepointName )
	{
		$this->type = 'releaseSavepoint';

		if (is_null($this->releaseSavepoint)) {
			$this->releaseSavepoint = new JDatabaseQueryElement('RELEASE SAVEPOINT', $this->escape($savepointName));
		}
		else {
			$this->releaseSavepoint->append( $this->escape($savepointName) );
		}
		
		return $this;
		
		/*$this->setQuery('RELEASE SAVEPOINT ' . $this->escape($savepointName) );
		$this->query();*/
	}

	/**
	 * Method to initialize a transaction.
	 *
	 * @param   string	The transaction mode
	 * @return  void
	 *
	 * @since   11.1
	 */
	public function startTransaction( $transactionMode = null )
	{
		$this->type = 'startTransaction';
		
		$arrayMode = array( 'SERIALIZABLE', 'REPEATABLE READ', 'READ COMMITTED', 'READ UNCOMMITTED', 'READ WRITE', 'READ ONLY' );
		$mode = (isset($transactionMode) && in_array($transactionMode, $arrayMode) ) ? $transactionMode : '' ;
		
		if (is_null($this->startTransaction)) {
			$this->startTransaction = new JDatabaseQueryElement('START TRANSACTION', $mode);
		}
		
		return $this;
		
		/*$this->setQuery('START TRANSACTION ' . $mode );
		$this->query();*/
	}
	
	/**
	 * Add the RETURNING element to INSERT INTO statement.
	 *
	 * @param   mixed  $pkCol  The name of the primary key column.
	 *
	 * @return  JDatabaseQuery  Returns this object to allow chaining.
	 *
	 * @since   11.1
	 */
	public function returning( $pkCol )
	{
		$this->type = 'returning';
		
		if (is_null($this->returning)) 
		{
			$this->returning = new JDatabaseQueryElement('RETURNING', $pkCol);
		}

		return $this;
	}
}