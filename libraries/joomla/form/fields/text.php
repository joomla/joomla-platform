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
 * Form Field class for the Joomla Platform.
 * Supports a one line text field.
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 * @link        http://www.w3.org/TR/html-markup/input.text.html#input.text
 * @since       11.1
 */
class JFormFieldText extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 *
	 * @since  11.1
	 */
	protected $type = 'Text';

	/**
	 * The HTML5 placeholder for this field.
	 *
	 * @var    string
	 * @since  12.3
	 */
	protected $placeholder;

	/**
	 * Method to attach a JForm object to the field.
	 *
	 * @param   SimpleXMLElement  $element  The SimpleXMLElement object representing the <field /> tag for the form field object.
	 * @param   mixed             $value    The form field value to validate.
	 * @param   string            $group    The field name group control value. This acts as as an array container for the field.
	 *                                      For example if the field has name="foo" and the group value is set to "bar" then the
	 *                                      full field name would end up being "bar[foo]".
	 *
	 * @return  boolean  True on success.
	 *
	 * @see     JFormField::setup()
	 * @since   12.3
	 */
	public function setup(SimpleXMLElement $element, $value, $group = null)
	{
		parent::setup($element, $value, $group);

		if (!empty($element['placeholder']))
		{
			$this->placeholder = (string) $element['placeholder'];
		}

		return true;
	}

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   11.1
	 */
	protected function getInput()
	{
		// Initialize some field attributes.
		$size = !empty($this->size) ? ' size="' . $this->size . '"' : '';
		$readonly = !empty($this->readonly) ? ' readonly="readonly"' : '';
		$disabled = !empty($this->disabled) ? ' disabled="disabled"' : '';
		$class = !empty($this->class) ? ' class="' . $this->class . '"' : '';
		$onchange = !empty($this->onchange) ? ' onchange="' . $this->onchange . '"' : '';
		$maxLength = !empty($this->maxlength) ? ' maxlength="' . $this->maxlength . '"' : '';
		$placeholder = !empty($this->placeholder) ? ' placeholder="' . $this->placeholder . '"' : '';

		return '<input type="text" name="' . $this->name . '" id="' . $this->id . '"' . ' value="' . htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8')
			. '"' . $class . $size . $disabled . $readonly . $onchange . $maxLength . $placeholder . '/>';
	}
}
