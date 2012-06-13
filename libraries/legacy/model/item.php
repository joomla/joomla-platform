<?php
/**
 * @package     Joomla.Legacy
 * @subpackage  Model
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Prototype item model.
 *
 * @package     Joomla.Legacy
 * @subpackage  Model
 * @since       11.1
 */
abstract class JModelItem extends JModel
{
	/**
	 * An item.
	 *
	 * @var    array
	 */
	protected $_item = null;

	/**
	 * Model context string.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $_context = 'group.type';

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id  A prefix for the store id.
	 *
	 * @return  string  A store id.
	 *
	 * @since   11.1
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		return md5($id);
	}
}
