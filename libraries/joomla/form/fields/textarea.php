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
 * Supports a multi line area for entry of plain text
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 * @link        http://www.w3.org/TR/html-markup/textarea.html#textarea
 * @since       11.1
 */
class JFormFieldTextarea extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $type = 'Textarea';

	/**
	 * The number of cols for the field
	 *
	 * @var    string
	 * @since  12.3
	 */
	protected $cols;

	/**
	 * The number of rows for the field
	 *
	 * @var    string
	 * @since  12.3
	 */
	protected $rows;

	/**
	 * The HTML5 placeholder for this field.
	 *
	 * @var    string
	 * @since  12.3
	 */
	protected $placeholder = null;

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

		if (!empty($this->element['cols']))
		{
			$this->cols = (int) $this->element['cols'];
		}

		if (!empty($this->element['rows']))
		{
			$this->rows = (int) $this->element['rows'];
		}

		if (!empty($this->element['placeholder']))
		{
			$this->placeholder = (string) $this->element['placeholder'];
		}

		return true;
	}

	/**
	 * Method to get the textarea field input markup.
	 * Use the rows and columns attributes to specify the dimensions of the area.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   11.1
	 */
	protected function getInput()
	{
		// Initialize some field attributes.
		$class = !empty($this->class) ? ' class="' . $this->class . '"' : '';
		$disabled = !empty($this->disabled) ? ' disabled="disabled"' : '';
		$columns = !empty($this->cols) ? ' cols="' . $this->cols . '"' : '';
		$rows = !empty($this->rows) ? ' rows="' . $this->rows . '"' : '';
		$onchange = !empty($this->onchange) ? ' onchange="' . $this->onchange . '"' : '';
		$placeholder = !empty($this->placeholder) ? ' placeholder="' . $this->placeholder . '"' : '';

		return '<textarea name="' . $this->name . '" id="' . $this->id . '"' . $columns . $rows . $class . $disabled . $onchange . $placeholder . '>'
			. htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '</textarea>';
	}
}
