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
        $path = '?action=sitematrissx';

        $response = $this->client->get($this->fetchUrl($path));

        $output = JFactory::getXML($response->body, false);

        // Validate the response code.
		if ($output->error)
		{
			throw new DomainException($output->error['info'], $output->error['code']);
		}

        return $output;
    }
}