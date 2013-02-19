<?php
/**
 * @package     Joomla.Platform
 * @subpackage  GitHub
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * GitHub API Repository class for the Joomla Platform.
 *
 * @package     Joomla.Platform
 * @subpackage  GitHub
 * @since       12.1
 */
class JGithubRepositories extends JGithubObject
{
	/**
	 * Method to list repositories for the current account.
	 *
	 * @param   integer  $page   Page to request
	 * @param   integer  $limit  Number of results to return per page
	 *
	 * @return  array
	 *
	 * @since   12.1
	 */
	public function getList($page = 0, $limit = 0)
	{
		// Build the request path.
		$path = '/user/repos';

		// Send the request.
		$response = $this->client->get($this->fetchUrl($path, $page, $limit));

		// Validate the response code.
		if ($response->code != 200)
		{
			// Decode the error response and throw an exception.
			$error = json_decode($response->body);
			throw new DomainException($error->message, $response->code);
		}

		return json_decode($response->body);
	}

	/**
	 * Method to list repositories for a user.
	 *
	 * @param   string   $user   The name of the owner of the GitHub repository.
	 * @param   integer  $page   Page to request
	 * @param   integer  $limit  Number of results to return per page
	 *
	 * @return  array
	 *
	 * @since   12.1
	 */
	public function getListFromUser($user, $repo, $page = 0, $limit = 0)
	{
		// Build the request path.
		$path = '/users/' . $user . '/repos';

		// Send the request.
		$response = $this->client->get($this->fetchUrl($path, $page, $limit));

		// Validate the response code.
		if ($response->code != 200)
		{
			// Decode the error response and throw an exception.
			$error = json_decode($response->body);
			throw new DomainException($error->message, $response->code);
		}

		return json_decode($response->body);
	}

	/**
	 * Method to list repositories for an organization.
	 *
	 * @param   string   $org    The name of the Github organization
	 * @param   string   $type   The type of repository (all, public, private, forks, sources, member)
 	 * @param   integer  $page   Page to request
	 * @param   integer  $limit  Number of results to return per page
	 *
	 * @return  array
	 *
	 * @since   12.1
	 */
	public function getListFromOrganization($org, $type = 'all', $page = 0, $limit = 0)
	{
		// Build the request path.
		$path = '/orgs/' . $org . '/repos';

		// Send the request.
		$response = $this->client->get($this->fetchUrl($path, $page, $limit));

		// Validate the response code.
		if ($response->code != 200)
		{
			// Decode the error response and throw an exception.
			$error = json_decode($response->body);
			throw new DomainException($error->message, $response->code);
		}

		return json_decode($response->body);
	}

	/**
	 * Method to get a repository for an organization.
	 *
	 * @param   string   $org    The name of the Github organization
	 * @param   string   $name   The name of the repository
	 *
	 * @return  array
	 *
	 * @since   12.1
	 */
	public function getRepoFromOrganization($org, $name)
	{
		// Build the request path.
		$path = '/repos/' . $org . '/' . $name;

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

	/**
	 * Method to get a repository for a user.
	 *
	 * @param   string   $user   The name of the Github user
	 * @param   string   $name   The name of the repository
	 *
	 * @return  array
	 *
	 * @since   12.1
	 */
	public function getRepoFromUser($user, $name)
	{
		// Build the request path.
		$path = '/repos/' . $user . '/' . $name;

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
