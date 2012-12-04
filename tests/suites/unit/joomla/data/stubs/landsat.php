<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Data
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * Derived mapper class for testing purposes (cannot test DataMapper directly because it is abstract).
 *
 * @package     Joomla.UnitTest
 * @subpackage  Data
 * @since       12.3
 */
class Landsat extends JDataMapper
{
	/**
	 * Place holder for doDelete testing.
	 *
	 * @var    mixed
	 * @since  12.3
	 */
	public $deleted;

	/**
	 * Dummy data for the mapper to use.
	 *
	 * @var    array
	 * @since  12.3
	 */
	protected $data = array();

	/**
	 * Customisable method to create an object or list of objects in the data store.
	 *
	 * @param   mixed  $input  An object or list of objects.
	 *
	 * @return  mixed  The JData or JDataSet created.
	 *
	 * @since   12.3
	 */
	protected function doCreate(array $input)
	{
		// Create $input.
		$result = new JDataSet;

		foreach ($input as $k => $object)
		{
			$result[$k] = new JData($object);
		}

		return $result;
	}

	/**
	 * Customisable method to delete an object or list of objects from the data store.
	 *
	 * @param   mixed  $input  An object, a list of objects, an object id or a list of object id's.
	 *
	 * @return  mixed  The object or object list deleted.
	 *
	 * @since   12.3
	 */
	protected function doDelete(array $input)
	{
		$this->deleted = $input;
	}

	/**
	 * Customisable method to find the primary identifiers for a list of objects from the data store based on an
	 * associative array of key/value pair criteria.
	 *
	 * @param   mixed    $where   The criteria by which to search the data source.
	 * @param   mixed    $sort    The sorting to apply to the search.
	 * @param   integer  $offset  The pagination offset for the result set.
	 * @param   integer  $limit   The number of results to return (zero for all).
	 *
	 * @return  JDataSet  An array of objects matching the search criteria and pagination settings.
	 *
	 * @since   12.3
	 */
	protected function doFind($where = null, $sort = null, $offset = 0, $limit = 0)
	{
		if ($where == 6)
		{
			return new JDataSet;
		}
		else
		{
			$found = new JDataSet;
			$found[1] = $this->data[1];
			$found[2] = $this->data[2];
			$found[3] = $this->data[3];

			return $found;
		}
	}

	/**
	 * Customisable method to update an object or list of objects in the data store.
	 *
	 * @param   mixed  $input  An object or list of objects.
	 *
	 * @return  mixed  The modified JData object or JDataSet.
	 *
	 * @since   12.3
	 */
	protected function doUpdate(array $input)
	{
		// Update $input.
		$result = new JDataSet;

		foreach ($input as $k => $object)
		{
			$object->updated = true;
			$result[$k] = new JData($object);
		}

		return $result;
	}

	/**
	 * Method to inialise the object.
	 *
	 * @return  void
	 *
	 * @since   12.3
	 */
	protected function initialise()
	{
		$this->data = array(
			'1' => new JData(array('id' => 1, 'name' => 'Landsat 1', 'launch' => '1972-07-23')),
			'2' => new JData(array('id' => 2, 'name' => 'Landsat 2', 'launch' => '1975-01-22')),
			'3' => new JData(array('id' => 3, 'name' => 'Landsat 3', 'launch' => '1978-03-05')),
			'4' => new JData(array('id' => 4, 'name' => 'Landsat 4', 'launch' => '1982-07-16')),
			'5' => new JData(array('id' => 5, 'name' => 'Landsat 5', 'launch' => '1984-03-01')),
		);
	}
}
