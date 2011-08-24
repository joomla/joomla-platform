<?php
/**
 * @package     Joomla.Platform
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Menu Query Object
 *
 *
 * @package     Joomla.Platform
 * @subpackage  Query
 * @since       11.1
 * @tutorial	Joomla.Platform/menu.cls
 * @link		http://docs.joomla.org/menu
 */
class Menu extends JORMDatabaseQuery
{
	/**
	 * Menu config settings
	 * 
	 * @var array
	 * @since 11.1
	 */
	protected $_config_options = array(
		'tbl' => 'menu',
		'jtable' => array(
			'type' => 'menu'
		)
	);
}