<?php
/**
 * @package     Joomla.Platform
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.orm.database.databasequeryexception');

/**
 * JOrm Database Query class
 *
 * Joomla Object Relational Map Query
 *
 * @package     Joomla.Platform
 * @subpackage  Database
 * @since       11.1
 * @tutorial	Joomla.Platform/JOrmDatabaseQuery.cls
 * @link		http://docs.joomla.org/JOrmDatabaseQuery
 */
class JOrmDatabaseQuery
{
	/**
	 * Array that will default options to JOrmClassOptions object.
	 *
	 * @var    array
	 * @since  11.1
	 */
	protected $_default_options = array(
			//name
			'name' => '',
			//select fields
			'fields' => array(),
			//table prefix
			'tbl_prefix' => '#__',
			//table alias
			'tbl_alias' => null,
			//reference to anothers
			'references' => array(),
			//foreign tables
			'foreign_tbls' => array(),
			//jtable config
			'jtable' => array(
				'prefix' => 'JTable',
				'tbl' => '',
				'tbl_key' => '',
				'db' => ''
			)
	);
	
	/**
	 * JORMClassOptions object
	 * 
	 * @var	JORMClassOptions object
	 * @since 11.1
	 */
	protected $_options;
	
	/**
	 * Config options to bind JORMClassOptions object
	 * 
	 * @var array
	 */
	protected $_config_options = array();
	
	/**
	 * JDatabase connector object.
	 *
	 * @var	JDatabase object
	 * @since  11.1
	 */
	protected $_db;
	
	/**
	 * The JTable Class.
	 *
	 * @var	JTable object
	 * @since  11.1
	 */
	protected $_jtable;
	
	/**
	 * The JDatabaseQuery Class.
	 *
	 * @var	JDatabaseQuery object
	 * @since  11.1
	 */
	protected $_query;
	
	/**
	 * Constructor class can recive another JOrmDatabaseQuery object by reference
	 * 
	 * @param JOrmDatabaseQuery object
	 * 
	 * @since 11.1
	 */
	public function __construct(JOrmDatabaseQuery $reference=null)
	{
		// Set internal variables.
		$this->_db 		 = JFactory::getDbo();
		
		$this->_default_options['jtable']['db'] = $this->_db;
		
		jimport('joomla.orm.class.options');
		$this->_options	= new JOrmClassOptions($this->_default_options);
		$this->_options->setOptions($this->_config_options);
		
		//checking object
		if ( is_object($reference) )
		{
			JOrmDatabaseQueryException::checkObjectSubclass($reference);
			
			//Copy query instance
			$this->_query = &$reference->_query;
			//Initialize
			$this->initialize();
			//Auto join
			$this->autoJoin($reference);
			//Create a reference to back to scope
			$this->addReference($reference->getName(),get_class($reference));
		}
		else {
			$this->_query = $this->_db->getQuery(true);
			//Initialize
			$this->initialize();
			//Create select
			$this->createSelect();
		}
	}
	
	/**
	 * Create a instance from object that extends JDatabaseQuery Class these objects helps to construct a query builder
	 * 
	 * @param string name of object instance
	 * 
	 * @return object instance of JOrmDatabaseQuery
	 * 
	 * @since 11.1
	 */
	public static function getInstance($queryObject,$reference=null)
	{
		// Sanitize and prepare the table class name.
		$queryObject = preg_replace('/[^A-Z0-9_\.-]/i', '', $queryObject);
		$queryObjectClass = ucfirst($queryObject);
		
		// Only try to load the class if it doesn't already exist.
		if (!class_exists($queryObjectClass)) {
			// Search for the class file in the JOrmDatabaseQuery include paths.
			jimport('joomla.filesystem.path');

			if ($path = JPath::find(self::addIncludePath(), strtolower($queryObject).'.php')) {
				// Import the class file.
				require_once $path;

				// If we were unable to load the proper class, raise a warning and return false.
				if (!class_exists($queryObjectClass)) {
					JError::raiseWarning(0, JText::sprintf('JLIB_ORM_DATABASE_QUERY_OBJECT_ERROR_CLASS_NOT_FOUND_IN_FILE', $queryObjectClass));
					return false;
				}
			}
			else {
				// If we were unable to find the class file in the JTable include paths, raise a warning and return false.
				JError::raiseWarning(0, JText::sprintf('JLIB_ORM_OBJECT_ERROR_NOT_SUPPORTED_FILE_NOT_FOUND', $queryObject));
				return false;
			}
		}
		
		// Instantiate a new JOrmDatabaseQuery class and return it.
		return new $queryObjectClass($reference);
	}
	
	/**
	 * Create a dinamyc instance of array options passed by reference
	 * 
	 * @param Array options
	 * 
	 * @param JOrmDatabaseQuery reference
	 * 
	 * @return JOrmDatabaseQuery Object
	 * 
	 * @since 11.1
	 */
	public static function createInstance(array $options,JOrmDatabaseQuery $reference = null)
	{
		$instance = new JOrmDatabaseQuery();
		
		//set options
		$instance->_options->setOptions($options);
		
		//Initialize
		$instance->initialize();
		
		//Create select
		if ( is_object($reference) )
		{
			$instance->_query = &$reference->_query;
			$instance->autoJoin($reference);
			//Create a reference to back to scope
			$instance->addReference($reference->getName(),get_class($reference));
		}
		else {
			$instance->createSelect();
		}
		
		return $instance;
	}
	
	/**
	 * Create a reference to another JOrmDatabaseQuery Object
	 * 
	 * @param string $alias
	 * 
	 * @param string|array|JOrmDatabaseQuery Object $config
	 * 
	 * @return object current object or JOrmDatabaseQuery object
	 * 
	 * @since 11.1
	 */
	public function addReference($alias,$config)
	{
		//clean alias name
		$alias	= preg_replace('/[^A-Z0-9_]/i', '', $alias);
		
		//check object
		if ( is_object($config) ){
			JOrmDatabaseQueryException::checkObjectSubclass($config);
		}
		
		$this->_options->references[$alias] = $config;
		
		return $this;
	} 
	
	/**
	 * This function will build a select on table if options properties tbl e fields is set
	 * 
	 * @return void
	 * 
	 * @since 11.1
	 */
	private function createSelect()
	{
		if ( !empty($this->_options->fields) && !empty($this->_options->tbl) )
		{
			$tmp_options = $this->_options->fields;
			
			foreach ($tmp_options as &$field)
			{
				$field = $this->addAliasToField($field);
			}
		
			$this->_query->select($tmp_options)->from($this->getTableName());
		}
	}
	
	/**
	 * Return complete table name and alias or only table name/alias 
	 * 
	 * @param boolean $onlyName if true will return only table name or alias
	 * 
	 * @since 11.1
	 */
	private function getTableName($onlyName=false)
	{
		$table = $this->_options->tbl_prefix . $this->_options->tbl;
		if ($onlyName) {
			if( !empty($this->_options->tbl_alias) )
			{
				$table = $this->_options->tbl_alias;
			}
			
			return $table;
		}
		
		if( !empty($this->_options->tbl_alias) )
		{
			$table .= ' AS '.$this->_options->tbl_alias;
		}
		
		return $table;
	}
	
	/**
	 * Return a JTable instance
	 * 
	 * @since 11.1
	 * 
	 * @return JTable Object
	 */
	public function getJTable()
	{
		return $this->_jtable;
	}
	
	/**
	 * Check the autojoin between JOrmDatabaseQuery objects
	 * 
	 * @param JOrmDatabaseQuery $reference reference to another object
	 * 
	 * @return boolean false if cant join tables
	 * 
	 * @since 11.1
	 */
	private function autoJoin(JOrmDatabaseQuery $reference)
	{
		$foreign_tbls = $this->_options->foreign_tbls;
		if ( !array_key_exists($reference->_options->tbl, $foreign_tbls) )
		{
			return false;	
		}
		
		$foreign = $foreign_tbls[$reference->_options->tbl];
		
		$join_type 		= $foreign['jointype'];
		$join_columns 	= $foreign['joincolumn'];
		$columns 		= !empty($foreign['column']) ? $foreign['column'] : array() ;
		$conditions 	= $this->getTableName();
		
		$arrJoinColumns = array();
		if( array_key_exists(0, $join_columns) )
		{
			foreach($join_columns as $join_column)
			{
				$arrJoinColumns[] = $reference->getTableName(true).$join_column['name'].' = '.$this->getTableName(true).$join_column['referencedColumnName'];
			}
		}
		else {
			$arrJoinColumns[] = $reference->addAliasToField($join_columns['name']).' = '.$this->addAliasToField($join_columns['referencedColumnName']);
		}
		
		$conditions .= ' ON ('.implode(' AND ',$arrJoinColumns).')';
		
		//create join type
		switch ($join_type)
		{
			case 'left':
				$this->_query->leftJoin($conditions);
				break;
			case 'rigth':
				$this->_query->rightJoin($conditions);
				break;
			default:
				$this->_query->join($join_type,$conditions);
				break;
		}
		
		//add columns to select
		if( !empty($columns) )
		{
			$this->_query->select($columns);
		}
	}
	
	/**
	 * Method to initialize class variables
	 * 
	 * @return void
	 * 
	 * @since 11.1
	 */
	protected function initialize()
	{
		if ( !empty($this->_options->tbl) )
		{
			jimport('joomla.string.stringinflector');
			
			//get table columns
			$columns = $this->_db->getTableColumns($this->_options->tbl_prefix.$this->_options->tbl);
			
			//check column type and add to countable work if has a numeric type
			foreach ($columns as $field => $field_type)
			{
				switch ($field_type)
				{
					case 'tinyint':
					case 'int':
						JStringInflector::addCountable($field);
				}
			}
		}

		//set the select fields if empty
		if ( empty($this->_options->fields) )
		{
			$this->_options->fields = array_keys($columns);
		}
			
		//check config of JTable class
		if ( !empty($this->_options->jtable) && is_array($this->_options->jtable) )
		{
			$this->getInstanceJTable($this->_options->jtable);
		}
	}
	
	/**
	 * Create or get instance of JTable class and set to jtable property
	 * 
	 * @param array Config
	 * 
	 * @return object current object
	 * 
	 * @since 11.1
	 */
	public function getInstanceJTable(array $config)
	{
		//add tables path
		jimport('joomla.database.table');
		JTable::addIncludePath(dirname(__FILE__).DS.'table');
		
		if(!empty($config['tbl_key']) && !empty($config['tbl']) && ($config['db'] instanceof JDatabase))
		{
			$jtable = new JORMDatabaseTable($config['tbl'], $config['tbl_key'], $config['db']);
		}
		else if(isset($config['type']) && !empty($config['type']) && isset($config['prefix']) && !empty($config['prefix'])){
			$jtable = JORMDatabaseTable::getInstance($config['type'],$config['prefix']);
		}
		else {
			$jtable = $config;
		}
		
		$this->_jtable = $jtable;
		
		return $this;
	}
	
	/**
	 * Return name of class or self name property
	 * 
	 * @return name of query object class
	 * 
	 * @since 11.1
	 */
	public function getName()
	{
		return !empty($this->_options->name) ? $this->_options->name : get_class($this) ;
	}
	
	/**
	 * Adds to the stack of JOrmDatabaseQueryHelpers paths in LIFO order.
	 * 
	 * @param mixed  $path  A filesystem path or array of filesystem paths to add.
	 * 
	 * @return void
	 * 
	 * @since 11.1
	 */
	public static function addHelperPath($path = null)
	{
		jimport('joomla.orm.query.helper');
		JOrmDatabaseQueryHelper::addIncludePath($path);
	}
	
	/**
	 * Instance a Helper class that do stuffs like: render modules, dump data, etc.
	 * 
	 * @param string $helper name of helper instance
	 * 
	 * @param string $prefix preifx of helper class
	 * 
	 * @return JOrmHelper object
	 * 
	 * @since 11.1
	 */
	public function getHelper($helper,$prefix='JOrmDatabaseQueryHelper')
	{
		return JOrmDatabaseQueryHelper::getInstance($helper,$prefix, $this);
	}
	
	/**
	 * Add a filesystem path where JOrmDatabaseQuery should search for table class files.
	 * You may either pass a string or an array of paths.
	 *
	 * @param   mixed  A filesystem path or array of filesystem paths to add.
	 *
	 * @return  array  An array of filesystem paths to find JOrmDatabaseQuery classes in.
	 *
	 * @link    http://docs.joomla.org/JOrmDatabaseQuery/addIncludePath
	 * 
	 * @since   11.1
	 */
	public static function addIncludePath($path = null)
	{
		// Declare the internal paths as a static variable.
		static $_paths;

		// If the internal paths have not been initialised, do so with the base table path.
		if (!isset($_paths))
		{
			$_paths = array(dirname(__FILE__) . '/query');
		}

		// Convert the passed path(s) to add to an array.
		settype($path, 'array');

		// If we have new paths to add, do so.
		if (!empty($path) && !in_array($path, $_paths))
		{
			// Check and add each individual new path.
			foreach ($path as $dir)
			{
				// Sanitize path.
				$dir = trim($dir);

				// Add to the front of the list so that custom paths are searched first.
				array_unshift($_paths, $dir);
			}
		}

		return $_paths;
	}
	
	/**
	 * Method to set property on JOrmClassOptions or set JTable property
	 * 
	 * @return void
	 * 
	 * @since 11.1
	 */
	public function __set($property,$value)
	{
		if ($this->_options->hasProperty($property))
		{
			$this->_options->set($property,$value);
		}
		else {
			if (!($this->_jtable instanceof JTable)) {
				throw new Exception(JText::_('You must set JTable Class'),500);
			}
			
			$this->_jtable->set($property,$value);
		}
	}
	
	/**
	 * This function will check method and callback using these order
	 * 
	 * Call order
	 * 
	 * 1 - Reference
	 * 2 - Field
	 * 3 - JTable 
	 * 4 - JDatabaseQuery
	 * 5 - JDatabase
	 * 
	 * @return mixed current object when call to a field, referenced object when have it, JTable method, JDatabaseQuery method or JDatabase method
	 * 
	 * @since 11.1
	 */
	public function __call($method,$arguments)
	{
		settype($arguments, 'array');
		
		//check to call another instance
		$return = $this->callReference($method);
		if (is_object($return))
		{
			return $return;
		}
		
		//check if method is a field
		$return = $this->callField($method, $arguments);
		if (is_object($return))
		{
			return $return;
		}
		
		/**
		 * Call JTable methods
		 */
		if ( method_exists($this->_jtable, $method) )
		{
			return call_user_method_array($method, $this->_jtable, $arguments);
		}
		
		/**
		 * Call JDatabaseQuery methods
		 */
		if ( method_exists($this->_query, $method) )
		{
			call_user_method_array($method, $this->_query, $arguments);
			return $this;
		}
		
		/**
		 * Call JDatabase methods
		 */
		if ( method_exists($this->_db, $method) )
		{
			$this->_db->setQuery($this->_query);
			return call_user_method_array($method, $this->_db, $arguments);
		}
		
		JOrmDatabaseQueryException::callMethodNotExists($method,$this);
	}
	
	/**
	 * Checking referenced config and return a JOrmDatabaseQuery object when exists, or false
	 * 
	 * @param string, object, array $method
	 * 
	 * @throws Exception
	 * 
	 * @return JOrmDatabaseQuery object or FALSE
	 * 
	 * @since 11.1
	 */
	final private function callReference($method)
	{
		//check if method is a reference
		$references = $this->_options->references;
		
		if ( array_key_exists($method, $references) )
		{
			$reference_data = $references[$method];
			
			/**
			 * If reference is a string try to get instance
			 */
			if( is_string($reference_data) && !class_exists($reference_data) )
			{
				$reference = self::getInstance($reference_data,$this);
			}
			/**
			 * If reference is an array create a new instance
			 */
			else if ( is_array($reference_data) )
			{
				$reference_data['name'] = $method;
				$reference = self::createInstance($reference_data,$this);
			}
				
			/**
			 * Check object class
			 */
			JOrmDatabaseQueryException::checkObjectSubclass($reference);
			
			return $reference;
		}
		
		return false;
	}
	
	/**
	 * Retrun field with table name or table alias
	 * 
	 * @param string $field
	 * 
	 * @return string field with alias
	 */
	private function addAliasToField($field)
	{
		return $this->_getTable(true).'.'.$field;
	}
	
	/**
	 * Check if call method is a table field and return self if exists, else returns false
	 * 
	 * @param string $method
	 * 
	 * @param array $arguments
	 * 
	 * @return mixed current object when field exists, other else false
	 */
	protected function callField($method,$arguments)
	{
		$count_arguments = count($arguments);
		
		$field_key = array_search($method, $this->_options->fields);
		
		//check if exists on fields list
		if ( array_search($method, $this->_options->fields) !== false )
		{
			$string = $this->addAliasToField($method);
			
			//check if is one argument set the condition equal argument
			if ( $count_arguments == 1 )
			{
				$string .= '=' . $this->_db->quote($arguments[0],true);
			}
			else {
				//add quote to every argument
				call_user_method_array('quote',$this->_db,$arguments);
				
				//if countable field change the comparison method to IN, else use LIKE
				jimport('joomla.string.stringinflector');
				if ( JStringInflector::countable($method) )
				{
					$string .= ' IN('. implode(',',$arguments) .')';
				}
				else {
					$string .= ' LIKE("'. implode('","',$arguments) .'")';
				}
			}
			
			//add to where clause
			$this->_query->where($string);
			
			return $this;
		}
		
		return false;
	}
}