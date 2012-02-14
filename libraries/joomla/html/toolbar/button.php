<?php
/**
 * @package     Joomla.Platform
 * @subpackage  HTML
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Button base class
 *
 * The JButton is the base class for all JButton types
 *
 * @package     Joomla.Platform
 * @subpackage  HTML
 * @since       11.1
 */
abstract class JButton
{
	/**
	 * element name
	 *
	 * This has to be set in the final renderer classes.
	 *
	 * @var    string
	 */
	protected $name = null;

	/**
	 * reference to the object that instantiated the element
	 *
	 * @var    JButton
	 */
	protected $parent = null;

	/**
	 * Constructor
	 *
	 * @param   object  $parent  The parent
	 */
	public function __construct($parent = null)
	{
		$this->parent = $parent;
	}

	/**
	 * magic get method
	 *
	 * @param   $propertyName  Property name
	 *
	 * @return  mixed  the property value
	 *
	 * @since   12.1
	 * @deprecated  12.3
	 */
	public function __get($propertyName)
	{
		if ($propertyName[0] == '_' && property_exists($this, $newPropertyName = substr($propertyName, 1)))
		{
			JLog::add(get_called_class() . '::$' . $propertyName . ' is deprecated. Use ' . get_called_class() . '::$'. $newPropertyName . ' instead.', JLog::WARNING, 'deprecated');
			return $this->$newPropertyName;
		}
		else
		{
			// Trigger an error
			return $this->$propertyName;
		}
	}

	/**
	 * magic set method
	 *
	 * @param   $propertyName  Property name
	 * @param   $value         Property name
	 *
	 * @return  void
	 *
	 * @since   12.1
	 * @deprecated  12.3
	 */
	public function __set($propertyName, $value)
	{
		if ($propertyName[0] == '_' && property_exists($this, $newPropertyName = substr($propertyName, 1)))
		{
			JLog::add(get_called_class() . '::$' . $propertyName . ' is deprecated. Use ' . get_called_class() . '::$'. $newPropertyName . ' instead.', JLog::WARNING, 'deprecated');
			$this->$newPropertyName = $value;
		}
		else
		{
			$this->$propertyName = $value;
		}
	}

	/**
	 * Get the element name
	 *
	 * @return  string   type of the parameter
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Get the HTML to render the button
	 *
	 * @param   array  &$definition  Parameters to be passed
	 *
	 * @return  string
	 */
	public function render(&$definition)
	{
		/*
		 * Initialise some variables
		 */
		$html = null;
		$id = call_user_func_array(array(&$this, 'fetchId'), $definition);
		$action = call_user_func_array(array(&$this, 'fetchButton'), $definition);

		// Build id attribute
		if ($id)
		{
			$id = "id=\"$id\"";
		}

		// Build the HTML Button
		$html .= "<li class=\"button\" $id>\n";
		$html .= $action;
		$html .= "</li>\n";

		return $html;
	}

	/**
	 * Method to get the CSS class name for an icon identifier
	 *
	 * Can be redefined in the final class
	 *
	 * @param   string  $identifier  Icon identification string
	 *
	 * @return  string  CSS class name
	 *
	 * @since   11.1
	 */
	public function fetchIconClass($identifier)
	{
		return "icon-32-$identifier";
	}

	/**
	 * Get the button
	 *
	 * Defined in the final button class
	 *
	 * @return  string
	 *
	 * @since   11.1
	 */
	abstract public function fetchButton();
}
