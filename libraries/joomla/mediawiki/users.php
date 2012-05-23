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
     * Method to get site information.
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
}