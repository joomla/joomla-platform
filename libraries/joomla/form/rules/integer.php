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
 * @since       11.3
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
	 * @param   object   $element  The JXMLElement object representing the <field /> tag for the
	 *                             form field object.
	 * @param   mixed    $value    The form field value to validate.
	 * @param   string   $group    The field name group control value. This acts as as an array
	 *                             container for the field. For example if the field has name="foo"
	 *                             and the group value is set to "bar" then the full field name
	 *                             would end up being "bar[foo]".
	 * @param   object   $input    An optional JRegistry object with the entire data set to validate
	 *                             against the entire form.
	 * @param   object   $form     The form object for which the field is being tested.
	 *
	 * @return  boolean  True if the value is valid, false otherwise.
	 *
	 * @since   11.3
	 */
	public function test(& $element, $value, $group = null, & $input = null, & $form = null)
	{
		// If the field is empty and not required, the field is valid.
		$required = ((string) $element['required'] == 'true' || (string) $element['required'] == 'required');
		if (!$required && strlen($value) == 0)
		{

			return true;
		}

		// Deal with character strings
		if (!is_numeric($value))
		{

			return false;
		}
		// Deal with doubles

		if ( (double) $value - floor($value) > 0)
		{

			return false;
		}
		$value = (int) $value; 

		// If no integertype is specified assume that all are permitted.
		if (!isset($element['integertype']))
		{
			$element['integertype'] = 'all';
		}
		$inttype = (string) $element['integertype'];
		//Simple elimination of false results
		if ( isset($element['max']))
		{
			$max = (int) $element['max'];
			if ( $value > $max)
			{

				return false;
			}
			if (($max > 0 && $inttype == 'negative') || ($max < 0 && $inttype == 'nonnegative') || ($max < 1 && $inttype == 'positive' ))
			{
				// Form settings warning.
				JLog::add('Integer rule is misconfigured.', JLog::WARNING, 'Form');

				return false;
			}
		}
		if ( isset($element['min']))
		{
			$min = (int) $element['min'];
			if ( $value < $min)
			{

				return false;
			}

			if (($min > 0 && $inttype == 'negative') || ($min < 0 && $inttype == 'nonnegative') || ($min < 1 && $inttype == 'positive' ))
			{
				// Form settings warning.
				JLog::add('Integer rule is misconfigured.', JLog::WARNING, 'Form');

				return false;
			
			}
			if ((isset($max) && $min >= $max))
			{
				// Form settings warning.
				JLog::add('Integer rule is misconfigured.', JLog::WARNING, 'Form');

				return false;
			}
		}
		if ($inttype == 'positive' &&  $value < 1)
		{

			return false;
		} 
		elseif ($inttype == 'nonnegative' && $value < 0)
		{

			return false;
		} 
		elseif ($inttype == 'negative' && $value >= 0)
		{

			return false;
		}

		return true;
	}
}