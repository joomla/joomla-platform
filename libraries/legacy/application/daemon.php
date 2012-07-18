<?php
/**
 * @package     Joomla.Legacy
 * @subpackage  Application
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

JLog::add('JDaemon has been renamed to JApplicationDaemon.', JLog::WARNING, 'deprecated');

/**
 * Backward Compatability Stub for JApplicationDaemon
 *
 * @package     Joomla.Legacy
 * @subpackage  Application
 * @since       11.1
 * @deprecated  12.3 Use JApplicationDaemon instead.
 */
class JDaemon extends JApplicationDaemon
{
	/**
	 * Class constructor.
	 *
	 * @param   mixed  $input       An optional argument to provide dependency injection for the application's
	 *                              input object.  If the argument is a JInputCli object that object will become
	 *                              the application's input object, otherwise a default input object is created.
	 * @param   mixed  $config      An optional argument to provide dependency injection for the application's
	 *                              config object.  If the argument is a JRegistry object that object will become
	 *                              the application's config object, otherwise a default config object is created.
	 * @param   mixed  $dispatcher  An optional argument to provide dependency injection for the application's
	 *                              event dispatcher.  If the argument is a JEventDispatcher object that object will become
	 *                              the application's event dispatcher, if it is null then the default event dispatcher
	 *                              will be created based on the application's loadDispatcher() method.
	 *
	 * @since   11.1
	 * @deprecated  12.3 Use JApplicationDaemon instead.
	 * @throws  RuntimeException
	 */
	public function __construct(JInputCli $input = null, JRegistry $config = null, JEventDispatcher $dispatcher = null)
	{
		JLog::add('JDaemon is deprecated. Use JApplicationDaemon instead.', JLog::WARNING, 'deprecated');
		parent::__construct($input, $config, $dispatcher);
	}
}
