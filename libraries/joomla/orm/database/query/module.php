<?php
/**
 * @package     Joomla.Platform
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Module Query Object
 *
 *
 * @package     Joomla.Platform
 * @subpackage  Query
 * @since       11.1
 * @tutorial	Joomla.Platform/module.cls
 * @link		http://docs.joomla.org/module
 */
class Module extends JORMDatabaseQuery
{
	/**
	 * Module config settings
	 * 
	 * @var array
	 * @since 11.1
	 */
	protected $_config_options = array(
		'tbl' => 'modules',
		'tbl_alias' => 'm',
		'jtable' => array(
			'type' => 'Module'
		)
	);
	
	/**
	 * Reference to Menu ORM
	 * 
	 * @since  11.1
	 */
	public function Menu()
	{
		$menu = self::getInstance('Menu');
		$menu->_query = &$this->_query;
		$menu->_tbl_alias = 'menu';
		$menu->_query->select('mm.menuid');
		$menu->_query->leftJoin('#__modules_menu AS mm ON(mm.moduleid = m.id)');
		$menu->_query->leftJoin('#__menu AS menu ON(menu.id = mm.menuid)');
		
		//Create a reference to back to scope
		$menu->addReference($this->getName(),get_class($this));
		
		return $menu;
	}
}