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
 * Renders a list element
 *
 * @package     Joomla.Platform
 * @subpackage  Parameter
 * @since       11.1
 * @deprecated  Use JForm instead
 */

class JElementList extends JElement
{
	/**
	* Element type
	*
	* @var    string
	*/
	protected $_name = 'List';

	/**
	 * Get the options for the element
	 *
	 * @param   object  The current XML node.
	 * 
	 * @return  array
	 * @deprecated
	 * @since   11.1
	 */
	protected function _getOptions(&$node)
	{
		$options = array ();
		foreach ($node->children() as $option)
		{
			$val	= $option->attributes('value');
			$text	= $option->data();
			$options[] = JHtml::_('select.option', $val, JText::_($text));
		}
		return $options;
	}

	/**
	 * Fetch the HTML code for the parameter element.
	 *
	 * @param   string   The field name.
	 * @param   mixed    The value of the field.
	 * @param   object   The current XML node.
	 * @param   string   The name of the HTML control.
	 * 
	 * @since   11.1
	 * 
	 * @deprecated
	 */
	public function fetchElement($name, $value, &$node, $control_name)
	{
		$ctrl	= $control_name .'['. $name .']';
		$attribs	= ' ';

		if ($v = $node->attributes('size')) {
			$attribs	.= 'size="'.$v.'"';
		}
		if ($v = $node->attributes('class')) {
			$attribs	.= 'class="'.$v.'"';
		} else {
			$attribs	.= 'class="inputbox"';
		}
		if ($m = $node->attributes('multiple'))
		{
			$attribs	.= 'multiple="multiple"';
			$ctrl		.= '[]';
		}

		return JHtml::_(
			'select.genericlist',
			$this->_getOptions($node),
			$ctrl,
			array(
				'id' => $control_name.$name,
				'list.attr' => $attribs,
				'list.select' => $value
			)
		);
	}
}
