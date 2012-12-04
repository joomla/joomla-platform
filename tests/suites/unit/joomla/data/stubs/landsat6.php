<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Data
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * Landsat 6 was launched on 5 October 1993 but failed to make orbit.
 * This class is used to test some exception handling.
 *
 * @package     Joomla.UnitTest
 * @subpackage  Data
 * @since       12.3
 */
class Landsat6 extends JDataMapper
{
	/**
	 * Customisable method to create an object or list of objects in the data store.
	 *
	 * @param   mixed  $input  An object or list of objects.
	 *
	 * @return  mixed  The object or object list created.
	 *
	 * @since   12.3
	 */
	protected function doCreate(array $input)
	{
		return false;
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
		return false;
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
		return false;
	}

	/**
	 * Customisable method to update an object or list of objects in the data store.
	 *
	 * @param   mixed  $input  An object or list of objects.
	 *
	 * @return  mixed  The modified object or list of objects.
	 *
	 * @since   12.3
	 * @throws  RuntimeException
	 */
	protected function doUpdate(array $input)
	{
		return false;
	}
}
