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
JLog::add('JDatabase is deprecated, use JDatabaseDriver instead.', JLog::NOTICE, 'deprecated');

/**
 * Database connector class.
 *
 * @package     Joomla.Platform
 * @subpackage  Database
 * @since       11.1
 * @deprecated  11.4
 */
abstract class JDatabase extends JDatabaseDriver
{
}
