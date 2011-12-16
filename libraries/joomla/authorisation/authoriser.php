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
 * Interface that defines an object to be an authoriser.
 *
 * Requires that 'isAllowed' and 'setRules' methods is implemented in the class.
 *
 * @package     Joomla.Platform
 * @subpackage  Authorisation
 * @since       12.1
 */
interface JAuthorisationAuthoriser
{
	/**
	 * Checks that this action can be performed by an identity.
	 *
	 * @param   string                   $action     The name of the action to check.
	 * @param   JAuthorisationRequestor  $requestor  An object that implements the JAuthorisationRequestor interface.
	 *
	 * @return  mixed  True if allowed, false for an explicit deny, null for an implicit deny.
	 *
	 * @since   12.1
	 */
	public function isAllowed($action, JAuthorisationRequestor $requestor);

	/**
	 * Sets the rules for the authoriser object.
	 *
	 * @param   array  $rules  The rules array.
	 *
	 * @return  JAuthorisationAuthoriser  This method must support chaining.
	 *
	 * @since   12.1
	 */
	public function setRules(array $rules = null);
}
