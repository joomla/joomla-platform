<?php
/**
 * @package     Joomla.Platform
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * category ORM
 *
 *
 * @package     Joomla.Platform
 * @subpackage  Query
 * @since       11.1
 * @tutorial	Joomla.Platform/category.cls
 * @link		http://docs.joomla.org/category
 */
class Category extends JORMDatabaseQuery
{
	/**
	 * Content config settings
	 * 
	 * @var array
	 * @since 11.1
	 */
	protected $_config_options = array(
		'fields' => array(
			'id',
			'level',
			'extension',
			'path',
			'title',
			'alias',
			'description',
			'access',
			'params',
			'metadesc',
			'metakey',
			'metadata',
			'hits'
		),
		'tbl' => 'categories',
		'tbl_alias' => 'cat',
		'jtable' => array(
			'type' => 'category'
		),
		'foreign_tbls' => array(
			//relation table (whitout prefix)
			'content' => array(
				//join method
				'jointype' => 'LEFT',
				'joincolumn' => array(
					//referenced table field
					'name' => 'catid',
					//reference category to table column
					'referencedColumnName' => 'id'
				),
				'columns' => array(
					'title AS category_title',
					'path AS category_route',
					'access AS category_access',
					'alias AS category_alias'
				)
			),
			//relation table (whitout prefix)
			'banners' => array(
				//join method
				'jointype' => 'LEFT',
				'joincolumn' => array(
					//referenced table field
					'name' => 'catid',
					//reference category to table column
					'referencedColumnName' => 'id'
				),
				'columns' => array(
					'title AS category_title',
					'path AS category_route',
					'access AS category_access',
					'alias AS category_alias'
				)
			)
		)
	);
}