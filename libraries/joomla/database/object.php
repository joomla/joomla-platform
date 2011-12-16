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
 * Joomla Platform Database Object Class
 *
 * @package     Joomla.Platform
 * @subpackage  Database
 * @since       12.1
 */
abstract class JDatabaseObject extends JCacheObject
{
	/**
	 * The database adapter.
	 *
	 * @var    JDatabase
	 * @since  12.1
	 */
	protected $db;

	/**
	 * The initialized state of the tables.
	 *
	 * @var    boolean
	 * @since  12.1
	 */
	protected $initialized = false;

	/**
	 * The object properties.
	 *
	 * @var    array
	 * @since  12.1
	 */
	protected $properties = array();

	/**
	 * The dump properties. These properties are added into JContent::dump()
	 * calls so that properties can be dumped that are not necessarily in the
	 * database.
	 *
	 * @var    array
	 * @since  12.1
	 */
	protected $dump = array();

	/**
	 * The object methods.
	 *
	 * @var    array
	 * @since  12.1
	 */
	protected $methods = array();

	/**
	 * The object tables.
	 *
	 * @var    array
	 * @since  12.1
	 */
	protected $tables = array();

	/**
	 * The table keys.
	 *
	 * @var    array
	 * @since  12.1
	 */
	protected $keys = array();

	/**
	 * The table columns.
	 *
	 * @var    array
	 * @since  12.1
	 */
	protected $columns = array();

	/**
	 * Method to instantiate a database object.
	 *
	 * @param   mixed  $db  An optional argument to provide dependency injection for the database
	 *                      adapter.  If the argument is a JDatbase adapter that object will become
	 *                      the database adapter, otherwise the default adapter will be used.
	 *
	 * @since   12.1
	 * @throws  LogicException
	 */
	public function __construct(JDatabase $db = null)
	{
		// If a database adapter is given, use it.
		if ($db instanceof JDatabase)
		{
			$this->db = $db;
		}
		// Create the database adapter.
		else
		{
			$this->db = JFactory::getDbo();
		}

		// Set the class methods.
		$this->methods = get_class_methods($this);

		// Assert that a primary table is set.
		if (empty($this->tables['primary']))
		{
			throw new LogicException(JText::_('JDATABASEOBJECT_PRIMARY_TABLE_NOT_SET'));
		}

		// Assert that the primary table is first.
		if (array_shift(array_keys($this->tables)) !== 'primary')
		{
			throw new LogicException(JText::_('JDATABASEOBJECT_PRIMARY_TABLE_NOT_FIRST'));
		}

		// Assert that a primary key is set.
		if (empty($this->keys['primary']['primary']))
		{
			throw new LogicException(JText::_('JDATABASEOBJECT_PRIMARY_KEY_NOT_SET'));
		}
	}

	/**
	 * Method to bind the object properties.
	 *
	 * @param   mixed  $properties  The object properties.
	 *
	 * @return  JDatatbaseObject  The database object.
	 *
	 * @since   12.1
	 * @throws  InvalidArgumentException
	 */
	public function bind($properties)
	{
		// Check the properties data type.
		if (!is_array($properties) && !is_object($properties))
		{
			throw new InvalidArgumentException(JText::sprintf('JDATABASEOBJECT_INVALID_BIND_DATA', gettype($properties)));
		}

		// Convert properties to an array.
		$properties = (array) $properties;

		// Bind the properties.
		foreach ($properties as $property => $value)
		{
			// Set the property.
			$this->setProperty($property, $value);
		}

		return $this;
	}

	/**
	 * Method to create an object in the database.
	 *
	 * @return  JDatabaseObject  The object.
	 *
	 * @since   12.1
	 * @throws  RuntimeException
	 */
	public function create()
	{
		// Validate the object.
		$this->validate();

		// Perform the actual database work.
		$this->doCreate();

		return $this;
	}

	/**
	 * Method to delete an object from the database.
	 *
	 * @return  JDatabaseObject  The object.
	 *
	 * @since   12.1
	 * @throws  LogicException
	 * @throws  RuntimeException
	 */
	public function delete()
	{
		// Assert the object is loaded.
		$this->assertIsLoaded();

		// Perform the actual database work.
		$this->doDelete();

		return $this;
	}

	/**
	 * Method to dump the object properties.
	 *
	 * @param   boolean  $useMethod  If true, properties will be unpacked using the custom get method.
	 *
	 * @return  object  The object properties.
	 *
	 * @since   12.1
	 */
	public function dump($useMethod = true)
	{
		// Setup a container.
		$dump = new stdClass;

		// Merge the dump properties and the object properties.
		$properties = array_merge($this->dump, array_keys($this->properties));

		// Dump all object properties.
		foreach ($properties as $property)
		{
			// Get the property.
			$dump->$property = $this->getProperty($property, $useMethod);
		}

		return $dump;
	}

	/**
	 * Method to load an object from the database by primary id.
	 *
	 * @param   integer  $primaryId  The primary id.
	 *
	 * @return  JDatabaseObject  The object.
	 *
	 * @since   12.1
	 * @throws  LogicException
	 * @throws  RuntimeException
	 */
	public function load($primaryId)
	{
		// Get the primary key.
		$primaryKey = $this->getTableKey('primary', 'primary');

		// Set the primary id.
		$this->$primaryKey = (int) $primaryId;

		// Assert the object is loaded.
		$this->assertIsLoaded();

		// Perform the actual database work.
		$this->doLoad();

		return $this;
	}

	/**
	 * Method to update an object in the database.
	 *
	 * @return  JDatabaseObject  The object.
	 *
	 * @since   12.1
	 * @throws  LogicException
	 * @throws  RuntimeException
	 */
	public function update()
	{
		// Assert the object is loaded.
		$this->assertIsLoaded();

		// Validate the object.
		$this->validate();

		// Perform the actual database work.
		$this->doUpdate();

		return $this;
	}

	/**
	 * Runs custom validation methods against the object before storing.
	 *
	 * This method looks for and executes all other class methods prefixed with "validate".
	 *
	 * @return  JDatabaseObject  The object.
	 *
	 * @since   12.1
	 */
	public function validate()
	{
		// Iterate through the object methods.
		foreach ($this->methods as $method)
		{
			// Check if the method name starts with 'validate' but is not 'validate'.
			if (strpos($method, 'validate') === 0 && $method !== 'validate')
			{
				// Execute the method.
				$this->$method();
			}
		}

		return $this;
	}

	/**
	 * Checks if the primary key of the object is set.
	 *
	 * @return  boolean  True if loaded, false otherwise.
	 *
	 * @since   12.1
	 */
	public function isLoaded()
	{
		// Get the primary key.
		$primaryKey = $this->getTableKey('primary', 'primary');

		return isset($this->$primaryKey);
	}

	/**
	 * The magic set method is used to set an object property.
	 *
	 * This is a public proxy for the protected setProperty method.
	 *
	 * @param   string  $property  The property name.
	 * @param   mixed   $value     The property value.
	 *
	 * @return  JDatabaseObject  The database object.
	 *
	 * @since   12.1
	 */
	public function __set($property, $value)
	{
		// Set the property.
		$this->setProperty($property, $value);

		return $this;
	}

	/**
	 * The magic get method is used to get an object property.
	 *
	 * This method is a public proxy for the protected getProperty method.
	 *
	 * Note: Magic __get does not allow recursive calls. This can be tricky
	 * because the error generated by recursing into __get is "Undefined
	 * property:  {CLASS}::{PROPERTY}" which is misleading. This is relevant
	 * for this class because requesting a non-visible property can trigger
	 * a call to a getter for that property. If the getter references the
	 * property directly in the object, it will cause a recursion into __get.
	 *
	 * @param   string  $property  The property name.
	 *
	 * @return  mixed  The property value, null otherwise.
	 *
	 * @since   12.1
	 */
	public function __get($property)
	{
		// Get the property.
		return $this->getProperty($property);
	}

	/**
	 * The magic isset method is used to check the state of an object property.
	 *
	 * @param   string  $property  The property name.
	 *
	 * @return  boolean  True if set, false otherwise.
	 *
	 * @since   12.1
	 */
	public function __isset($property)
	{
		return isset($this->properties[$property]);
	}

	/**
	 * The magic unset method is used to unset an object property.
	 *
	 * @param   string  $property  The property name.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function __unset($property)
	{
		unset($this->properties[$property]);
	}

	/**
	 * Method to convert an object to a JSON string.
	 *
	 * @return  string  The object as a JSON string.
	 *
	 * @since   12.1
	 */
	public function __toString()
	{
		return json_encode($this->dump());
	}

	/**
	 * Method to assert the object is loaded or throw an exception.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 * @throws  LogicException
	 */
	protected function assertIsLoaded()
	{
		// Assert the object is loaded.
		if (!$this->isLoaded())
		{
			throw new LogicException(JText::_('JDATABASE_OBJECT_NOT_LOADED'));
		}
	}

	/**
	 * Method to add an object to the database.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 * @throws  RuntimeException
	 */
	protected function doCreate()
	{
		// Get the primary key.
		$primaryKey = $this->getTableKey('primary', 'primary');

		try
		{
			// Start a transaction.
			$this->db->transactionStart();

			// Store the data for each table.
			foreach ($this->tables as $alias => $table)
			{
				// Get the data for the table.
				$dump = $this->dumpTable($alias);

				// Store the data to the database.
				$this->db->insertObject($table, $dump, $primaryKey);

				// Update the primary id.
				if ($alias == 'primary')
				{
					// Set the primary key value.
					$this->$primaryKey = (int) $dump->$primaryKey;
				}
			}

			// Commit the transaction.
			$this->db->transactionCommit();
		}
		catch (RuntimeException $error)
		{
			// Rollback the transaction.
			$this->db->transactionRollback();

			// Rethrow the error.
			throw $error;
		}
	}

	/**
	 * Method to delete an object from the database.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 * @throws  RuntimeException
	 */
	protected function doDelete()
	{
		// Get the primary key.
		$primaryKey = $this->getTableKey('primary', 'primary');

		// Build the query to delete the item.
		$query = $this->db->getQuery(true);
		$query->delete();
		$query->from($this->getTableExpression('primary', false));
		$query->where($this->getTableKeyExpression('primary', 'primary', false) . ' = ' . (int) $this->$primaryKey);

		// Delete the item.
		$this->db->setQuery($query);
		$this->db->query();
	}

	/**
	 * Method to load an object by primary id.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 * @throws  RuntimeException
	 */
	protected function doLoad()
	{
		// Get the primary key.
		$primaryKey = $this->getTableKey('primary', 'primary');
		$primaryTable = $this->getTableExpression('primary');

		// Build the query object.
		$query = $this->db->getQuery(true);
		$query->select($this->getTableKeyExpression('primary', '*'));
		$query->from($primaryTable);
		$query->where($this->getTableKeyExpression('primary', 'primary') . ' = ' . (int) $this->$primaryKey);

		// Get the subtables.
		$tables = $this->tables;
		unset($tables['primary']);

		// Add additional tables to the query.
		foreach ($tables as $alias => $table)
		{
			// Add the table select and join clauses.
			$query->select($this->getTableKeyExpression($alias, '*'));
			$query->join('INNER', $this->getTableExpression($alias));
		}

		// Get the content data.
		$this->db->setQuery($query);
		$data = $this->db->loadObject();

		// Check the type data.
		if (empty($data))
		{
			throw new RuntimeException(JText::sprintf('JDATABASEOBJECT_NOT_FOUND', $this->$primaryKey, $primaryTable));
		}

		// Bind the data.
		$this->bind($data);
	}

	/**
	 * Method to update an object in the database.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 * @throws  RuntimeException
	 */
	protected function doUpdate()
	{
		// Get the primary key.
		$primaryKey = $this->getTableKey('primary', 'primary');

		try
		{
			// Start a transaction.
			$this->db->transactionStart();

			// Update the data for each table.
			foreach ($this->tables as $alias => $table)
			{
				// Store the data to the database.
				$this->db->updateObject($table, $this->dumpTable($alias), $primaryKey);
			}

			// Commit the transaction.
			$this->db->transactionCommit();
		}
		catch (RuntimeException $error)
		{
			// Rollback the transaction.
			$this->db->transactionRollback();

			// Rethrow the error.
			throw $error;
		}
	}

	/**
	 * Method to dump the object properties for a specific table.
	 *
	 * @param   string  $alias  The database table alias.
	 *
	 * @return  object  The table properties.
	 *
	 * @since   12.1
	 * @throws  InvalidArgumentException
	 */
	protected function dumpTable($alias)
	{
		// Check if the table exists.
		if (!array_key_exists($alias, $this->tables))
		{
			throw new InvalidArgumentException(JText::sprintf('JDATABASEOBJECT_INVALID_TABLE', $alias));
		}

		// Initialize the database tables.
		$this->initializeTables();

		// Setup a container.
		$dump = new stdClass;

		// Dump the properties for the table.
		foreach ($this->columns[$alias] as $property => $field)
		{
			// Check if the property is defined.
			if (array_key_exists($property, $this->properties))
			{
				// Set the value.
				$dump->$property = $this->properties[$property];
			}
			// The property is not defined.
			else
			{
				// Set to null.
				$dump->$property = null;
			}
		}

		return $dump;
	}

	/**
	 * Method to initialize the database tables.
	 *
	 * @return	void
	 *
	 * @since   12.1
	 * @throws  RuntimeException
	 */
	protected function initializeTables()
	{
		// Check if the tables have been initialized.
		if (!$this->initialized)
		{
			// Fetch the column data for each table.
			foreach ($this->tables as $alias => $table)
			{
				// Get the table columns.
				$columns = $this->db->getTableColumns($table, false);

				// Set the table columns.
				$this->columns[$alias] = $columns;
			}

			// Set the initialized flag.
			$this->initialized = true;
		}
	}

	/**
	 * Method to get an object property.
	 *
	 * When using getters/setters, it is worth noting that this method will
	 * attempt to get the property from object cache before calling the getter.
	 *
	 * @param   string   $property   The property name.
	 * @param   boolean  $useMethod  True to use an available getter method, false otherwise.
	 * @param   boolean  $useCache   True to try to load the data from cache, false otherwise.
	 *
	 * @return  mixed  The property value.
	 *
	 * @see     JDatabaseObject::__get()
	 * @since   12.1
	 */
	protected function getProperty($property, $useMethod = true, $useCache = true)
	{
		// Check if we should use the getter method.
		if ($useMethod)
		{
			// Get the property getter.
			$method = 'get' . JStringNormalise::toCamelCase($property);

			// Check for a getter method for the property.
			// Check that the property is not named "property" which would be recursive.
			if (in_array($method, $this->methods) && $method !== 'getProperty')
			{
				// Check if we should try to load the data from cache.
				if ($useCache)
				{
					// Attempt to retrieve the value from cache first.
					$cached = $this->retrieve($this->getStoreId($property), false);

					// Check if the cached value is usable.
					if (!is_null($cached))
					{
						return $cached;
					}
				}

				// Get the value from the getter.
				$value = $this->$method();

				// Store the value in cache.
				return $this->store($this->getStoreId($property), $value, false);
			}
		}

		// Get the value.
		return array_key_exists($property, $this->properties) ? $this->properties[$property] : null;
	}

	/**
	 * Method to set an object property.
	 *
	 * When using getters/setters, it is worth noting that this method will
	 * automatically call the getter after a property has been set to prime
	 * and/or update the object cache.
	 *
	 * @param   string   $property   The property name.
	 * @param   mixed    $value      The property value.
	 * @param   boolean  $useMethod  True to use an available getter method, false otherwise.
	 * @param   boolean  $useCache   True to try to load the data from cache, false otherwise.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	protected function setProperty($property, $value, $useMethod = true, $useCache = true)
	{
		// Check if we should use the setter method.
		if ($useMethod)
		{
			// Get the property setter.
			$method = 'set' . JStringNormalise::toCamelCase($property);

			// Check for a setter method for the property.
			// Check that the property is not named "property" which would be recursive.
			if (in_array($method, $this->methods) && $method !== 'setProperty')
			{
				// Set the value using the setter.
				$this->$method($value);

				// Check if we should use cache.
				if ($useCache)
				{
					// Load the property, not from cache.
					$value = $this->getProperty($property, true, false);

					// Store the value in cache.
					$this->store($this->getStoreId($property), $value, false);
				}

				return;
			}
		}

		// Set the value.
		$this->properties[$property] = $value;
	}

	/**
	 * Method to get a store id.
	 *
	 * @param   string  $id  An identifier string to generate the store id.
	 *
	 * @return  string  A store id.
	 *
	 * @since   12.1
	 */
	protected function getStoreId($id = '')
	{
		return md5(spl_object_hash($this) . ':' . $id);
	}

	/**
	 * Method to get a query expression for a table.
	 *
	 * @param   string   $alias     The table alias.
	 * @param   boolean  $useAlias  True to use the alias in the expression, false otherwise.
	 *
	 * @return  string  The table expression.
	 *
	 * @since   12.1
	 * @throws  InvalidArgumentException
	 */
	protected function getTableExpression($alias, $useAlias = true)
	{
		// Assert that the table alias is defined.
		if (!array_key_exists($alias, $this->tables))
		{
			throw new InvalidArgumentException(JText::sprintf('JDATABASEOBJECT_INVALID_TABLE', $alias));
		}

		// Get the table name.
		$table = $this->tables[$alias];

		// Build the table expression.
		$return = $this->db->quoteName($table);

		// Check if we should use the table alias.
		if ($useAlias)
		{
			$return .= ' AS ' . $this->db->quoteName($alias);
		}

		// Check if the table is the primary table.
		if ($alias != 'primary')
		{
			// Get the table key expressions.
			$tableKeyExpr	= $this->getTableKeyExpression($alias, 'primary', $useAlias);
			$primaryKeyExpr	= $this->getTableKeyExpression('primary', 'primary', $useAlias);

			// Add the table key expressions.
			$return .= ' ON ' . $tableKeyExpr . ' = ' . $primaryKeyExpr;
		}

		return $return;
	}

	/**
	 * Method to get a table key.
	 *
	 * @param   string  $alias  The table alias.
	 * @param   string  $key    The table key alias.
	 *
	 * @return  string  The table key name.
	 *
	 * @since   12.1
	 * @throws  InvalidArgumentException
	 */
	protected function getTableKey($alias, $key)
	{
		// Assert that the table alias is defined.
		if (!array_key_exists($alias, $this->keys))
		{
			throw new InvalidArgumentException(JText::sprintf('JDATABASEOBJECT_INVALID_TABLE', $alias));
		}

		// Assert that the table key is defined.
		if (!array_key_exists($key, $this->keys[$alias]))
		{
			throw new InvalidArgumentException(JText::sprintf('JDATABASEOBJECT_INVALID_TABLE_KEY', $alias, $key));
		}

		return $this->keys[$alias][$key];
	}

	/**
	 * Method to get a query expression for a table key.
	 *
	 * @param   string   $alias     The table alias.
	 * @param   string   $key       The table key alias.
	 * @param   boolean  $useAlias  True to use the alias in the expression, false otherwise.
	 *
	 * @return  string  The table expression.
	 *
	 * @since   12.1
	 * @throws  InvalidArgumentException
	 */
	protected function getTableKeyExpression($alias, $key, $useAlias = true)
	{
		$return = '';

		// Assert that the table alias is defined.
		if (!array_key_exists($alias, $this->tables) || !array_key_exists($alias, $this->keys))
		{
			throw new InvalidArgumentException(JText::sprintf('JDATABASEOBJECT_INVALID_TABLE', $alias));
		}

		// Check if the key is a column.
		if (isset($this->keys[$alias][$key]))
		{
			// Quote the column name.
			$column = $this->db->quoteName($this->keys[$alias][$key]);
		}
		// The key must be an expression.
		else
		{
			// Escape the expression.
			$column = $this->db->escape($key);
		}

		// Check if we should use the table alias.
		if ($useAlias)
		{
			$return .= $this->db->quoteName($alias) . '.';
		}

		// Build the table expression.
		$return .= $column;

		return $return;
	}
}
