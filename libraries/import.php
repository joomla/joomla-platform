<?php
/**
 * @package    Joomla.Platform
 *
 * @copyright  Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

// Set the platform root path as a constant if necessary.
if (!defined('JPATH_PLATFORM'))
{
	define('JPATH_PLATFORM', __DIR__);
}

// Detect the native operating system type.
$os = strtoupper(substr(PHP_OS, 0, 3));
if (!defined('IS_WIN'))
{
	define('IS_WIN', ($os === 'WIN') ? true : false);
}
if (!defined('IS_MAC'))
{
	define('IS_MAC', ($os === 'MAC') ? true : false);
}
if (!defined('IS_UNIX'))
{
	define('IS_UNIX', (($os !== 'MAC') && ($os !== 'WIN')) ? true : false);
}

// Import the platform version library if necessary.
if (!class_exists('JPlatform'))
{
	require_once JPATH_PLATFORM . '/platform.php';
}

// Import the library loader if necessary.
if (!class_exists('JLoader'))
{
	require_once JPATH_PLATFORM . '/loader.php';
}

class_exists('JLoader') or die;

// Setup the autoloaders.
JLoader::setup();

// Import the base Joomla Platform libraries.
JLoader::import('joomla.factory');
JLoader::import('joomla.error.exception');
JLoader::import('joomla.base.object');

// Register JRequest for legacy reasons
JLoader::register('JRequest', JPATH_PLATFORM . '/joomla/environment/request.php');

// Register classes that don't follow one file per class naming conventions.
JLoader::register('JText', JPATH_PLATFORM . '/joomla/methods.php');
JLoader::register('JRoute', JPATH_PLATFORM . '/joomla/methods.php');

// Register classes where the names have been changed to fit the autoloader rules
// @deprecated  12.3
JLoader::register('JDatabaseQueryMySQL', JPATH_PLATFORM . '/database/query/mysql.php');
JLoader::register('JDatabaseQueryMySQLi', JPATH_PLATFORM . '/database/query/mysqli.php');
JLoader::register('JDatabaseQuerySQLAzure', JPATH_PLATFORM . '/database/query/sqlazure.php');
JLoader::register('JDatabaseQuerySQLSrv', JPATH_PLATFORM . '/database/query/sqlsrv.php');
