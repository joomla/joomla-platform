<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.form.formfield');

/**
 * Form Field class for the Joomla Platform.
 * Provides an embedded Image preview
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 * @since       11.3
 */
class JFormFieldImage extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.3
	 */
	protected $type = 'Image';

	/**
	 * Method to get the field input markup for the image.
	 * Use attributes to identify specific fields
	 *
	 * @return  string  The field input markup.
	 * @since   11.3
	 */
	protected function getInput()
	{
		// Initialize attributes
		$attr = '';

		// Initialize some field attributes.
		$attr .= $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';

		// Initialize JavaScript field attributes.
		$attr .= $this->element['onclick'] ? ' onclick="'.(string) $this->element['onclick'].'"' : '';

		// Set directory
		$directory = (string) $this->element['directory'];

		// Compile source
		$src = JURI::base(true) . '/' . $directory . '/' . htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8');

		$html = array();

		// The image container and tag
		$html[] = '<div class="fltlft">';
		$html[] = ' <img src="'. $src .'"'.
					' id="'. $this->id .'"'.$attr.' />';
		$html[] = '</div>';

		return implode("\n", $html);
	}
}
