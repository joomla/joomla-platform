<?php
/**
 * Bootstrap file for the Joomla Platform [with legacy libraries].  Including this file into your application
 * will make Joomla Platform libraries [including legacy libraries] available for use.
 *
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
if (!defined('IS_UNIX'))
{
	define('IS_UNIX', (($os !== 'MAC') && ($os !== 'WIN')) ? true : false);
}

/**
 * @deprecated 13.3	Use IS_UNIX instead
 */
if (!defined('IS_MAC'))
{
	define('IS_MAC', (IS_UNIX === true && ($os === 'DAR' || $os === 'MAC')) ? true : false);
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

// Make sure that the Joomla Platform has been successfully loaded.
if (!class_exists('JLoader'))
{
	throw new RuntimeException('Joomla Platform not loaded.');
}

// Setup the autoloaders.
JLoader::setup();

// Register the legacy library base path for deprecated or legacy libraries.
JLoader::registerPrefix('J', JPATH_PLATFORM . '/legacy');

// Import the Joomla Factory.
JLoader::import('joomla.factory');

// Register classes for compatability with PHP 5.3
if (version_compare(PHP_VERSION, '5.4.0', '<'))
{
	JLoader::register('JsonSerializable', __DIR__ . '/compat/jsonserializable.php');
}

// Register classes that don't follow one file per class naming conventions.
JLoader::register('JText', JPATH_PLATFORM . '/joomla/language/text.php');
JLoader::register('JRoute', JPATH_PLATFORM . '/joomla/application/route.php');

// Register the folder for the moved JHtml classes
JHtml::addIncludePath(JPATH_PLATFORM . '/legacy/html');
