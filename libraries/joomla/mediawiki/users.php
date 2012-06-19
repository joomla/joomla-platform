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
	 * @param   string  $lgtoken     Login token obtained in first request.
     *
     * @return  object
     *
     * @since   12.1
     */
	public function login($lgname, $lgpassword, $lgdomain = null, $lgtoken)
	{
		// Build the request path.
		$path = '?action=login&lgname=' . $lgname . '&lgpassword=' . $lgpassword;

		// Send the request.
		$response = $this->client->post($this->fetchUrl($path));

		// Convert xml string to an object.
		$xml = simplexml_load_string($response->body);

		// @TODO validate the response

		return $xml;
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
     * @param   array   $users
     *
     * @return  object
     *
     * @since   12.1
     */
    public function getUserInfo($users)
    {
        // extract the list of users
        $ususers = '';
        foreach ($users as $user) {
            $ususers .= $user . '|'; // a trailing | does not throw an error
        }

        // @TODO undo hardcoding
        $usprop = 'blockinfo|groups|implicitgroups|rights|editcount|registration|emailable|gender';

        // Build the request path.
        $path = '?action=query&list=users&ususers=' . $ususers . '&usprop=' . $usprop;

        // Send the request.
        $response = $this->client->get($this->fetchUrl($path));

        // convert xml string to an object
        $xml = simplexml_load_string($response->body);

        // validate the response

        return $response;
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

        // @TODO undo hardcoding
        $uiprop = 'blockinfo|hasmsg|groups|implicitgroups|rights|changeablegroups|options|preferencestoken|editcount|ratelimits|realname|email|acceptlang|registrationdate';

        // Build the request path.
        $path = '?action=query&&meta=userinfo&uiprop=' . $uiprop;

        // Send the request.
        $response = $this->client->get($this->fetchUrl($path));

        // convert xml string to an object
        $xml = simplexml_load_string($response->body);

        // validate the response

        return $response;
    }

    /**
     * Method to get user contributions.
     *
     * @param   string   $user
     *
     * @return  object
     *
     * @since   12.1
     */
    public function getUserContribs($user)
    {
        // Build the request path.
        $path = '?action=query&list=usercontribs&ucuser=' . $user;

        // Send the request.
        $response = $this->client->get($this->fetchUrl($path));

        // convert xml string to an object
        $xml = simplexml_load_string($response->body);

        // validate the response

        return $response;
    }

    /**
     * Method to block a user.
     *
     * @param   string   $user
     * @param   string   $token
     * @return  object
     *
     * @since   12.1
     */
    public function blockUser($user, $token)
    {
        // Build the request path.
        $path = '?action=query&list=usercontribs&ucuser=' . $user;

        // Send the request.
        $response = $this->client->get($this->fetchUrl($path));

        // convert xml string to an object
        $xml = simplexml_load_string($response->body);

        // validate the response

        return $response;
    }

    /**
     * Method to unblock a user.
     *
     * @param   string   $user
     * @param   string   $token
     *
     * @return  object
     *
     * @since   12.1
     */
    public function unBlockUser($user, $token)
    {
        // Build the request path.
        $path = '?action=query&list=usercontribs&ucuser=' . $user;

        // Send the request.
        $response = $this->client->get($this->fetchUrl($path));

        // convert xml string to an object
        $xml = simplexml_load_string($response->body);

        // validate the response

        return $response;
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