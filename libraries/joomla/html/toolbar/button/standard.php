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
 * Renders a standard button
 *
 * @package     Joomla.Platform
 * @subpackage  HTML
 * @since       11.1
 */
class JButtonStandard extends JButton
{
	/**
	 * Button type
	 *
	 * @var    string
	 */
	protected $_name = 'Standard';

	public function fetchButton($type = 'Standard', $name = '', $text = '', $task = '', $list = true)
	{
		$i18n_text = JText::_($text);
		$class = $this->fetchIconClass($name);
		$doTask = $this->_getCommand($text, $task, $list);

		$html = "<a href=\"#\" onclick=\"$doTask\" class=\"toolbar\">\n";
		$html .= "<span class=\"$class\">\n";
		$html .= "</span>\n";
		$html .= "$i18n_text\n";
		$html .= "</a>\n";

		return $html;
	}

	/**
	 * Get the button CSS Id
	 *
	 * @return  string  Button CSS Id
	 * @since   11.1
	 */
	public function fetchId($type = 'Standard', $name = '', $text = '', $task = '', $list = true, $hideMenu = false)
	{
		return $this->_parent->getName() . '-' . $name;
	}

	/**
	 * Get the JavaScript command for the button
	 *
	 * @param   string   $name	The task name as seen by the user
	 * @param   string   $task	The task used by the application
	 * @param   ???		$list
	 *
	 * @return  string   JavaScript command string
	 * @since   11.1
	 */
	protected function _getCommand($name, $task, $list)
	{
		JHtml::_('behavior.framework');
		$message = JText::_('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST');
		$message = addslashes($message);

		if ($list)
		{
			$cmd = "javascript:if (document.adminForm.boxchecked.value==0){alert('$message');}else{ Joomla.submitbutton('$task')}";
		}
		else
		{
			$cmd = "javascript:Joomla.submitbutton('$task')";
		}

		return $cmd;
	}
}
