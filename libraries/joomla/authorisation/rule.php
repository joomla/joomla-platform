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
 * Authorisation rule class.
 *
 * @package     Joomla.Platform
 * @subpackage  Authorisation
 * @since       12.1
 */
class JAuthorisationRule
{
	/**
	 * An array in the form: array('action name' => array(-42 => true, 3 => true, 4 => false))
	 *
	 * @var    array
	 * @since  12.1
	 */
	private $_data = array();

	/**
	 * Constructor.
	 *
	 * The input must be in the form:
	 * array('action name' => array(-42 => true, 3 => true, 4 => false))
	 * or an equivalent JSON encoded string.
	 *
	 * @param   mixed  $data  A JSON string or permissions array.
	 *
	 * @since   12.1
	 */
	public function __construct($data = null)
	{
		$this->merge($data);
	}

	/**
	 * Converts this object into a JSON encoded string.
	 *
	 * @return  string  JSON encoded string
	 *
	 * @since   12.1
	 */
	public function __toString()
	{
		return json_encode($this->_data);
	}

	/**
	 * Imports a set of permissions.
	 *
	 * @param   mixed  $data  A JSON string or permissions array.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 * @throws  UnexpectedValueException if $permissions is not an expected variable type.
	 */
	public function merge($data)
	{
		if (!empty($data))
		{
			// Check if a string.
			if (is_string($data))
			{
				// Must be JSON format.
				$data = json_decode($data, true);
			}
			elseif (!is_array($data))
			{
				throw new UnexpectedValueException(JText::sprintf('JAUTHORISATION_INVALID_DATA_TYPE', gettype($data)));
			}

			foreach ($data as $action => $rules)
			{
				foreach ($rules as $identity => $allow)
				{
					$this->set($action, $identity, $allow);
				}
			}
		}
	}

	/**
	 * Sets a rule.
	 *
	 * If the rule already, it will be overwritten unless the value is false (zero -explicit deny).
	 *
	 * @param   string   $action    The name of the action.
	 * @param   string   $identity  The name of the identity referencing the action.
	 * @param   boolean  $allowed   True if the action is allowed or false if it is not.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function set($action, $identity, $allowed)
	{
		$allowed = (int) ((boolean) $allowed);

		// Check if the action exists.
		if (!isset($this->_data[$action]))
		{
			$this->_data[$action] = array();
		}

		// Check if the identity for the action already exists.
		if (isset($this->_data[$action][$identity]))
		{
			// TODO Allow for a rule strategy to determine how merging happens.

			// Explicit deny always wins a merge.
			if ($this->_data[$action][$identity] !== 0)
			{
				$this->_data[$action][$identity] = $allowed;
			}
		}
		else
		{
			$this->_data[$action][$identity] = $allowed;
		}
	}
}
