<?php
/**
 * @package    Joomla.Test
 *
 * @copyright  Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

/**
 * Class to mock JDatabaseQuery.
 *
 * @package  Joomla.Test
 * @since    12.1
 */
class TestMockDatabaseQuery extends JDatabaseQuery
{
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
		return 'GUID()';
	}
}
