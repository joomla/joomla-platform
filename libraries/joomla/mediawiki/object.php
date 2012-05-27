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
 * MediaWiki API object class for the Joomla Platform.
 *
 * @package     Joomla.Platform
 * @subpackage  GitHub
 * @since       12.1
 */
abstract class JMediawikiObject
{

    /**
     * @var    JRegistry  Options for the MediaWiki object.
     * @since  12.1
     */
    protected $options;

    /**
     * @var    JMediawikiHttp  The HTTP client object to use in sending HTTP requests.
     * @since  12.1
     */
    protected $client;

    /**
     * Constructor.
     *
     * @param   JRegistry    $options  GitHub options object.
     * @param   JMediawikiHttp  $client   The HTTP client object.
     *
     * @since   11.3
     */
    public function __construct(JRegistry $options = null, JMediawikiHttp $client = null)
    {
        $this->options = isset($options) ? $options : new JRegistry;
        $this->client = isset($client) ? $client : new JMediawikiHttp($this->options);
    }

    /**
     * Method to build and return a full request URL for the request.
     *
     * @param   string   $path   URL to inflect
     *
     * @return  string   The request URL.
     *
     * @since   12.1
     */
    protected function fetchUrl($path)
    {
        // append the path with output format
        $path .= '&format=xml';

        $uri = new JUri($this->options->get('api.url') . '/api.php' . $path);

        if ($this->options->get('api.username', false)) {
            $uri->setUser($this->options->get('api.username'));
        }

        if ($this->options->get('api.password', false)) {
            $uri->setPass($this->options->get('api.password'));
        }

        return (string)$uri;
    }

    /**
     * Method to build request parameters from a string array.
     *
     * @param   array    $params   string array that contains the parameters
     *
     * @return  string   request parameter
     *
     * @since   12.1
     */
    public function buildParameter(array $params)
    {
        $path = '';
        foreach ($params as $param) {
            $path .= $param;
            if (next($params) == true) {
                $path .= '|';
            }
        }
        return $path;
    }

    /**
     * Method to validate response for errors
     *
     * @param   JHttpresponse   $response   reponse from the mediawiki server
     *
     * @return  Object
     *
     * @since   12.1
     */
    public function validateResponse($response)
    {
        $xml = simplexml_load_string($response->body);

        if (isset($xml->warnings)) {
            throw new DomainException($xml->warnings->siteinfo);
        }

        if (isset($xml->error)) {
            throw new DomainException($xml->error['info'], $xml->error['code']);
        }

        return $xml;
    }

}