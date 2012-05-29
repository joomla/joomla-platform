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
    public function getLinks(array $titles, array $plnamespace = null, $pllimit = null, $plcontinue = null, array $pltitles = null, $pldir = null)
    {
        // build the request
        $path = '?action=query&prop=links';

        // append titles to the request
        $path .= '&titles=' . $this->buildParameter($titles);


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
     * @param   array       $titles             Page titles to retrieve links.
     *
     * @return  object
     *
     * @since   12.1
     */
    public function getLinksInfo(array $titles)
    {
        // build the request
        $path = '?action=query&generator=links&prop=info';

        // append titles to the request
        $path .= '&titles=' . $this->buildParameter($titles);

        // Send the request.
        $response = $this->client->get($this->fetchUrl($path));

        return $this->validateResponse($response);
    }

    /**
     * Method to return all interwiki links from the given page(s).
     *
     * @param   array       $titles             Page titles to retrieve links.
     * @param   boolean     $iwurl              Whether to get the full url.
     * @param   integer     $iwlimit            Number of interwiki links to return.
     * @param   boolean     $iwcontinue         When more results are available, use this to continue.
     * @param   string      $iwprefix           Prefix for the interwiki.
     * @param   string      $iwtitle            Interwiki link to search for.
     * @param   string      $iwdir              The direction in which to list.
     *
     * @return  object
     *
     * @since   12.1
     */
    public function getIWLinks(array $titles, $iwurl = null, $iwlimit = null, $iwcontinue = null, $iwprefix = null, $iwtitle = null, $iwdir = null)
    {
        // build the request
        $path = '?action=query&prop=links';

        // append titles to the request
        $path .= '&titles=' . $this->buildParameter($titles);

        if ($iwurl) {
            $path .= 'iwurl=';
        }

        if (isset($iwlimit)) {
            $path .= '&iwlimit=' . $iwlimit;
        }

        if ($iwcontinue) {
            $path .= 'iwcontinue=';
        }

        if (isset($iwprefix)) {
            $path .= '&iwprefix=' . $iwprefix;
        }

        if (isset($iwtitle)) {
            $path .= '&iwtitle=' . $iwtitle;
        }

        if (isset($iwdir)) {
            $path .= '&iwdir=' . $iwdir;
        }

        // Send the request.
        $response = $this->client->get($this->fetchUrl($path));

        return $this->validateResponse($response);
    }

    /**
     * Method to return all interlanguage links from the given page(s).
     *
     * @param   array       $titles             Page titles to retrieve links.
     * @param   integer     $lllimit            Number of langauge links to return.
     * @param   boolean     $llcontinue         When more results are available, use this to continue.
     * @param   string      $llurl              Whether to get the full URL.
     * @param   string      $lllang             Language code.
     * @param   string      $lltitle            Link to search for.
     * @param   string      $lldir              The direction in which to list.
     *
     * @return  object
     *
     * @since   12.1
     */
    public function getLangLinks(array $titles, $lllimit = null, $llcontinue = null, $llurl = null, $lllang = null, $lltitle = null, $lldir = null)
    {
        // build the request
        $path = '?action=query&prop=langlinks';

        // append titles to the request
        $path .= '&titles=' . $this->buildParameter($titles);

        if (isset($lllimit)) {
            $path .= '&lllimit=' . $lllimit;
        }

        if ($llcontinue) {
            $path .= 'llcontinue=';
        }

        if (isset($llurl)) {
            $path .= '&llurl=' . $llurl;
        }

        if (isset($lllang)) {
            $path .= '&lllang=' . $lllang;
        }

        if (isset($lltitle)) {
            $path .= '&lltitle=' . $lltitle;
        }

        if (isset($lldir)) {
            $path .= '&lldir=' . $lldir;
        }

        // Send the request.
        $response = $this->client->get($this->fetchUrl($path));

        return $this->validateResponse($response);
    }

    /**
     * Method to return all external urls from the given page(s).
     *
     * @param   array       $titles             Page titles to retrieve links.
     * @param   integer     $ellimit            Number of links to return.
     * @param   string      $eloffset           When more results are available, use this to continue.
     * @param   string      $elprotocol         Protocol of the url.
     * @param   string      $elquery            Search string without protocol.
     *
     * @return  object
     *
     * @since   12.1
     */
    public function getExtLinks(array $titles, $ellimit = null, $eloffset = null, $elprotocol = null, $elquery = null)
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