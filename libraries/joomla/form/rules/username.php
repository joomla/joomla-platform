<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Form Rule class for the Joomla Platform.
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 * @since       11.1
 */
class JFormRuleUsername extends JFormRule
{
	/**
	 * Method to test the username for uniqueness.
	 *
	 * @param   JXMLElement  $element  The JXMLElement object representing the <field /> tag for the form field object.
	 * @param   mixed        $value    The form field value to validate.
	 * @param   string       $group    The field name group control value. This acts as as an array container for the field.
	 *                                 For example if the field has name="foo" and the group value is set to "bar" then the
	 *                                 full field name would end up being "bar[foo]".
	 * @param   JRegistry    $input    An optional JRegistry object with the entire data set to validate against the entire form.
	 * @param   JForm        $form     The form object for which the field is being tested.
	 *
	 * @return  boolean  True if the value is valid, false otherwise.
	 *
	 * @since   11.1
	 */
	public function test($element, $value, $group = null, $input = null, $form = null)
	{
		// Get the database object and a new query object.
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		// Build the query.
		$query->select('COUNT(*)');
		$query->from('#__users');
		$query->where('username = ' . $db->quote($value));

		// Get the extra field check attribute.
		$userId = ($form instanceof JForm) ? $form->getValue('id') : '';
		$query->where($db->quoteName('id') . ' <> ' . (int) $userId);

		// Set and query the database.
		$db->setQuery($query);
		$duplicate = (bool) $db->loadResult();

		// Check for a database error.
		if ($db->getErrorNum())
		{
			JLog::add($db->getErrorMsg(), JLog::WARNING, 'jerror');
		}

		if ($duplicate)
		{
			return false;
		}

		return true;
	}
}
