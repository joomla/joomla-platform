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
 * Extended Utility class for batch processing widgets.
 *
 * @package     Joomla.Platform
 * @subpackage  HTML
 * @since       11.1
 */
abstract class JHtmlBatch
{
	/**
	 * Display a batch widget for the access level selector.
	 *
	 * @return  string  The necessary HTML for the widget.
	 *
	 * @since   11.1
	 */
	public static function access()
	{
		// Create the batch selector to change an access level on a selection list.
		$lines = array(
			'<label id="batch-access-lbl" for="batch-access" class="hasTip" title="' . JText::_('JLIB_HTML_BATCH_ACCESS_LABEL') . '::'
				. JText::_('JLIB_HTML_BATCH_ACCESS_LABEL_DESC') . '">', JText::_('JLIB_HTML_BATCH_ACCESS_LABEL'), '</label>',
			JHtml::_(
				'access.assetgrouplist',
				'batch[assetgroup_id]', '',
				'class="inputbox"',
				array(
					'title' => JText::_('JLIB_HTML_BATCH_NOCHANGE'),
					'id' => 'batch-access')
				)
			);

		return implode("\n", $lines);
	}

	/**
	 * Displays a batch widget for moving or copying items.
	 *
	 * @param   string  $extension  The extension that owns the category.
	 *
	 * @return  string  The necessary HTML for the widget.
	 *
	 * @since   11.1
	 */
	public static function item($extension)
	{
		// Create the copy/move options.
		$options = array(JHtml::_('select.option', 'c', JText::_('JLIB_HTML_BATCH_COPY')),
			JHtml::_('select.option', 'm', JText::_('JLIB_HTML_BATCH_MOVE')));

		// Create the batch selector to change select the category by which to move or copy.
		$lines = array('<label id="batch-choose-action-lbl" for="batch-choose-action">', JText::_('JLIB_HTML_BATCH_MENU_LABEL'), '</label>',
			'<fieldset id="batch-choose-action" class="combo">', '<select name="batch[category_id]" class="inputbox" id="batch-category-id">',
			'<option value="">' . JText::_('JSELECT') . '</option>',
			JHtml::_('select.options', JHtml::_('category.options', $extension)), '</select>',
			JHtml::_('select.radiolist', $options, 'batch[move_copy]', '', 'value', 'text', 'm'), '</fieldset>');

		return implode("\n", $lines);
	}

	/**
	 * Display a batch widget for the language selector.
	 *
	 * @return  string  The necessary HTML for the widget.
	 *
	 * @since   11.3
	 */
	public static function language()
	{
		// Create the batch selector to change an access level on a selection list.
		$lines = array(
			'<label id="batch-language-lbl" for="batch-language" class="hasTip"'
			.' title="' . JText::_('JLIB_HTML_BATCH_LANGUAGE_LABEL') . '::' . JText::_('JLIB_HTML_BATCH_LANGUAGE_LABEL_DESC') . '">',
			JText::_('JLIB_HTML_BATCH_LANGUAGE_LABEL'),
			'</label>',
			'<select name="batch[language_id]" class="inputbox" id="batch-language-id">',
				'<option value="">' . JText::_('JLIB_HTML_BATCH_LANGUAGE_NOCHANGE') . '</option>',
				JHtml::_('select.options', JHtml::_('contentlanguage.existing', true, true), 'value', 'text'),
			'</select>'
		);

		return implode("\n", $lines);
	}
}
