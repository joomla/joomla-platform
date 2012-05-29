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