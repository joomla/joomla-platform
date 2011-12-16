<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Authorisation
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Interface that defines an object to be authorisable.
 *
 * Requires that an 'authorise' method is implemented in the class.
 *
 * @package     Joomla.Platform
 * @subpackage  Authorisation
 * @since       12.1
 */
interface JAuthorisationAuthorisable
{
	/**
	 * Checks that this action can be performed by an identity.
	 *
	 * @param   string                   $action     The name of the action to check.
	 * @param   JAuthorisationRequestor  $requestor  An (optional) object that implements the JAuthorisationRequestor interface.
	 *
	 * @return  mixed  True if allowed, false for an explicit deny, null for an implicit deny.
	 *
	 * @since   12.1
	 */
	public function authorise($action, JAuthorisationRequestor $requestor = null);
}
