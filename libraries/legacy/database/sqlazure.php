<?php
/**
 * @package     Joomla.Legacy
 * @subpackage  Database
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

JLog::add('JDatabaseSqlazure is deprecated, use JDatabaseDriverSqlazure instead.', JLog::WARNING, 'deprecated');

/**
 * SQL Server database driver
 *
 * @package     Joomla.Legacy
 * @subpackage  Database
 * @see         http://msdn.microsoft.com/en-us/library/ee336279.aspx
 * @since       11.1
 * @deprecated  13.1 Use JDatabaseDriverSqlazure instead.
 */
class JDatabaseSqlazure extends JDatabaseDriverSqlazure
{
}
