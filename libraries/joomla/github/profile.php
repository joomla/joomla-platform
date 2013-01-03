<?php
/**
 * @package     Joomla.Platform
 * @subpackage  GitHub
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * GitHub API Profile class for the Joomla Platform.
 *
 * @package     Joomla.Platform
 * @subpackage  GitHub
 * @since       11.4
 */

class JGithubProfile extends JGithubObject
{
	/**
	 * Method to create a gist.
	 *
	 * @param   string	$login	The login name of a github user.
	 *
	 * @return  object
	 *
	 * @since   11.3
	 */
	
	public function getProfile($login)
	{
		//build user path
		$path = '/users/' . $login;
		
		//send the request
		$response = $this->client->post($this->fetchUrl($path));
		
		// Validate the response code.
		if ($response->code != 200)
		{
			// Decode the error response and throw an exception.
			$error = json_decode($response->body);
			throw new DomainException($error->message, $response->code);
		}
		
		return $response;
	}
}