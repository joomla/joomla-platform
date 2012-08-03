<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

JFormHelper::loadFieldClass('list');

/**
 * Form Field class for the Joomla Platform.
 * Implements a combo box field.
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 * @since       11.1
 */
class JFormFieldCombo extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $type = 'Combo';

	/**
	 * Method to get the field input markup for a combo box field.
	 *
	 * @return  string   The field input markup.
	 *
	 * @since   11.1
	 */
	protected function getInput()
	{
		// Initialize variables.
		$html = array();
		$attr = array();

		// Initialize some field attributes.
		$attr['class'] = $this->element['class'] ? 'combobox ' . $this->element['class'] : 'combobox';
		if ((string) $this->element['readonly'] == 'true')
		{
			$attr['readonly'] = 'readonly';
		}
		if ((string) $this->element['disabled'] == 'true')
		{
			$attr['disabled'] = 'disabled';
		}

		// Initialize JavaScript field attributes.
		if ($this->element['onchange'])
		{
			$attr['onchange'] = (string) $this->element['onchange'];
		}

		// Get the field options.
		$options = $this->getOptions();

		// Load the combobox behavior.
		JHtml::_('behavior.combobox');

		// Generate the list
		$html[] = JHTML::_('select.genericlist', $options, $this->name, $attr, 'value', 'text', $this->value, $this->id);

		return implode($html);
	}
}
