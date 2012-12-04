<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Mapper
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Data source mapper class.
 *
 * This class is used to provide a layer between data objects and their datasource.
 *
 * An <em>initialise</em> method may be implemented to perform custom setup.
 *
 * @package     Joomla.Platform
 * @subpackage  Mapper
 * @since       12.3
 */
abstract class JDataMapper
{
	/**
	 * The class constructor.
	 *
	 * @since   12.3
	 */
	public function __construct()
	{
		// Do the customisable initialisation.
		$this->initialise();
	}

	/**
	 * Creates a new object or list of objects in the data store.
	 *
	 * @param   JDataDumpable  $input  An object or an array of objects for the mapper to create in the data store.
	 *
	 * @return  mixed  The JData or JDataSet object that was created.
	 *
	 * @since   12.3
	 * @throws  UnexpectedValueException if doCreate does not return an array.
	 */
	public function create(JDataDumpable $input)
	{
		$dump = $input->dump();
		$objects = $this->doCreate(is_array($dump) ? $dump : array($dump));

		if ($objects instanceof JDataSet)
		{
			if (is_array($dump))
			{
				return $objects;
			}
			else
			{
				$objects->rewind();
				return $objects->current();
			}
		}

		throw new UnexpectedValueException(sprintf('%s::update()->doUpdate() returned %s', get_class($this), gettype($input)));
	}

	/**
	 * Deletes an object or a list of objects from the data store.
	 *
	 * @param   mixed  $input  An object identifier or an array of object identifier.
	 *
	 * @return  void
	 *
	 * @since   12.3
	 * @throws  UnexpectedValueException if doDelete returned something other than an object or an array.
	 */
	public function delete($input)
	{
		if (!is_array($input))
		{
			$input = array($input);
		}

		$this->doDelete($input);
	}

	/**
	 * Finds a list of objects based on arbitrary criteria.
	 *
	 * @param   mixed    $where   The criteria by which to search the data source.
	 * @param   mixed    $sort    The sorting to apply to the search.
	 * @param   integer  $offset  The pagination offset for the result set.
	 * @param   integer  $limit   The number of results to return (zero for all).
	 *
	 * @return  JDataSet  An array of objects matching the search criteria and pagination settings.
	 *
	 * @since   12.3
	 * @throws  UnexpectedValueException if JDataMapper->doFind does not return a JDataSet.
	 */
	public function find($where = null, $sort = null, $offset = 0, $limit = 0)
	{
		// Find the appropriate results based on the critera.
		$objects = $this->doFind($where, $sort, $offset, $limit);

		if ($objects instanceof JDataSet)
		{
			// The doFind method should honour the limit, but let's check just in case.
			if ($limit > 0 && count($objects) > $limit)
			{
				$count = 1;
				foreach ($objects as $k => $v)
				{
					if ($count > $limit)
					{
						unset($objects[$k]);
					}
					$count += 1;
				}
			}

			return $objects;
		}

		throw new UnexpectedValueException(sprintf('%s->doFind cannot return a %s', __METHOD__, gettype($objects)));
	}

	/**
	 * Finds a single object based on arbitrary criteria.
	 *
	 * @param   mixed    $where  The criteria by which to search the data source.
	 * @param   mixed    $sort   The sorting to apply to the search.
	 *
	 * @return  JData  An object matching the search criteria, or null if none found.
	 *
	 * @since   12.3
	 * @throws  UnexpectedValueException if JDataMapper->doFind (via JDataMapper->find) does not return a JDataSet.
	 */
	public function findOne($where = null, $sort = null)
	{
		// Find the appropriate results based on the critera.
		$objects = $this->find($where, $sort, 0, 1);

		// Check the results (empty doesn't work on JDataSet).
		if (count($objects) == 0)
		{
			// Should we throw an exception?
			return null;
		}

		// Load the object from the first element of the array (emulates array_shift on an ArrayAccess object).
		$objects->rewind();

		return $objects->current();
	}

	/**
	 * Updates an object or a list of objects in the data store.
	 *
	 * @param   mixed  $input  An object or a list of objects to update.
	 *
	 * @return  mixed  The object or object list updated.
	 *
	 * @since   12.3
	 * @throws  UnexpectedValueException if doUpdate returned something unexpected.
	 */
	public function update(JDataDumpable $input)
	{
		$dump = $input->dump();
		$objects = $this->doUpdate(is_array($dump) ? $dump : array($dump));

		if ($objects instanceof JDataSet)
		{
			if (is_array($dump))
			{
				return $objects;
			}
			else
			{
				$objects->rewind();
				return $objects->current();
			}
		}

		throw new UnexpectedValueException(sprintf('%s::update()->doUpdate() returned %s', get_class($this), gettype($objects)));
	}

	/**
	 * Customisable method to create an object or list of objects in the data store.
	 *
	 * @param   mixed  $input  An array of dumped objects.
	 *
	 * @return  array  The array JData objects that were created, keyed on the unique identifier.
	 *
	 * @since   12.3
	 * @throws  RuntimeException if there was a problem with the data source.
	 */
	abstract protected function doCreate(array $input);

	/**
	 * Customisable method to delete a list of objects from the data store.
	 *
	 * @param   mixed  $input  An array of unique object identifiers.
	 *
	 * @return  void
	 *
	 * @since   12.3
	 * @throws  RuntimeException
	 */
	abstract protected function doDelete(array $input);

	/**
	 * Customisable method to find the primary identifiers for a list of objects from the data store based on an
	 * associative array of key/value pair criteria.
	 *
	 * @param   mixed    $where   The criteria by which to search the data source.
	 * @param   mixed    $sort    The sorting to apply to the search.
	 * @param   integer  $offset  The pagination offset for the result set.
	 * @param   integer  $limit   The number of results to return (zero for all).
	 *
	 * @return  JDataSet  The set of data that matches the criteria.
	 *
	 * @since   12.3
	 * @throws  RuntimeException
	 */
	abstract protected function doFind($where = null, $sort = null, $offset = 0, $limit = 0);

	/**
	 * Customisable method to update an object or list of objects in the data store.
	 *
	 * @param   mixed  $input  An array of dumped objects.
	 *
	 * @return  array  The array of JData objects that were updated, keyed on the unique identifier.
	 *
	 * @since   12.3
	 * @throws  RuntimeException
	 */
	abstract protected function doUpdate(array $input);

	/**
	 * Customisable initialise method for extended classes to use.
	 *
	 * This method is called last in the JDataMapper constructor.
	 *
	 * @return  void
	 *
	 * @codeCoverageIgnore
	 * @since   12.3
	 */
	protected function initialise()
	{
	}
}
