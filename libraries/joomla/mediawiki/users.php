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
    public function login($username, $password, $token)
    {
        // Build the request path.
        $path = '?action=query&list=users';
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
        // Build the request path.
        $path = '?action=query&list=users';

        // Send the request.
        $response = $this->client->get($this->fetchUrl($path));
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
        // Build the request path.
        $path = '?action=query&list=users';

        // Send the request.
        $response = $this->client->get($this->fetchUrl($path));
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