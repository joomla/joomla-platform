<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Base
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die();

/**
 * Abstract observable class to implement the observer design pattern
 *
 * @package     Joomla.Platform
 * @subpackage  Base
 * @since       11.1
 */
class JObservable2 extends JObject
{
	/**
	 * An array of Observer objects to notify
	 *
	 * @var    array
	 * @since  11.1
	 */
	protected $_observers = array();

	/**
	 * An array of discoverable events
	 *
	 * @var array
	 **/
	public $discoverable_events = array();

	/**
	 * It contains a list of plugin available event triggers from Joomla.
	 * All of the current values on the array, would need to be set by the CMS and not the platform
	 * Having a list like this sames memory from adding possible events that will never get called
	 */
	public $default_discoverable_events = array(
		'onContentAfterSave',
		'onUserAfterDelete',
		'onAfterDisplay',
		'onContentAfterTitle',
		'onContentAfterDisplay',
		'onAfterInitialise',
		'onAfterRoute',
		'onAfterDispatch',
		'onAfterRender',
		'onAfterStoreUser',
		'onUserAuthenticate',
		'onAuthenticateFailure',
		'onContentBeforeSave',
		'onUserBeforeDelete',
		'onBeforeDisplay',
		'onContentBeforeDisplay',
		'onBeforeStoreUser',
		'onCustomEditorButton',
		'onDisplay',
		'onGetContent',
		'onGetInsertMethod',
		'onGetWebServices',
		'onInit',
		'onUserLoginFailure',
		'onUserLogoutFailure',
		'onUserLogin',
		'onUserLogout',
		'onPrepareContent',
		'onSave',
		'onUserAfterSave',
		'onUserBeforeSave',
		'onContentSearch',
		'onContentSearchAreas',
		'onSetContent',
		'onSubmitContact',
		'onValidateContact',
		'onContentPrepareData',
		'onContentPrepareForm'
		);

	/**
	 * Constructor
	 *
	 * Note: Make Sure it's not directly instansiated
	 */
	public function __construct()
	{
		$this->_observers = array();
	}

	/**
	 * Attach an observer object
	 *
	 * @param   object  $observer  An observer object to attach
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	public function attach( $observer )
	{
		$methods = array_diff( get_class_methods( $observer ), get_class_methods( 'JPlugin' ) );

		foreach ( $methods as $event ) {
			if ( in_array( $event, $this->discoverable_events ) ) {
				$this->addObserver( $event, array( $observer, $event ) );
			}
		}
	}

	/**
	 * Detach an observer object
	 *
	 * @param   object  $observer  An observer object to detach.
	 *
	 * @return  boolean  True if the observer object was detached.
	 *
	 * @since   11.1
	 */
	public function detach($observer, $event = '')
	{
		return $this->removeObserver( $event, $observer );
	}

	public function addDiscoverableEvents( $events )
	{
		$this->discoverable_events = array_merge( $this->discoverable_events, (array) $events );
	}
}