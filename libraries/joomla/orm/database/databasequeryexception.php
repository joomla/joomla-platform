<?php
/**
 * @package     Joomla.Platform
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * JORM Database Query Exception class
 *
 * Throws Exception
 *
 * @package     Joomla.Platform
 * @subpackage  Database
 * @since       11.1
 * @tutorial	Joomla.Platform/jormdatabasequeryexception.cls
 * @link		http://docs.joomla.org/JORMDatabaseQueryException
 */
abstract class JORMDatabaseQueryException
{
	/**
	 * Throws when not support object class
	 * 
	 * @param Object $object
	 * @throws Exception
	 * @since 11.1
	 */
	static function checkObjectSubclass($object)
	{
		$reflection = new ReflectionClass(get_class($object));
		if(!$reflection->isSubclassOf('JORMDatabaseQuery') && !($object instanceof JORMDatabaseQuery))
			throw new Exception(JText::sprintf('JORMLIB_ERROR_CLASS_NOT_SUPORTED',get_class($object)),500);
	}
	
	/**
	 * Throwns when call to undefined method on reference
	 * 
	 * @param string $method
	 * @param Object $reference
	 * @throws Exception
	 * @since 11.1
	 */
	static function callMethodNotExists($method,$reference)
	{
		throw new Exception(JText::sprintf('JORMLIB_ERROR_UNDEFINED_METHOD',$method,get_class($reference)),500);
	}
}