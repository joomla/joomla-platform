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
 * Interface that defines an authorisation requestor.
 *
 * Requires that a 'getIdentities' method is implemented in the class.
 *
 * @package     Joomla.Platform
 * @subpackage  Authorisation
 * @since       12.1
 */
interface JAuthorisationRequestor
{
	/**
	 * Gets the identities of the authorisation requestor.
	 *
	 * @return  array  Returns an array of scalars representing the identities of the requestor.
	 *
	 * @since   12.1
	 */
	public function getIdentities();
}
