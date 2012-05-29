<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  HTML
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * A mock editor button plugin.
 *
 * @package     Joomla.UnitTest
 * @subpackage  HTML
 * @since       12.2
 */
class plgButtonFake extends JPlugin
{
	/**
	 * @var    string
	 * @since  12.2
	 */
	public $name = 'fake';

	/**
	 * onDisplay() event.
	 *
	 * @param   string  $editor  Editor Name
	 * @param   string  $asset   Asset
	 * @param   string  $author  Author name
	 *
	 * @return  string
	 */
	public function onDisplay($editor, $asset, $author)
	{
		if ($editor === 'fake' && $asset === 'test_asset' && $author === 'test_author')
		{
			return 'triggered';
		}
	}
}
