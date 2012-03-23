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
     * @var    JGithubTest  MediaWiki API object for gists.
     * @since  11.3
     */
    protected $test;

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