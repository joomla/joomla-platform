<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Content
 * @copyright   Copyright 2011 eBay, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

/**
 * Extended JContentType class to test access from a differnt table.
 *
 * @package     Joomla.UnitTest
 * @subpackage  Content
 * @since       12.1
 */
class UcmContentHelper extends JContentHelper
{
	/**
	 * Overrides the table property.
	 *
	 * @var    string
	 * @since  12.1
	 */
	protected $table = '#__ucmcontent_types';
}
