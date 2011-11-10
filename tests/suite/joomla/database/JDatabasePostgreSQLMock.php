<?php
/**
 * @package    Joomla.UnitTest
 * @copyright  Copyright (C) 2005 - 2011 Open Source Matters. All rights reserved.
 * @license    GNU General Public License
 */

require_once dirname(__FILE__).'/JDatabaseMock.php';

/**
 * Mock class for JDatabase.
 *
 * @package  Joomla.UnitTest
 * @since    11.3
 */
class JDatabasePostgreSQLMock extends JDatabaseGlobalMock
{
	/**
	 * Creates and instance of the mock JDatabase object.
	 *
	 * @param   object  $test   A test object.
	 *
	 * @return  object
	 *
	 * @since   11.3
	 */
	public static function create($test)
	{
		// Collect all the relevant methods in JDatabase.
		$methods = array(
			'addQuoted',
			'connected',
			'debug',
			'escape',
			'explain',
			'fetchArray',
			'fetchAssoc',
			'fetchObject',
			'freeResult',
			'getAffectedRows',
			'getCollation',
			'getConnectors',
			'getDateFormat',
			'getErrorMsg',
			'getErrorNum',
			'getEscaped',
			'getInstance',
			'getLog',
			'getNullDate',
			'getNumRows',
			'getPrefix',
			'getQuery',
			'getTableColumns',
			'getTableCreate',
			'getTableFields',
			'getTableKeys',
			'getTableList',
			'getTicker',
			'getUtfSupport',
			'getVersion',
			'hasUTF',
			'insertId',
			'insertObject',
			'loadAssoc',
			'loadAssocList',
			'loadColumn',
			'loadObject',
			'loadObjectList',
			'loadResult',
			'loadResultArray',
			'loadRow',
			'loadRowList',
			'nameQuote',
			'query',
			'queryBatch',
			'quote',
			'quoteName',
			'replacePrefix',
			'select',
			'setQuery',
			'setUTF',
			'splitSql',
			'test',
			'transactionCommit',
			'transactionRollback',
			'transactionStart',
			'updateObject',
		);

		// Create the mock.
		$mockObject = $test->getMock(
			'JDatabase',
			$methods,
			// Constructor arguments.
			array(),
			// Mock class name.
			'',
			// Call original constructor.
			false
		);

		// Mock selected methods.
		$test->assignMockReturns(
			$mockObject, array(
				'getNullDate' => '1970-01-01 00:00:00'
			)
		);

		$test->assignMockCallbacks(
			$mockObject,
			array(
				'getQuery' => array(get_called_class(), 'mockGetQuery'),
				'quote' => array(get_called_class(), 'mockQuote'),
				'quoteName' => array(get_called_class(), 'mockQuoteName'),
				'setQuery' => array(get_called_class(), 'mockSetQuery'),
			)
		);

		return $mockObject;
	}

	/**
	 * Mocking the quote method.
	 *
	 * @param  string  $value  The value to be quoted.
	 *
	 * @return string  The value passed wrapped in MySQL quotes.
	 *
	 * @since  11.3
	 */
	public function mockQuote($value)
	{
		return '\'$value\'';
	}

	/**
	 * Mock nameQuote method.
	 *
	 * @param  string  $value  The value to be quoted.
	 *
	 * @return string  The value passed wrapped in MySQL quotes.
	 *
	 * @since  11.3
	 */
	public function mockQuoteName($value)
	{
		return '"$value"';
	}
}