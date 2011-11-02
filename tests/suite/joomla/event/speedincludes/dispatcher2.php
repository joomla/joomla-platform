<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Event
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.base.observable');

/**
 * Class to handle dispatching of events.
 *
 * This is the Observable part of the Observer design pattern
 * for the event architecture.
 *
 * @package     Joomla.Platform
 * @subpackage  Event
 * @link        http://docs.joomla.org/Tutorial:Plugins Plugin tutorials
 * @see         JPlugin
 * @since       11.1
 */
class JDispatcher2 extends JObservable2
{
	public $merged_events = array();
	public $current_event = '';

	/**
	 * Returns a reference to the global Event JDispatcher object, only creating it
	 * if it doesn't already exist.
	 *
	 * This method must be invoked as:
	 * 		<pre>  $dispatcher = &JDispatcher::getInstance();</pre>
	 *
	 * @access	public
	 * @return	JDispatcher	The EventDispatcher object.
	 * @since	1.0
	 */
	public function &getInstance()
	{
		static $instance;

		if ( !is_object( $instance ) ) {
			$instance = new JDispatcher2();
			$instance->addDiscoverableEvents( $instance->default_discoverable_events );
		}

		return $instance;
	}

	/**
	 * Triggers a event for filtering
	 *
	 * @param string The name for the event
	 * @param array An array of arguments
	 * @return array Returns results from called events
	 */
	public function trigger( $event, $args = null )
	{
		// If no arguments were passed, we still need to pass an empty array to
		// the call_user_func_array function.
		if ( $args === null ) {
			$args = array();
		}

		$result = array();

		// Record current event
		$this->current_event[] = $event;

		if ( !isset( $this->_observers[$event] ) ) {
			array_pop( $this->current_event );
			return $result;
		}

		// Sort
		if ( !isset( $this->merged_events[$event] ) ) {
			ksort( $this->_observers[$event] );
			$this->merged_events[$event] = true;
		}

		reset( $this->_observers[$event] );

		foreach ( $this->_observers[$event] as $priority ) {
			foreach ( (array) $priority as $function ) {
				if ( !is_null( $function['function'] ) ) {
					$result[] = call_user_func_array( $function['function'], $args );
				}
			}
		}

		// Done with the event, remove it
		array_pop( $this->current_event );

		return $result;
	}

	public function addObserver( $event, $function, $priority = 10 )
	{
		$idx = $this->_callUniqueId( $event, $function, $priority );
		$this->_observers[$event][$priority][$idx] = array( 'function' => $function );
		unset( $this->merged_events[$event] );

		return true;
	}

	public function hasObserver( $event, $function = false )
	{
		$has = !empty( $this->_observers[$event] );
		if ( false === $function || false == $has ) {
			return $has;
		}

		if ( !$idx = $this->_callUniqueId( $event, $function, false ) ) {
			return false;
		}

		foreach ( (array) array_keys( $this->_observers[$event] ) as $priority ) {
			if ( isset( $this->_observers[$event][$priority][$idx] ) ) {
				return $priority;
			}
		}

		return false;
	}

	public function removeObserver( $event, $function, $priority = 10 )
	{
		$function = $this->_callUniqueId( $event, $function, $priority );

		$r = isset( $this->_observers[$event][$priority][$function] );

		if ( true === $r ) {
			unset( $this->_observers[$event][$priority][$function] );

			if ( empty( $this->_observers[$event][$priority] ) ) {
				unset( $this->_observers[$event][$priority] );
			}

			unset( $this->merged_events[$event] );
		}

		return $r;
	}

	public function removeAllObservers( $event, $priority = false )
	{
		if ( isset( $this->_observers[$event] ) ) {
			if ( false !== $priority && isset( $this->_observers[$event][$priority] ) ) {
				unset( $this->_observers[$event][$priority] );
			} else {
				unset( $this->_observers[$event] );
			}
		}

		if ( isset( $this->merged_events[$event] ) ) {
			unset( $this->merged_events[$event] );
		}

		return true;
	}

	public function currentEvent()
	{
		return end( $this->current_event );
	}

	/**
	 * TODO: Should be moved to some JUtilities class for everyone to use
	 * Method gets a unique id for the event function
	 */
	public function _callUniqueId( $event, $function, $priority )
	{
		if ( is_string( $function ) ) {
			return $function;
		}

		if ( is_object( $function ) ) {
			$function = array( $function, '' );
		} else {
			$function = (array) $function;
		}

		if ( is_object( $function[0] ) ) {
			// Object Class Calling
			return spl_object_hash( $function[0] ) . $function[1];
		} else if ( is_string( $function[0] ) ) {
			// Static Calling
			return $function[0] . $function[1];
		}
	}
}