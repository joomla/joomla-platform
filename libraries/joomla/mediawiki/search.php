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
 * MediaWiki API Search class for the Joomla Platform.
 *
 * @package     Joomla.Platform
 * @subpackage  MediaWiki
 * @since       12.1
 */
class JMediawikiSearch extends JMediawikiObject
{

    /**
     * Method to perform a full text search.
     *
     * @param   string      $srsearch           Search for all page titles (or content) that has this value.
     * @param   array       $srnamespace        The namespace(s) to enumerate.
     * @param   string      $srwhat             Search inside the text or titles.
     * @param   array       $srinfo             What metadata to return.
     * @param   array       $srprop             What properties to return.
     * @param   boolean     $srredirects        Include redirect pages in the search.
     * @param   integer     $sroffest           Use this value to continue paging.
     * @param   integer     $srlimit            How many total pages to return.
     *
     * @return  object
     *
     * @since   12.1
     */
    public function search($srsearch, array $srnamespace = null, $srwhat = null, array $srinfo = null, array $srprop = null, $srredirects = null, $sroffest = null, $srlimit = null)
    {
        // build the request
        $path = '?action=query&list=search';

        if (isset($srsearch)) {
            $path .= '&srsearch=' . $srsearch;
        }

        if (isset($srnamespace)) {
            $path .= '&srnamespace=' . $this->buildParameter($srnamespace);
        }

        if (isset($srwhat)) {
            $path .= '&srwhat=' . $srwhat;
        }

        if (isset($srinfo)) {
            $path .= '&srinfo=' . $this->buildParameter($srinfo);
        }

        if (isset($srprop)) {
            $path .= '&srprop=' . $this->buildParameter($srprop);
        }

        if ($srredirects) {
            $path .= '&srredirects=';
        }

        if (isset($sroffest)) {
            $path .= '&sroffest=' . $sroffest;
        }

        if (isset($srlimit)) {
            $path .= '&srlimit=' . $srlimit;
        }

        // Send the request.
        $response = $this->client->get($this->fetchUrl($path));

        return $this->validateResponse($response);
    }

    /**
     * Method to search the wiki using opensearch protocol.
     *
     * @return  object
     *
     * @since   12.1
     */
    public function openSearch()
    {

    }
}