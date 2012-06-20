<?php
/**
 * @package     Joomla.Platform
 * @subpackage  MediaWiki
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * MediaWiki API Users class for the Joomla Platform.
 *
 * @package     Joomla.Platform
 * @subpackage  MediaWiki
 * @since       12.1
 */
class JMediawikiUsers extends JMediawikiObject
{
	/**
     * Method to login and get authentication tokens.
     *
     * @param   string  $lgname      User Name.
     * @param   string  $lgpassword  Password.
     * @param   string  $lgdomain    Domain (optional).
     *
     * @return  object
     *
     * @since   12.1
     */
	public function login($lgname, $lgpassword, $lgdomain = null)
	{
		// Build the request path.
		$path = '?action=login&lgname=' . $lgname . '&lgpassword=' . $lgpassword;

		// Send the request.
		$response = $this->client->post($this->fetchUrl($path));

		return $this->validateResponse($response);
	}

	/**
	 * Method to logout and clear session data.
	 *
	 * @return  object
	 *
	 * @since   12.1
	 */
	public function logout()
	{
	}

	/**
     * Method to get user information.
     *
     * @return  object
     *
     * @since   12.1
     */
	public function getUserInfo()
	{
	}

	/**
     * Method to get current user information.
     *
     * @return  object
     *
     * @since   12.1
     */
	public function getCurrentUserInfo()
	{
	}

	/**
     * Method to get user contributions.
     *
     * @param   string   $ucuser        The users to retrieve contributions for.
	 * @param   string   $ucuserprefix  Retrieve contibutions for all users whose names begin with this value.
     * @param   integer  $uclimit       The users to retrieve contributions for.
     * @param   string   $ucstart       The start timestamp to return from.
     * @param   string   $ucend         The end timestamp to return to.
     * @param   boolean  $uccontinue    When more results are available, use this to continue.
     * @param   string   $ucdir         In which direction to enumerate.
     * @param   array    $ucnamespace   Only list contributions in these namespaces.
     * @param   array    $ucprop        Include additional pieces of information.
     * @param   array    $ucshow        Show only items that meet this criteria.
     * @param   string   $uctag         Only list revisions tagged with this tag.
     * @param   string   $uctoponly     Only list changes which are the latest revision
     *
     * @return  object
     *
     * @since   12.1
     */
	public function getUserContribs($ucuser = null, $ucuserprefix = null, $uclimit = null, $ucstart = null, $ucend = null, $uccontinue = null, $ucdir = null, array $ucnamespace = null, array $ucprop = null, array $ucshow = null, $uctag = null, $uctoponly = null)
	{
		// Build the request path.
		$path = '?action=query&list=usercontribs';

		if (isset($ucuser))
		{
			$path .= '&ucuser=' . $ucuser;
		}

		if (isset($ucuserprefix))
		{
			$path .= '&ucuserprefix=' . $ucuserprefix;
		}

		if (isset($uclimit))
		{
			$path .= '&uclimit=' . $uclimit;
		}

		if (isset($ucstart))
		{
			$path .= '&ucstart=' . $ucstart;
		}

		if (isset($ucend))
		{
			$path .= '&ucend=' . $ucend;
		}

		if ($uccontinue)
		{
			$path .= '&uccontinue=';
		}

		if (isset($ucdir))
		{
			$path .= '&ucdir=' . $ucdir;
		}

		if (isset($ucnamespace))
		{
			$path .= '&ucnamespace=' . $this->buildParameter($ucnamespace);
		}

		if (isset($ucprop))
		{
			$path .= '&ucprop=' . $this->buildParameter($ucprop);
		}

		if (isset($ucshow))
		{
			$path .= '&ucshow=' . $this->buildParameter($ucshow);
		}

		if (isset($uctag))
		{
			$path .= '&uctag=' . $uctag;
		}

		if (isset($uctoponly))
		{
			$path .= '&uctoponly=' . $uctoponly;
		}

		// Send the request.
		$response = $this->client->get($this->fetchUrl($path));

		return $this->validateResponse($response);
	}

	/**
     * Method to block a user.
     *
     * @return  object
     *
     * @since   12.1
     */
	public function blockUser()
	{
	}

	/**
     * Method to unblock a user.
	 *
	 * @param   string   $user    Username, IP address or IP range you want to unblock.
	 * @param   integer  $token   An unblock token.
	 * @param   string   $reason  Reason for unblock (optional).
     *
     * @return  object
     *
     * @since   12.1
     */
	public function unBlockUser($user, $token, $reason = null)
	{
		// Build the request path.
		$path = '?action=query&list=usercontribs&ucuser=' . $user;

		// Send the request.
		$response = $this->client->get($this->fetchUrl($path));

		return $this->validateResponse($response);
	}

	/**
     * Method to assign a user to a group.
     *
     * @return  object
     *
     * @since   12.1
     */
	public function assignGroup()
	{

	}

	/**
     * Method to email a user.
     *
     * @return  object
     *
     * @since   12.1
     */
	public function emailUser()
	{
	}
}
