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
 * Joomla Platform Content Class
 *
 * @package     Joomla.Platform
 * @subpackage  Content
 * @since       12.1
 *
 * @property    integer  $content_id
 * @property    integer  $type_id
 * @property    string   $title
 * @property    string   $alias
 * @property    string   $body
 * @property    integer  $access
 * @property    integer  $state
 * @property    integer  $temporary
 * @property    integer  $featured
 * @property    string   $created_date
 * @property    integer  $created_user_id
 * @property    string   $modified_date
 * @property    integer  $modified_user_id
 * @property    string   $checked_out_session
 * @property    integer  $checked_out_user_id
 * @property    string   $publish_start_date
 * @property    string   $publish_end_date
 * @property    integer  $likes
 * @property    integer  $revision
 * @property    object   $config
 * @property    string   $media
 * @property    string   $rules
 */
class JContent extends JDatabaseObject implements JAuthorisationAuthorisable
{
	/**
	 * The application object.
	 *
	 * @var    JWeb
	 * @since  12.1
	 */
	protected $app;

	/**
	 * The authoriser object.
	 *
	 * @var    JAuthorisationAuthoriser
	 * @since  12.1
	 */
	protected $authoriser;

	/**
	 * The content factory.
	 *
	 * @var    JContentFactory
	 * @since  12.1
	 */
	protected $factory;

	/**
	 * The user object.
	 *
	 * @var    JUser
	 * @since  12.1
	 */
	protected $user;

	/**
	 * The class prefix.
	 *
	 * @var    string
	 * @since  12.1
	 */
	protected $prefix;

	/**
	 * The content type data.
	 *
	 * @var    JContentType
	 * @since  12.1
	 */
	protected $type;

	/**
	 * The object tables.
	 *
	 * @var    array
	 * @since  12.1
	 */
	protected $tables = array(
		'primary' => '#__content',
		'hits' => '#__content_hits'
	);

	/**
	 * The table keys.
	 *
	 * @var    array
	 * @since  12.1
	 */
	protected $keys = array(
		'primary'	=> array('primary' => 'content_id'),
		'hits'		=> array('primary' => 'content_id')
	);

	/**
	 * Method to instantiate the object.
	 *
	 * @param   string  $prefix   The custom prefix.
	 * @param   object  $type     The JContentType object for the content object.
	 * @param   mixed   $factory  An optional argument to provide dependency injection for the content
	 *                            factory.  If the argument is a JContentFactory that object will become
	 *                            the content factory, otherwise the default factory will be used.
	 * @param   mixed   $db       An optional argument to provide dependency injection for the database
	 *                            adapter.  If the argument is a JDatbase adapter that object will become
	 *                            the database adapter, otherwise the default adapter will be used.
	 * @param   mixed   $app      An optional argument to provide dependency injection for the application.
	 *                            If the argument is a JWeb instance that object will become the application,
	 *                            otherwise the default application will be used.
	 * @param   mixed   $user     An optional argument to provide dependency injection for the user. If the
	 *                            argument is a JUser instance that object will become the user, otherwise the
	 *                            default user will be used.
	 *
	 * @since   12.1
	 * @throws  RuntimeException
	 */
	public function __construct($prefix, $type, JContentFactory $factory = null, JDatabase $db = null, JWeb $app = null, JUser $user = null)
	{
		// Check if the type table is defined.
		if (!empty($type->table))
		{
			// Add the type table and key.
			$this->tables['secondary'] = $type->table;
			$this->keys['secondary'] = array('primary' => 'content_id');
		}

		parent::__construct($db);

		// Set the content properties.
		$this->type		= $type;
		$this->prefix	= $prefix;

		// If an application object is given, use it.
		if ($app instanceof JWeb)
		{
			$this->app = $app;
		}
		// Create the application object.
		else
		{
			$this->app = JFactory::getApplication();
		}

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

		// If a factory object is given, use it.
		if ($factory instanceof JContentFactory)
		{
			$this->factory = $factory;
		}
		// Create the application object.
		else
		{
			$this->factory = JContentFactory::getInstance($this->prefix, $this->db, $this->app, $this->user);
		}

		// Get an authoriser from the factory.
		$this->authoriser = JAuthorisationFactory::getInstance()->getAuthoriser();

		// Set the cache group.
		$this->cacheGroup = $this->type->alias;

		// Register the dump properties.
		$this->dump[] = 'route';
		$this->dump[] = 'config';
	}

	/**
	 * Method to authorise a user action.
	 *
	 * @param   string  $action  The action to authorise.
	 * @param   mixed   $user    The (optional) user to authorise.
	 *
	 * @return  mixed  True if allowed, false for an explicit deny, null for an implicit deny.
	 *
	 * @since   12.1
	 * @throws  LogicException
	 */
	public function authorise($action, JAuthorisationRequestor $user = null)
	{
		// Assert the object is loaded.
		$this->assertIsLoaded();

		return $this->authoriser
			->setRules($this->getRules())
			->isAllowed($action, $user ? $user : $this->user);
	}

	/**
	 * Method to check an item in for editing so that other users can have an opportunity
	 * to make changes.  While checked out no other users are allowed to make changes to
	 * the item except the user who has checked it out.  Checking the item in signifies that
	 * the current user has finished editing the item.
	 *
	 * @return  JContent  The content object.
	 *
	 * @since   12.1
	 * @throws  LogicException
	 * @throws  RuntimeException
	 */
	public function checkin()
	{
		// Assert the object is loaded.
		$this->assertIsLoaded();

		// Check if the object is still temporary.
		if ($this->isTemporary())
		{
			// Checkout and delete the object.
			$this->checkout()->delete();

			return $this;
		}

		// Build the query checkin the item.
		$query = $this->db->getQuery(true);
		$query->update($this->db->qn('#__content'));
		$query->set('checked_out_user_id = NULL');
		$query->set('checked_out_session = ""');
		$query->where('content_id = ' . (int) $this->content_id);

		// Checkin the item.
		$this->db->setQuery($query);
		$this->db->query();

		// Reset the checked out state.
		$this->checked_out_user_id = null;
		$this->checked_out_session = '';

		// Cleanup orphaned content objects.
		$this->cleanup();

		return $this;
	}

	/**
	 * Method to check an item out for editing for the current user so that other users cannot
	 * get into a race condition with overwriting each other's changes to the item.  The item
	 * will be marked with the current user and their current session id so that the checked
	 * out state will only last for the length of the current session.
	 *
	 * @return  JContent  The content object.
	 *
	 * @since   12.1
	 * @throws  LogicException
	 * @throws  RuntimeException
	 */
	public function checkout()
	{
		// Assert the object is loaded.
		$this->assertIsLoaded();

		// Get the user id (or null) and session id.
		$userId = !$this->user->get('guest') ? $this->user->get('id') : null;
		$sessionId = $this->app->getSession()->getId();

		// Check if the item is checked out by this session.
		if ($this->checked_out_session == $sessionId)
		{
			return $this;
		}
		// Check if the item is checked out by this (non-guest) user from another session.
		elseif ($userId && $this->checked_out_user_id == $userId)
		{
			return $this;
		}

		try
		{
			// Start a transaction.
			$this->db->transactionStart();

			// Check if the item is checked out at all.
			if (!empty($this->checked_out_session))
			{
				// Build the query to check if the item is editable.
				$query = $this->db->getQuery(true);
				$query->select('a.content_id');
				$query->from($this->db->qn('#__content') . ' AS a');
				$query->join('INNER', '#__session AS s ON s.session_id = a.checked_out_session');
				$query->where('a.content_id = ' . (int) $this->content_id);

				// Get the checked out state.
				$this->db->setQuery($query);
				$checkedOut = (int) $this->db->loadResult();

				// Check if the checked out session is still valid.
				if ($checkedOut)
				{
					// Rollback the transaction.
					$this->db->transactionRollback();

					// Throw an exception.
					throw new RuntimeException(JText::_('JCONTENT_CHECKED_OUT'));
				}
			}

			// Build the query to checkout the item.
			$query = $this->db->getQuery(true);
			$query->update($this->db->qn('#__content'));
			$query->set('checked_out_user_id = ' . (!empty($userId) ? (int) $userId : 'NULL'));
			$query->set('checked_out_session = ' . $this->db->quote($sessionId));
			$query->where('content_id = ' . (int) $this->content_id);

			// Checkin the item.
			$this->db->setQuery($query);
			$this->db->query();

			// Commit the transaction.
			$this->db->transactionCommit();
		}
		catch (RuntimeException $error)
		{
			// Rollback the transaction.
			$this->db->transactionRollback();

			// Rethrow the error.
			throw $error;
		}

		// Set the checked out state.
		$this->checked_out_user_id = $userId;
		$this->checked_out_session = $sessionId;

		// Cleanup orphaned content objects.
		$this->cleanup();

		return $this;
	}

	/**
	 * Method to cleanup orphaned content objects.
	 *
	 * @return  JContent  The content object.
	 *
	 * @since	12.1
	 */
	public function cleanup()
	{
		try
		{
			// Start a transaction.
			$this->db->transactionStart();

			// Build a query to get the content ids that are checked out to active sessions.
			$sub = $this->db->getQuery(true)->select('c.content_id');
			$sub->from('#__content AS c');
			$sub->innerJoin('#__session AS s ON c.checked_out_session = s.session_id');
			$sub->where('c.temporary = 1');

			// Build a query to get the content objects that are not checked out to active sessions.
			$query = $this->db->getQuery(true);
			$query->select('a.content_id, b.alias AS type');
			$query->from('#__content AS a');
			$query->innerJoin('#__content_types AS b ON b.type_id = a.type_id');
			$query->where('a.temporary = 1');
			$query->where('a.content_id NOT IN(' . $sub . ')');

			// Get the orphaned content objects.
			$this->db->setQuery($query);
			$orphans = $this->db->loadObjectList();

			// Iterate through the orphaned objects.
			foreach ($orphans as $orphan)
			{
				// Get a new content object to delete.
				$content = $this->factory->getContent($orphan->type);

				// Load the content object.
				$content->load($orphan->content_id);

				// Delete the object.
				$content->delete();
			}

			// Commit the transaction.
			$this->db->transactionCommit();
		}
		catch (RuntimeException $error)
		{
			// Rollback the transaction.
			$this->db->transactionRollback();

			// Rethrow the error.
			throw $error;
		}

		return $this;
	}

	/**
	 * Method to copy the content object.
	 *
	 * @return  JContent  The content object copy.
	 *
	 * @since   12.1
	 * @throws  LogicException
	 * @throws  RuntimeException
	 */
	public function copy()
	{
		// Assert the object is loaded.
		$this->assertIsLoaded();

		// Create a new content item.
		$copy = $this->factory->getContent($this->type->alias)->create();

		// Get the data from this item.
		$data = (array) $this->dump();

		// Remove unique values.
		unset($data['content_id']);
		unset($data['featured']);
		unset($data['created_date']);
		unset($data['created_user_id']);
		unset($data['likes']);
		unset($data['revision']);

		// Update the content title and alias.
		$data['title'] = JString::increment($data['title']);
		$data['alias'] = JFilterOutput::stringURLSafe($data['title']);

		// Update the copy data.
		$copy->bind($data);

		return $copy;
	}

	/**
	 * Method to create a new content item.
	 *
	 * @return  JContent  The content object.
	 *
	 * @since   12.1
	 * @throws  RuntimeException
	 */
	public function create()
	{
		// Get the user id.
		$userId = !$this->user->get('guest') ? (int) $this->user->get('id') : null;

		// Set the base data for the content object.
		$this->type_id = (int) $this->type->type_id;
		$this->created_date = JDate::getInstance();
		$this->created_user_id = $userId;
		$this->state = 1;
		$this->temporary = 1;

		// Check the content object.
		$this->validate();

		// Trigger the before create event.
		$this->app->triggerEvent('onContentBeforeCreate', array($this));

		// Perform the actual database creation.
		$this->doCreate();

		// Trigger the after create event.
		$this->app->triggerEvent('onContentAfterCreate', array($this));

		return $this;
	}

	/**
	 * Method to delete the content object.
	 *
	 * @return  JContent  The content object.
	 *
	 * @since   12.1
	 * @throws  LogicException
	 * @throws  RuntimeException
	 */
	public function delete()
	{
		// Assert the object is loaded.
		$this->assertIsLoaded();

		// Trigger the before delete event.
		$this->app->triggerEvent('onContentBeforeDelete', array($this));

		// Perform the actual database delete.
		$this->doDelete();

		// Trigger the after delete event.
		$this->app->triggerEvent('onContentAfterDelete', array($this));

		return $this;
	}

	/**
	 * Method to like the content object.
	 *
	 * @return  JContent  The content object.
	 *
	 * @since   12.1
	 * @throws  LogicException
	 * @throws  RuntimeException
	 */
	public function like()
	{
		// Assert the object is loaded.
		$this->assertIsLoaded();

		// Build the query to create the like record.
		$query = $this->db->getQuery(true);
		$query->insert($this->db->qn('#__content_likes'));
		$query->set('content_id = ' . (int) $this->content_id);
		$query->set('user_id = ' . (int) $this->user->get('id'));

		// Create the like record.
		$this->db->setQuery($query);
		$this->db->query();

		// Build the query to update the likes count.
		$query = $this->db->getQuery(true);
		$query->update($this->db->qn('#__content'));
		$query->set('likes = likes+1');
		$query->where('content_id = ' . (int) $this->content_id);

		// Update the likes count.
		$this->db->setQuery($query);
		$this->db->query();

		// Update the likes.
		$this->likes += 1;

		return $this;
	}

	/**
	 * Method to load a content object.
	 *
	 * @param   integer  $contentId  The content id.
	 *
	 * @return  JContent  The content object.
	 *
	 * @since   12.1
	 * @throws  LogicException
	 * @throws  RuntimeException
	 */
	public function load($contentId)
	{
		// Get the primary key.
		$primaryKey = $this->getTableKey('primary', 'primary');

		// Set the primary id.
		$this->$primaryKey = (int) $contentId;

		// Assert the object is loaded.
		$this->assertIsLoaded();

		// Trigger the before load event.
		$this->app->triggerEvent('onContentBeforeLoad', array($this));

		// Perform the actual database load.
		$this->doLoad();

		// Trigger the after load event.
		$this->app->triggerEvent('onContentAfterLoad', array($this));

		return $this;
	}

	/**
	 * Method to increment the hit count for a content object.
	 *
	 * @return  JContent  The content object.
	 *
	 * @since   12.1
	 * @throws  LogicException
	 * @throws  RuntimeException
	 */
	public function hit()
	{
		// Assert the object is loaded.
		$this->assertIsLoaded();

		// Build a query to update the hit count.
		$query = $this->db->getQuery(true);
		$query->update($this->db->qn('#__content_hits'));
		$query->set('hits = hits + 1');
		$query->where('content_id = ' . (int) $this->content_id);

		// Checkin the item.
		$this->db->setQuery($query);
		$this->db->query();

		// Update the internal hit count.
		$this->hits += 1;

		return $this;
	}

	/**
	 * Method to build a route to the content.
	 *
	 * The following URL variables are automatically set for the route (but can be overridden):
	 * - type: the content type
	 * - view: the name of the view (defaults to "item")
	 * - content_id: the id of the content
	 *
	 * If "task" is set as a URL variable, then "view" will be unset.
	 *
	 * @param   array  $vars  An array of URL variables.
	 *
	 * @return  string  The route.
	 *
	 * @since   12.1
	 */
	public function route($vars = array())
	{
		// Create a base URI.
		$route = clone(JUri::getInstance('index.php'));

		// Add the base vars.
		$base = array(
				'type' => $this->type->alias,
				'view' => 'item',
				'content_id' => $this->content_id
		);

		// Merge in the base and extra vars.
		$vars = array_merge($base, $vars);

		// Set the route variables.
		foreach ($vars as $key => $value)
		{
			// Set the variable.
			$route->setVar($key, $value);
		}

		// Remove the view if a task is set.
		if ($route->getVar('task'))
		{
			$route->delVar('view');
		}

		return $route->toString(array('path', 'query'));
	}

	/**
	 * Method to unlike the content object.
	 *
	 * @return  JContent  The content object.
	 *
	 * @since   12.1
	 * @throws  LogicException
	 * @throws  RuntimeException
	 */
	public function unlike()
	{
		// Assert the object is loaded.
		$this->assertIsLoaded();

		// Build a query to delete the like record.
		$query = $this->db->getQuery(true);
		$query->delete('#__content_likes');
		$query->where('content_id = ' . (int) $this->content_id);
		$query->where('user_id = ' . (int) $this->user->get('id'));

		// Delete the like record.
		$this->db->setQuery($query);
		$this->db->query();

		// Build the query to update the likes count.
		$query = $this->db->getQuery(true);
		$query->update($this->db->qn('#__content'));
		$query->set('likes = likes-1');
		$query->where('likes > 0');
		$query->where('content_id = ' . (int) $this->content_id);

		// Update the likes count.
		$this->db->setQuery($query);
		$this->db->query();

		// Update the likes.
		$this->likes -= 1;

		return $this;
	}

	/**
	 * Method to update an object in the database.
	 *
	 * @return  JContent  The content object.
	 *
	 * @since   12.1
	 * @throws  LogicException
	 * @throws  RuntimeException
	 */
	public function update()
	{
		// Assert the object is loaded.
		$this->assertIsLoaded();

		// Make sure the item is checked out.
		$this->checkout();

		// Check for an alias.
		if (empty($this->alias))
		{
			$this->alias = $this->title;
		}

		// Sanitize the alias.
		$this->alias = JFilterOutput::stringURLSafe($this->alias);

		// Update the last modified data for the content.
		$this->modified_date = JDate::getInstance();
		$this->modified_user_id	= !$this->user->get('guest') ? (int) $this->user->get('id') : null;

		// Update the revision number.
		if (!$this->isTemporary())
		{
			$this->revision += 1;
		}

		// Check the content object.
		$this->validate();

		// Trigger the before update event.
		$this->app->triggerEvent('onContentBeforeUpdate', array($this));

		// Perform the actual database updates.
		$this->doUpdate();

		// Trigger the after update event.
		$this->app->triggerEvent('onContentAfterUpdate', array($this));

		return $this;
	}

	/**
	 * Method to check whether the user can check out an item.
	 *
	 * @return  boolean  True if the item can be checked out, false otherwise.
	 *
	 * @since   12.1
	 * @throws  LogicException
	 * @throws  RuntimeException
	 */
	public function canCheckout()
	{
		// Assert the object is loaded.
		$this->assertIsLoaded();

		// Get the session id.
		$sessionId	= $this->app->getSession()->getId();

		// Check if the item is checked out at all.
		if (!empty($this->checked_out_session) && $this->checked_out_session != $sessionId)
		{
			// Build the query to check if the item is editable.
			$query = $this->db->getQuery(true);
			$query->select('a.content_id');
			$query->from('#__content AS a');
			$query->join('INNER', '#__session AS s ON s.session_id = a.checked_out_session');
			$query->where('a.content_id = ' . (int) $this->content_id);

			// Get the checked out state.
			$this->db->setQuery($query);
			$checkedOut = (int) $this->db->loadResult();

			// Check if the checked out session is still valid.
			if ($checkedOut)
			{
				return false;
			}
		}

		return true;
	}

	/**
	 * Method to check whether the user can delete the object.
	 *
	 * @param   boolean  $full  True for a comprehensive check, false for a cursory check.
	 *
	 * @return  mixed  True if allowed, false for an explicit deny, null for an implicit deny.
	 *
	 * @since   12.1
	 * @throws  LogicException
	 * @throws  RuntimeException
	 */
	public function canDelete($full = true)
	{
		// Assert the object is loaded.
		$this->assertIsLoaded();

		// Any temporary object that the user can checkout can be deleted.
		if ($this->isTemporary() && $this->canCheckout())
		{
			return true;
		}

		// Assert the object can be checked out.
		if ($full && !$this->canCheckout())
		{
			return false;
		}

		// Check if the user is authorised.
		return $this->authorise('delete');
	}

	/**
	 * Method to check whether the user can feature the object.
	 *
	 * @param   boolean  $full  True for a comprehensive check, false for a cursory check.
	 *
	 * @return  mixed  True if allowed, false for an explicit deny, null for an implicit deny.
	 *
	 * @since   12.1
	 * @throws  LogicException
	 * @throws  RuntimeException
	 */
	public function canFeature($full = true)
	{
		// Assert the object is loaded.
		$this->assertIsLoaded();

		// Assert the object can be checked out.
		if ($full && !$this->canCheckout())
		{
			return false;
		}

		// Check if the user is authorised.
		return $this->authorise('feature');
	}

	/**
	 * Method to check whether the user can like the object.
	 *
	 * @return  mixed  True if allowed, false for an explicit deny, null for an implicit deny.
	 *
	 * @since   12.1
	 * @throws  LogicException
	 */
	public function canLike()
	{
		// Assert the object is loaded.
		$this->assertIsLoaded();

		// Check if the user is a guest.
		if ($this->user->get('guest'))
		{
			return false;
		}

		// Check if the user is authorised.
		return $this->authorise('like');
	}

	/**
	 * Method to check whether the user can update the object.
	 *
	 * @param   boolean  $full  True for a comprehensive check, false for a cursory check.
	 *
	 * @return  mixed  True if allowed, false for an explicit deny, null for an implicit deny.
	 *
	 * @since   12.1
	 * @throws  LogicException
	 * @throws  RuntimeException
	 */
	public function canUpdate($full = true)
	{
		// Assert the object is loaded.
		$this->assertIsLoaded();

		// Assert the object can be checked out.
		if ($full && !$this->canCheckout())
		{
			return false;
		}

		// Check if the user is authorised.
		return $this->authorise('update');
	}

	/**
	 * Method to check whether the user can view the object.
	 *
	 * @return  mixed  True if allowed, false for an explicit deny, null for an implicit deny.
	 *
	 * @since   12.1
	 * @throws  LogicException
	 * @throws  RuntimeException
	 */
	public function canView()
	{
		// Assert the object is loaded.
		$this->assertIsLoaded();

		// Check if an access level is set.
		if (isset($this->access))
		{
			// Get the user's authorised view levels.
			$levels = $this->user->getAuthorisedViewLevels();

			// Check if the user has access.
			return in_array($this->access, $levels);
		}

		return null;
	}

	/**
	 * Checks if the content is archived.
	 *
	 * Content is archived if state = 2.
	 *
	 * @return  boolean  True if archived, false otherwise.
	 *
	 * @since   12.1
	 * @throws  LogicException
	 */
	public function isArchived()
	{
		// Assert the object is loaded.
		$this->assertIsLoaded();

		return $this->state == 2 ? true : false;
	}

	/**
	 * Checks if the content is active.
	 *
	 * Content is active if state = 1.
	 *
	 * @return  boolean  True if active, false otherwise.
	 *
	 * @since   12.1
	 * @throws  LogicException
	 */
	public function isActive()
	{
		// Assert the object is loaded.
		$this->assertIsLoaded();

		return $this->state == 1 ? true : false;
	}

	/**
	 * Checks if the content is draft.
	 *
	 * Content is draft if state = 0.
	 *
	 * @return  boolean  True if draft, false otherwise.
	 *
	 * @since   12.1
	 * @throws  LogicException
	 */
	public function isDraft()
	{
		// Assert the object is loaded.
		$this->assertIsLoaded();

		return $this->state == 0 ? true : false;
	}

	/**
	 * Checks if the content is featured.
	 *
	 * @return  boolean  True if featured, false otherwise.
	 *
	 * @since   12.1
	 * @throws  LogicException
	 */
	public function isFeatured()
	{
		// Assert the object is loaded.
		$this->assertIsLoaded();

		return $this->featured == 1 ? true : false;
	}

	/**
	 * Checks if the content is liked.
	 *
	 * @return  boolean  True if user liked the content, false otherwise.
	 *
	 * @since   12.1
	 * @throws  LogicException
	 * @throws  RuntimeException
	 */
	public function isLiked()
	{
		// Assert the object is loaded.
		$this->assertIsLoaded();

		// Build a database query to check the liked state.
		$query = $this->db->getQuery(true);
		$query->select('*');
		$query->from($this->db->qn('#__content_likes'));
		$query->where('content_id = ' . (int) $this->content_id);
		$query->where('user_id = ' . (int) $this->user->get('id'));

		// Check the liked state.
		$this->db->setQuery($query);
		$result = $this->db->loadObject();

		return empty($result) ? false : true;
	}

	/**
	 * Checks if the content is trashed.
	 *
	 * Content is trashed if state = -1.
	 *
	 * @return  boolean  True if trashed, false otherwise.
	 *
	 * @since   12.1
	 * @throws  LogicException
	 */
	public function isTrashed()
	{
		// Assert the object is loaded.
		$this->assertIsLoaded();

		return $this->state == -1 ? true : false;
	}

	/**
	 * Checks if the content is temporary.
	 *
	 * @return  boolean  True if temporary, false otherwise.
	 *
	 * @since   12.1
	 * @throws  LogicException
	 */
	public function isTemporary()
	{
		// Assert the object is loaded.
		$this->assertIsLoaded();

		return $this->temporary ? true : false;
	}

	/**
	 * Checks if the content is visible. Visibility is determined by checking
	 * the content state as well as the publish start and end dates.
	 *
	 * @return  boolean  True if visible, false otherwise.
	 *
	 * @since   12.1
	 * @throws  LogicException
	 */
	public function isVisible()
	{
		// Assert the object is loaded.
		$this->assertIsLoaded();

		// Check if the item is active.
		if (!$this->isActive() && !$this->isArchived())
		{
			return false;
		}

		// Get the date/time information.
		$nullDate = $this->db->getNullDate();
		$timeZone = new DateTimeZone('GMT');
		$currDate = new DateTime('NOW', $timeZone);

		// Check the publish start date.
		if (!empty($this->publish_start_date) && $this->publish_start_date != $nullDate)
		{
			// Load the start date.
			$startDate = new DateTime($this->publish_start_date, $timeZone);

			// Check the start date.
			if ($startDate >= $currDate)
			{
				return false;
			}
		}

		// Check the publish end date.
		if (!empty($this->publish_end_date) && $this->publish_end_date != $nullDate)
		{
			// Load the end date.
			$endDate = new DateTime($this->publish_end_date, $timeZone);

			// Check the end date.
			if ($endDate <= $currDate)
			{
				return false;
			}
		}

		return true;
	}

	/**
	 * Method to convert an object to a JSON string.
	 *
	 * @return  string  The object as a JSON string.
	 *
	 * @since   12.1
	 */
	public function __toString()
	{
		// Dump the object.
		$dump = $this->dump();

		// Check if the user has update permission.
		if (!$this->canUpdate(false))
		{
			// Remove sensitive properties.
			unset($dump->access);
			unset($dump->created_user_id);
			unset($dump->modified_user_id);
			unset($dump->checked_out_user_id);
			unset($dump->checked_out_session);
			unset($dump->publish_start_date);
			unset($dump->publish_end_date);
			unset($dump->rules);
		}

		return json_encode($dump);
	}

	/**
	 * Method to assert the object is loaded or throw an exception.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 * @throws  LogicException
	 */
	protected function assertIsLoaded()
	{
		// Assert the object is loaded.
		if (!$this->isLoaded())
		{
			throw new LogicException(JText::_('JCONTENT_NOT_LOADED'));
		}
	}

	/**
	 * Method to get the configuration data.
	 *
	 * @return  object  The configuration data.
	 *
	 * @since   12.1
	 */
	protected function getConfig()
	{
		// See notes for JDatabaseObject::__get() on why we use getProperty().
		return json_decode($this->getProperty('config', false));
	}

	/**
	 * Method to set the configuration data.
	 *
	 * @param   mixed  $config  The configuration as an object or JSON string.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	protected function setConfig($config)
	{
		// Convert the value to a string.
		$config = is_string($config) ? $config : json_encode($config);

		// Set the value.
		$this->setProperty('config', $config, false);
	}

	/**
	 * Method to get the created date.
	 *
	 * @return  mixed  A JDate object if set, null otherwise.
	 *
	 * @since   12.1
	 */
	protected function getCreatedDate()
	{
		// Get the date.
		$date = $this->getProperty('created_date', false);

		// Convert the date to a JDate if set.
		if (isset($date))
		{
			$date = JDate::getInstance($date);
		}

		return $date;
	}

	/**
	 * Method to set the created date.
	 *
	 * @param   mixed  $date  The date as a string or JDate object.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 * @throws  InvalidArgumentException
	 */
	protected function setCreatedDate($date)
	{
		// Get the current value.
		$current = $this->getProperty('created_date', false, false);

		// Only set the date if it is not set already.
		if ($current == null)
		{
			// Convert the date if necessary.
			if (is_string($date))
			{
				$date = JDate::getInstance($date);
			}

			// Verify the date is valid.
			if (!($date instanceof JDate))
			{
				throw new InvalidArgumentException(JText::_('JCONTENT_INVALID_DATE_TYPE'));
			}

			// Set the created date.
			$this->setProperty('created_date', $date->format($this->db->getDateFormat()), false);
		}
	}

	/**
	 * Method to get the modified date.
	 *
	 * @return  mixed  A JDate object if set, null otherwise.
	 *
	 * @since   12.1
	 */
	protected function getModifiedDate()
	{
		// Get the date.
		$date = $this->getProperty('modified_date', false);

		// Convert the date to a JDate if set.
		if (isset($date))
		{
			$date = JDate::getInstance($date);
		}

		return $date;
	}

	/**
	 * Method to set the modified date.
	 *
	 * @param   mixed  $date  The date as a string or JDate object.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 * @throws  InvalidArgumentException
	 */
	protected function setModifiedDate($date)
	{
		// Convert the date if necessary.
		if (is_string($date))
		{
			$date = JDate::getInstance($date);
		}

		// Verify the date is valid.
		if (!($date instanceof JDate))
		{
			throw new InvalidArgumentException(JText::_('JCONTENT_INVALID_DATE_TYPE'));
		}

		// Set the modified date.
		$this->setProperty('modified_date', $date->format($this->db->getDateFormat()), false);
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
	 * @return  JContent  The content object.
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
	 * Method to validate the access level.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 * @throws  UnexpectedValueException
	 */
	protected function validateAccessLevel()
	{
		// Check if an access level is set.
		if (isset($this->access))
		{
			// Build a query to get the access level.
			$query = $this->db->getQuery(true);
			$query->select('id');
			$query->from('#__viewlevels');
			$query->where('id = ' . (int) $this->access);

			// Get the access level.
			$this->db->setQuery($query);
			$level = $this->db->loadResult();

			// Check if the access level exists.
			if ($this->access != $level)
			{
				throw new UnexpectedValueException(JText::sprintf('JCONTENT_INVALID_ACCESS_LEVEL', $this->access));
			}
		}
	}
}
