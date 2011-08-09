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
 * @package     Joomla.Platform
 * @subpackage  Form
 * @since       11.3
 */
class JFormRuleMaxLength extends JFormRule
{
	/**
	 * Method to test the value.
	 *
	 * @param   object  &$element  The JXmlElement object representing the <field /> tag for the form field object.
	 * @param   mixed   $value     The form field value to validate.
	 * @param   string  $group     The field name group control value. This acts as as an array container for the field.
	 *                             For example if the field has name="foo" and the group value is set to "bar" then the
	 *                             full field name would end up being "bar[foo]".
	 * @param   object  &$input    An optional JRegistry object with the entire data set to validate against the entire form.
	 * @param   object  &$form     The form object for which the field is being tested.
	 *
	 * @return  boolean  True if the value is valid.
	 *
	 * @since   11.3
	 * @throws  JException on invalid value or on error.
	 */
	public function test(& $element, $value, $group = null, & $input = null, & $form = null)
	{
		if (JString::strlen($value) <= (int) $element['maxLength'])
		{
			return true;
		} else {
			throw new JException($this->getErrorMsg($element));
		}
	}

	/**
	 * Method to get the translated error message
	 *
	 * @param   object  $element  The JXMLElement object representing the <field /> tag for the
	 *                            form field object.
	 *
	 * @return  string  The translated error message
	 *
	 * @since   11.3
	 */
	protected function getErrorMsg($element)
	{
		return JText::sprintf('JLIB_FORM_VALIDATE_FIELD_INVALID_MAXLENGTH', (string)$element['label'], (string)$element['maxLength']);
	}
}