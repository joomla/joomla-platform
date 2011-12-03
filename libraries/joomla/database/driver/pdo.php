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
 * PDO database driver
 *
 * @package     Joomla.Platform
 * @subpackage  Database
 * @see         http://php.net/pdo
 * @since       11.4
 */
abstract class JDatabaseDriverPDO extends JDatabase
{
	/**
	 * The name of the database driver.
	 *
	 * @var    string
	 * @since  11.4
	 */
	public $name = 'pdo';

	/**
	 * The character(s) used to quote SQL statement names such as table names or field names,
	 * etc.  The child classes should define this as necessary.  If a single character string the
	 * same character is used for both sides of the quoted name, else the first character will be
	 * used for the opening quote and the second for the closing quote.
	 *
	 * @var    string
	 * @since  11.4
	 */
	protected $nameQuote = "'";

	/**
	 * The null or zero representation of a timestamp for the database driver.  This should be
	 * defined in child classes to hold the appropriate value for the engine.
	 *
	 * @var    string
	 * @since  11.4
	 */
	protected $nullDate = '0000-00-00 00:00:00';

	/**
	 * @var    resource  The prepared statement.
	 * @since  11.4
	 */
	protected $prepared;

	/**
	 * Contains the current query execution status
	 *
	 * @var array
	 * @since 11.4
	 */
	protected $executed = false;

	/**
	 * Constructor.
	 *
	 * @param   array  $options  List of options used to configure the connection
	 *
	 * @since   11.4
	 */
	protected function __construct($options)
	{
		// Get some basic values from the options.
		$options['driver'] = (isset($options['driver'])) ? $options['driver'] : 'odbc';
		$options['dsn'] = (isset($options['dsn'])) ? $options['dsn'] : '';
		$options['host'] = (isset($options['host'])) ? $options['host'] : 'localhost';
		$options['database'] = (isset($options['database'])) ? $options['database'] : '';
		$options['user'] = (isset($options['user'])) ? $options['user'] : '';
		$options['password'] = (isset($options['password'])) ? $options['password'] : '';
		$options['driverOptions'] = (isset($options['driverOptions'])) ? $options['driverOptions'] : array();

		// Initialize the connection string variable:
		$connectionString = '';
		$replace = array();
		$with = array();

		// Find the correct PDO DSN Format to use:
		switch($options['driver'])
		{
			case 'cubrid':
				$options['port'] = (isset($options['port'])) ? $options['port'] : 33000;

				$format = 'cubrid:host=#HOST#;port=#PORT#;dbname=#DBNAME#';

				$replace = array('#HOST#', '#PORT#', '#DBNAME#');
				$with = array($options['host'], $options['port'], $options['database']);

				break;

			case 'dblib':
				$options['port'] = (isset($options['port'])) ? $options['port'] : 1433;

				$format = 'dblib:host=#HOST#;port=#PORT#;dbname=#DBNAME#';

				$replace = array('#HOST#', '#PORT#', '#DBNAME#');
				$with = array($options['host'], $options['port'], $options['database']);

				break;

			case 'firebird':
				$options['port'] = (isset($options['port'])) ? $options['port'] : 3050;

				$format = 'firebird:dbname=#DBNAME#';

				$replace = array('#DBNAME#');
				$with = array($options['database']);

				break;

			case 'ibm':
				$options['port'] = (isset($options['port'])) ? $options['port'] : 56789;

				if (!empty($options['dsn']))
				{
					$format = 'ibm:DSN=#DSN#';

					$replace = array('#DSN#');
					$with = array($options['dsn']);
				}
				else
				{
					$format = 'ibm:hostname=#HOST#;port=#PORT#;database=#DBNAME#';

					$replace = array('#HOST#', '#PORT#', '#DBNAME#');
					$with = array($options['host'], $options['port'], $options['database']);
				}

				break;

			case 'informix':
				$options['port'] = (isset($options['port'])) ? $options['port'] : 1526;
				$options['protocol'] = (isset($options['protocol'])) ? $options['protocol'] : 'onsoctcp';

				if (!empty($options['dsn']))
				{
					$format = 'informix:DSN=#DSN#';

					$replace = array('#DSN#');
					$with = array($options['dsn']);
				}
				else
				{
					$format = 'informix:host=#HOST#;service=#PORT#;database=#DBNAME#;server=#SERVER#;protocol=#PROTOCOL#';

					$replace = array('#HOST#', '#PORT#', '#DBNAME#', '#SERVER#', '#PROTOCOL#');
					$with = array($options['host'], $options['port'], $options['database'], $options['server'], $options['protocol']);
				}

				break;

			case 'mssql':
				$options['port'] = (isset($options['port'])) ? $options['port'] : 1433;

				$format = 'mssql:host=#HOST#;port=#PORT#;dbname=#DBNAME#';

				$replace = array('#HOST#', '#PORT#', '#DBNAME#');
				$with = array($options['host'], $options['port'], $options['database']);

				break;

			case 'mysql':
				$options['port'] = (isset($options['port'])) ? $options['port'] : 3306;

				$format = 'mysql:host=#HOST#;port=#PORT#;dbname=#DBNAME#';

				$replace = array('#HOST#', '#PORT#', '#DBNAME#');
				$with = array($options['host'], $options['port'], $options['database']);

				break;

			case 'oci':
				$options['port'] = (isset($options['port'])) ? $options['port'] : 1521;
				$options['charset'] = (isset($options['charset'])) ? $options['charset'] : 'AL32UTF8';

				if (!empty($options['dsn']))
				{
					$format = 'oci:dbname=#DSN#';

					$replace = array('#DSN#');
					$with = array($options['dsn']);
				}
				else
				{
					$format = 'oci:dbname=//#HOST#:#PORT#/#DBNAME#';

					$replace = array('#HOST#', '#PORT#', '#DBNAME#');
					$with = array($options['host'], $options['port'], $options['database']);
				}

				$format .= ';charset=' . $options['charset'];

				break;

			case 'odbc':
				$format = 'odbc:DSN=#DSN#;UID:#USER#;PWD=#PASSWORD#';

				$replace = array('#DSN#', '#USER#', '#PASSWORD#');
				$with = array($options['dsn'], $options['user'], $options['password']);

				break;

			case 'pgsql':
				$options['port'] = (isset($options['port'])) ? $options['port'] : 5432;

				$format = 'pgsql:host=#HOST#;port=#PORT#;dbname=#DBNAME#';

				$replace = array('#HOST#', '#PORT#', '#DBNAME#');
				$with = array($options['host'], $options['port'], $options['database']);

				break;

			case 'sqlite':

				if (isset($options['version']) && $options['version'] == 2)
				{
					$format = 'sqlite2:#DBNAME#';
				}
				else
				{
					$format = 'sqlite:#DBNAME#';
				}

				$replace = array('#DBNAME#');
				$with = array($options['database']);

				break;

			case 'sybase':
				$options['port'] = (isset($options['port'])) ? $options['port'] : 1433;

				$format = 'mssql:host=#HOST#;port=#PORT#;dbname=#DBNAME#';

				$replace = array('#HOST#', '#PORT#', '#DBNAME#');
				$with = array($options['host'], $options['port'], $options['database']);

				break;
		}

		// Create the connection string:
		$connectionString = str_replace($replace, $with, $format);

		// Make sure the PDO extension for PHP is installed and enabled.
		if (!self::test())
		{
			// Legacy error handling switch based on the JError::$legacy switch.
			// @deprecated  12.1
			if (JError::$legacy)
			{
				$this->errorNum = 1;
				$this->errorMsg = JText::_('JLIB_DATABASE_ERROR_ADAPTER_PDO');
				return;
			}
			else
			{
				throw new RuntimeException(JText::_('JLIB_DATABASE_ERROR_ADAPTER_PDO'));
			}
		}

		try
		{
			$this->connection = new PDO($connectionString,
										$options['user'],
										$options['password'],
										$options['driverOptions']);
		}
		catch (PDOException $e)
		{
			// Legacy error handling switch based on the JError::$legacy switch.
			// @deprecated  12.1
			if (JError::$legacy)
			{
				$this->errorNum = 2;
				$this->errorMsg = JText::_('JLIB_DATABASE_ERROR_CONNECT_PDO') . ': ' .
								  $e->getMessage();
				return;
			}
			else
			{
				throw new RuntimeException(JText::_('JLIB_DATABASE_ERROR_CONNECT_PDO') . ': ' .
								  $e->getMessage());
			}
		}

		// Finalize initialisation
		parent::__construct($options);
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
	 * Method to escape a string for usage in an SQL statement.
	 * Doesn't do anything in the PDO driver at this time.
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
	 * Execute the SQL statement.
	 *
	 * @return  mixed  A database cursor resource on success, boolean false on failure.
	 *
	 * @since   11.4
	 * @throws  RuntimeException
	 */
	public function execute()
	{
		if (!is_object($this->connection))
		{
			// Legacy error handling switch based on the JError::$legacy switch.
			// @deprecated  12.1
			if (JError::$legacy)
			{

				if ($this->debug)
				{
					JError::raiseError(500, 'JDatabaseDriverPDO::query: ' . $this->errorNum . ' - ' . $this->errorMsg);
				}
				return false;
			}
			else
			{
				JLog::add(JText::sprintf('JLIB_DATABASE_QUERY_FAILED', $this->errorNum, $this->errorMsg), JLog::ERROR, 'database');
				throw new RuntimeException($this->errorMsg, $this->errorNum);
			}
		}

		// Take a local copy so that we don't modify the original query and cause issues later
		$sql = $this->replacePrefix((string) $this->sql);
		if ($this->limit > 0 || $this->offset > 0)
		{
			// @TODO
			$sql .= ' LIMIT ' . $this->offset . ', ' . $this->limit;
		}

		// If debugging is enabled then let's log the query.
		if ($this->debug)
		{
			// Increment the query counter and add the query to the object queue.
			$this->count++;
			$this->log[] = $sql;

			JLog::add($sql, JLog::DEBUG, 'databasequery');
		}

		// Reset the error values.
		$this->errorNum = 0;
		$this->errorMsg = '';

		// Execute the query.
		$this->executed = false;
		if ($this->prepared instanceof PDOStatement)
		{
			// Bind the variables:
			if ($this->sql instanceof JDatabaseQueryPreparable)
			{
				$bounded =& $this->sql->getBounded();
				foreach($bounded as $key => $obj)
				{
					$this->prepared->bindParam($key, $obj->value, $obj->dataType, $obj->length, $obj->driverOptions);
				}
			}

			$this->executed = $this->prepared->execute();
		}

		// If an error occurred handle it.
		if (!$this->executed)
		{
			$this->errorNum = (int) $this->connection->errorCode();
			$this->errorMsg = (string) 'SQL: ' . implode(", ", $this->connection->errorInfo());

			// Legacy error handling switch based on the JError::$legacy switch.
			// @deprecated  12.1
			if (JError::$legacy)
			{

				if ($this->debug)
				{
					JError::raiseError(500, 'JDatabaseDriverPDO::query: ' . $this->errorNum . ' - ' . $this->errorMsg);
				}
				return false;
			}
			else
			{
				JLog::add(JText::sprintf('JLIB_DATABASE_QUERY_FAILED', $this->errorNum, $this->errorMsg), JLog::ERROR, 'databasequery');
				throw new RuntimeException($this->errorMsg, $this->errorNum);
			}
		}

		return $this->prepared;
	}

	/**
	 * Retrieve a PDO database connection attribute
	 *
	 * @param  mixed $key
	 * @return resource
	 *
	 * @since  11.4
	 */
	public function getOption($key)
	{
		return $this->connection->getAttribute($key);
	}

	/**
	 * Sets an attribute on the PDO database handle.
	 * http://www.php.net/manual/en/pdo.setattribute.php
	 *
	 * @param  integer $key
	 * @param  mixed   $value
	 * @return resource
	 *
	 * @since  11.4
	 */
	public function setOption($key, $value)
	{
		return $this->connection->setAttribute($key, $value);
	}

	/**
	 * Test to see if the PDO extension is available.
	 * Override as needed to check for specific PDO Drivers.
	 *
	 * @return  boolean  True on success, false otherwise.
	 *
	 * @since   11.4
	 */
	public static function test()
	{
		return defined('PDO::ATTR_DRIVER_NAME');
	}

	/**
	 * Determines if the connection to the server is active.
	 *
	 * @return  boolean  True if connected to the database engine.
	 *
	 * @since   11.4
	 */
	public function connected()
	{
		return $this->connection;
	}

	/**
	 * Get the number of affected rows for the previous executed SQL statement.
	 * Only applicable for DELETE, INSERT, or UPDATE statements.
	 *
	 * @return  integer  The number of affected rows.
	 *
	 * @since   11.4
	 */
	public function getAffectedRows()
	{
		if ($this->prepared instanceof PDOStatement)
		{
			return $this->prepared->rowCount();
		}
		else
		{
			return 0;
		}
	}

	/**
	 * Get the number of returned rows for the previous executed SQL statement.
	 *
	 * @param   resource  $cursor  An optional database cursor resource to extract the row count from.
	 *
	 * @return  integer   The number of returned rows.
	 *
	 * @since   11.4
	 */
	public function getNumRows($cursor = null)
	{
		if ($cursor instanceof PDOStatement)
		{

		}
		else if ($this->prepared instanceof PDOStatement)
		{
			return $this->prepared->rowCount();
		}
		else
		{
			return 0;
		}
	}

	/**
	 * Method to get the auto-incremented value from the last INSERT statement.
	 *
	 * @return  integer  The value of the auto-increment field from the last inserted row.
	 *
	 * @since   11.4
	 */
	public function insertid()
	{
		return $this->connection->lastInsertId();
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
	 * Sets the SQL statement string for later execution.
	 *
	 * @param   mixed    $query           The SQL statement to set either as a JDatabaseQuery object or a string.
	 * @param   integer  $offset          The affected row offset to set.
	 * @param   integer  $limit           The maximum affected rows to set.
	 * @param   array    $driverOptions   The optional PDO driver options
	 *
	 * @return  JDatabase  This object to support method chaining.
	 *
	 * @since   11.4
	 */
	public function setQuery($query, $offset = null, $limit = null, $driverOptions = array())
	{
		$this->freeResult();

		if (is_string($query))
		{
			// Allows taking advantage of bound variables in a direct query:
			$query = $this->getQuery(true)->setQuery($query);
		}

		if ($query instanceof JDatabaseQueryLimitable && !empty($offset) && !empty($limit))
		{
			$query->setLimit($limit, $offset);
		}

		$sql = $this->replacePrefix((string) $query);

		$this->prepared = $this->connection->prepare($sql, $driverOptions);

		// Store reference to the JDatabaseQuery instance:
		parent::setQuery($query, $offset, $limit);

		return $this;
	}

	/**
	 * Set the connection to use UTF-8 character encoding.
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
	 * Method to commit a transaction.
	 *
	 * @return  bool
	 *
	 * @since   11.4
	 * @throws  RuntimeException
	 */
	public function transactionCommit()
	{
		return $this->connection->commit();
	}

	/**
	 * Method to roll back a transaction.
	 *
	 * @return  bool
	 *
	 * @since   11.4
	 * @throws  RuntimeException
	 */
	public function transactionRollback()
	{
		return $this->connection->rollBack();
	}

	/**
	 * Method to initialize a transaction.
	 *
	 * @return  bool
	 *
	 * @since   11.4
	 * @throws  RuntimeException
	 */
	public function transactionStart()
	{
		return $this->connection->beginTransaction();
	}

	/**
	 * Method to fetch a row from the result set cursor as an array.
	 *
	 * @param   mixed  $cursor  The optional result set cursor from which to fetch the row.
	 *
	 * @return  mixed  Either the next row from the result set or false if there are no more rows.
	 *
	 * @since   11.4
	 */
	protected function fetchArray($cursor = null)
	{
		if (!empty($cursor) && $cursor instanceof PDOStatement)
		{
			return $cursor->fetch(PDO::FETCH_NUM);
		}
		if ($this->prepared instanceof PDOStatement)
		{
			return $this->prepared->fetch(PDO::FETCH_NUM);
		}
	}

	/**
	 * Method to fetch a row from the result set cursor as an associative array.
	 *
	 * @param   mixed  $cursor  The optional result set cursor from which to fetch the row.
	 *
	 * @return  mixed  Either the next row from the result set or false if there are no more rows.
	 *
	 * @since   11.4
	 */
	protected function fetchAssoc($cursor = null)
	{
		if (!empty($cursor) && $cursor instanceof PDOStatement)
		{
			return $cursor->fetch(PDO::FETCH_ASSOC);
		}
		if ($this->prepared instanceof PDOStatement)
		{
			return $this->prepared->fetch(PDO::FETCH_ASSOC);
		}
	}

	/**
	 * Method to fetch a row from the result set cursor as an object.
	 *
	 * @param   mixed   $cursor  The optional result set cursor from which to fetch the row.
	 * @param   string  $class   Unused, only necessary so method signature will be the same as parent.
	 *
	 * @return  mixed   Either the next row from the result set or false if there are no more rows.
	 *
	 * @since   11.4
	 */
	protected function fetchObject($cursor = null, $class = 'stdClass')
	{
		if (!empty($cursor) && $cursor instanceof PDOStatement)
		{
			return $cursor->fetchObject($class);
		}
		if ($this->prepared instanceof PDOStatement)
		{
			return $this->prepared->fetchObject($class);
		}
	}

	/**
	 * Method to free up the memory used for the result set.
	 *
	 * @param   mixed  $cursor  The optional result set cursor from which to fetch the row.
	 *
	 * @return  void
	 *
	 * @since   11.4
	 */
	protected function freeResult($cursor = null)
	{
		$this->executed = false;

		if ($cursor instanceof PDOStatement)
		{
			$cursor->closeCursor();
			$cursor = null;
		}
		if ($this->prepared instanceof PDOStatement)
		{
			$this->prepared->closeCursor();
			$this->prepared = null;
		}
	}

	/**
	 * Method to get the next row in the result set from the database query as an object.
	 *
	 * @param   string  $class  The class name to use for the returned row object.
	 *
	 * @return  mixed   The result of the query as an array, false if there are no more rows.
	 *
	 * @since   11.4
	 * @throws  DatabaseException
	 */
	public function loadNextObject($class = 'stdClass')
	{
		// Execute the query and get the result set cursor.
		if (!$this->executed)
		{
			if (!($this->execute()))
			{
				return $this->errorNum ? null : false;
			}
		}

		// Get the next row from the result set as an object of type $class.
		if ($row = $this->fetchObject(null, $class))
		{
			return $row;
		}

		// Free up system resources and return.
		$this->freeResult();

		return false;
	}

	/**
	 * Method to get the next row in the result set from the database query as an array.
	 *
	 * @return  mixed  The result of the query as an array, false if there are no more rows.
	 *
	 * @since   11.4
	 * @throws  DatabaseException
	 */
	public function loadNextAssoc()
	{
		// Execute the query and get the result set cursor.
		if (!$this->executed)
		{
			if (!($this->execute()))
			{
				return $this->errorNum ? null : false;
			}
		}

		// Get the next row from the result set as an object of type $class.
		if ($row = $this->fetchAssoc())
		{
			return $row;
		}

		// Free up system resources and return.
		$this->freeResult();

		return false;
	}

	/**
	 * Method to get the next row in the result set from the database query as an array.
	 *
	 * @return  mixed  The result of the query as an array, false if there are no more rows.
	 *
	 * @since   11.4
	 * @throws  DatabaseException
	 */
	public function loadNextRow()
	{
		// Execute the query and get the result set cursor.
		if (!$this->executed)
		{
			if (!($this->execute()))
			{
				return $this->errorNum ? null : false;
			}
		}

		// Get the next row from the result set as an object of type $class.
		if ($row = $this->fetchArray())
		{
			return $row;
		}

		// Free up system resources and return.
		$this->freeResult();

		return false;
	}
}