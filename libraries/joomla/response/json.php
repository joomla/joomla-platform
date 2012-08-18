<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Response
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * JSON Response class.
 *
 * This class serves to provide the Joomla Platform with a common interface to access
 * response variables for e.g. Ajax requests.
 *
 * @package     Joomla.Platform
 * @subpackage  Response
 * @since       12.2
 */
class JResponseJson
{
	/**
	 * Determines whether the request was successful
	 *
	 * @var		boolean
	 * @since	12.2
	 */
	public $success		= true;

	/**
	 * The main response message
	 *
	 * @var		string
	 * @since	12.2
	 */
	public $message		= null;

	/**
	 * Array of messages gathered in the JApplication object
	 *
	 * @var		array
	 * @since	12.2
	 */
	public $messages	= null;

	/**
	 * The response data
	 *
	 * var		array/object
	 * @since	12.2
	 */
	public $data			= null;

	/**
	 * Constructor
	 *
	 * @param   array/object  $response  The Response data
	 * @param   string        $message   The main response message
	 * @param   boolean       $error     True, if the success flag shall be set to false, defaults to false
	 *
	 * @since		12.2
	 */
	public function __construct($response = null, $message = null, $error = false)
	{
		$this->message = $message;

		// Get the message queue if available
		if (!is_null(JFactory::$application) && is_callable(array(JFactory::$application, 'getMessageQueue')))
		{
			$messages = JFactory::getApplication()->getMessageQueue();

			// Build the sorted messages list
			if (is_array($messages) && count($messages))
			{
				foreach ($messages as $message)
				{
					if (isset($message['type']) && isset($message['message']))
					{
						$lists[$message['type']][] = $message['message'];
					}
				}
			}

			// If messages exist add them to the output
			if (isset($lists) && is_array($lists))
			{
				$this->messages = $lists;
			}
		}

		// Check if we are dealing with an error
		if ($response instanceof Exception)
		{
			// Prepare the error response
			$this->success	= false;
			$this->message	= $response->getMessage();
		}
		else
		{
			// Prepare the response data
			$this->success	= !$error;
			$this->data			= $response;
		}
	}

	/**
	 * Magic toString method for sending the response in JSON format
	 *
	 * @return	string	The response in JSON format
	 *
	 * @since		12.2
	 */
	public function __toString()
	{
		return json_encode($this);
	}
}
