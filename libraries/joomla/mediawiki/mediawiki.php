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
 * Joomla Platform class for interacting with a GitHub server instance.
 *
 * @package     Joomla.Platform
 * @subpackage  MediaWiki
 * @since       12.1
 */
class JMediawiki {
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
     * @param   JRegistry    $options  MediaWiki options object.
     * @param   JMediawikiHttp  $client   The HTTP client object.
     *
     * @since   12.1
     */
    public function __construct(JRegistry $options = null, JMediawikiHttp $client = null)
    {
        $this->options = isset($options) ? $options : new JRegistry;
        $this->client  = isset($client) ? $client : new JMediawikiHttp($this->options);

        //@TODO define mediawiki API URL here
    }

    /**
     * Magic method to lazily create API objects
     *
     * @param   string  $name  Name of property to retrieve
     *
     * @return  JGithubObject  MediaWiki API object.
     *
     * @since   12.1
     */
    public function __get($name)
    {

    }
}