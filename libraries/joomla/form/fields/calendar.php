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
 *
 * Provides a pop up date picker linked to a button.
 * Optionally may be filtered to use user's or server's time zone.
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 * @since       11.1
 */
class JFormFieldCalendar extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $type = 'Calendar';

	/**
	 * The form field date format.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $format = '%Y-%m-%d';

	/**
	 * The form field date filter.
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $filter;

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

		if (!empty($element['format']))
		{
			$this->format = (string) $element['format'];
		}

		if (!empty($element['filter']))
		{
			$this->filter = (string) $element['filter'];
		}

		return true;
	}

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string   The field input markup.
	 *
	 * @since   11.1
	 */
	protected function getInput()
	{
		// Build the attributes array.
		$attributes = array(
			'size' => $this->size,
			'maxlength' => $this->maxlength,
			'class' => $this->class,
			'readonly' => $this->readonly,
			'disabled' => $this->disabled,
			'onchange' => $this->onchange
		);

		// Handle the special case for "now".
		if (strtoupper($this->value) == 'NOW')
		{
			$this->value = strftime($this->format);
		}

		// Get some system objects.
		$config = JFactory::getConfig();
		$user = JFactory::getUser();

		// If a known filter is given use it.
		switch (strtoupper($this->filter))
		{
			case 'SERVER_UTC':
				// Convert a date to UTC based on the server timezone.
				if ((int) $this->value)
				{
					// Get a date object based on the correct timezone.
					$date = JFactory::getDate($this->value, 'UTC');
					$date->setTimezone(new DateTimeZone($config->get('offset')));

					// Transform the date string.
					$this->value = $date->format('Y-m-d H:i:s', true, false);
				}
				break;

			case 'USER_UTC':
				// Convert a date to UTC based on the user timezone.
				if ((int) $this->value)
				{
					// Get a date object based on the correct timezone.
					$date = JFactory::getDate($this->value, 'UTC');
					$date->setTimezone(new DateTimeZone($user->getParam('timezone', $config->get('offset'))));

					// Transform the date string.
					$this->value = $date->format('Y-m-d H:i:s', true, false);
				}
				break;
		}

		return JHtml::_('calendar', $this->value, $this->name, $this->id, $this->format, $attributes);
	}
}
