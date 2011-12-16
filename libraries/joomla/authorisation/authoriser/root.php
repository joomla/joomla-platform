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
 * The root-user strategy for action authorisation.
 *
 * @package     Joomla.Platform
 * @subpackage  Authorisation
 * @since       12.1
 */
class JAuthorisationAuthoriserRoot implements JAuthorisationAuthoriser
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
	public function isAllowed($action, JAuthorisationRequestor $requestor)
	{
		return true;
	}

	/**
	 * Sets the rules for the authoriser object.
	 *
	 * @param   array  $rules  The rules array.
	 *
	 * @return  JAuthorisationAuthoriser
	 *
	 * @since   12.1
	 */
	public function setRules(array $rules = null)
	{
		// Do nothing.
		return $this;
	}
}
