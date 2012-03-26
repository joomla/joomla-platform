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
 * MediaWiki API Extensions class for the Joomla Platform.
 *
 * @package     Joomla.Platform
 * @subpackage  MediaWiki
 * @since       12.1
 */
class JMediawikiExtensions extends JMediawikiObject
{
    public function getSiteMatrix(array $params = null)
    {
        // build the request parameters
        $params = 'titles='.$titles;

        $response = $this->client->get($this->fetchUrl($params));

        // @TODO need to check this
        return json_decode($response->body);
    }
}