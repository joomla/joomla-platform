<?php
/**
 * @package     Joomla.Legacy
 * @subpackage  Pathway
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Class to maintain a pathway.
 *
 * The user's navigated path within the application.
 *
 * @package     Joomla.Legacy
 * @subpackage  Pathway
 * @since       11.1
 * @deprecated  13.3
 */
class JPathway
{
	/**
	 * @var    array  Array to hold the pathway item objects
	 * @since  11.1
	 */
	protected $_pathway = array();

	/**
	 * @var    integer  Integer number of items in the pathway
	 * @since  11.1
	 */
	protected $_count = 0;

	/**
	 * @var    array  JPathway instances container.
	 * @since  11.3
	 */
	protected static $instances = array();

	/**
	 * Class constructor
	 *
	 * @param   array  $options  The class options.
	 *
	 * @since   11.1
	 */
	public function __construct($options = array())
	{
	}

	/**
	 * Returns a JPathway object
	 *
	 * @param   string  $client   The name of the client
	 * @param   array   $options  An associative array of options
	 *
	 * @return  JPathway  A JPathway object.
	 *
	 * @since   11.1
	 * @throws  RuntimeException
	 */
	public static function getInstance($client, $options = array())
	{
		if (empty(self::$instances[$client]))
		{
			// Create a JPathway object
			$classname = 'JPathway' . ucfirst($client);

			if (!class_exists($classname))
			{
				trigger_error('Non-autoloadable JPathway subclasses are deprecated.', E_USER_DEPRECATED);

				// Load the pathway object
				$info = JApplicationHelper::getClientInfo($client, true);

				if (is_object($info))
				{
					$path = $info->path . '/includes/pathway.php';

					if (file_exists($path))
					{
						include_once $path;
					}
				}
			}

			if (class_exists($classname))
			{
				self::$instances[$client] = new $classname($options);
			}
			else
			{
				throw new RuntimeException(JText::sprintf('JLIB_APPLICATION_ERROR_PATHWAY_LOAD', $client), 500);
			}
		}

		return self::$instances[$client];
	}

	/**
	 * Return the JPathWay items array
	 *
	 * @return  array  Array of pathway items
	 *
	 * @since   11.1
	 */
	public function getPathway()
	{
		$pw = $this->_pathway;

		// Use array_values to reset the array keys numerically
		return array_values($pw);
	}

	/**
	 * Set the JPathway items array.
	 *
	 * @param   array  $pathway  An array of pathway objects.
	 *
	 * @return  array  The previous pathway data.
	 *
	 * @since   11.1
	 */
	public function setPathway($pathway)
	{
		$oldPathway = $this->_pathway;

		// Set the new pathway.
		$this->_pathway = array_values((array) $pathway);

		return array_values($oldPathway);
	}

	/**
	 * Create and return an array of the pathway names.
	 *
	 * @return  array  Array of names of pathway items
	 *
	 * @since   11.1
	 */
	public function getPathwayNames()
	{
		$names = array();

		// Build the names array using just the names of each pathway item
		foreach ($this->_pathway as $item)
		{
			$names[] = $item->name;
		}

		// Use array_values to reset the array keys numerically
		return array_values($names);
	}

	/**
	 * Create and add an item to the pathway.
	 *
	 * @param   string  $name  The name of the item.
	 * @param   string  $link  The link to the item.
	 *
	 * @return  boolean  True on success
	 *
	 * @since   11.1
	 */
	public function addItem($name, $link = '')
	{
		$ret = false;

		if ($this->_pathway[] = $this->_makeItem($name, $link))
		{
			$ret = true;
			$this->_count++;
		}

		return $ret;
	}

	/**
	 * Set item name.
	 *
	 * @param   integer  $id    The id of the item on which to set the name.
	 * @param   string   $name  The name to set.
	 *
	 * @return  boolean  True on success
	 *
	 * @since   11.1
	 */
	public function setItemName($id, $name)
	{
		$ret = false;

		if (isset($this->_pathway[$id]))
		{
			$this->_pathway[$id]->name = $name;
			$ret = true;
		}

		return $ret;
	}

	/**
	 * Create and return a new pathway object.
	 *
	 * @param   string  $name  Name of the item
	 * @param   string  $link  Link to the item
	 *
	 * @return  JPathway  Pathway item object
	 *
	 * @since   11.1
	 */
	protected function _makeItem($name, $link)
	{
		$item = new stdClass;
		$item->name = html_entity_decode($name, ENT_COMPAT, 'UTF-8');
		$item->link = $link;

		return $item;
	}
}
