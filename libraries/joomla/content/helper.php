<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Content
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Joomla Platform Content Helper Class
 *
 * @package     Joomla.Platform
 * @subpackage  Content
 * @since       12.1
 */
class JContentHelper extends JCacheObject
{
	/**
	 * The database adapter.
	 *
	 * @var    JDatabase
	 * @since  12.1
	 */
	protected $db;

	/**
	 * The content factory.
	 *
	 * @var    JContentFactory
	 * @since  12.1
	 */
	protected $factory;

	/**
	 * Method to instantiate the object.
	 *
	 * @param   JContentFactory  $factory  An argument to provide dependency injection for the content
	 *                                     factory.
	 *
	 * @param   mixed            $db       An optional argument to provide dependency injection for the database
	 *                                     adapter.  If the argument is a JDatbase adapter that object will become
	 *                                     the database adapter, otherwise the factory's default adapter will be used.
	 *
	 * @since   12.1
	 */
	public function __construct(JContentFactory $factory, JDatabase $db = null)
	{
		$this->factory = $factory;

		// If a database adapter is given, use it.
		if ($db instanceof JDatabase)
		{
			$this->db = $db;
		}
		// Create the database adapter.
		else
		{
			$this->db = JFactory::getDbo();
		}
	}

	/**
	 * Method to get the content types.
	 *
	 * @return  array  An array of JContentType objects.
	 *
	 * @since   12.1
	 * @throws  RuntimeException
	 */
	public function getTypes()
	{
		$types = array();

		// Get the cache store id.
		$storeId = $this->getStoreId('getTypes');

		// Attempt to retrieve the types from cache first.
		$cached = $this->retrieve($storeId);

		// Check if the cached value is usable.
		if (is_array($cached))
		{
			return $cached;
		}

		// Build the query to get the content types.
		$query = $this->db->getQuery(true);
		$query->select('a.*');
		$query->from($query->qn('#__content_types') . ' AS a');

		// Get the content types.
		$this->db->setQuery($query);
		$results = $this->db->loadObjectList();

		// Reorganize the type information.
		foreach ($results as $result)
		{
			// Create a new JContentType object.
			$type = $this->factory->getType();

			// Bind the type data.
			$type->bind($result);

			// Add the type, keyed by alias.
			$types[$result->alias] = $type;
		}

		// Store the types in cache.
		return $this->store($storeId, $types);
	}

	/**
	 * Method to get a store id.
	 *
	 * @param   string  $id  An identifier string to generate the store id.
	 *
	 * @return  string  A store id.
	 *
	 * @since   12.1
	 */
	protected function getStoreId($id = '')
	{
		return md5(spl_object_hash($this) . ':' . $id);
	}
}
