<?php
/**
 * @package    Joomla.UnitTest
 *
 * @copyright  Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

/**#@+
 * Constants
 */
/**
 * LEAF_LOADED a constant to ensure this file was loaded
 */
define('LEAF_LEAF_LOADED', true);

/**
 * A lambda class to test prefix loader.
 *
 * @package  Joomla.UnitTest
 * @since    12.3
 */
abstract class TreeLeaf
{
	static public function myDir()
	{
		return __DIR__;
	}

}
