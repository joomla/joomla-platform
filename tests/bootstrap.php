<?php
/**
 * Prepares a minimalist framework for unit testing.
 *
 * Joomla is assumed to include the /unittest/ directory.
 * eg, /path/to/joomla/unittest/
 *
 * @package     Joomla.UnitTest
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 * @link        http://www.phpunit.de/manual/current/en/installation.html
 */

define('_JEXEC', 1);

// Fix magic quotes.
@ini_set('magic_quotes_runtime', 0);

// Maximise error reporting.
@ini_set('zend.ze1_compatibility_mode', '0');
error_reporting(E_ALL);
ini_set('display_errors', 1);

/*
 * Ensure that required path constants are defined.  These can be overriden within the phpunit.xml file
 * if you chose to create a custom version of that file.
 */
if (!defined('JPATH_TESTS'))
{
	define('JPATH_TESTS', realpath(__DIR__));
}
if (!defined('JPATH_PLATFORM'))
{
	define('JPATH_PLATFORM', realpath(dirname(JPATH_TESTS) . '/libraries'));
}
if (!defined('JPATH_BASE'))
{
	define('JPATH_BASE', realpath(JPATH_TESTS . '/tmp'));
}
if (!defined('JPATH_ROOT'))
{
	define('JPATH_ROOT', realpath(JPATH_BASE));
}
if (!defined('JPATH_CACHE'))
{
	define('JPATH_CACHE', JPATH_BASE . '/cache');
}
if (!defined('JPATH_CONFIGURATION'))
{
	define('JPATH_CONFIGURATION', JPATH_BASE);
}
if (!defined('JPATH_MANIFESTS'))
{
	define('JPATH_MANIFESTS', JPATH_BASE . '/manifests');
}
if (!defined('JPATH_PLUGINS'))
{
	define('JPATH_PLUGINS', JPATH_BASE . '/plugins');
}
if (!defined('JPATH_THEMES'))
{
	define('JPATH_THEMES', JPATH_BASE . '/themes');
}

// Load a configuration file for the tests.
if (file_exists(JPATH_TESTS . '/config.php'))
{
	include_once JPATH_TESTS . '/config.php';
}
else
{
	require_once JPATH_TESTS . '/config.dist.php';
}

// Import the platform.
require_once JPATH_PLATFORM . '/import.php';

JLoader::register('JSessionStorage', JPATH_PLATFORM.'/joomla/session/storage.php');
JLoader::register('JRegistryFormat', JPATH_PLATFORM.'/joomla/registry/format.php');
JLoader::register('JButton', JPATH_PLATFORM.'/joomla/html/toolbar/button.php');
JLoader::register('JElement', JPATH_PLATFORM.'/joomla/html/parameter/element.php');
JLoader::register('JButton', JPATH_PLATFORM.'/joomla/html/toolbar/button.php');
JLoader::discover('JHTML', JPATH_PLATFORM.'/joomla/html/html');
JLoader::register('JCacheStorage', JPATH_PLATFORM.'/joomla/cache/storage.php');
JLoader::register('JCacheController', JPATH_PLATFORM.'/joomla/cache/controller.php');
JLoader::register('JTable', JPATH_PLATFORM.'/joomla/database/table.php');
JLoader::register('JDatabase', JPATH_PLATFORM.'/joomla/database/database.php');
JLoader::register('JDocumentRenderer', JPATH_PLATFORM.'/joomla/document/renderer.php');
JLoader::register('JFormHelper', JPATH_PLATFORM.'/joomla/form/helper.php');

// Include the base test cases.
require_once JPATH_TESTS . '/includes/JoomlaTestCase.php';
require_once JPATH_TESTS . '/includes/JoomlaDatabaseTestCase.php';

// Exclude all of the tests and platform files from code coverage reports
PHP_CodeCoverage_Filter::getInstance()->addDirectoryToBlacklist(JPATH_TESTS);
