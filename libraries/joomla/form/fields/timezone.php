<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

JFormHelper::loadFieldClass('groupedlist');

/**
 * Form Field class for the Joomla Platform.
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 * @since       11.1
 */
class JFormFieldTimezone extends JFormFieldGroupedList
{

	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $type = 'Timezone';

	/**
	 * The field containing the key for the input field.
	 *
	 * @var    string
	 * @since  12.3
	 */
	protected $keyField = 'id';

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

		if (!empty($this->element['key_field']))
		{
			$this->keyField = (string) $this->element['key_field'];
		}

		return true;
	}

	/**
	 * The list of available timezone groups to use.
	 *
	 * @var    array
	 *
	 * @since  11.1
	 */
	protected static $zones = array('Africa', 'America', 'Antarctica', 'Arctic', 'Asia', 'Atlantic', 'Australia', 'Europe', 'Indian', 'Pacific');

	/**
	 * Method to get the time zone field option groups.
	 *
	 * @return  array  The field option objects as a nested array in groups.
	 *
	 * @since   11.1
	 */
	protected function getGroups()
	{
		$groups = array();
		$keyValue = $this->form->getValue($this->keyField);

		// If the timezone is not set use the server setting.
		if (strlen($this->value) == 0 && empty($keyValue))
		{
			$this->value = JFactory::getConfig()->get('offset');
		}

		// Get the list of time zones from the server.
		$zones = DateTimeZone::listIdentifiers();

		// Build the group lists.
		foreach ($zones as $zone)
		{

			// Time zones not in a group we will ignore.
			if (strpos($zone, '/') === false)
			{
				continue;
			}

			// Get the group/locale from the timezone.
			list ($group, $locale) = explode('/', $zone, 2);

			// Only use known groups.
			if (in_array($group, self::$zones))
			{

				// Initialize the group if necessary.
				if (!isset($groups[$group]))
				{
					$groups[$group] = array();
				}

				// Only add options where a locale exists.
				if (!empty($locale))
				{
					$groups[$group][$zone] = JHtml::_('select.option', $zone, str_replace('_', ' ', $locale), 'value', 'text', false);
				}
			}
		}

		// Sort the group lists.
		ksort($groups);

		foreach ($groups as $zone => & $location)
		{
			sort($location);
		}

		// Merge any additional groups in the XML definition.
		return array_merge(parent::getGroups(), $groups);
	}
}
