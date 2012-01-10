<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Application
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * Inspector class for the JCli library.
 */

/**
 * @package		Joomla.UnitTest
 * @subpackage  Application
 */
class JInputInspector extends JInput
{
	public $options;
	public $filter;
	public $data;
	public $inputs;
	public static $registered;

	public static function register()
	{
		return parent::register();
	}
}
