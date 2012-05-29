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
     * @return  object
     *
     * @since   12.1
     */
    public function getImagesUsed()
    {

    }

    /**
     * Method to get all image information and upload history.
     *
     * @return  object
     *
     * @since   12.1
     */
    public function getImageInfo()
    {

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