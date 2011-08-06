<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.form.formrule');

/**
 * Form Rule class for the Joomla Framework.
 *
 * @package     Joomla.Framework
 * @subpackage  Form
 * @since       11.2
 */
class JFormRuleColor extends JFormRule
{
	/**
	 * Method to test for a valid color in hexadecimaö.
	 *
	 * @param   object  &$element  The JXmlElement object representing the <field /> tag for the form field object.
	 * @param   mixed   $value     The form field value to validate.
	 * @param   string  $group     The field name group control value. This acts as as an array container for the field.
	 *                             For example if the field has name="foo" and the group value is set to "bar" then the
	 *                             full field name would end up being "bar[foo]".
	 * @param   object  &$input    An optional JRegistry object with the entire data set to validate against the entire form.
	 * @param   object  &$form     The form object for which the field is being tested.
	 *
	 * @return  boolean  True if the value is valid, false otherwise.
	 *
	 * @since   11.2
	 * @throws  JException on invalid rule.
	 */
	public function test(&$element, $value, $group = null, &$input = null, &$form = null)
	{
		$value = trim($value);
		if (empty($value))
		{
			// A color field can't be empty, we default to black. This is the same as the HTML5 spec.
			$value = '#000000';
			return true;
		}

		// Remove the leading # if present to validate the numeric part
		$value = ltrim($value, '#');

		// The value must be 6 or 6 characters long
		if (!((strlen($value) == 7 || strlen($value) == 4) && ctype_xdigit($value)))
		{
			return false;
		}

		// Prepend the # again
		$value = '#' . $value;

		return true;
	}
}
