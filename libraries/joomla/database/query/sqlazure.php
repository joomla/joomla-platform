<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Database
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Query Building Class.
 *
 * @package     Joomla.Platform
 * @subpackage  Database
 * @since       11.1
 */
class JDatabaseQuerySqlazure extends JDatabaseQuerySqlsrv
{
	/**
	 * The character(s) used to quote SQL statement names such as table names or field names,
	 * etc.  The child classes should define this as necessary.  If a single character string the
	 * same character is used for both sides of the quoted name, else the first character will be
	 * used for the opening quote and the second for the closing quote.
	 *
	 * @var    string
	 *
	 * @since  11.1
	 */
	protected $name_quotes = '';

	/**
	 * Generates a Globally Unique Identifier (32 hexadecimal digits separated by hyphens as 8-4-4-4-12).
	 *
	 * Usage:
	 * $query->set('guid = ' . $query->GUID());
	 *
	 * @return  string
	 *
	 * @since   12.3
	 */
	public function GUID()
	{
		return 'NEWID()';
	}
}
