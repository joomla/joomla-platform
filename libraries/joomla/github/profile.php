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
	 * Method to get github profile.
	 *
	 * @param   string	$username	The login name of a github user.
	 *
	 * @return  object
	 *
	 * @since   12.3
	 */
	
	public function getProfile($username, $page = 0, $limit = 0)
	{
		// Build the request path.
		$path = '/users/' . $username;

		// Send the request.
		$response = $this->client->get($this->fetchUrl($path));

		// Validate the response code.
		if ($response->code != 200)
		{
			// Decode the error response and throw an exception.
			$error = json_decode($response->body);
			throw new DomainException($error->message, $response->code);
		}

		return json_decode($response->body);
	}
}