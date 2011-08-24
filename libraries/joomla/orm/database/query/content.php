<?php
/**
 * @package     Joomla.Platform
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Content Query Object
 *
 *
 * @package     Joomla.Platform
 * @subpackage  Query
 * @since       11.1
 * @tutorial	Joomla.Platform/category.cls
 * @link		http://docs.joomla.org/category
 */
class Content extends JORMDatabaseQuery
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
			'title',
			'alias',
			'title_alias',
			'introtext',
			'fulltext',
			'created',
			'catid',
			'created_by',
			'creted_by_alias',
			'modified',
			'metakey',
			'metadesc',
			'version',
			'hits',
			'metadata',
			'featured',
			'ordering',
			'attribs'
		),
		'tbl' => 'content',
		'tbl_alias' => 'a',
		'jtable' => array(
			'type' => 'content',
			'prefix' => 'jtable'
		)
	);
}