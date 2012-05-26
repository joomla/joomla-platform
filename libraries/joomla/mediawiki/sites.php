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
     * @param   array    $siprop            The sysinfo properties to get.
     * @param   string   $sifilteriw        Only local or only non local entries to return.
     * @param   boolean  $sishowalldb       List all database servers.
     * @param   boolean  $sinumberingroup   List the number of users in usergroups.
     * @param   array    $siinlanguagecode  Language code for localized languages.
     *
     * @return  object
     *
     * @since   12.1
     */
    public function getSiteInfo(array $siprop = null, $sifilteriw = null, $sishowalldb = false, $sinumberingroup = false, array $siinlanguagecode = null)
    {
        // build the request
        $path = '?action=query&meta=siteinfo';

        if (isset($siprop)) {
            $path .= '&siprop=' . $this->buildParameter($siprop);
        }

        if (isset($sifilteriw)) {
            $path .= '&sifilteriw=' . $sifilteriw;
        }

        if ($sishowalldb) {
            $path .= 'sishowalldb=';
        }

        if ($sinumberingroup) {
            $path .= 'sinumberingroup=';
        }

        if (isset($siinlanguagecode)) {
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
     * @param   array    $leprop              The array of properties to get.
     * @param   string   $letype              Filter log actions to only this type.
     * @param   string   $leaction            Filter log actions to only this type.
     * @param   string   $letitle             Filter entries to those related to a page.
     * @param   string   $leprefix            Filter entries that start with this prefix.
     * @param   string   $letag               Filter entries with tag.
     * @param   string   $leuser              Filter entries made by the given user.
     * @param   string   $lestart             Starting timestamp.
     * @param   string   $leend               Ending timestamp.
     * @param   string   $ledir               Direction of enumeration.
     * @param   integer  $lelimit             Event limit to return.
     *
     * @return  object
     *
     * @since   12.1
     */
    public function getEvents(array $leprop = null, $letype = null, $leaction = null, $letitle, $leprefix = null, $letag = null, $leuser = null, $lestart = null, $leend = null, $ledir = null, $lelimit = null)
    {
        // build the request
        $path = '?action=query&list=logevents';

        if (isset($leprop)) {
            $path .= '&leprop=' . $this->buildParameter($leprop);
        }

        if (isset($letype)) {
            $path .= '&letype=' . $letype;
        }

        if (isset($leaction)) {
            $path .= '&leaction=' . $leaction;
        }

        if (isset($letitle)) {
            $path .= '&letitle=' . $letitle;
        }

        if (isset($leprefix)) {
            $path .= '&leprefix=' . $leprefix;
        }

        if (isset($letag)) {
            $path .= '&letag=' . $letag;
        }

        if (isset($leuser)) {
            $path .= '&leuser=' . $leuser;
        }

        if (isset($lestart)) {
            $path .= '&lestart=' . $lestart;
        }

        if (isset($leend)) {
            $path .= '&leend=' . $leend;
        }

        if (isset($ledir)) {
            $path .= '&ledir=' . $ledir;
        }

        if (isset($lelimit)) {
            $path .= '&lelimit=' . $lelimit;
        }

        // Send the request.
        $response = $this->client->get($this->fetchUrl($path));

        // convert xml string to an object
        $xml = simplexml_load_string($response->body);

        // validate the response

        return $xml;
    }

    /**
     * Method to get recent changes on a site.
     *
     * @param   string   $rcstart             Starting timestamp.
     * @param   string   $rcend               Ending timestamp.
     * @param   string   $rcdir               Direction of enumeration.
     * @param   array    $rcnamespace         Filter changes to only this namespace(s).
     * @param   string   $rcuser              Filter changes by this user.
     * @param   string   $rcexcludeuser       Filter changes to exclude changes by this user.
     * @param   string   $rctag               Filter changes by this tag.
     * @param   array    $rcprop              Filter log actions to only this type.
     * @param   array    $rctoken             Which token to obtain for each change.
     * @param   array    $rcshow              Filter changes by this criteria.
     * @param   string   $rclimit             Changes limit to return.
     * @param   string   $rctype              Filter event by type of changes.
     * @param   string   $rctoponly           Filter changes which are latest revision.
     *
     * @return  object
     *
     * @since   12.1
     */
    public function getRecentChanges($rcstart = null, $rcend = null, $rcdir = null, array $rcnamespace = null, $rcuser = null, $rcexcludeuser = null, $rctag = null, array $rcprop = null, array $rctoken = null, array $rcshow = null, $rclimit = null, $rctype = null, $rctoponly = null)
    {
        // build the request
        $path = '?action=query&list=recentchanges';

        if (isset($rcstart)) {
            $path .= '&rcstart=' . $rcstart;
        }

        if (isset($rcend)) {
            $path .= '&rcend=' . $rcend;
        }

        if (isset($rcdir)) {
            $path .= '&rcdir=' . $rcdir;
        }

        if (isset($rcnamespace)) {
            $path .= '&rcnamespaces=' . $this->buildParameter($rcnamespace);
        }

        if (isset($rcuser)) {
            $path .= '&rcuser=' . $rcuser;
        }

        if (isset($rcexcludeuser)) {
            $path .= '&rcexcludeuser=' . $rcexcludeuser;
        }

        if (isset($rctag)) {
            $path .= '&rctag=' . $rctag;
        }

        if (isset($rcprop)) {
            $path .= '&rcprop=' . $this->buildParameter($rcprop);
        }

        if (isset($rctoken)) {
            $path .= '&rctoken=' . $this->buildParameter($rctoken);
        }

        if (isset($rcshow)) {
            $path .= '&rcshow=' . $this->buildParameter($rcshow);
        }

        if (isset($rclimit)) {
            $path .= '&rclimit=' . $rclimit;
        }

        if (isset($rctype)) {
            $path .= '&rctype=' . $rctype;
        }

        if (isset($rctoponly)) {
            $path .= '&rctoponly=' . $rctoponly;
        }

        // Send the request.
        $response = $this->client->get($this->fetchUrl($path));

        // convert xml string to an object
        $xml = simplexml_load_string($response->body);

        // validate the response

        return $xml;
    }

    /**
     * Method to get protected titles on a site.
     *
     * @return  object
     *
     * @since   12.1
     */
    public function getProtectedTitles(array $ptnamespace = null, array $ptlevel, $ptlimit = null, $ptdir = null, $ptstart = null, $ptend = null, array $ptprop = null)
    {

    }
}

