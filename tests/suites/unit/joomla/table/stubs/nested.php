<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Table
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * NestedTable class.
 *
 * @package     Joomla.UnitTest
 * @subpackage  Table
 * @since       12.1
 */
class NestedTable extends JTableNested
{
	public static $unlocked = false;

	/**
	 * Constructor
	 *
	 * @param   JDatabaseDriver  $db  Database driver object.
	 *
	 * @since   12.1
	 */
	public function __construct($db)
	{
		parent::__construct('#__categories', 'id', $db);
	}

	/**
	 * Test...
	 *
	 * @return void
	 */
	public static function mockUnlock()
	{
		self::$unlocked = true;
	}
}
