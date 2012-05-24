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
class JMediawikiHttp extends JHttp
{

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
        $this->options = isset($options) ? $options : new JRegistry;
        $this->transport = isset($transport) ? $transport : new JHttpTransportStream($this->options);
        // @TODO define user agent and timeout

    }

    /**
     * Method to send the GET command to the server.
     *
     * @param   string  $url      Path to the resource.
     * @param   array   $headers  An array of name-value pairs to include in the header of the request.
     *
     * @return  JHttpResponse
     *
     * @since   12.1
     */
    public function get($url, array $headers = null)
    {
        return $this->transport->request('GET', new JUri($url), null, $headers, null, $this->options->get('api.useragent'));
    }

    /**
     * Method to send the POST command to the server.
     *
     * @param   string  $url      Path to the resource.
     * @param   mixed   $data     Either an associative array or a string to be sent with the request.
     * @param   array   $headers  An array of name-value pairs to include in the header of the request.
     *
     * @return  JHttpResponse
     *
     * @since   12.1
     */
    public function post($url, $data = null, array $headers = null)
    {
        return $this->transport->request('POST', new JUri($url), $data, $headers);
    }

}
