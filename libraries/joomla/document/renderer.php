<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Document
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Abstract class for a renderer
 *
 * @package     Joomla.Platform
 * @subpackage  Document
 * @since       11.1
 */
class JDocumentRenderer
{
	/**
	 * Reference to the JDocument object that instantiated the renderer
	 *
	 * @var    JDocument
	 * @since  11.1
	 */
	protected $doc = null;

	/**
	 * Renderer mime type
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $mime = "text/html";

	/**
	 * Class constructor
	 *
	 * @param   JDocument  &$doc  A reference to the JDocument object that instantiated the renderer
	 *
	 * @since   11.1
	 */
	public function __construct(&$doc)
	{
		$this->doc = &$doc;
	}

	/**
	 * magic get method
	 *
	 * @param   $propertyName  Property name
	 *
	 * @return  mixed  the property value
	 *
	 * @since   12.1
	 * @deprecated  12.3
	 */
	public function __get($propertyName)
	{
		if ($propertyName[0] == '_' && property_exists($this, $newPropertyName = substr($propertyName, 1)))
		{
			JLog::add(get_called_class() . '::$' . $propertyName . ' is deprecated. Use ' . get_called_class() . '::$'. $newPropertyName . ' instead.', JLog::WARNING, 'deprecated');
			return $this->$newPropertyName;
		}
		else
		{
			// Trigger an error
			return $this->$propertyName;
		}
	}

	/**
	 * magic set method
	 *
	 * @param   $propertyName  Property name
	 * @param   $value         Property name
	 *
	 * @return  void
	 *
	 * @since   12.1
	 * @deprecated  12.3
	 */
	public function __set($propertyName, $value)
	{
		if ($propertyName[0] == '_' && property_exists($this, $newPropertyName = substr($propertyName, 1)))
		{
			JLog::add(get_called_class() . '::$' . $propertyName . ' is deprecated. Use ' . get_called_class() . '::$'. $newPropertyName . ' instead.', JLog::WARNING, 'deprecated');
			$this->$newPropertyName = $value;
		}
		else
		{
			$this->$propertyName = $value;
		}
	}

	/**
	 * Renders a script and returns the results as a string
	 *
	 * @param   string  $name     The name of the element to render
	 * @param   array   $params   Array of values
	 * @param   string  $content  Override the output of the renderer
	 *
	 * @return  string  The output of the script
	 *
	 * @since   11.1
	 */
	public function render($name, $params = null, $content = null)
	{
	}

	/**
	 * Return the content type of the renderer
	 *
	 * @return  string  The contentType
	 *
	 * @since   11.1
	 */
	public function getContentType()
	{
		return $this->mime;
	}
}
