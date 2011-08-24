<?php
/**
 * @package     Joomla.Platform
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * UserGroup Query Object
 *
 *
 * @package     Joomla.Platform
 * @subpackage  Query
 * @since       11.1
 * @tutorial	Joomla.Platform/usergroup.cls
 * @link		http://docs.joomla.org/usergroup
 */
class UserGroup extends JORMDatabaseQuery
{
	/**
	 * Usergroup config settings
	 * 
	 * @var array
	 * @since 11.1
	 */
	protected $_config_options = array(
		'fields' => array(
			'id',
			'parent_id',
			'title'
		),
		'tbl' => 'usergroups',
		'tbl_alias' => 'grp',
		'jtable' => array(
			'type' => 'usergroup'
		)
	);
}