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
class JFormRuleEmail extends JFormRule
{
	/**
	 * The regular expression to use in testing a form field value.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $regex = '^[\w.-]+(\+[\w.-]+)*@\w+[\w.-]*?\.\w{2,4}$';

	/**
	 * Method to test the email address and optionally check for uniqueness.
	 *
	 * @param   SimpleXMLElement  $element  The SimpleXMLElement object representing the <field /> tag for the form field object.
	 * @param   mixed             $value    The form field value to validate.
	 * @param   string            $group    The field name group control value. This acts as as an array container for the field.
	 *                                      For example if the field has name="foo" and the group value is set to "bar" then the
	 *                                      full field name would end up being "bar[foo]".
	 * @param   JRegistry         $input    An optional JRegistry object with the entire data set to validate against the entire form.
	 * @param   JForm             $form     The form object for which the field is being tested.
	 *
	 * @return  boolean  True if the value is valid, false otherwise.
	 *
	 * @since   11.1
	 */
	public function test(SimpleXMLElement $element, $value, $group = null, JRegistry $input = null, JForm $form = null)
	{
		// If the field is empty and not required, the field is valid.
		$required = ((string) $element['required'] == 'true' || (string) $element['required'] == 'required');
		if (!$required && empty($value))
		{
			return true;
		}

		// Test the value against the regular expression.
		if (!parent::test($element, $value, $group, $input, $form))
		{
			return false;
		}

		// Check if we should test for uniqueness.
		$unique = ((string) $element['unique'] == 'true' || (string) $element['unique'] == 'unique');
		if ($unique)
		{

			// Get the database object and a new query object.
			$db = JFactory::getDBO();
			$query = $db->getQuery(true);

			// Build the query.
			$query->select('COUNT(*)');
			$query->from('#__users');
			$query->where('email = ' . $db->quote($value));

			// Get the extra field check attribute.
			$userId = ($form instanceof JForm) ? $form->getValue('id') : '';
			$query->where($db->quoteName('id') . ' <> ' . (int) $userId);

			// Set and query the database.
			$db->setQuery($query);
			$duplicate = (bool) $db->loadResult();

			if ($duplicate)
			{
				return false;
			}
		}

		return true;
	}
}
