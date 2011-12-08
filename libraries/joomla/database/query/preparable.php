<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Query
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Joomla Database Query Preparable Interface.
 * Adds bind/unbind methods as well as a getBounded() method
 * to retrieve the stored bounded variables on demand prior to
 * query execution.
 *
 * @package     Joomla.Platform
 * @subpackage  Query
 * @since       11.3
 */
interface JDatabaseQueryPreparable
{
	/**
	 * Method to add a variable to an internal $bounded array that
	 * will later be bound to a prepared SQL statement at the time
	 * of query execution. Also removes a variable that has been
	 * bounded from the internal bounded array when the passed in value
	 * is null.
	 *
	 * @param  string|integer  $key            The key that will be used in your SQL
	 *                                         query to reference the value. Usually
	 *                                         of the form ':key', but can also be an
	 *                                         integer.
	 * @param  mixed           $value         The value that will be bound.
	 * @param  integer         $dataType       Constant corresponding to a SQL datatype.
	 * @param  integer         $length         The length of the variable. Usually required
	 *                                    for OUTPUT variables.
	 * @param  array       $driverOptions  Optional driver options to be used.
	 *
	 * @return JDatabaseQuery
	 *
	 * @since  11.4
	 */
	public function bind($key = null, $value = null, $dataType = PDO::PARAM_STR, $length = 0, $driverOptions = array());

	/**
	 * Retrieves the internal $bounded array when key is null and
	 * returns it by reference. If a key is provided then that
	 * item is returned from the $bounded array if available.
	 *
	 * @return array|stdClass
	 *
	 * @since  11.4
	 */
	public function &getBounded($key = null);
}
