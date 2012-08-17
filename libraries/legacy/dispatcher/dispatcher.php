<?php
/**
 * @package     Joomla.Legacy
 * @subpackage  Dispatcher
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Deprecated class placeholder.  You should use JEventDispatcher instead.
 *
 * @package     Joomla.Legacy
 * @subpackage  Dispatcher
 * @since       11.1
 * @deprecated  12.3
 */
class JDispatcher extends JEventDispatcher
{
	/**
	 * Constructor.
	 *
	 * @since   11.1
	 */
	public function __construct()
	{
		JLog::add('JDispatcher is deprecated. Use JEventDispatcher instead.', JLog::WARNING, 'deprecated');
		parent::__construct();
	}
}
