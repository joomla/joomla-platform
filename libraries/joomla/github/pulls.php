<?php
/**
 * @package     Joomla.Platform
 * @subpackage  GitHub
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die();

/**
 * GitHub API Pull Requests class for the Joomla Platform.
 *
 * @package     Joomla.Platform
 * @subpackage  GitHub
 * @since       11.3
 */
class JGithubPulls extends JGithubObject
{
	/**
	 * Method to create a pull request.
	 *
	 * @param   string  $user   The name of the owner of the GitHub repository.
	 * @param   string  $repo   The name of the GitHub repository.
	 * @param   string  $title  The title of the new pull request.
	 * @param   string  $base   The branch (or git ref) you want your changes pulled into. This
	 *                          should be an existing branch on the current repository. You cannot
	 *                          submit a pull request to one repo that requests a merge to a base
	 *                          of another repo.
	 * @param   string  $head   The branch (or git ref) where your changes are implemented.
	 * @param   string  $body   The body text for the new pull request.
	 *
	 * @return  object
	 *
	 * @since   11.3
	 */
	public function create($user, $repo, $title, $base, $head, $body = '')
	{
		// Build the request path.
		$path = '/repos/' . $user . '/' . $repo . '/pulls';

		// Build the request data.
		$data = json_encode(
			array(
				'title' => $title,
				'base' => $base,
				'head' => $head,
				'body' => $body
			)
		);

		// Send the request.
		$response = $this->client->post($this->fetchUrl($path), $data);

		// Validate the response code.
		if ($response->code != 201)
		{
			// Decode the error response and throw an exception.
			$error = json_decode($response->body);
			throw new DomainException($error->message, $response->code);
		}

		return json_decode($response->body);
	}

	/**
	 * Method to create a comment on a pull request.
	 *
	 * @param   string   $user      The name of the owner of the GitHub repository.
	 * @param   string   $repo      The name of the GitHub repository.
	 * @param   integer  $pullId    The pull request number.
	 * @param   string   $body      The comment body text.
	 * @param   string   $commitId  The SHA1 hash of the commit to comment on.
	 * @param   string   $filePath  The Relative path of the file to comment on.
	 * @param   string   $position  The line index in the diff to comment on.
	 *
	 * @return  object
	 *
	 * @since   11.3
	 */
	public function createComment($user, $repo, $pullId, $body, $commitId, $filePath, $position)
	{
		// Build the request path.
		$path = '/repos/' . $user . '/' . $repo . '/pulls/' . (int) $pullId . '/comments';

		// Build the request data.
		$data = json_encode(
			array(
				'body' => $body,
				'commit_id' => $commitId,
				'path' => $filePath,
				'position' => $position
			)
		);

		// Send the request.
		$response = $this->client->post($this->fetchUrl($path), $data);

		// Validate the response code.
		if ($response->code != 201)
		{
			// Decode the error response and throw an exception.
			$error = json_decode($response->body);
			throw new DomainException($error->message, $response->code);
		}

		return json_decode($response->body);
	}

	/**
	 * Method to create a comment in reply to another comment.
	 *
	 * @param   string   $user       The name of the owner of the GitHub repository.
	 * @param   string   $repo       The name of the GitHub repository.
	 * @param   integer  $pullId     The pull request number.
	 * @param   string   $body       The comment body text.
	 * @param   integer  $inReplyTo  The id of the comment to reply to.
	 *
	 * @return  object
	 *
	 * @since   11.3
	 */
	public function createCommentReply($user, $repo, $pullId, $body, $inReplyTo)
	{
		// Build the request path.
		$path = '/repos/' . $user . '/' . $repo . '/pulls/' . (int) $pullId . '/comments';

		// Build the request data.
		$data = json_encode(
			array(
				'body' => $body,
				'in_reply_to' => (int) $inReplyTo
			)
		);

		// Send the request.
		$response = $this->client->post($this->fetchUrl($path), $data);

		// Validate the response code.
		if ($response->code != 201)
		{
			// Decode the error response and throw an exception.
			$error = json_decode($response->body);
			throw new DomainException($error->message, $response->code);
		}

		return json_decode($response->body);
	}

	/**
	 * Method to create a pull request from an existing issue.
	 *
	 * @param   string   $user     The name of the owner of the GitHub repository.
	 * @param   string   $repo     The name of the GitHub repository.
	 * @param   integer  $issueId  The issue number for which to attach the new pull request.
	 * @param   string   $base     The branch (or git ref) you want your changes pulled into. This
	 *                             should be an existing branch on the current repository. You cannot
	 *                             submit a pull request to one repo that requests a merge to a base
	 *                             of another repo.
	 * @param   string   $head     The branch (or git ref) where your changes are implemented.
	 *
	 * @return  object
	 *
	 * @since   11.3
	 */
	public function createFromIssue($user, $repo, $issueId, $base, $head)
	{
		// Build the request path.
		$path = '/repos/' . $user . '/' . $repo . '/pulls';

		// Build the request data.
		$data = json_encode(
			array(
				'issue' => (int) $issueId,
				'base' => $base,
				'head' => $head
			)
		);

		// Send the request.
		$response = $this->client->post($this->fetchUrl($path), $data);

		// Validate the response code.
		if ($response->code != 201)
		{
			// Decode the error response and throw an exception.
			$error = json_decode($response->body);
			throw new DomainException($error->message, $response->code);
		}

		return json_decode($response->body);
	}

	/**
	 * Method to delete a comment on a pull request.
	 *
	 * @param   string   $user       The name of the owner of the GitHub repository.
	 * @param   string   $repo       The name of the GitHub repository.
	 * @param   integer  $commentId  The id of the comment to delete.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function deleteComment($user, $repo, $commentId)
	{
		// Build the request path.
		$path = '/repos/' . $user . '/' . $repo . '/pulls/comments/' . (int) $commentId;

		// Send the request.
		$response = $this->client->delete($this->fetchUrl($path));

		// Validate the response code.
		if ($response->code != 204)
		{
			// Decode the error response and throw an exception.
			$error = json_decode($response->body);
			throw new DomainException($error->message, $response->code);
		}
	}

	/**
	 * Method to update a pull request.
	 *
	 * @param   string   $user    The name of the owner of the GitHub repository.
	 * @param   string   $repo    The name of the GitHub repository.
	 * @param   integer  $pullId  The pull request number.
	 * @param   string   $title   The optional new title for the pull request.
	 * @param   string   $body    The optional new body text for the pull request.
	 * @param   string   $state   The optional new state for the pull request. [open, closed]
	 *
	 * @return  object
	 *
	 * @since   11.3
	 */
	public function edit($user, $repo, $pullId, $title = null, $body = null, $state = null)
	{
		// Build the request path.
		$path = '/repos/' . $user . '/' . $repo . '/pulls/' . (int) $pullId;

		// Craete the data object.
		$data = new stdClass;

		// If a title is set add it to the data object.
		if (isset($title))
		{
			$data->title = $title;
		}

		// If a body is set add it to the data object.
		if (isset($body))
		{
			$data->body = $body;
		}

		// If a state is set add it to the data object.
		if (isset($state))
		{
			$data->state = $state;
		}

		// Encode the request data.
		$data = json_encode($data);

		// Send the request.
		$response = $this->client->patch($this->fetchUrl($path), $data);

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
	 * Method to update a comment on a pull request.
	 *
	 * @param   string   $user       The name of the owner of the GitHub repository.
	 * @param   string   $repo       The name of the GitHub repository.
	 * @param   integer  $commentId  The id of the comment to update.
	 * @param   string   $body       The new body text for the comment.
	 *
	 * @return  object
	 *
	 * @since   11.3
	 */
	public function editComment($user, $repo, $commentId, $body)
	{
		// Build the request path.
		$path = '/repos/' . $user . '/' . $repo . '/pulls/comments/' . (int) $commentId;

		// Build the request data.
		$data = json_encode(
			array(
				'body' => $body
			)
		);

		// Send the request.
		$response = $this->client->patch($this->fetchUrl($path), $data);

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
	 * Method to get a single pull request.
	 *
	 * @param   string   $user    The name of the owner of the GitHub repository.
	 * @param   string   $repo    The name of the GitHub repository.
	 * @param   integer  $pullId  The pull request number.
	 *
	 * @return  object
	 *
	 * @since   11.3
	 */
	public function get($user, $repo, $pullId)
	{
		// Build the request path.
		$path = '/repos/' . $user . '/' . $repo . '/pulls/' . (int) $pullId;

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
	 * Method to get a specific comment on a pull request.
	 *
	 * @param   string   $user       The name of the owner of the GitHub repository.
	 * @param   string   $repo       The name of the GitHub repository.
	 * @param   integer  $commentId  The comment id to get.
	 *
	 * @return  object
	 *
	 * @since   11.3
	 */
	public function getComment($user, $repo, $commentId)
	{
		// Build the request path.
		$path = '/repos/' . $user . '/' . $repo . '/pulls/comments/' . (int) $commentId;

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
	 * Method to get the list of comments on a pull request.
	 *
	 * @param   string   $user    The name of the owner of the GitHub repository.
	 * @param   string   $repo    The name of the GitHub repository.
	 * @param   integer  $pullId  The pull request number.
	 * @param   integer  $page    The page number from which to get items.
	 * @param   integer  $limit   The number of items on a page.
	 *
	 * @return  array
	 *
	 * @since   11.3
	 */
	public function getComments($user, $repo, $pullId, $page = 0, $limit = 0)
	{
		// Build the request path.
		$path = '/repos/' . $user . '/' . $repo . '/pulls/' . (int) $pullId . '/comments';

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
	 * Method to get a list of commits for a pull request.
	 *
	 * @param   string   $user    The name of the owner of the GitHub repository.
	 * @param   string   $repo    The name of the GitHub repository.
	 * @param   integer  $pullId  The pull request number.
	 * @param   integer  $page    The page number from which to get items.
	 * @param   integer  $limit   The number of items on a page.
	 *
	 * @return  array
	 *
	 * @since   11.3
	 */
	public function getCommits($user, $repo, $pullId, $page = 0, $limit = 0)
	{
		// Build the request path.
		$path = '/repos/' . $user . '/' . $repo . '/pulls/' . (int) $pullId . '/commits';

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
	 * Method to get a list of files for a pull request.
	 *
	 * @param   string   $user    The name of the owner of the GitHub repository.
	 * @param   string   $repo    The name of the GitHub repository.
	 * @param   integer  $pullId  The pull request number.
	 * @param   integer  $page    The page number from which to get items.
	 * @param   integer  $limit   The number of items on a page.
	 *
	 * @return  array
	 *
	 * @since   11.3
	 */
	public function getFiles($user, $repo, $pullId, $page = 0, $limit = 0)
	{
		// Build the request path.
		$path = '/repos/' . $user . '/' . $repo . '/pulls/' . (int) $pullId . '/files';

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
	 * Method to list pull requests.
	 *
	 * @param   string   $user   The name of the owner of the GitHub repository.
	 * @param   string   $repo   The name of the GitHub repository.
	 * @param   string   $state  The optional state to filter requests by. [open, closed]
	 * @param   integer  $page   The page number from which to get items.
	 * @param   integer  $limit  The number of items on a page.
	 *
	 * @return  array
	 *
	 * @since   11.3
	 */
	public function getList($user, $repo, $state = 'open', $page = 0, $limit = 0)
	{
		// Build the request path.
		$path = '/repos/' . $user . '/' . $repo . '/pulls';

		// If a state exists append it as an option.
		if ($state != 'open')
		{
			$path .= '?state=' . $state;
		}

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
	 * Method to check if a pull request has been merged.
	 *
	 * @param   string   $user    The name of the owner of the GitHub repository.
	 * @param   string   $repo    The name of the GitHub repository.
	 * @param   integer  $pullId  The pull request number.  The pull request number.
	 *
	 * @return  boolean  True if the pull request has been merged.
	 *
	 * @since   11.3
	 */
	public function isMerged($user, $repo, $pullId)
	{
		// Build the request path.
		$path = '/repos/' . $user . '/' . $repo . '/pulls/' . (int) $pullId . '/merge';

		// Send the request.
		$response = $this->client->get($this->fetchUrl($path));

		// Validate the response code.
		if ($response->code == 204)
		{
			return true;
		}
		elseif ($response->code == 404)
		{
			return false;
		}
		else
		{
			// Decode the error response and throw an exception.
			$error = json_decode($response->body);
			throw new DomainException($error->message, $response->code);
		}
	}

	/**
	 * Method to merge a pull request.
	 *
	 * @param   string   $user     The name of the owner of the GitHub repository.
	 * @param   string   $repo     The name of the GitHub repository.
	 * @param   integer  $pullId   The pull request number.
	 * @param   string   $message  The message that will be used for the merge commit.
	 *
	 * @return  object
	 *
	 * @since   11.3
	 */
	public function merge($user, $repo, $pullId, $message = '')
	{
		// Build the request path.
		$path = '/repos/' . $user . '/' . $repo . '/pulls/' . (int) $pullId . '/merge';

		// Build the request data.
		$data = json_encode(
			array(
				'commit_message' => $message
			)
		);

		// Send the request.
		$response = $this->client->put($this->fetchUrl($path), $data);

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
