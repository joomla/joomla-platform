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
     * @return  object
     *
     * @since   12.1
     */
    public function getCategories()
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

        // Send the request.
        $response = $this->client->get($this->fetchUrl($path));

        return $this->validateResponse($response);
    }
}