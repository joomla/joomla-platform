<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Document
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.document.document');

/**
 * DocumentXML class, provides an easy interface to parse and display XML output
 *
 * @package     Joomla.Platform
 * @subpackage  Document
 * @since       11.1
 */
class JDocumentXML extends JDocument
{
	/**
	 * Document name
	 *
	 * @var    string
	 */
	protected $_name = 'joomla';

	/**
	 * Class constructor
	 *
	 * @param   array  $options  Associative array of options
	 */
	public function __construct($options = array())
	{
		parent::__construct($options);

		//set mime type
		$this->_mime = 'application/xml';

		//set document type
		$this->_type = 'xml';
	}

	/**
	 * Render the document.
	 *
	 * @param   boolean  $cache   If true, cache the output
	 * @param   array    $params  Associative array of attributes
	 *
	 * @return  The rendered data
	 */
	public function render($cache = false, $params = array())
	{
		parent::render();
		JResponse::setHeader('Content-disposition', 'inline; filename="'.$this->getName().'.xml"', true);

		return $this->getBuffer();
	}

	/**
<<<<<<< HEAD
	 * Get the document head data
	 *
	 * @return  array  The document head data in array form
	 */
	public function getHeadData()
	{
	}

	/**
	 * Set the document head data
	 *
	 * @param   array  $data  The document head data in array form
	 *
	 * @return	void
	 */
	public function setHeadData($data)
	{
	}

	/**
=======
>>>>>>> master
	 * Returns the document name
	 *
	 * @return  string
	 */
	public function getName()
	{
		return $this->_name;
	}

	/**
	 * Sets the document name
	 *
	 * @param   string  $name  Document name
	 *
	 * @return  void
	 */
	public function setName($name = 'joomla')
	{
		$this->_name = $name;
	}
}
