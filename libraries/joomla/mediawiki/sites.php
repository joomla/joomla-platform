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
 * MediaWiki API Site class for the Joomla Platform.
 *
 * @package     Joomla.Platform
 * @subpackage  MediaWiki
 * @since       12.1
 */
class JMediawikiSites extends JMediawikiObject
{
    public function getSiteInfo() {
        // build the request
        $path = '?action=query&meta=siteinfo';

        // Send the request.
		$response = $this->client->get($this->fetchUrl($path));

        // convert xml string to an object
        $xml = simplexml_load_string($response->body);

        // Validate the response code.
        if($xml->error)
        {
            throw new DomainException($xml->error['info']);
        }

        if($xml->warnings)
        {
            throw new DomainException($xml->warnings->query);
        }

        return $xml->query;
    }
}