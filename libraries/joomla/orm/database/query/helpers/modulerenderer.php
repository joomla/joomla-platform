<?php
/**
 * @package     Joomla.Platform
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.orm.database.query.helper.abstract');

/**
 * Module Render Helper
 *
 * Render Modules from JORMDatabaseQuery object
 *
 * @package     Joomla.Platform
 * @subpackage  Database.Helper
 * @since       11.1
 * @tutorial	Joomla.Platform/jormdatabasequeryhelperabstract.cls
 * @link		http://docs.joomla.org/JORMDatabaseQueryHelperAbstract
 */
class JORMDatabaseQueryHelperModuleRenderer extends JORMDatabaseQueryHelperAbstract
{
	/**
	 * Render modules from JORMDatabaseQuery object;
	 * 
	 * @since 11.1
	 */
	public function render()
	{
		$modules = $this->_reference->loadObjectList();
		
		$html = '';
		
		if ( !empty($modules) )
		{
			foreach ($modules as $module)
			{
				/**
				 * Set a user property to 0
				 * 
				 * @see JModulesHelper
				 */
				$module->user = 0;
				$html .= JModuleHelper::renderModule($module);
			}
		}
		
		return $html;
	}
}