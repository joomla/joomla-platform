<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Database
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

require_once JPATH_PLATFORM . '/joomla/database/object.php';

/**
 * Joomla Platform Database Object Inspector Class
 *
 * @package     Joomla.UnitTest
 * @subpackage  Database
 * @since       12.1
 */
class JDatabaseObjectInspector extends JDatabaseObject
{
	/**
	 * The object tables.
	 *
	 * @var    array
	 * @since  12.1
	 */
	protected $tables = array(
		'primary'	=> '#__content',
		'hits'		=> '#__content_hits'
	);

	/**
	 * The table keys.
	 *
	 * @var    array
	 * @since  12.1
	 */
	protected $keys = array(
		'primary'	=> array('primary' => 'content_id'),
		'hits'		=> array('primary' => 'content_id')
	);

	/**
	 * Method to instantiate a database object.
	 *
	 * @param   mixed  $db  An optional argument to provide dependency injection for the database
	 *                      adapter.  If the argument is a JDatbase adapter that object will become
	 *                      the database adapter, otherwise the default adapter will be used.
	 *
	 * @since   12.1
	 * @throws  RuntimeException
	 */
	public function __construct(JDatabase $db = null)
	{
		parent::__construct($db);
	}

	/**
	 * Method to test that magic __get recursively will generate an error.
	 *
	 * @return  string  The value.
	 *
	 * @since   12.1
	 */
	public function getRecursionError()
	{
		return $this->recursion_error;
	}

	/**
	 * Method to set the test_value.
	 *
	 * @param   string  $value  The test value.
	 *
	 * @return  JContent  The content object.
	 *
	 * @since   12.1
	 */
	public function setTestValue($value)
	{
		// Set the property as uppercase.
		$this->properties['test_value'] = strtoupper($value);

		return $this;
	}
}