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
 * The default strategy for action authorisation.
 *
 * @package     Joomla.Platform
 * @subpackage  Authorisation
 * @since       12.1
 */
class JAuthorisationAuthoriserDefault implements JAuthorisationAuthoriser
{
	/**
	 * The permissions object.
	 *
	 * @var    array
	 * @since  12.1
	 */
	private $_rules = array();

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
		// Implicit deny by default.
		$result = null;

		foreach ($requestor->getIdentities() as $identity)
		{
			// Check if the identity is known.
			if (isset($this->_rules[$action][$identity]))
			{
				$result = (boolean) $this->_rules[$action][$identity];

				// An explicit deny wins.
				if ($result === false)
				{
					break;
				}
			}
		}

		return $result;
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
		$this->_rules = $rules === null ? array() : $rules;

		return $this;
	}
}
