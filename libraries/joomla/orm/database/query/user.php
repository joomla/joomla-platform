<?php
/**
 * @package     Joomla.Platform
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * User Query Object
 *
 *
 * @package     Joomla.Platform
 * @subpackage  Query
 * @since       11.1
 * @tutorial	Joomla.Platform/user.cls
 * @link		http://docs.joomla.org/user
 */
class User extends JORMDatabaseQuery
{
	/**
	 * User config settings
	 * 
	 * @var array
	 * @since 11.1
	 */
	protected $_config_options = array(
		'fields' => array(
			'id',
			'name',
			'email',
			'username',
			'gid',
			'registerDate',
			'lastvisitDate',
			'activation',
			'params'
		),
		'tbl' => 'users',
		'tbl_alias' => 'u',
		'jtable' => array(
			'type' => 'user'
		),
		'foreign_tbls' => array(
			'content' => array(
					'jointype' => 'left',
					'joincolumn' => array(
						array(
							//referenced table field
							'name' => 'created_by',
							//reference category to table column
							'referencedColumnName' => 'id'
						),
						array(
							//referenced table field
							'name' => 'modified_by',
							//reference category to table column
							'referencedColumnName' => 'id'
						)
					)
				)
		)
	);
	
	/**
	 * Reference to UserGroup ORM
	 * 
	 * @since  11.1
	 */
	public function userGroup()
	{
		$userGroup = self::getInstance('UserGroup');
		$userGroup->_query = &$this->_query;
		
		$userGroup->_query->leftJoin('#__user_usergroup_map AS map2 ON map2.user_id = u.id');
		$userGroup->_query->select('grp.title AS usergroup');
		$userGroup->_query->leftJoin('#__usergroup AS grp ON grp.group_id = u.gid');
		
		//Create a reference to back to scope
		$userGroup->addReference($this->getName(),get_class($this));
		
		return $userGroup;
	}
}