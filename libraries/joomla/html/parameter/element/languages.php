<?php
/**
 * @package     Joomla.Platform
 * @subpackage  HTML
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Renders a languages element
 *
 * @package     Joomla.Platform
 * @subpackage  Parameter
 * @since       11.1
 * @deprecated  12.1   Use JFormFieldLanguage instead
 * @note        In updating please noe that JFormFieldLanguage does not end in s.
 */
class JElementLanguages extends JElement
{
	/**
	 * Element name
	 *
	 * @var    string
	 */
	protected $name = 'Languages';

	/**
	 * Element name
	 *
	 * @var    string
	 * @deprecated use $name or declare as private
	 */
	protected $_name = 'Languages';

	/**
	 * Fetch the language list element
	 *
	 * @param   string       $name          Element name
	 * @param   string       $value         Element value
	 * @param   JXMLElement  &$node         JXMLElement node object containing the settings for the element
	 * @param   string       $control_name  Control name
	 *
	 * @return  string
	 *
	 * @deprecated    12.1   Use JFormFieldLanguage
	 * @note    When updating note that JFormFieldLanguage has no s.
	 * @since   11.1
	 */
	public function fetchElement($name, $value, &$node, $control_name)
	{
		// Deprecation warning.
		JLog::add('JElementLanguages::fetchElement() is deprecated.', JLog::WARNING, 'deprecated');

		$client = $node->attributes('client');

		$languages = JLanguageHelper::createLanguageList($value, constant('JPATH_' . strtoupper($client)), true);
		array_unshift($languages, JHtml::_('select.option', '', JText::_('JOPTION_SELECT_LANGUAGE')));

		return JHtml::_(
			'select.genericlist',
			$languages,
			$control_name . '[' . $name . ']',
			array('id' => $control_name . $name, 'list.attr' => 'class="inputbox"', 'list.select' => $value)
		);
	}
}
