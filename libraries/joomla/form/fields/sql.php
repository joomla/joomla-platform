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
 * Supports an custom SQL select list
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 * @since       11.1
 */
class JFormFieldSQL extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $type = 'SQL';

	/**
	 * The item key name
	 *
	 * @var    string
	 * @since  12.3
	 */
	protected $keyField;

	/**
	 * The item field containing the value
	 *
	 * @var    string
	 * @since  12.3
	 */
	protected $valueField;

	/**
	 * Whether or not to translate the field
	 *
	 * @var    boolean
	 * @since  12.3
	 */
	protected $translate = false;

	/**
	 * Whether or not to translate the field
	 *
	 * @var    boolean
	 * @since  12.3
	 */
	protected $query = false;

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

		if (!empty($this->element['value_field']))
		{
			$this->valueField = (string) $this->element['value_field'];
		}

		if (!empty($this->element['translate']))
		{
			$this->translate = (boolean) $this->element['translate'];
		}

		if (!empty($this->element['query']))
		{
			$this->query = (string) $this->element['query'];
		}

		return true;
	}

	/**
	 * Method to get the custom field options.
	 * Use the query attribute to supply a query to generate the list.
	 *
	 * @return  array  The field option objects.
	 *
	 * @since   11.1
	 */
	protected function getOptions()
	{
		$options = array();

		// Initialize some field attributes.
		$key = $this->keyField;
		$value = !empty($this->valueField) ? $this->valueField : (string) $this->element['name'];

		// Get the database object.
		$db = JFactory::getDBO();

		// Set the query and get the result list.
		$db->setQuery($this->query);
		$items = $db->loadObjectlist();

		// Build the field options.
		if (!empty($items))
		{
			foreach ($items as $item)
			{
				if ($this->translate)
				{
					$options[] = JHtml::_('select.option', $item->$key, JText::_($item->$value));
				}
				else
				{
					$options[] = JHtml::_('select.option', $item->$key, $item->$value);
				}
			}
		}

		// Merge any additional options in the XML definition.
		return array_merge(parent::getOptions(), $options);
	}
}
