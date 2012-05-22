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
 * @property-read  JMediawikiSites          $sites          MediaWiki API object for sites.
 * @property-read  JMediawikiPages          $pages          MediaWiki API object for pages.
 * @property-read  JMediawikiUsers          $users          MediaWiki API object for users.
 * @property-read  JMediawikiLinks          $links          MediaWiki API object for links.
 * @property-read  JMediawikiCategories     $categories     MediaWiki API object for categories.
 * @property-read  JMediawikiReviews        $reviews        MediaWiki API object for reviews.
 * @property-read  JMediawikiImages         $images         MediaWiki API object for images.
 * @property-read  JMediawikiFiles          $files          MediaWiki API object for files.
 * @property-read  JMediawikiSearch         $search         MediaWiki API object for search.
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
   	 * @var    JMediawikiSites  MediaWiki API object for Site.
   	 * @since  12.1
   	 */
   	protected $sites;

    /**
   	 * @var    JMediawikiPages  MediaWiki API object for pages.
   	 * @since  12.1
   	 */
   	protected $pages;

    /**
     * @var    JMediawikiUsers  MediaWiki API object for users.
     * @since  12.1
     */
    protected $users;

    /**
     * @var    JMediawikiLinks  MediaWiki API object for links.
     * @since  12.1
     */
    protected $links;

    /**
     * @var    JMediawikiCategories  MediaWiki API object for categories.
     * @since  12.1
     */
    protected $categories;

    /**
     * @var    JMediawikiImages  MediaWiki API object for images.
     * @since  12.1
     */
    protected $images;

    /**
     * @var    JMediawikiFiles  MediaWiki API object for files.
     * @since  12.1
     */
    protected $files;

    /**
     * @var    JMediawikiSearch  MediaWiki API object for search.
     * @since  12.1
     */
    protected $search;

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

        // Setup the default User-Agent if not already set.
        $this->options->def('api.useragent', 'Joomla-Wiki-Bot');

    }

    /**
     * Magic method to lazily create API objects
     *
     * @param   string  $name  Name of property to retrieve
     *
     * @return  JMediaWikiObject  MediaWiki API object (users, reviews, etc).
     *
     * @since   12.1
     */
    public function __get($name)
   	{
   		if ($name == 'sites')
   		{
   			if ($this->sites == null)
   			{
   				$this->sites = new JMediawikiSites($this->options, $this->client);
   			}
   			return $this->sites;
   		}

   		if ($name == 'pages')
   		{
   			if ($this->pages == null)
   			{
   				$this->pages = new JMediawikiPages($this->options, $this->client);
   			}
   			return $this->pages;
   		}

   		if ($name == 'users')
   		{
   			if ($this->users == null)
   			{
   				$this->users = new JMediawikiUsers($this->options, $this->client);
   			}
   			return $this->users;
   		}

   		if ($name == 'links')
   		{
   			if ($this->links == null)
   			{
   				$this->links = new JMediawikiLinks($this->options, $this->client);
   			}
   			return $this->links;
   		}

   		if ($name == 'categories')
   		{
   			if ($this->categories == null)
   			{
   				$this->categories = new JMediawikiCategories($this->options, $this->client);
   			}
   			return $this->categories;
   		}

   		if ($name == 'reviews')
   		{
   			if ($this->reviews == null)
   			{
   				$this->reviews = new JMediawikiReviews($this->options, $this->client);
   			}
   			return $this->reviews;
   		}

        if ($name == 'images')
        {
            if ($this->images == null)
            {
                $this->images = new JMediawikiImages($this->options, $this->client);
            }
            return $this->images;
        }

        if ($name == 'files')
        {
            if ($this->files == null)
            {
                $this->files = new JMediawikiFiles($this->options, $this->client);
            }
            return $this->files;
        }

        if ($name == 'search')
        {
           if ($this->search == null)
           {
               $this->search = new JMediawikiSearch($this->options, $this->client);
           }
           return $this->search;
        }

   	}
}