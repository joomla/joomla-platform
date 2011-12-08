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
 * Oracle database driver
 *
 * @package     Joomla.Platform
 * @subpackage  Database
 * @see         http://php.net/pdo
 * @since       11.4
 */
class JDatabaseDriverOracle extends JDatabaseDriverPDO
{
	/**
	 * The name of the database driver.
	 *
	 * @var    string
	 * @since  11.4
	 */
	public $name = 'oracle';

	/**
	 * Returns the current dateformat
	 *
	 * @var   string
	 * @since 11.4
	 */
	protected $dateformat;

	/**
	 * Returns the current character set
	 *
	 * @var   string
	 * @since 11.4
	 */
	protected $charset;

	/**
	 * Constructor.
	 *
	 * @param   array  $options  List of options used to configure the connection
	 *
	 * @since   11.4
	 */
	protected function __construct($options)
	{
		$options['driver'] = 'oci';
		$options['charset']    = (isset($options['charset'])) ? $options['charset']   : 'AL32UTF8';
		$options['dateformat'] = (isset($options['dateformat'])) ? $options['dateformat'] : 'RRRR-MM-DD HH24:MI:SS';

		$this->charset = $options['charset'];
		$this->dateformat = $options['dateformat'];

		// Finalize initialisation
		parent::__construct($options);

		$this->setDateFormat($this->dateformat);
	}

	/**
	 * Destructor.
	 *
	 * @since   11.4
	 */
	public function __destruct()
	{
		$this->freeResult();
		unset($this->connection);
	}

	/**
	 * Drops a table from the database.
	 *
	 * Note: The IF EXISTS flag is unused in the Oracle driver.
	 *
	 * @param   string   $tableName  The name of the database table to drop.
	 * @param   boolean  $ifExists   Optionally specify that the table must exist before it is dropped.
	 *
	 * @return  JDatabaseSQLSrv  Returns this object to support chaining.
	 *
	 * @since   11.4
	 */
	public function dropTable($tableName, $ifExists = true)
	{
		$query = $this->getQuery(true);

		$query->setQuery('DROP TABLE :tableName');
		$query->bind(':tableName', $tableName);

		$this->setQuery($query);

		$this->execute();

		return $this;
	}

	/**
	 * Method to escape a string for usage in an SQL statement.
	 * Doesn't do anything in the Oracle driver at this time.
	 *
	 * Note: Use query objects with bound variables instead!
	 *
	 * @param   string   $text   The string to be escaped.
	 * @param   boolean  $extra  Optional parameter to provide extra escaping.
	 *
	 * @return  string  The escaped string.
	 *
	 * @since   11.4
	 */
	public function escape($text, $extra = false)
	{
		return $text;
	}

	/**
	 * Method to get the database collation in use by sampling a text field of a table in the database.
	 *
	 * @return  mixed  The collation in use by the database or boolean false if not supported.
	 *
	 * @since   11.4
	 */
	public function getCollation()
	{
		return $this->charset;
	}

	/**
     * Returns the current date format
     * This method should be useful in the case that
     * somebody actually wants to use a different
     * date format and needs to check what the current
     * one is to see if it needs to be changed.
     *
     * @return string The current date format
     *
     * @since 11.4
     */
	public function getDateFormat()
	{
		return $this->dateformat;
	}

	/**
	 * Shows the table CREATE statement that creates the given tables.
	 *
	 * Note: You must have the correct privileges before this method
	 * will return usable results!
	 *
	 * @param   mixed  $tables  A table name or a list of table names.
	 *
	 * @return  array  A list of the create SQL for the tables.
	 *
	 * @since   11.4
	 * @throws  RuntimeException
	 */
	public function getTableCreate($tables)
	{
		// Initialise variables.
		$result = array();
		$query = $this->getQuery(true);

		$query->select('dbms_metadata.get_ddl(:type, :tableName)');
		$query->from('dual');

		$query->bind(':type', 'TABLE');

		// Sanitize input to an array and iterate over the list.
		settype($tables, 'array');
		foreach ($tables as $table)
		{
			$query->bind(':tableName', $table);
			$this->setQuery($query);
			$statement = (string) $this->loadResult();
			$result[$table] = $statement;
		}

		return $result;
	}

	/**
	 * Retrieves field information about a given table.
	 *
	 * @param   string   $table     The name of the database table.
	 * @param   boolean  $typeOnly  True to only return field types.
	 *
	 * @return  array  An array of fields for the database table.
	 *
	 * @since   11.4
	 * @throws  RuntimeException
	 */
	public function getTableColumns($table, $typeOnly = true)
	{
		$columns = array();
		$query = $this->getQuery(true);

		$fieldCasing = $this->getOption(PDO::ATTR_CASE);

		$this->setOption(PDO::ATTR_CASE, PDO::CASE_UPPER);

		$table = strtoupper($table);

		$query->select('*');
		$query->from('ALL_TAB_COLUMNS');
		$query->where('table_name = :tableName');

		$query->bind(':tableName', $table);
		$this->setQuery($query);
		$fields = $this->loadObjectList();

		if ($typeOnly)
		{
			foreach ($fields as $field)
			{
				$columns[$table][$field->COLUMN_NAME] = $field->DATA_TYPE;
			}
		}
		else
		{
			foreach ($fields as $field)
			{
				$columns[$table][$field->COLUMN_NAME] = $field;
			}
		}

		$this->setOption(PDO::ATTR_CASE, $fieldCasing);

		return $columns;
	}

	/**
	 * Get the details list of keys for a table.
	 *
	 * @param   string  $table  The name of the table.
	 *
	 * @return  array  An array of the column specification for the table.
	 *
	 * @since   11.4
	 * @throws  RuntimeException
	 */
	public function getTableKeys($table)
	{
		$keys = array();
		$query = $this->getQuery(true);

		$fieldCasing = $this->getOption(PDO::ATTR_CASE);

		$this->setOption(PDO::ATTR_CASE, PDO::CASE_UPPER);

		$table = strtoupper($table);
		$query->select('*');
		$query->from('ALL_CONSTRAINTS');
		$query->where('table_name = :tableName');

		$query->bind(':tableName', $table);

		$this->setQuery($query);
		$keys = $this->loadObjectList();

		$this->setOption(PDO::ATTR_CASE, $fieldCasing);

		return $keys;
	}

	/**
	 * Method to get an array of all tables in the database (schema).
	 *
	 * @param   string   $databaseName         The database (schema) name
	 * @param   boolean  $includeDatabaseName  Whether to include the schema name in the results
	 *
	 * @return  array    An array of all the tables in the database.
	 *
	 * @since   11.4
	 * @throws  RuntimeException
	 */
	public function getTableList($databaseName = null, $includeDatabaseName = false)
	{
		$query = $this->getQuery(true);

		$tables = array();

		if ($includeDatabaseName)
		{
			$query->select('owner, table_name');
		}
		else
		{
			$query->select('table_name');
		}

		$query->from('all_tables');
		if ($databaseName)
		{
			$query->where('owner = :database');
			$query->bind(':database', $databaseName);
		}

		$query->order('table_name');

		$this->setQuery($query);

		if ($includeDatabaseName)
		{
			$tables = $this->loadAssocList();
		}
		else
		{
			$tables = $this->loadResultArray();
		}

		return $tables;
	}

	/**
	 * Get the version of the database connector.
	 *
	 * @return  string  The database connector version.
	 *
	 * @since   11.4
	 */
	public function getVersion()
	{
		$this->setQuery("select value from nls_database_parameters where parameter = 'NLS_RDBMS_VERSION'");
		return $this->loadResult();
	}

	/**
	 * Select a database for use.
	 *
	 * @param   string  $database  The name of the database to select for use.
	 *
	 * @return  boolean  True if the database was successfully selected.
	 *
	 * @since   11.4
	 * @throws  RuntimeException
	 */
	public function select($database)
	{
		return true;
	}

	/**
     * Sets the Oracle Date Format for the session
     * Default date format for Oracle is = DD-MON-RR
     * The default date format for this driver is:
     * 'RRRR-MM-DD HH24:MI:SS' since it is the format
     * that matches the MySQL one used within most Joomla
     * tables.
     *
     * @param   string  $dateformat  Oracle Date Format String
     *
     * @return boolean
     *
     * @since  11.4
     */
	public function setDateFormat($dateformat = 'DD-MON-RR')
	{
		$this->setQuery("alter session set nls_date_format = '$dateformat'");

		if (!$this->execute())
		{
			return false;
		}

		$this->dateformat = $dateformat;

		return true;
	}

	/**
	 * Set the connection to use UTF-8 character encoding.
	 *
	 * Returns false automatically for the Oracle driver since
	 * you can only set the character set when the connection
	 * is created.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   11.4
	 */
	public function setUTF()
	{
		return false;
	}

	/**
	 * Test to see if the PDO ODBC connector is available.
	 *
	 * @return  boolean  True on success, false otherwise.
	 *
	 * @since   11.4
	 */
	public static function test()
	{
		return in_array('oci', PDO::getAvailableDrivers());
	}
}
