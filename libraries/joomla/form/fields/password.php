<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

JFormHelper::loadFieldClass('text');

/**
 * Form Field class for the Joomla Platform.
 * Text field for passwords
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 * @link        http://www.w3.org/TR/html-markup/input.password.html#input.password
 * @note        Two password fields may be validated as matching using JFormRuleEquals
 * @since       11.1
 */
class JFormFieldPassword extends JFormFieldText
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $type = 'Password';

	/**
	 * Whether or not to use the strength meter
	 *
	 * @var    boolean
	 * @since  12.3
	 */
	protected $strengthmeter;

	/**
	 * Strength threshold for the strength meter
	 *
	 * @var    string
	 * @since  12.3
	 */
	protected $threshold = 66;

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

		if (!empty($this->element['strengthmeter']))
		{
			$this->strengthmeter = (string) $this->element['strengthmeter'];
		}

		if (!empty($this->element['threshold']))
		{
			$this->threshold = (int) $this->element['threshold'];
		}

		return true;
	}

	/**
	 * Method to get the field input markup for password.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   11.1
	 */
	protected function getInput()
	{
		// Initialize some field attributes.
		$size = !empty($this->size) ? ' size="' . $this->size . '"' : '';
		$maxLength = !empty($this->maxlength) ? ' maxlength="' . $this->maxlength . '"' : '';
		$class = !empty($this->class) ? ' class="' . $this->class . '"' : '';
		$readonly = !empty($this->readonly) ? ' readonly="readonly"' : '';
		$disabled = !empty($this->disabled) ? ' disabled="disabled"' : '';
		$auto = !empty($this->autocomplete) ? ' autocomplete="off"' : '';
		$placeholder = !empty($this->placeholder) ? ' placeholder="' . $this->placeholder . '"' : '';

		$script = '';

		if ($this->strengthmeter)
		{
			JHtml::_('script', 'system/passwordstrength.js', true, true);
			$script = '<script type="text/javascript">new Form.PasswordStrength("' . $this->id . '",
				{
					threshold: ' . $this->threshold . ',
					onUpdate: function(element, strength, threshold) {
						element.set("data-passwordstrength", strength);
					}
				}
			);</script>';
		}

		return '<input type="password" name="' . $this->name . '" id="' . $this->id . '"' .
			' value="' . htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '"' .
			$auto . $class . $readonly . $disabled . $size . $maxLength . $placeholder . '/>' . $script;
	}
}
