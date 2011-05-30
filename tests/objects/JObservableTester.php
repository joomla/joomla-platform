<?php
/**
 * Abstract observable class to implement the observer design pattern
 *
 * @package     Joomla.Platform.UnitTesting
 * @subpackage  Base
 * @since       11.1
 */

require_once JPATH_PLATFORM.'/joomla/base/observable.php';

class JObservableTester extends JObservable
{
	public function setState( $setMe )
	{
		$this->_state = $setMe;
		return $setMe;
	}
}
?>