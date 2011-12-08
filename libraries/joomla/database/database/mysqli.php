<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Database
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

// Deprecation warning.
JLog::add('JDatabaseMySQLi is deprecated, use JDatabaseDriverMySQLi instead.', JLog::NOTICE, 'deprecated');

/**
 * MySQLi database driver
 *
 * @package     Joomla.Platform
 * @subpackage  Database
 * @see         http://php.net/manual/en/book.mysqli.php
 * @since       11.1
 * @deprecated  11.4
 */
class JDatabaseMySQLi extends JDatabaseDriverMySQLi
{
}
