<?php
/**
 * @package     Joomla.Platform
 * @subpackage  HTML
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Renders a password element
 *
 * @package     Joomla.Platform
 * @subpackage	Parameter
 * @since    11.1
 * @deprecated	JParameter is deprecated and will be removed in a future version. Use JForm instead.
 */

class JElementPassword extends JElement
{
	/**
	* Element name
	*
	* @var    string
	*/
	protected $_name = 'Password';

	public function fetchElement($name, $value, &$node, $control_name)
	{
		$size = ($node->attributes('size') ? 'size="'.$node->attributes('size').'"' : '');
		$class = ($node->attributes('class') ? 'class="'.$node->attributes('class').'"' : 'class="text_area"');

		return '<input type="password" name="'.$control_name.'['.$name.']" id="'.$control_name.$name.'" value="'.$value.'" '.$class.' '.$size.' />';
	}
}
