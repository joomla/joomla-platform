<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

// Import the com_menus helper.
require_once realpath(JPATH_ADMINISTRATOR.'/components/com_menus/helpers/menus.php');

/**
 * Supports an HTML select list of menus
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 * @since       11.1
 */
class JFormFieldMenu extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $type = 'Menu';

	/**
	 * Method to get the list of menus for the field options.
	 *
	 * @return  array  The field option objects.
	 * @since   11.1
	 */
	protected function getOptions()
	{
		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), JHtml::_('menu.menus'));

		return $options;
	}
}