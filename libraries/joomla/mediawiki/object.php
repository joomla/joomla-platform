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
abstract class JMediawikiObject {

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

}