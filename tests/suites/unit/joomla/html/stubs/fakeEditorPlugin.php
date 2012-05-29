<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  HTML
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * A mock editor plugin.
 *
 * @package     Joomla.UnitTest
 * @subpackage  HTML
 * @since       12.2
 */
class plgEditorFake extends JPlugin
{
	/**
	 * @var    string
	 * @since  12.2
	 */
	public $name = 'fake';

	/**
	 * onInit() event.
	 *
	 * @return  string
	 */
	public function onInit()
	{
		return '<script type=\"text/javascript\"></script>';
	}

	/**
	 * onSave() event.
	 *
	 * @param   mixed  $editor  Editor name or NULL
	 *
	 * @return  mixed a string or NULL
	 */
	public function onSave($editor)
	{
		return $editor;
	}

	/**
	 * onGetContent() event.
	 *
	 * @param   mixed  $editor  Editor name or NULL
	 *
	 * @return  mixed a string or NULL
	 */
	public function onGetContent($editor)
	{
		return $editor;
	}

	/**
	 * onSetContent() event.
	 *
	 * @param   mixed  $editor   Editor name or NULL
	 * @param   mixed  $content  The content or NULL
	 *
	 * @return  mixed a string or NULL
	 */
	public function onSetContent($editor, $content)
	{
		return $editor . $content;
	}

	/**
	 * onDisplay() event.
	 *
	 * @param   mixed   $name     The name of the editor area or NULL
	 * @param   string  $content  The content of the field
	 * @param   mixed   $width    The width of the editor area
	 * @param   mixed   $height   The height of the editor area
	 * @param   int     $col      The number of columns for the editor area
	 * @param   int     $row      The number of rows for the editor area
	 * @param   bool    $buttons  True and the editor buttons will be displayed
	 * @param   string  $id       An optional ID for the textarea
	 *
	 * @return  mixed a string or NULL
	 */
	public function onDisplay($name, $content, $width, $height, $col, $row, $buttons = true, $id = null)
	{
		return $name . $content . $width . $height . $col . $row . $buttons . $id;
	}
}
