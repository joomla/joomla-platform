<?php
/**
 * @package     Joomla.Platform
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * JORM Database Query Helper Abstract class
 *
 * Default Helper Abstract class
 *
 * @package     Joomla.Platform
 * @subpackage  Database.Helper
 * @since       11.1
 * @tutorial	Joomla.Platform/jormdatabasequeryhelperabstract.cls
 * @link		http://docs.joomla.org/JORMDatabaseQueryHelperAbstract
 */
abstract class JORMDatabaseQueryHelperAbstract
{
	/**
	 * JORMDatabaseQuery object
	 *
	 * @var    JORMDatabaseQuery
	 * @since  11.1
	 */
	protected $_reference;
	
	/**
	 * Default constructor for Helpers
	 * 
	 * @param JORMDatabaseQuery $reference
	 * @since 11.1
	 */
	public function __construct(JORMDatabaseQuery $reference)
	{
		$this->_reference = $reference;
		$this->initialize();
	}
	
	/**
	 * Initialize void method
	 * 
	 * @since 11.1
	 */
	protected function initialize()
	{
		
	}
}