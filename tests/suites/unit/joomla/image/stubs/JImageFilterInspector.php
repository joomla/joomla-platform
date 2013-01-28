<?php
/**
 * @package		 Joomla.Platform
 * @subpackage	Media
 *
 * @copyright	 Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		 GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.media.imagefilter');

/**
 * Image Filter class inspector for testing purposes.
 *
 * @package		 Joomla.UnitTest
 * @subpackage	Media
 * @since			 11.3
 */
class JImageFilterInspector extends JImageFilter
{
	/**
	 * Method to apply a filter to an image resource.
	 *
	 * @param	 array	$options	An array of options for the filter.
	 *
	 * @return	void
	 *
	 * @since	 11.3
	 */
	public function execute(array $options = array())
	{
	}
}
