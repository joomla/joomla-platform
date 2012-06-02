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
 * MediaWiki API Pages class for the Joomla Platform.
 *
 * @package     Joomla.Platform
 * @subpackage  MediaWiki
 * @since       12.1
 */
class JMediawikiPages extends JMediawikiObject
{
    /**
     * Method to edit a page.
     *
     * @return  object
     *
     * @since   12.1
     */
    public function editPage()
    {

    }

    /**
     * Method to delete a page.
     *
     * @return  object
     *
     * @since   12.1
     */
    public function deletePage()
    {

    }

    /**
     * Method to restore certain revisions of a deleted page.
     *
     * @return  object
     *
     * @since   12.1
     */
    public function undeletePage()
    {

    }

    /**
     * Method to move a page.
     *
     * @return  object
     *
     * @since   12.1
     */
    public function movePage()
    {

    }

    /**
     * Method to undo the last edit to the page.
     *
     * @return  object
     *
     * @since   12.1
     */
    public function rollback()
    {

    }

    /**
     * Method to change the protection level of a page.
     *
     * @return  object
     *
     * @since   12.1
     */
    public function changeProtection()
    {

    }

    /**
     * Method to get basic page information.
     *
     * @param   array       $titles             Page titles to retrieve info.
     * @param   array       $inprop             Which additional properties to get.
     * @param   array       $intoken            Request a token to perform a data-modifying action on a page
     * @param   boolean     $incontinue         When more results are available, use this to continue.
     *
     * @return  object
     *
     * @since   12.1
     */
    public function getPageInfo(array $titles, array $inprop = null, array $intoken = null, $incontinue = null)
    {
        // build the request
        $path = '?action=query&prop=info';

        // append titles to the request
        $path .= '&titles=' . $this->buildParameter($titles);

        if (isset($inprop)) {
            $path .= '&inprop=' . $this->buildParameter($inprop);
        }

        if (isset($intoken)) {
            $path .= '&intoken=' . $this->buildParameter($intoken);
        }

        if ($incontinue) {
            $path .= '&incontinue=';
        }

        // Send the request.
        $response = $this->client->get($this->fetchUrl($path));

        return $this->validateResponse($response);
    }

    /**
     * Method to get various properties defined in the page content.
     *
     * @return  object
     *
     * @since   12.1
     */
    public function getPageProperties()
    {

    }

    /**
     * Method to get a list of revisions.
     *
     * @return  object
     *
     * @since   12.1
     */
    public function getRevisions()
    {

    }

    /**
     * Method to get a list of deleted revisions.
     *
     * @return  object
     *
     * @since   12.1
     */
    public function getDeletedRevisions()
    {

    }

    /**
     * Method to get all page templates from the given page.
     *
     * @param   array       $titles             Page titles to retrieve templates.
     * @param   array       $tlnamespace             Page titles to retrieve links.
     * @param   integer     $tllimit             Page titles to retrieve links.
     * @param   boolean     $tlcontinue             Page titles to retrieve links.
     * @param   string      $tltemplates             Page titles to retrieve links.
     * @param   string      $tldir             Page titles to retrieve links.
     *
     * @return  object
     *
     * @since   12.1
     */
    public function getPageTemplates(array $titles, array $tlnamespace = null, $tllimit = null, $tlcontinue = null, $tltemplates = null, $tldir = null)
    {
        // build the request
        $path = '?action=query&prop=templates';

        // append titles to the request
        $path .= '&titles=' . $this->buildParameter($titles);

        if (isset($tlnamespace)) {
            $path .= '&tlnamespace=' . $this->buildParameter($tlnamespace);
        }

        if (isset($tllimit)) {
            $path .= '&tllimit=' . $tllimit;
        }

        if ($tlcontinue) {
            $path .= '&tlcontinue=';
        }

        if (isset($tltemplates)) {
            $path .= '&tltemplates=' . $tltemplates;
        }

        if (isset($tldir)) {
            $path .= '&tldir=' . $tldir;
        }

        // Send the request.
        $response = $this->client->get($this->fetchUrl($path));

        return $this->validateResponse($response);
    }

    /**
     * Method to get all pages that link to the given page.
     *
     * @return  object
     *
     * @since   12.1
     */
    public function getBackLinks()
    {

    }

    /**
     * Method to get all pages that link to the given interwiki link.
     *
     * @return  object
     *
     * @since   12.1
     */
    public function getIWBackLinks()
    {

    }

    /**
     * Method to get all pages that link to the given language link .
     *
     * @return  object
     *
     * @since   12.1
     */
    public function getLangBackLinks()
    {

    }

    /**
     * Method to get all pages in a given category.
     *
     * @return  object
     *
     * @since   12.1
     */
    public function getPagesByCategory()
    {

    }

    /**
     * Method to get all pages  that use the given image title.
     *
     * @return  object
     *
     * @since   12.1
     */
    public function getImageUsage()
    {

    }

}