<?php
/**
 * @package		Joomla.Platform
 * @subpackage	Database
 * 
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_PLATFORM') or die;

JLoader::register('JDatabaseQueryPostgreSQL', dirname(__FILE__).'/postgresqlquery.php');

/**
 * PostgreSQL database driver
 *
 * @package		Joomla.Framework
 * @subpackage	Database
 * @since		11.1
 */
class JDatabasePostgreSQL extends JDatabase
{
	/**
	 * The database driver name
	 *
	 * @var string
	 */
	public $name = 'postgresql';

	/**
	 *  The null/zero date string
	 *
	 * @var string
	 */
	protected $_nullDate = 'epoch';

	/**
	 * Quote for named objects
	 *
	 * @var string
	 */
	protected $_nameQuote = '"';

	/**
	 * Operator used for concatenation
	 *
	 * @var string
	 */
	protected $_concat_operator = '||';

	/**
	 * ID returned by last insert statement
	 *
	 * @var integer
	 */
	private $_insert_id = 0;


	/**
	 * Database object constructor
	 *
	 * @param	array	List of options used to configure the connection
	 * @since	1.5
	 * @see		JDatabase
	 */
	function __construct( $options )
	{
		$host		= array_key_exists('host', $options)	? $options['host']		: 'localhost';
		$user		= array_key_exists('user', $options)	? $options['user']		: '';
		$password	= array_key_exists('password',$options)	? $options['password']	: '';
		$database	= array_key_exists('database',$options)	? $options['database']	: '';
		$prefix		= array_key_exists('prefix', $options)	? $options['prefix']	: 'jos_';
		/* manca select del db dall'array options */

		// perform a number of fatality checks, then return gracefully
		if (!function_exists( 'pg_connect' )) {
			$this->_errorNum = 1;
			$this->_errorMsg = JText::_('JLIB_DATABASE_ERROR_ADAPTER_POSTGRESQL');  // -> 'The PostgreSQL adapter "pg" is not available.';
			return;
		}

		// connect to the server  --->>> aggiunta di opzione $database visto che non esiste select
		if (!($this->_connection = @pg_connect( "host=$host user=$user password=$password" ))) {
			$this->_errorNum = 2;
			$this->_errorMsg = JText::_('JLIB_DATABASE_ERROR_CONNECT_POSTGRESQL');  // -> 'Could not connect to PostgreSQL';
			return;
		}

		// finalize initialization
		parent::__construct($options);
		
		/* manca set session.sqlmode & la select del db (necessaria in postgresql?) */
	}

	/**
	 * Database object destructor
	 *
	 * @return boolean
	 * @since 1.5
	 */
	public function __destruct()
	{
		$return = false;
		if (is_resource($this->_connection)) {
			$return = pg_close($this->_connection);
		}
		return $return;
	}

	/**
	 * Test to see if the PostgreSQL connector is available
	 *
	 * @return boolean  True on success, false otherwise.
	 */
	public function test()
	{
		return (function_exists( 'pg_connect' ));
	}

	/**
	 * Determines if the connection to the server is active.
	 *
	 * @return	boolean
	 * @since	1.5
	 */
	public function connected()
	{
		if(is_resource($this->_connection)) {
			return pg_ping($this->_connection);
		}
		return false;
	}

	/**
	 * Selects the database, but redundant for PostgreSQL
	 *
	 * @return bool Always true
	 */
	public function select($database=null)  /* no null */
	{
		return true;  /* quando si selezione un db in postgresql ?? */
	}
	
	/**
	 * Determines UTF support
	 *
	 * @return boolean True - UTF is supported
	 */
	public function hasUTF()
	{
		return true;  /* controllare se UTF sempre supportato o solo da versione K */
	}

	/**
	 * Custom settings for UTF support
	 */
	public function setUTF()
	{
		pg_set_client_encoding( $this->_connection, 'UTF8' );
	}

	/**
	 * Get a database escaped string
	 *
	 * @param	string	The string to be escaped
	 * @param	boolean	Optional parameter to provide extra escaping
	 * @return	string
	 */
	public function getEscaped( $text, $extra = false )
	{
		$result = pg_escape_string( $this->_connection, $text );
		if ($extra) {
			$result = addcslashes( $result, '%_' );
		}

		return $result;
	}
	
	/**
	 * Execute the query
	 *
	 * @return mixed A database resource if successful, FALSE if not.
	 */
	public function query()
	{
		if (!is_resource($this->_connection)) {
			return false;
		}

		// Take a local copy so that we don't modify the original query and cause issues later
		$sql = $this->_sql;  /*$this->replacePrefix((string) $this->_sql);*/
		if ($this->_limit > 0 || $this->_offset > 0) {
			$sql .= ' LIMIT '.$this->_limit.' OFFSET '.$this->_offset;
		}
		if ($this->_debug) {
			$this->_ticker++;
			$this->_log[] = $sql;
		}
		$this->_errorNum = 0;
		$this->_errorMsg = '';
		$this->_cursor = pg_query( $this->_connection, $sql );

		if (!$this->_cursor) {
			$this->_errorNum = pg_result_error_field( $this->_cursor, PGSQL_DIAG_SQLSTATE ) . ' ';
			$this->_errorMsg = pg_result_error_field( $this->_cursor, PGSQL_DIAG_MESSAGE_PRIMARY )." SQL=$sql <br />";
			if ($this->_debug) {
				JError::raiseError(500, 'JDatabasePostgreSQL::query: '.$this->_errorNum.' - '.$this->_errorMsg );
			}
			return false;
		}
		return $this->_cursor;
	}

/**********  MANCA GETQUERY -> manca anche su mysql 1.6.3 ********************/


	/**
	 * Description
	 *
	 * @return int The number of affected rows in the previous operation
	 * @since 1.0.5
	 */
	public function getAffectedRows()
	{
		return pg_affected_rows( $this->_connection );
	}
	
	/**
	 * Execute a batch query
	 *
	 * @return mixed A database resource if successful, FALSE if not.
	 */
	public function queryBatch( $abort_on_error=true, $p_transaction_safe = false)
	{
		$this->_errorNum = 0;
		$this->_errorMsg = '';
		if ($p_transaction_safe) {
			$this->_sql = rtrim($this->_sql, "; \t\r\n\0");
			$this->_sql = 'START TRANSACTION;' . $this->_sql . '; COMMIT;';
		}
		$query_split = $this->splitSql($this->_sql);
		$error = 0;
		foreach ($query_split as $command_line) {
			$command_line = trim( $command_line );
			if ($command_line != '') {
				$this->_cursor = pg_query( $this->_connection, $command_line );
				if ($this->_debug) {
					$this->_ticker++;
					$this->_log[] = $command_line;
				}
				if (!$this->_cursor) {
					$error = 1;
					$this->_errorNum .= pg_result_error_field( $this->_cursor, PGSQL_DIAG_SQLSTATE ) . ' ';
					$this->_errorMsg .= pg_result_error_field( $this->_cursor, PGSQL_DIAG_MESSAGE_PRIMARY ).
										" SQL=$command_line <br />";
					if ($abort_on_error) {
						return $this->_cursor;
					}
				}
			}
		}
		return $error ? false : true;
	}

	/**
	 * Diagnostic function
	 *
	 * @return	string
	 */
	public function explain()
	{
		$temp = $this->_sql;
		$this->_sql = "EXPLAIN $this->_sql";

		if (!($cur = $this->query())) {
			return null;
		}
		$first = true;

		$buffer = '<table id="explain-sql">';
		$buffer .= '<thead><tr><td colspan="99">'.$this->getQuery().'</td></tr>';
		while ($row = pg_fetch_assoc( $cur )) {
			if ($first) {
				$buffer .= '<tr>';
				foreach ($row as $k=>$v) {
					$buffer .= '<th>'.$k.'</th>';
				}
				$buffer .= '</tr>';
				$first = false;
			}
			$buffer .= '</thead><tbody><tr>';
			foreach ($row as $k=>$v) {
				$buffer .= '<td>'.$v.'</td>';
			}
			$buffer .= '</tr>';
		}
		$buffer .= '</tbody></table>';
		pg_free_result( $cur );

		$this->_sql = $temp;

		return $buffer;
	}

	/**
	 * Description
	 *
	 * @return int The number of rows returned from the most recent query.
	 */
	public function getNumRows( $cur=null )
	{
		return pg_num_rows( $cur ? $cur : $this->_cursor );
	}

	/**
	 * This method loads the first field of the first row returned by the query.
	 *
	 * @return The value returned in the query or null if the query failed.
	 */
	public function loadResult()
	{
		if (!($cur = $this->query())) {
			return null;
		}
		$ret = null;
		if ($row = pg_fetch_row( $cur )) {
			$ret = $row[0];
		}
		pg_free_result( $cur );
		return $ret;
	}

	/**
	 * Load an array of single field results into an array
	 */
	public function loadResultArray( $numinarray = 0 )
	{
		if (!($cur = $this->query())) {
			return null;
		}
		$array = array();
		while ($row = pg_fetch_row( $cur )) {
			$array[] = $row[$numinarray];
		}
		pg_free_result( $cur );
		return $array;
	}

	/**
	 * Fetch a result row as an associative array
	 *
	 * @return array
	 */
	public function loadAssoc()
	{
		if (!($cur = $this->query())) {
			return null;
		}
		$ret = null;
		if ($array = pg_fetch_assoc( $cur )) {
			$ret = $array;
		}
		pg_free_result( $cur );
		return $ret;
	}

	/**
	 * Load a assoc list of database rows
	 *
	 * @param string The field name of a primary key
	 * @return array If <var>key</var> is empty as sequential list of returned records.
	 */
	public function loadAssocList( $key='' )  /* $key = null, $column = null */
	{
		if (!($cur = $this->query())) {
			return null;
		}
		$array = array();
		while ($row = pg_fetch_assoc( $cur )) {
			if ($key) {
				$array[$row[$key]] = $row;
			} else {
				$array[] = $row;
			}
		}
		pg_free_result( $cur );
		return $array;
	}

	/**
	 * This global function loads the first row of a query into an object
	 *
	 * @return 	object
	 */
	public function loadObject( )  /* $className = 'stdClass' */
	{
		if (!($cur = $this->query())) {
			return null;
		}
		$ret = null;
		if ($object = pg_fetch_object( $cur )) {
			$ret = $object;
		}
		pg_free_result( $cur );
		return $ret;
	}

	/**
	 * Load a list of database objects
	 *
	 * If <var>key</var> is not empty then the returned array is indexed by the value
	 * the database key.  Returns <var>null</var> if the query fails.
	 *
	 * @param string The field name of a primary key
	 * @return array If <var>key</var> is empty as sequential list of returned records.
	 */
	public function loadObjectList( $key='' )  /* $key='', $className = 'stdClass' */
	{
		if (!($cur = $this->query())) {
			return null;
		}
		$array = array();
		while ($row = pg_fetch_object( $cur )) {
			if ($key) {
				$array[$row->$key] = $row;
			} else {
				$array[] = $row;
			}
		}
		pg_free_result( $cur );
		return $array;
	}

	/**
	 * Description
	 *
	 * @return The first row of the query.
	 */
	public function loadRow()
	{
		if (!($cur = $this->query())) {
			return null;
		}
		$ret = null;
		if ($row = pg_fetch_row( $cur )) {
			$ret = $row;
		}
		pg_free_result( $cur );
		return $ret;
	}

	/**
	 * Load a list of database rows (numeric column indexing)
	 *
	 * @param string The field name of a primary key
	 * @return array If <var>key</var> is empty as sequential list of returned records.
	 * If <var>key</var> is not empty then the returned array is indexed by the value
	 * the database key.  Returns <var>null</var> if the query fails.
	 */
	public function loadRowList( $key=null )
	{
		if (!($cur = $this->query())) {
			return null;
		}
		$array = array();
		while ($row = pg_fetch_row( $cur )) {
			if ($key !== null) {
				$array[$row[$key]] = $row;
			} else {
				$array[] = $row;
			}
		}
		pg_free_result( $cur );
		return $array;
	}
	
	
/***********  MANCA LOADNEXTROW , LOADNEXTOBJECT ***********/	

	/**
	 * Inserts a row into a table based on an objects properties
	 *
	 * @param	string	The name of the table
	 * @param	object	An object whose properties match table fields
	 * @param	string	The name of the primary key. If provided the object property is updated.
	 */
	public function insertObject( $table, &$object, $keyName = NULL )
	{
		$fmtsql = 'INSERT INTO '.$table.' ( %s ) VALUES ( %s ) ';
		$verParts = explode( '.', $this->getVersion() );

		$fields = array();
		foreach (get_object_vars( $object ) as $k => $v) {
			if (is_array($v) or is_object($v) or $v === NULL) {
				continue;
			}
			if ($k[0] == '_') { // internal field
				continue;
			}

			$fields[] = $this->nameQuote( $k );
			$values[] = $this->isQuoted( $k ) ? $this->Quote( $v ) : (int) $v;
		}

		if ( !in_array($this->nameQuote($keyName), $fields) ) {
			if ( $verParts[0] > 8 || ($verParts[0] == 8 && $verParts[1] >= 2) ) {
				$fmtsql .= "RETURNING $keyName AS ".$this->nameQuote('id').";";
			} else {
				$fmtsql .= ";
                                	SELECT $keyName AS \"id\" FROM $table;";
			}
		}
		$this->setQuery( sprintf( $fmtsql, implode( ",", $fields ) ,  implode( ",", $values ) ) );

		$result = $this->query();

		if (!$result) {
			return false;
		}

		if ( $results[0][0]['id'] ) {
			$this->_insert_id = $results[0][0]['id'];
		}

		if ($keyName && $id) {
			$object->$keyName = $this->_insert_id;
		}
		return true;
	}

	/**
	 * Description
	 *
	 * @param [type] $updateNulls
	 */
	public function updateObject( $table, &$object, $keyName, $updateNulls=true )  /* updatenulls=false*/
	{
		$fmtsql = 'UPDATE '.$table.' SET %s WHERE %s';
		$tmp = array();
		foreach (get_object_vars( $object ) as $k => $v) {
			if( is_array($v) or is_object($v) or $k[0] == '_' ) { // internal or NA field
				continue;
			}
			if( $k == $keyName ) { // PK not to be updated
				$where = $keyName . '=' . $this->Quote( $v );
				continue;
			}
			if ($v === null) {
				if ($updateNulls) {
					$val = 'NULL';
				} else {
					continue;
				}
			} else {
				$val = $this->isQuoted( $k ) ? $this->Quote( $v ) : (int) $v;
			}
			$tmp[] = $this->nameQuote( $k ) . '=' . $val;
		}
		$this->setQuery( sprintf( $fmtsql, implode( ",", $tmp ) , $where ) );
		return $this->query();
	}

	/**
	 * Description
	 */
	public function insertid()
	{
		return $this->_insert_id;
	}

	/**
	 * Description
	 */
	public function getVersion()
	{
		$version = pg_version( $this->_connection );
		return $version['server'];
	}

	/**
	 * Assumes database collation in use by sampling one text field in one table
	 *
	 * @return string Collation in use
	 */
	public function getCollation()
	{
		if ( $this->hasUTF() ) {
			$cur = $this->query( 'SHOW LC_COLLATE;' );
			$coll = pg_fetch_row( $cur, 0 );
			return $coll['lc_ctype'];
		} else {
			return "N/A";
		}
	}

	/**
	 * Description
	 *
	 * @return array A list of all the tables in the database
	 */
	public function getTableList()
	{
		$this->setQuery( "select tablename from pg_tables where schemaname='public';" );
		return $this->loadResultArray();
	}
	
	
/**********  MANCA GETTABLECREATE **************/

	/**
	 * Retrieves information about the given tables
	 *
	 * @param 	array|string 	A table name or a list of table names
	 * @param	boolean			Only return field types, default true
	 * @return	array An array of fields by table
	 */
	public function getTableFields( $tables, $typeonly = true )
	{
		settype($tables, 'array'); //force to array
		$result = array();

		foreach ($tables as $tblval) {
			$this->setQuery( 'SELECT column_name FROM information_schema.columns WHERE table_name = '.$tblval.';' );
			$fields = $this->loadObjectList();

			if ($typeonly) {
				foreach ($fields as $field) {
					$result[$tblval][$field->Field] = preg_replace("/[(0-9)]/",'', $field->Type );
				}
			} else {
				foreach ($fields as $field) {
					$result[$tblval][$field->Field] = $field;
				}
			}
		}

		return $result;
	}
	
	
	
	/* EXTRA FUNCTION postgreSQL */
	
	/**
	 * Generate SQL command for getting string position
	 *
	 * @param string The string being sought
	 * @param string The string/column being searched
	 * @return string The resulting SQL
	 */
	public function stringPositionSQL($substring, $string)
	{
		$sql = "POSITION($substring, $string)";

		return $sql;
	}

	/**
	 * Generate SQL command for returning random value
	 *
	 * @return string The resulting SQL
	 */
	public function stringRandomSQL()
	{
		return "RANDOM()";
	}

	/**
	 * Create database
	 *
	 * @param string The database name
	 * @param bool Whether or not to create with UTF support (only here for function signature compatibility)
	 * @return string Database creation string
	 */
	public function createDatabase($DBname, $DButfSupport)
	{
		$sql = "CREATE DATABASE ".$this->nameQuote($DBname)." ENCODING UTF8";

		$this->setQuery($sql);
		$this->query();
		$result = $this->getErrorNum();

		if ($result != 0) {
			return false;
		}

		return true;
	}

	/**
	 * Rename a database table
	 *
	 * @param string The old table name
	 * @param string The new table name
	 */
	public function renameTable($oldTable, $newTable)
	{
		$query = "ALTER TABLE ".$oldTable." RENAME TO ".$newTable;
		$db->setQuery($query);
		$db->query();

		$result = $db->getErrorNum();

		if ($result != 0) {
			return false;
		}

		return true;
	}

}
