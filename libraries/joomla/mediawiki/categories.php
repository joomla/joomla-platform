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
 * MediaWiki API Categories class for the Joomla Platform.
 *
 * @package     Joomla.Platform
 * @subpackage  MediaWiki
 * @since       12.1
 */
class JMediawikiCategories extends JMediawikiObject
{
    /**
     * Method to list all categories the page(s) belong to.
     *
     * @param   array       $titles             Page titles to retrieve categories.
     * @param   array       $clprop             List of additional properties to get.
     * @param   array       $clshow             Type of categories to show.
     * @param   string      $cllimit            Number of categories to return.
     * @param   string      $clcontinue         Continue when more results are available.
     * @param   array       $clcategories       Only list these categories.
     * @param   string      $cldir              Direction of listing.
     *
     * @return  object
     *
     * @since   12.1
     */
    public function getCategories(array $titles = null, array $clprop = null, array $clshow = null, $cllimit = null, $clcontinue = null, array $clcategories = null, $cldir = null)
    {
        // build the request
        $path = '?action=query&prop=categories';

        // Send the request.
        $response = $this->client->get($this->fetchUrl($path));

        return $this->validateResponse($response);
    }

    /**
     * Method to get information about all categories used.
     *
     * @return  object
     *
     * @since   12.1
     */
    public function getAllCategories()
    {

    }

    /**
     * Method to get information about the given categories.
     *
     * @return  object
     *
     * @since   12.1
     */
    public function getCategoryInfo()
    {

    }

    /**
     * Method to enumerate all categories.
     *
     * @return  object
     *
     * @since   12.1
     */
    public function enumerateCategories()
    {

    }

    /**
     * Method to list change tags.
     *
     * @param   array    $tgprop              List of properties to get.
     * @param   string   $tglimit             The maximum number of tags to limit.
     *
     * @return  object
     *
     * @since   12.1
     */
    public function getChangeTags(array $tgprop = null, $tglimit = null)
    {
        // build the request
        $path = '?action=query&list=tags';

        if (isset($tgprop)) {
            $path .= '&tgprop=' . $this->buildParameter($tgprop);
        }

        if (isset($tglimit)) {
            $path .= '&tglimit=' . $tglimit;
        }

        // @TODO add support for $tgcontinue

        // Send the request.
        $response = $this->client->get($this->fetchUrl($path));

        return $this->validateResponse($response);
    }
}