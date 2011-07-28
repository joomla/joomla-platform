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
 * @since       11.1
 */
class JFormRuleInteger extends JFormRule
{
	/**
	 * Method to test for integer input with optional restrictions to positive, non negative
	 * negative and maximam and minimum values.
	 *
	 * The rule for integers is that they must consist only of digits which
	 * may be preceeded by a + or - but no other characters are permitted.
	 * This validation will permit a blank to pass.
	 * Integer type optionally allows restricting values to a specific subset of all integers.
	 * Non negative refers to all positive numbers and 0.
	 * All refers to all integers.
	 *
	 * @param   object  $element  The JXMLElement object representing the <field /> tag for the
	 *                            form field object.
	 * @param   mixed   $value    The form field value to validate.
	 * @param   string  $group    The field name group control value. This acts as as an array
	 *                            container for the field. For example if the field has name="foo"
	 *                            and the group value is set to "bar" then the full field name
	 *                            would end up being "bar[foo]".
	 * @param   object  $input    An optional JRegistry object with the entire data set to validate
	 *                            against the entire form.
	 * @param   object  $form     The form object for which the field is being tested.
	 *
	 * @return  boolean  True if the value is valid, false otherwise.
	 *
	 */
	public function test(& $element, $value, $group = null, & $input = null, & $form = null)
	{

		$regexarray = array(
			'all' => '/^[\+\-]?[0-9]* $/',
			'positive'=> '/^[\+]?[0-9]*[1-9]*[0-9]* $/',
			'negative'=> '/^[-?[0-9]*[1-9]*[0-9]* $/',
			'nonnegative' => '/^[\+]?[0-9]* $/'
		);
		// If the field is empty and not required, the field is valid.
		$required = ((string) $element['required'] == 'true' || (string) $element['required'] == 'required');
		if (!$required && empty($value)) {
			return true;
		}
		// If no integertype is specified assume that all are permitted.
		if (!isset($element['integertype'])) {
			$element['integertype'] = 'all';
		}
		$inttype = $element['integertype'];
		
		$regex = $regexarray[$inttype];
		// Test the value against the regular expression.
		if (preg_match($regex, trim($value)) == false || $value > $element['max'] || $value < $element['min'] ) {

			return false;
		}
	}
}