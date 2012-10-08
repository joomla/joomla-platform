<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Feed
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Feed factory class.
 *
 * @package     Joomla.Platform
 * @subpackage  Feed
 * @since       12.1
 */
class JFeedFactory
{
	/**
	 * @var    JHttp  The HTTP client object for requesting feeds as necessary.
	 * @since  12.1
	 */
	protected $http;

	/**
	 * @var    array  The list of registered parser classes for feeds.
	 * @since  12.1
	 */
	protected $parsers = array('rss' => 'JFeedParserRss', 'feed' => 'JFeedParserAtom');

	/**
	 * Constructor.
	 *
	 * @param   JHttp  $http  The HTTP client object.
	 *
	 * @since   12.1
	 */
	public function __construct(JHttp $http = null)
	{
		$this->http   = isset($http) ? $http : new JHttp;
	}

	/**
	 * Method to load a URI into the feed reader for parsing.
	 *
	 * @param   string  $uri  The URI of the feed to load.
	 *
	 * @return  JFeedReader
	 *
	 * @since   12.1
	 * @throws  InvalidArgumentException
	 * @throws  RuntimeException
	 */
	public function getFeed($uri)
	{
		// Create the XMLReader object.
		$reader = new XMLReader;

		// Open the URI within the stream reader.
		if (!$reader->open($uri, null, LIBXML_NOERROR | LIBXML_ERR_NONE | LIBXML_NOWARNING))
		{
			// @codeCoverageIgnoreStart
			throw new RuntimeException('Unable to open the feed.');
			// @codeCoverageIgnoreEnd
		}

		try
		{
			// Skip ahead to the root node.
			while ($reader->read() && ($reader->nodeType !== XMLReader::ELEMENT));
		}
		catch (Exception $e)
		{
			throw new RuntimeException('Error reading feed.');
		}

		// Setup the appopriate feed parser for the feed.
		$parser = $this->_fetchFeedParser($reader->name, $reader);

		return $parser->parse();
	}

	/**
	 * Method to register a JFeedParser class for a given root tag name.
	 *
	 * @param   string   $tagName    The root tag name for which to register the parser class.
	 * @param   string   $className  The JFeedParser class name to register for a root tag name.
	 * @param   boolean  $overwrite  True to overwrite the parser class if one is already registered.
	 *
	 * @return  JFeedFactory
	 *
	 * @since   12.1
	 * @throws  InvalidArgumentException
	 */
	public function registerParser($tagName, $className, $overwrite = false)
	{
		// Verify that the class exists.
		if (!class_exists($className))
		{
			throw new InvalidArgumentException('The feed parser class ' . $className . ' does not exist.');
		}

		// Validate that the tag name is valid.
		if (!preg_match('/\A(?!XML)[a-z][\w0-9-]*/i', $tagName))
		{
			throw new InvalidArgumentException('The tag name ' . $tagName . ' is not valid.');
		}

		// Register the given parser class for the tag name if nothing registered or the overwrite flag set.
		if (empty($this->parsers[$tagName]) || (bool) $overwrite)
		{
			$this->parsers[(string) $tagName] = (string) $className;
		}

		return $this;
	}

	/**
	 * Method to return a new JFeedParser object based on the registered parsers and a given type.
	 *
	 * @param   string     $type    The name of parser to return.
	 * @param   XMLReader  $reader  The XMLReader instance for the feed.
	 *
	 * @return  JFeedParser
	 *
	 * @since   12.1
	 * @throws  LogicException
	 */
	private function _fetchFeedParser($type, XMLReader $reader)
	{
		// Look for a registered parser for the feed type.
		if (empty($this->parsers[$type]))
		{
			throw new LogicException('No registered feed parser for type ' . $type . '.');
		}

		return new $this->parsers[$type]($reader);
	}
}
