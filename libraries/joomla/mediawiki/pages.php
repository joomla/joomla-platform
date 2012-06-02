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
     * @param   array       $titles             Page titles to retrieve properties.
     * @param   boolean     $ppcontinue         When more results are available, use this to continue.
     * @param   string      $ppprop             Page prop to look on the page for.
     *
     * @return  object
     *
     * @since   12.1
     */
    public function getPageProperties(array $titles, $ppcontinue = null, $ppprop = null)
    {
        // build the request
        $path = '?action=query&prop=pageprops';

        // append titles to the request
        $path .= '&titles=' . $this->buildParameter($titles);

        if ($ppcontinue) {
            $path .= '&ppcontinue=';
        }

        if (isset($ppprop)) {
            $path .= '&ppprop=' . $ppprop;
        }

        // Send the request.
        $response = $this->client->get($this->fetchUrl($path));

        return $this->validateResponse($response);
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
     * @param   array       $tlnamespace        Show templates in this namespace(s) only.
     * @param   integer     $tllimit            How many templates to return.
     * @param   boolean     $tlcontinue         When more results are available, use this to continue.
     * @param   string      $tltemplates        Only list these templates.
     * @param   string      $tldir              The direction in which to list.
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
     * @param   string      $bltitle                Title to search.
     * @param   integer     $blpageid               Pageid to search.
     * @param   boolean     $blcontinue             When more results are available, use this to continue.
     * @param   array       $blnamespace            The namespace to enumerate.
     * @param   string      $blfilterredirect       How to filter for redirects..
     * @param   integer     $bllimit                How many total pages to return.
     * @param   boolean     $blredirect             If linking page is a redirect, find all pages that link to that redirect as well.
     *
     * @return  object
     *
     * @since   12.1
     */
    public function getBackLinks($bltitle, $blpageid = null, $blcontinue = null, array $blnamespace, $blfilterredirect = null, $bllimit = null, $blredirect = null)
    {
        // build the request
        $path = '?action=query&list=backlinks';

        if (isset($bltitle)) {
            $path .= '&bltitle=' . $bltitle;
        }

        if (isset($blpageid)) {
            $path .= '&blpageid=' . $blpageid;
        }

        if ($blcontinue) {
            $path .= '&blcontinue=';
        }

        if (isset($blnamespace)) {
            $path .= '&blnamespace=' . $this->buildParameter($blnamespace);
        }

        if (isset($blfilterredirect)) {
            $path .= '&blfilterredirect=' . $blfilterredirect;
        }

        if (isset($bllimit)) {
            $path .= '&bllimit=' . $bllimit;
        }

        if ($blredirect) {
            $path .= '&blredirect=';
        }

        // Send the request.
        $response = $this->client->get($this->fetchUrl($path));

        return $this->validateResponse($response);
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