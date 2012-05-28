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
     * @param   array       $titles             Page titles to retrieve links.
     * @param   array       $plnamespace        Namespaces to get links.
     * @param   string      $pllimit            Number of links to return.
     * @param   string      $plcontinue         Continue when more results are available.
     * @param   array       $pltitles           List links to these titles.
     * @param   string      $pldir              Direction of listing.
     *
     * @return  object
     *
     * @since   12.1
     */
    public function getLinks(array $titles = null, array $plnamespace = null, $pllimit = null, $plcontinue = null, array $pltitles = null, $pldir = null)
    {
        // build the request
        $path = '?action=query&prop=links';

        if (isset($titles)) {
            $path .= '&titles=' . $this->buildParameter($titles);
        }

        if (isset($plnamespace)) {
            $path .= '&plnamespace=' . $this->buildParameter($plnamespace);
        }

        if (isset($pllimit)) {
            $path .= '&pllimit=' . $pllimit;
        }

        if (isset($plcontinue)) {
            $path .= '&plcontinue=' . $plcontinue;
        }

        if (isset($pltitles)) {
            $path .= '&pltitles=' . $this->buildParameter($pltitles);
        }

        if (isset($pldir)) {
            $path .= '&pldir=' . $pldir;
        }

        // Send the request.
        $response = $this->client->get($this->fetchUrl($path));

        return $this->validateResponse($response);
    }

    /**
     * Method to return info about the link pages.
     *
     * @return  object
     *
     * @since   12.1
     */
    public function getLinksInfo()
    {
        // build the request
        $path = '?action=query&generator=links&prop=info';

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