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
 * MediaWiki API Links class for the Joomla Platform.
 *
 * @package     Joomla.Platform
 * @subpackage  MediaWiki
 * @since       12.1
 */
class JMediawikiLinks extends JMediawikiObject
{

    /**
     * Method to return all links from the given page(s).
     *
     * @return  object
     *
     * @since   12.1
     */
    public function getLinks()
    {
        // build the request
        $path = '?action=query&meta=siteinfo';

        // Send the request.
        $response = $this->client->get($this->fetchUrl($path));

        return $this->validateResponse($response);
    }

    /**
     * Method to return all interwiki links from the given page(s).
     *
     * @return  object
     *
     * @since   12.1
     */
    public function getIWLinks()
    {
        // build the request
        $path = '?action=query&meta=siteinfo';

        // Send the request.
        $response = $this->client->get($this->fetchUrl($path));

        return $this->validateResponse($response);
    }

    /**
     * Method to return all interlanguage links from the given page(s).
     *
     * @return  object
     *
     * @since   12.1
     */
    public function getLangLinks()
    {
        // build the request
        $path = '?action=query&meta=siteinfo';

        // Send the request.
        $response = $this->client->get($this->fetchUrl($path));

        return $this->validateResponse($response);
    }

    /**
     * Method to return all external urls from the given page(s).
     *
     * @return  object
     *
     * @since   12.1
     */
    public function getExtLinks()
    {
        // build the request
        $path = '?action=query&meta=siteinfo';

        // Send the request.
        $response = $this->client->get($this->fetchUrl($path));

        return $this->validateResponse($response);
    }

    /**
     * Method to enumerate all links that point to a given namespace.
     *
     * @return  object
     *
     * @since   12.1
     */
    public function enumerateLinks()
    {
        // build the request
        $path = '?action=query&meta=siteinfo';

        // Send the request.
        $response = $this->client->get($this->fetchUrl($path));

        return $this->validateResponse($response);
    }
}