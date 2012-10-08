<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Feed
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.utilities.xmlelement');

/**
 * Feed Parser class.
 *
 * @package     Joomla.Platform
 * @subpackage  Feed
 * @since       12.1
 */
abstract class JFeedParser
{
	/**
	 * @var    string  The feed element name for the entry elements.
	 * @since  12.1
	 */
	protected $entryElementName = 'entry';

	/**
	 * @var    array
	 * @since  12.1
	 */
	protected $namespaces = array();

	/**
	 * @var    XMLReader
	 * @since  12.1
	 */
	protected $stream;

	/**
	 * @var    string  The feed format version.
	 * @since  12.1
	 */
	protected $version;

	/**
	 * @var    DOMDocument
	 * @since  12.1
	 */
	private $_node;

	/**
	 * Constructor.
	 *
	 * @param   XMLReader  $stream  The XMLReader stream object for the feed.
	 *
	 * @since   12.1
	 */
	public function __construct(XMLReader $stream)
	{
		$this->_node   = new DOMDocument;
		$this->stream  = $stream;
	}

	/**
	 * Method to parse the feed into a JFeed object.
	 *
	 * @return  JFeed
	 *
	 * @since   12.1
	 */
	public function parse()
	{
		$feed = new JFeed;

		// Detect the feed version.
		$this->detectVersion();

		// Let's get this party started...
		do
		{
			// Expand the element for processing.
			$el = $this->expandToSimpleXml();

			// Get the list of namespaces used within this element.
			$ns = $el->getNamespaces(true);

			// Get an array of available namespace objects for the element.
			$namespaces = array();
			foreach ($ns as $prefix => $uri)
			{
				// Ignore the empty namespace prefix.
				if (empty($prefix))
				{
					continue;
				}

				// Get the necessary namespace objects for the element.
				$namespace = $this->fetchNamespace($prefix);
				if ($namespace)
				{
					$namespaces[] = $namespace;
				}
			}

			// Process the element.
			$this->processElement($feed, $el, $namespaces);

			// Skip over this element's children since it has been processed.
			$this->moveToClosingElement();
		}
		while ($this->moveToNextElement());

		return $feed;
	}

	/**
	 * Method to register a namespace handler object.
	 *
	 * @param   string                $prefix     The XML namespace prefix for which to register the namespace object.
	 * @param   JFeedParserNamespace  $namespace  The namespace object to register.
	 *
	 * @return  JFeed
	 *
	 * @since   12.1
	 */
	public function registerNamespace($prefix, JFeedParserNamespace $namespace)
	{
		$this->namespaces[$prefix] = $namespace;

		return $this;
	}

	/**
	 * Method to detect the feed version.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	abstract protected function detectVersion();

	/**
	 * Method to parse a specific feed element.
	 *
	 * @param   JFeed        $feed        The JFeed object being built from the parsed feed.
	 * @param   JXMLElement  $el          The current XML element object to handle.
	 * @param   array        $namespaces  The array of relevant namespace objects to process for the element.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	protected function processElement(JFeed $feed, JXMLElement $el, array $namespaces)
	{
		// Build the internal method name.
		$method = 'handle' . ucfirst($this->stream->localName);

		// If we are dealing with an item then it is feed entry time.
		if ($this->stream->name == $this->entryElementName)
		{
			// Create a new feed entry for the item.
			$entry = new JFeedEntry;

			// First call the internal method.
			$this->processFeedEntry($entry, $el);

			foreach ($namespaces as $namespace)
			{
				if ($namespace instanceof JFeedParserNamespace)
				{
					$namespace->processElementForFeedEntry($entry, $el);
				}
			}

			// Add the new entry to the feed.
			$feed->addEntry($entry);
		}
		// Otherwise we treat it like any other element.
		else
		{
			// First call the internal method.
			if (is_callable(array($this, $method)))
			{
				$this->$method($feed, $el);
			}

			foreach ($namespaces as $namespace)
			{
				if ($namespace instanceof JFeedParserNamespace)
				{
					$namespace->processElementForFeed($feed, $el);
				}
			}
		}
	}

	/**
	 * Method to expand the current reader node into a SimpleXML node for more detailed reading
	 * and manipulation.
	 *
	 * @return  JXMLElement
	 *
	 * @since   12.1
	 * @throws  RuntimeException
	 */
	protected function expandToSimpleXml()
	{
		// Whizbang!  And now we have a JXMLElement element from the current stream node. **MAGIC** :-)
		$el = simplexml_import_dom($this->_node->importNode($this->stream->expand(), true), 'JXMLElement');

		// Let's take care of some sanity checking.
		if (!($el instanceof JXMLElement))
		{
			// @codeCoverageIgnoreStart
			throw new RuntimeException('Unable to expand node to SimpleXML element.');
			// @codeCoverageIgnoreEnd
		}

		return $el;
	}

	/**
	 * Method to get a namespace object for a given namespace prefix.
	 *
	 * @param   string  $prefix  The XML prefix for which to fetch the namespace object.
	 *
	 * @return  mixed  JFeedParserNamespace or false if none exists.
	 *
	 * @since   12.1
	 */
	protected function fetchNamespace($prefix)
	{
		if (isset($this->namespaces[$prefix]))
		{
			return $this->namespaces[$prefix];
		}

		$className = get_class($this) . ucfirst($prefix);
		if (class_exists($className))
		{
			$this->namespaces[$prefix] = new $className;

			return $this->namespaces[$prefix];
		}

		return false;
	}

	/**
	 * Method to move the stream parser to the next XML element node.
	 *
	 * @param   string  $name  The name of the element for which to move the stream forward until is found.
	 *
	 * @return  boolean  True if the stream parser is on an XML element node.
	 *
	 * @since   12.1
	 */
	protected function moveToNextElement($name = null)
	{
		// Only keep looking until the end of the stream.
		while ($this->stream->read())
		{
			// As soon as we get to the next ELEMENT node we are done.
			if ($this->stream->nodeType == XMLReader::ELEMENT)
			{
				// If we are looking for a specific name make sure we have it.
				if (isset($name) && ($this->stream->name != $name))
				{
					continue;
				}

				return true;
			}
		}

		return false;
	}

	/**
	 * Method to move the stream parser to the closing XML node of the current element.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 * @throws  RuntimeException  If the closing tag cannot be found.
	 */
	protected function moveToClosingElement()
	{
		// If we are on a self-closing tag then there is nothing to do.
		if ($this->stream->isEmptyElement)
		{
			return;
		}

		// Get the name and depth for the current node so that we can match the closing node.
		$name  = $this->stream->name;
		$depth = $this->stream->depth;

		// Only keep looking until the end of the stream.
		while ($this->stream->read())
		{
			// If we have an END_ELEMENT node with the same name and depth as the node we started with we have a bingo. :-)
			if (($this->stream->name == $name) && ($this->stream->depth == $depth) && ($this->stream->nodeType == XMLReader::END_ELEMENT))
			{
				return;
			}
		}

		// @codeCoverageIgnoreStart
		throw new RuntimeException('Unable to find the closing XML node.');
		// @codeCoverageIgnoreEnd
	}
}
