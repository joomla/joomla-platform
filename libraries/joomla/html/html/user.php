<?php
/**
 * @package     Joomla.Platform
 * @subpackage  HTML
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Utility class working with users
 *
 * @package     Joomla.Platform
 * @subpackage  HTML
 * @since       11.4
 */
abstract class JHtmlUser
{
	/**
	 * Displays a list of user groups.
	 *
	 * @return  array  An array containing a list of user groups.
	 *
	 * @since   11.4
	 */
	public static function groups()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('a.id AS value, a.title AS text, COUNT(DISTINCT b.id) AS level');
		$query->from($db->quoteName('#__usergroups') . ' AS a');
		$query->join('LEFT', $db->quoteName('#__usergroups') . ' AS b ON a.lft > b.lft AND a.rgt < b.rgt');
		$query->group('a.id, a.title, a.lft, b.lft, a.rgt');
		$query->order('a.lft ASC');
		$db->setQuery($query);
		$options = $db->loadObjectList();

		// Check for a database error.
		if ($db->getErrorNum())
		{
			JError::raiseNotice(500, $db->getErrorMsg());
			return null;
		}

		for ($i = 0, $n = count($options); $i < $n; $i++)
		{
			$options[$i]->text = str_repeat('- ', $options[$i]->level) . $options[$i]->text;
			$groups[] = JHtml::_('select.option', $options[$i]->value, $options[$i]->text);
		}

		return $groups;
	}

	/**
	 * Get a list of users.
	 *
	 * @return  string
	 *
	 * @since   11.4
	 */
	public static function userlist()
	{
		// Get the database object and a new query object.
		$db		= JFactory::getDBO();
		$query	= $db->getQuery(true);

		// Build the query.
		$query->select('a.id AS value, a.name AS text');
		$query->from('#__users AS a');
		$query->where('a.block = 0');
		$query->order('a.name');

		// Set the query and load the options.
		$db->setQuery($query);
		$items = $db->loadObjectList();

		// Detect errors
		if ($db->getErrorNum())
		{
			JError::raiseWarning(500, $db->getErrorMsg());
		}

		return $items;
	}
}
