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
     * @param   string   $username
     * @param   string   $password
     * @param   string   $token
     *
     * @return  object
     *
     * @since   12.1
     */
    public function login($username, $password, $token = '')
    {
        // Build the request path.
        $path = '?action=login&lgname=' . $username . '&lgpassword=' . $password;

        // Send the request.
        $response = $this->client->post($this->fetchUrl($path));

        // convert xml string to an object
        $xml = simplexml_load_string($response->body);

        // validate the response

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
     * @return  object
     *
     * @since   12.1
     */
    public function getUserInfo($users)
    {
        // extract the list of users
        $ususers = '';
        foreach ($users as $user) {
            $ususers .= $user . '|'; // a trailing | does not trhow an error
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
        $uiprop = 'blockinfo|hasmsg|groups|implicitgroups|rights|changeablegroups|options|preferencetokens|editcount|ratelimits|realname|email|acceptlang|registrationdate|';

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
     * Method to get current user contributions.
     *
     * @return  object
     *
     * @since   12.1
     */
    public function getCurrentUserContribs()
    {
        // Build the request path.
        $path = '?action=query&list=users';

        // Send the request.
        $response = $this->client->get($this->fetchUrl($path));
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
     * @return  object
     *
     * @since   12.1
     */
    public function unBlockUser()
    {

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