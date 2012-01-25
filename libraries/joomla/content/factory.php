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
 * Joomla Platform Content Factory Class
 *
 * @package     Joomla.Platform
 * @subpackage  Content
 * @since       12.1
 */
class JContentFactory
{
	/**
	 * The custom prefix.
	 *
	 * @var    string
	 * @since  12.1
	 */
	protected $prefix;

	/**
	 * The application object.
	 *
	 * @var     JApplicationWeb
	 * @since   12.1
	 */
	protected $app;

	/**
	 * The database adapter.
	 *
	 * @var    JDatabase
	 * @since  12.1
	 */
	protected $db;

	/**
	 * The user object.
	 *
	 * @var    JUser
	 * @since  12.1
	 */
	protected $user;

	/**
	 * Method to instantiate the content helper object.
	 *
	 * @param   string  $prefix  An optional, custom prefix.
	 * @param   mixed   $db      An optional argument to provide dependency injection for the database
	 *                           adapter.  If the argument is a JDatbase adapter that object will become
	 *                           the database adapter, otherwise the default adapter will be used.
	 * @param   mixed   $app     An optional argument to provide dependency injection for the application.
	 *                           If the argument is a JApplicationWeb instance that object will become the application,
	 *                           otherwise the default application will be used.
	 * @param   mixed   $user    An optional argument to provide dependency injection for the user. If the
	 *                           argument is a JUser instance that object will become the user, otherwise the
	 *                           default user will be used.
	 *
	 * @since   12.1
	 */
	public function __construct($prefix = '', JDatabase $db = null, JApplicationWeb $app = null, JUser $user = null)
	{
		// Set the prefix.
		$this->prefix = $prefix;

		// If a database object is given, use it.
		if (isset($db))
		{
			$this->db = $db;
		}
		// Create the database object.
		else
		{
			$this->db = JFactory::getDbo();
		}

		// If an application object is given, use it.
		if (isset($app))
		{
			$this->app = $app;
		}
		// Create the application object.
		else
		{
			$this->app = JFactory::getApplication();
		}

		// If a user object is given, use it.
		if (isset($user))
		{
			$this->user = $user;
		}
		// Create the user object.
		else
		{
			$this->user = JFactory::getUser();
		}
	}

	/**
	 * Gets an instance of a content factory.
	 *
	 * @param   string  $prefix  An optional, custom prefix.
	 * @param   mixed   $db      An optional argument to provide dependency injection for the database
	 *                           adapter.  If the argument is a JDatbase adapter that object will become
	 *                           the database adapter, otherwise the default adapter will be used.
	 * @param   mixed   $app     An optional argument to provide dependency injection for the application.
	 *                           If the argument is a JApplicationWeb instance that object will become the application,
	 *                           otherwise the default application will be used.
	 * @param   mixed   $user    An optional argument to provide dependency injection for the user. If the
	 *                           argument is a JUser instance that object will become the user, otherwise the
	 *                           default user will be used.
	 *
	 * @return  JContentFactory
	 *
	 * @since   12.1
	 */
	public static function getInstance($prefix = '', JDatabase $db = null, JApplicationWeb $app = null, JUser $user = null)
	{
		return new JContentFactory($prefix, $db, $app, $user);
	}

	/**
	 * Gets a content object.
	 *
	 * This method will return a class matching, in order, one of the following names:
	 *
	 * - {prefix}Content{type}
	 * - JContent{type}
	 * - {prefix}Content
	 * - JContent
	 *
	 * @param   string  $type    The content type.
	 * @param   object  $helper  An optional JContentHelper object.
	 *
	 * @return  JContent  The content object.
	 *
	 * @since   12.1
	 * @throws  InvalidArgumentException
	 */
	public function getContent($type, JContentHelper $helper = null)
	{
		// Get the content types.
		$helper	= isset($helper) ? $helper : $this->getHelper();
		$types	= $helper->getTypes();

		// Normalize the type to make the check more reliable.
		$normalized = JString::strtolower($type);

		// Check if the type exists using the normalized name.
		if (empty($types[$normalized]))
		{
			throw new InvalidArgumentException(JText::sprintf('JCONTENT_INVALID_TYPE', $type));
		}

		// Get the content class.
		$class = $this->getContentClass($type);

		// Instantiate the content object.
		return new $class($this->prefix, $types[$normalized], $this, $this->db, $this->app, $this->user);
	}

	/**
	 * Gets the name of a content class.
	 *
	 * This method tries the following possibilities in order:
	 *
	 * - {prefix}Content{type}
	 * - JContent{type}
	 * - {prefix}Content
	 * - JContent
	 *
	 * The class name will always safely fall back to JContent if no other matching class is found.
	 *
	 * @param   string  $type  The content type.
	 *
	 * @return  string  The class name.
	 *
	 * @since   12.1
	 */
	public function getContentClass($type)
	{
		// Set the class base and root.
		$base = 'J';
		$root = 'Content';

		// Check for custom prefix + type.
		if (class_exists($this->prefix . $root . $type))
		{
			$class = $this->prefix . $root . $type;
		}
		// Check for default prefix + type.
		elseif (class_exists($base . $root . $type))
		{
			$class = $base . $root . $type;
		}
		// Check for a custom prefix.
		elseif (class_exists($this->prefix . $root))
		{
			$class = $this->prefix . $root;
		}
		// Use default prefix.
		else
		{
			$class = $base . $root;
		}

		return $class;
	}

	/**
	 * Gets a content form.
	 *
	 * @param   string  $type  The content type.
	 *
	 * @return  JForm  The form object.
	 *
	 * @since   12.1
	 * @throws  RuntimeException
	 */
	public function getForm($type)
	{
		// Get the form name.
		$name = 'JContent' . $type;

		try
		{
			// Get the base form.
			$form = JForm::getInstance($name, 'content', array('control' => 'jform'));

			// Load the type form.
			$form->loadFile($type, false, false);

			// Trigger the form preparation event.
			$this->app->triggerEvent('onContentPrepareForm', array($form));
		}
		// Handle all exceptions.
		catch (Exception $error)
		{
			throw new RuntimeException($error->getMessage(), $error->getCode(), $error);
		}

		return $form;
	}

	/**
	 * Method to get a content helper object.
	 *
	 * This method will return a class matching, in order, one of the following names:
	 *
	 * - {prefix}Helper
	 * - JContentHelper
	 *
	 * @return  JContentHelper  The helper object.
	 *
	 * @since   12.1
	 */
	public function getHelper()
	{
		// Get the helper class.
		$class = $this->getHelperClass();

		// Instantiate the content helper.
		return new $class($this, $this->db);
	}

	/**
	 * Gets the name of a helper class.
	 *
	 * This method tries the following possibilities in order:
	 *
	 * - {prefix}Helper
	 * - JContentHelper
	 *
	 * The class name will always safely fall back to JContentHelper if no other matching class is found.
	 *
	 * @return  string  The class name.
	 *
	 * @since   12.1
	 */
	public function getHelperClass()
	{
		// Set the class base and root.
		$base = 'JContent';
		$root = 'Helper';

		// Check for a custom prefix.
		if (class_exists($this->prefix . $root))
		{
			$class = $this->prefix . $root;
		}
		// Use default prefix.
		else
		{
			$class = $base . $root;
		}

		return $class;
	}

	/**
	 * Method to get a type object.
	 *
	 * This method will return a class matching, in order, one of the following names:
	 *
	 * - {prefix}Type{type}
	 * - JContentType{type}
	 * - {prefix}Type
	 * - JContentType
	 *
	 * @param   string  $type  The (optional) content type.
	 *
	 * @return  JContentType  The type object.
	 *
	 * @since   12.1
	 */
	public function getType($type = null)
	{
		// Get the type class.
		$class = $this->getTypeClass($type);

		// Instantiate the type object.
		return new $class($this->db, $this->user);
	}

	/**
	 * Gets the name of a content type class.
	 *
	 * This method tries the following possibilities in order:
	 *
	 * - {prefix}Type{type}
	 * - JContentType{type}
	 * - {prefix}Type
	 * - JContentType
	 *
	 * The class name will always safely fall back to JContentType if no other matching class is found.
	 *
	 * @param   string  $type  The content type.
	 *
	 * @return  string  The class name.
	 *
	 * @since   12.1
	 */
	public function getTypeClass($type)
	{
		// Set the class base and root.
		$base = 'JContent';
		$root = 'Type';

		// Check for custom prefix + type.
		if (class_exists($this->prefix . $root . $type))
		{
			$class = $this->prefix . $root . $type;
		}
		// Check for default prefix + type.
		elseif (class_exists($base . $root . $type))
		{
			$class = $base . $root . $type;
		}
		// Check for a custom prefix.
		elseif (class_exists($this->prefix . $root))
		{
			$class = $this->prefix . $root;
		}
		// Use default prefix.
		else
		{
			$class = $base . $root;
		}

		return $class;
	}
}
