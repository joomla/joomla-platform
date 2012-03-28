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
 * HTTP client class for connecting to a MediaWiki instance.
 *
 * @package     Joomla.Platform
 * @subpackage  MediaWiki
 * @since       12.1
 */
class JMediawikiHttp extends JHttp {

    /**
     * Constructor.
     *
     * @param   JRegistry       $options    Client options object.
     * @param   JHttpTransport  $transport  The HTTP transport object.
     *
     * @since   12.1
     */
    public function __construct(JRegistry $options = null, JHttpTransport $transport = null)
    {
        // Override the JHttp contructor to use JHttpTransportStream.
        $this->options   = isset($options) ? $options : new JRegistry;
        $this->transport = isset($transport) ? $transport : new JHttpTransportStream($this->options);

    }

    public function get($url, array $headers = null)
    {
        return $this->transport->request('GET', new JUri($url), null, $headers, null, $this->options->get('api.useragent'));
    }

}
