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
 * MediaWiki API Images class for the Joomla Platform.
 *
 * @package     Joomla.Platform
 * @subpackage  MediaWiki
 * @since       12.1
 */
class JMediawikiImages extends JMediawikiObject
{

    /**
     * Method to get all images contained on the given page(s).
     *
     * @param   array       $titles                     Page titles to retrieve images.
     * @param   integer     $imagelimit                 How many images to return.
     * @param   boolean     $imagecontinue              When more results are available, use this to continue.
     * @param   integer     $imimages                   Only list these images.
     * @param   string      $imdir                      The direction in which to list.
     *
     * @return  object
     *
     * @since   12.1
     */
    public function getImages(array $titles, $imagelimit = null, $imagecontinue = null, $imimages = null, $imdir = null)
    {
        // build the request
        $path = '?action=query&prop=images';

        // append titles to the request
        $path .= '&titles=' . $this->buildParameter($titles);

        if (isset($imagelimit)) {
            $path .= '&imagelimit=' . $imagelimit;
        }

        if ($imagecontinue) {
            $path .= '&imagecontinue=';
        }

        if (isset($imimages)) {
            $path .= '&imimages=' . $imimages;
        }

        if (isset($imdir)) {
            $path .= '&imdir=' . $imdir;
        }

        // Send the request.
        $response = $this->client->get($this->fetchUrl($path));

        return $this->validateResponse($response);
    }

    /**
     * Method to get all images contained on the given page(s).
     *
     * @param   array       $titles             Page titles to retrieve links.
     *
     * @return  object
     *
     * @since   12.1
     */
    public function getImagesUsed(array $titles)
    {
        // build the request
        $path = '?action=query&generator=images&prop=info';

        // append titles to the request
        $path .= '&titles=' . $this->buildParameter($titles);

        // Send the request.
        $response = $this->client->get($this->fetchUrl($path));

        return $this->validateResponse($response);
    }

    /**
     * Method to get all image information and upload history.
     *
     * @param   array       $liprop                     What image information to get.
     * @param   integer     $lilimit                    How many image revisions to return.
     * @param   string      $listart                    Timestamp to start listing from.
     * @param   string      $liend                      Timestamp to stop listing at.
     * @param   integer     $liurlwidth                 URL to an image scaled to this width will be returned..
     * @param   integer     $liurlheight                URL to an image scaled to this height will be returned.
     * @param   string      $limetadataversion          Version of metadata to use.
     * @param   string      $liurlparam                 A handler specific parameter string.
     * @param   boolean     $licontinue                 When more results are available, use this to continue.
     *
     * @return  object
     *
     * @since   12.1
     */
    public function getImageInfo(array $liprop = null, $lilimit = null, $listart = null, $liend = null, $liurlwidth = null, $liurlheight = null, $limetadataversion = null, $liurlparam = null, $licontinue = null)
    {
        // build the request
        $path = '?action=query&meta=siteinfo';

        if (isset($liprop)) {
            $path .= '&liprop=' . $this->buildParameter($liprop);
        }

        if (isset($lilimit)) {
            $path .= '&lilimit=' . $lilimit;
        }

        if (isset($listart)) {
            $path .= '&listart=' . $listart;
        }

        if (isset($liend)) {
            $path .= '&liend=' . $liend;
        }

        if (isset($liurlwidth)) {
            $path .= '&liurlwidth=' . $liurlwidth;
        }

        if (isset($liurlheight)) {
            $path .= '&liurlheight=' . $liurlheight;
        }

        if (isset($limetadataversion)) {
            $path .= '&limetadataversion=' . $limetadataversion;
        }

        if (isset($liurlparam)) {
            $path .= '&liurlparam=' . $liurlparam;
        }

        if ($licontinue) {
            $path .= '&alcontinue=';
        }

        // Send the request.
        $response = $this->client->get($this->fetchUrl($path));

        return $this->validateResponse($response);
    }

    /**
     * Method to enumerate all images.
     *
     * @return  object
     *
     * @since   12.1
     */
    public function enumerateImages()
    {

    }
}