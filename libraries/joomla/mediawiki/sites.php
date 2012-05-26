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
 * MediaWiki API Sites class for the Joomla Platform.
 *
 * @package     Joomla.Platform
 * @subpackage  MediaWiki
 * @since       12.1
 */
class JMediawikiSites extends JMediawikiObject
{
    /**
     * Method to get site information.
     *
     * @return  object
     *
     * @since   12.1
     */
    public function getSiteInfo()
    {
        // build the request
        $path = '?action=query&meta=siteinfo';

        // Send the request.
        $response = $this->client->get($this->fetchUrl($path));

        // convert xml string to an object
        $xml = simplexml_load_string($response->body);

        return $xml->query;
    }

    /**
     * Method to get events from logs.
     *
     * @return  object
     *
     * @since   12.1
     */
    public function getEvents()
    {
        // @TODO support parameters

        // build the request
        $path = '?action=query&list=logevents';

        // Send the request.
        $response = $this->client->get($this->fetchUrl($path));

        // convert xml string to an object
        $xml = simplexml_load_string($response->body);

        // validate the response

        return $response;
    }

    /**
     * Method to edit a page.
     *
     * @return  object
     *
     * @since   12.1
     */
    public function getRecentChanges()
    {

    }

    /**
     * Method to edit a page.
     *
     * @return  object
     *
     * @since   12.1
     */
    public function getProtectedTitles()
    {

    }
}

