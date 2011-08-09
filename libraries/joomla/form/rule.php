<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

// Detect if we have full UTF-8 and unicode PCRE support.
if (!defined('JCOMPAT_UNICODE_PROPERTIES'))
{
	define('JCOMPAT_UNICODE_PROPERTIES', (bool) @preg_match('/\pL/u', 'a'));
}

/**
 * Form Rule class for the Joomla Platform.
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 * @since       11.1
 */
class JFormRule
{
	/**
	 * The regular expression to use in testing a form field value.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $regex;

	/**
	 * The regular expression modifiers to use when testing a form field value.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $modifiers;

	/**
	 * The error message displayed if the test fail.
	 *
	 * @var    string
	 * @since  11.3
	 */
	protected $errorMsg = 'JLIB_FORM_VALIDATE_FIELD_INVALID';

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
	 * @since   11.1
	 * @throws  JException on invalid value or on error.
	 */
	public function test(&$element, $value, $group = null, &$input = null, &$form = null)
	{
		// Check for a valid regex.
		if (empty($this->regex))
		{
			throw new JException(JText::sprintf('JLIB_FORM_INVALID_FORM_RULE', get_class($this)), -3, E_ERROR);
		}

		// Add unicode property support if available.
		if (JCOMPAT_UNICODE_PROPERTIES)
		{
			$this->modifiers = (strpos($this->modifiers, 'u') !== false) ? $this->modifiers : $this->modifiers . 'u';
		}

		// Test the value against the regular expression.
		if (preg_match(chr(1) . $this->regex . chr(1) . $this->modifiers, $value))
		{
			return true;
		}

		throw new JException($this->getErrorMsg($element), -4, E_WARNING);
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
		$msg = $this->errorMsg;
		if (preg_match('/^JFormRule([a-z0-9_]*)$/i', get_class($this), $matches))
		{
			$msg .= '_'.strtoupper($matches[1]);
		}
		return JText::sprintf($msg, (string)$element['label']);
	}
}
