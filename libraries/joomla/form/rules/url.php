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
class JFormRuleUrl extends JFormRule
{
	/**
	 * Method to test an external url for a valid parts.
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
	 * @throws  Exception on invalid value or on error.
	 * @link    http://www.w3.org/Addressing/URL/url-spec.txt
	 * @see	    Jstring
	 */
	public function test(&$element, $value, $group = null, &$input = null, &$form = null)
	{
		$urlParts = JString::parse_url($value);
		if ($urlParts === false)
		{
			throw new Exception($this->getErrorMsg($element), -4);
		}

		// See http://www.w3.org/Addressing/URL/url-spec.txt
		// Use the full list or optionally specify a list of permitted schemes.
		if ($element['schemes'] == '')
		{
			$scheme = array('http', 'https', 'ftp', 'ftps', 'gopher', 'mailto', 'news', 'prospero', 'telnet', 'rlogin', 'tn3270', 'wais', 'url',
				'mid', 'cid', 'nntp', 'tel', 'urn', 'ldap', 'file', 'fax', 'modem', 'git');
		}
		else
		{
			$scheme = explode(',', $element['schemes']);

		}
		// This rule is only for full URLs with schemes because  parse_url does not parse
		// accurately without a scheme.
		// @see http://php.net/manual/en/function.parse-url.php
		if (!array_key_exists('scheme', $urlParts))
		{
			throw new Exception($this->getErrorMsg($element), -4);
		}
		$urlScheme = (string) $urlParts['scheme'];
		$urlScheme = strtolower($urlScheme);
		if (in_array($urlScheme, $scheme) == false)
		{
			throw new Exception($this->getErrorMsg($element), -4);
		}
		// For some schemes here must be two slashes.
		if (($urlScheme == 'http' || $urlScheme == 'https' || $urlScheme == 'ftp' || $urlScheme == 'sftp' || $urlScheme == 'gopher'
			|| $urlScheme == 'wais' || $urlScheme == 'gopher' || $urlScheme == 'prospero' || $urlScheme == 'telnet' || $urlScheme == 'git')
			&& ((substr($value, strlen($urlScheme), 3)) !== '://'))
		{
			throw new Exception($this->getErrorMsg($element), -4);
		}
		// The best we can do for the rest is make sure that the strings are valid UTF-8
		// and the port is an integer.
		if ((array_key_exists('host', $urlParts) && !JString::valid((string) $urlParts['host']))
			|| (array_key_exists('port', $urlParts) && !is_int((int) $urlParts['port']))
			|| (array_key_exists('path', $urlParts) && !JString::valid((string) $urlParts['path'])))
		{
			throw new Exception($this->getErrorMsg($element), -4);
		}

		return true;
	}
}
