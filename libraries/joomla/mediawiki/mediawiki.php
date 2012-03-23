<?php
/**
 * @package     Joomla.Platform
 * @subpackage  MediaWiki
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

class JMediawiki {
    /**
     * @var    JRegistry  Options for the MediaWiki object.
     * @since  11.3
     */
    protected $options;

    /**
     * @var    JMediawikiHttp  The HTTP client object to use in sending HTTP requests.
     * @since  11.3
     */
    protected $client;

    /**
     * @var    JMediawikiTest  MediaWiki API object for test.
     * @since  11.3
     */
    protected $test;

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
     * @return  JGithubObject  GitHub API object (gists, issues, pulls, etc).
     *
     * @since   11.3
     */
    public function __get($name)
    {
        if ($name == 'test')
        {
            if ($this->test == null)
            {
                $this->test
                    = new JMediawikiTest($this->options, $this->client);
            }
            return $this->test;
        }

    }
}