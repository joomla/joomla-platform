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
 * Provides an input field for files
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 * @link        http://www.w3.org/TR/html-markup/input.file.html#input.file
 * @since       11.1
 */
class JFormFieldFile extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $type = 'File';

	/**
	 * A comma separated list of accepted
	 * file types for the file input field
	 *
	 * @var    string
	 * @since  12.3
	 */
	protected $accept;

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

		if (!empty($this->element['accept']))
		{
			$this->accept = (string) $this->element['accept'];
		}

		return true;
	}

	/**
	 * Method to get the field input markup for the file field.
	 * Field attributes allow specification of a maximum file size and a string
	 * of accepted file extensions.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   11.1
	 *
	 * @note    The field does not include an upload mechanism.
	 * @see     JFormFieldMedia
	 */
	protected function getInput()
	{
		// Initialize some field attributes.
		$size = !empty($this->size) ? ' size="' . $this->size . '"' : '';
		$class = !empty($this->class) ? ' class="' . $this->class . '"' : '';
		$disabled = !empty($this->disabled) ? ' disabled="disabled"' : '';
		$accept = !empty($this->accept) ? ' accept="' . $this->accept . '"' : '';
		$onchange = !empty($this->onchange) ? ' onchange="' . $this->onchange . '"' : '';

		return '<input type="file" name="' . $this->name . '" id="' . $this->id . '"' . ' value=""'
			. $accept . $disabled . $class . $size . $onchange . ' />';
	}
}
