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
 * Joomla Platform Content Type Class
 *
 * @package     Joomla.Platform
 * @subpackage  Content
 * @since       12.1
 */
class JContentType extends JDatabaseObject implements JAuthorisationAuthorisable
{
	/**
	 * The application object.
	 *
	 * @var     JWeb
	 * @since   12.1
	 */
	protected $app;

	/**
	 * The authoriser object.
	 *
	 * @var     JAuthorisationAuthoriser
	 * @since   12.1
	 */
	protected $authoriser;

	/**
	 * The user object.
	 *
	 * @var    JUser
	 * @since  12.1
	 */
	protected $user;

	/**
	 * The object tables.
	 *
	 * @var    array
	 * @since  12.1
	 */
	protected $tables = array(
		'primary' => '#__content_types'
	);

	/**
	 * The table keys.
	 *
	 * @var    array
	 * @since  12.1
	 */
	protected $keys = array(
		'primary' => array('primary' => 'type_id')
	);

	/**
	 * Method to instantiate a content type object.
	 *
	 * @param   mixed  $db    An optional argument to provide dependency injection for the database
	 *                        adapter.  If the argument is a JDatbase adapter that object will become
	 *                        the database adapter, otherwise the default adapter will be used.
	 * @param   mixed  $user  An optional argument to provide dependency injection for the user. If the
	 *                        argument is a JUser instance that object will become the user, otherwise the
	 *                        default user will be used.
	 *
	 * @since   12.1
	 * @throws  RuntimeException
	 */
	public function __construct(JDatabase $db = null, JUser $user = null)
	{
		parent::__construct($db);

		// If a user object is given, use it.
		if ($user instanceof JUser)
		{
			$this->user = $user;
		}
		// Create the user object.
		else
		{
			$this->user = JFactory::getUser();
		}

		// Get an authoriser from the factory.
		$this->authoriser = JAuthorisationFactory::getInstance()->getAuthoriser();
	}

	/**
	 * Method to authorise a user action.
	 *
	 * @param   string  $action  The action to authorise.
	 * @param   mixed   $user    The (optional) user to authorise.
	 *
	 * @return  boolean  True if authorised, false otherwise.
	 *
	 * @since   12.1
	 */
	public function authorise($action, JAuthorisationRequestor $user = null)
	{
		return $this->authoriser
			->setRules($this->getRules())
			->isAllowed($action, $user ? $user : $this->user);
	}

	/**
	 * Method to check whether the user can create a content object of this type.
	 *
	 * @return  boolean  True if an object can be created, false otherwise.
	 *
	 * @since   12.1
	 * @throws  LogicException
	 */
	public function canCreate()
	{
		// Assert the object is loaded.
		$this->assertIsLoaded();

		// Check if the user is authorised.
		return $this->authorise('create');
	}

	/**
	 * Method to get the ACL rules for the object.
	 *
	 * @return  array  An array of ACL rules.
	 *
	 * @since   12.1
	 */
	protected function getRules()
	{
		// See notes for JDatabaseObject::__get() on why we use getProperty().
		return json_decode($this->getProperty('rules', false), true);
	}

	/**
	 * Method to set the ACL rules for an object.
	 *
	 * @param   mixed  $rules  An array or string of ACL rules.
	 *
	 * @return  JContentType  The content type object.
	 *
	 * @since   12.1
	 */
	protected function setRules($rules)
	{
		// Convert the value to a string.
		$rules = is_string($rules) ? $rules : json_encode($rules);

		// Set the value.
		$this->setProperty('rules', $rules, false);

		return $this;
	}

	/**
	 * Method to validate that the content type table exists.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 * @throws  RuntimeException
	 */
	protected function validateTableExists()
	{
		// Check if a table is defined.
		if (!empty($this->table))
		{
			// Replace the table prefix.
			$table = $this->db->replacePrefix($this->table);

			// Check if the table exists.
			$this->db->setQuery('SHOW TABLES LIKE ' . $this->db->quote($table));
			$result = $this->db->loadResult();

			// Check the result.
			if (empty($result))
			{
				throw new RuntimeException(JText::sprintf('JCONTENT_TYPE_TABLE_NOT_DEFINED', $this->table));
			}
		}
	}
}
