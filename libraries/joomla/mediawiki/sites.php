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
    public function getSiteInfo(array $siprop = null, $sifilteriw = '', $sishowalldb = false, $sinumberingroup = false, array $siinlanguagecode = null)
    {
        // build the request
        $path = '?action=query&meta=siteinfo';

        if (!empty($siprop)) {
            $path .= '&siprop=' . $this->buildParameter($siprop);
        }

        if (!empty($sifilteriw)) {
            $path .= '&sifilteriw=' . $sifilteriw;
        }

        if (!empty($sishowalldb)) {
            $path .= 'sishowalldb=';
        }

        if (!empty($sinumberingroup)) {
            $path .= 'sinumberingroup=';
        }

        if (!empty($siinlanguagecode)) {
            $path .= '&siinlanguagecode=' . $this->buildParameter($siinlanguagecode);
        }

        // Send the request.
        $response = $this->client->get($this->fetchUrl($path));

        // convert xml string to an object
        $xml = simplexml_load_string($response->body);

        return $xml;

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

