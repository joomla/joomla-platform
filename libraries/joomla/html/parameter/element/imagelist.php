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
 * Renders a imagelist element
 *
 * @package     Joomla.Platform
 * @subpackage  Parameter
 * @since       11.1
 * @deprecated  12.1   Use JFormFieldImageList instead.
 */
class JElementImageList extends JElement
{
	/**
	 * Element name
	 *
	 * @var    string
	 */
	protected $_name = 'ImageList';

	/**
	 *
	 * @since   11.1
	 *
	 * @deprecated    12.1   Use JFormFieldImageLst instead.
	 */
	public function fetchElement($name, $value, &$node, $control_name)
	{
		// Deprecation warning.
		JLog::add('JElementImageList::fetchElement() is deprecated.', JLog::WARNING, 'deprecated');

		$filter = '\.png$|\.gif$|\.jpg$|\.bmp$|\.ico$';
		$node->addAttribute('filter', $filter);

		$parameter = $this->_parent->loadElement('filelist');

		return $parameter->fetchElement($name, $value, $node, $control_name);
	}
}
