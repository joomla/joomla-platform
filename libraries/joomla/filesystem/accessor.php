<?php

/**
 * @package     Joomla.Platform
 * @subpackage  FileSystem
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * A File system accessor handling class
 *
 * @package     Joomla.Platform
 * @subpackage  FileSystem
 *
 * @since       12.2
 */
abstract class JFilesystemAccessor
{
	/**
	 * @var  array  Array of readers
	 *
	 * @since   12.2
	 */
	protected static $readers = array();

	/**
	 * @var  array  Array of writers
	 *
	 * @since   12.2
	 */
	protected static $writers = array();

	/**
	 * @var  array  Array of pullers
	 *
	 * @since   12.2
	 */
	protected static $pullers = array();

	/**
	 * @var  array  Array of pushers
	 *
	 * @since   12.2
	 */
	protected static $pushers = array();

	/**
	 * @var  array  Array of accessors
	 *
	 * @since   12.2
	 */
	protected static $accessors = array();

	/**
	 * Read data from a file
	 *
	 * @param   string  $name  The reader name.
	 * @param   array   $args  Array of args
	 *
	 * @return  mixed  The data read.
	 *
	 * @since   12.2
	 */
	public static function read($name, $args)
	{
		return static::call('read', $name, $args);
	}

	/**
	 * Write data to a file
	 *
	 * @param   string  $name  The writer name.
	 * @param   array   $args  Array of args
	 *
	 * @return  int|FALSE  The number of bytes written, or FALSE on failure.
	 *
	 * @since   12.2
	 */
	public static function write($name, $args)
	{
		return static::call('write', $name, $args);
	}

	/**
	 * Pull data from a file
	 *
	 * @param   string  $name  The puller name.
	 * @param   array   $args  Array of args
	 *
	 * @return  mixed  The data read.
	 *
	 * @since   12.2
	 */
	public static function pull($name, $args)
	{
		return static::call('pull', $name, $args);
	}

	/**
	 * Push data to a file
	 *
	 * @param   string  $name  The pusher name.
	 * @param   array   $args  Array of args
	 *
	 * @return  int|FALSE  The number of bytes written, or FALSE on failure.
	 *
	 * @since   12.2
	 */
	public static function push($name, $args)
	{
		return static::call('push', $name, $args);
	}

	/**
	 * Call a read, write, pull or push operation
	 *
	 * @param   string  $type  Either 'read', 'write', 'pull' or 'push'.
	 * @param   string  $name  The reader/writer/puller/pusher name.
	 * @param   array   $args  Array of args
	 *
	 * @return  mixed
	 *
	 * @since   12.2
	 */
	protected static function call($type, $name, $args)
	{
		switch ($type)
		{
			case 'read':
				$storage = static::$readers;
				break;
			case 'write':
				$storage = static::$writers;
				break;
			case 'pull':
				$storage = static::$pullers;
				break;
			case 'push':
				$storage = static::$pushers;
				break;
		}

		if (isset($storage[$name]))
		{
			$call = $storage[$name];
		}
		elseif (isset(static::$accessors[$name]))
		{
			$call = array(static::$accessors[$name], $type);
		}
		else
		{
			list($prefix, $suffix) = static::extract($name);
			$call = array($prefix . 'FilesystemAccessor' . $suffix, $type);
		}
		if (is_callable($call))
		{
			if ($type == 'push')
			{
				$args[0]->directory->create();
			}
			return call_user_func_array($call, $args);
		}
		else
		{
			if (is_string($call))
			{
				$function = $call;
			}
			elseif (is_string($call[0]))
			{
				$function = $call[0] . '::' . $call[1];
			}
			else
			{
				$function = get_class($call[0]) . '::' . $call[1];
			}
			throw new RuntimeException(sprintf('%s could not be called', $function));
		}
	}

	/**
	 * Register an accessor
	 *
	 * @param   string  $name       The accessor name.
	 * @param   string  $className  The class name to register.
	 *
	 * @return  void
	 *
	 * @since   12.2
	 */
	public static function registerAccessor($name, $className)
	{
		static::$accessors[$name] = $className;
	}

	/**
	 * Register a reader
	 *
	 * @param   string  $name      The reader name.
	 * @param   string  $function  The function to register.
	 *
	 * @return  void
	 *
	 * @since   12.2
	 */
	public static function registerReader($name, $function)
	{
		static::$readers[$name] = $function;
	}

	/**
	 * Register a writer
	 *
	 * @param   string  $name      The writer name.
	 * @param   string  $function  The function to register.
	 *
	 * @return  void
	 *
	 * @since   12.2
	 */
	public static function registerWriter($name, $function)
	{
		static::$writers[$name] = $function;
	}

	/**
	 * Register a puller
	 *
	 * @param   string  $name      The puller name.
	 * @param   string  $function  The function to register.
	 *
	 * @return  void
	 *
	 * @since   12.2
	 */
	public static function registerPuller($name, $function)
	{
		static::$pullers[$name] = $function;
	}

	/**
	 * Register a pusher
	 *
	 * @param   string  $name      The pusher name.
	 * @param   string  $function  The function to register.
	 *
	 * @return  void
	 *
	 * @since   12.2
	 */
	public static function registerPusher($name, $function)
	{
		static::$pushers[$name] = $function;
	}

	/**
	 * Unregister an accessor
	 *
	 * @param   string  $name  The accessor name.
	 *
	 * @return  void
	 *
	 * @since   12.2
	 */
	public static function unregisterAccessor($name)
	{
		unset(static::$accessors[$name]);
	}

	/**
	 * Unregister a reader
	 *
	 * @param   string  $name  The reader name.
	 *
	 * @return  void
	 *
	 * @since   12.2
	 */
	public static function unregisterReader($name)
	{
		unset(static::$readers[$name]);
	}

	/**
	 * Unregister a writer
	 *
	 * @param   string  $name  The writer name.
	 *
	 * @return  void
	 *
	 * @since   12.2
	 */
	public static function unregisterWriter($name)
	{
		unset(static::$writers[$name]);
	}

	/**
	 * Unregister a puller
	 *
	 * @param   string  $name  The puller name.
	 *
	 * @return  void
	 *
	 * @since   12.2
	 */
	public static function unregisterPuller($name)
	{
		unset(static::$pullers[$name]);
	}

	/**
	 * Unregister a pusher
	 *
	 * @param   string  $name  The pusher name.
	 *
	 * @return  void
	 *
	 * @since   12.2
	 */
	public static function unregisterPusher($name)
	{
		unset(static::$pushers[$name]);
	}

	/**
	 * Tell if an accessor is registered
	 *
	 * @param   string  $name  The accessor name.
	 *
	 * @return  void
	 *
	 * @since   12.2
	 */
	public static function isAccessor($name)
	{
		return isset(static::$accessors[$name]);
	}

	/**
	 * Tell if a reader is registered
	 *
	 * @param   string  $name  The reader name.
	 *
	 * @return  void
	 *
	 * @since   12.2
	 */
	public static function isReader($name)
	{
		return isset(static::$readers[$name]);
	}

	/**
	 * Tell if a writer is registered
	 *
	 * @param   string  $name  The writer name.
	 *
	 * @return  void
	 *
	 * @since   12.2
	 */
	public static function isWriter($name)
	{
		return isset(static::$writers[$name]);
	}

	/**
	 * Tell if a puller is registered
	 *
	 * @param   string  $name  The puller name.
	 *
	 * @return  void
	 *
	 * @since   12.2
	 */
	public static function isPuller($name)
	{
		return isset(static::$pullers[$name]);
	}

	/**
	 * Tell if a pusher is registered
	 *
	 * @param   string  $name  The pusher name.
	 *
	 * @return  void
	 *
	 * @since   12.2
	 */
	public static function isPusher($name)
	{
		return isset(static::$pushers[$name]);
	}

	/**
	 * Get an accessor
	 *
	 * @param   string  $name  The accessor name.
	 *
	 * @return  string  Class registered
	 *
	 * @since   12.2
	 */
	public static function getAccessor($name)
	{
		return static::$accessors[$name];
	}

	/**
	 * Get a reader
	 *
	 * @param   string  $name  The reader name.
	 *
	 * @return  callable  The function registered
	 *
	 * @since   12.2
	 */
	public static function getReader($name)
	{
		return static::$readers[$name];
	}

	/**
	 * Get a writer
	 *
	 * @param   string  $name  The writer name.
	 *
	 * @return  callable  The function registered
	 *
	 * @since   12.2
	 */
	public static function getWriter($name)
	{
		return static::$writers[$name];
	}

	/**
	 * Get a puller
	 *
	 * @param   string  $name  The puller name.
	 *
	 * @return  callable  The function registered
	 *
	 * @since   12.2
	 */
	public static function getPuller($name)
	{
		return static::$pullers[$name];
	}

	/**
	 * Get a pusher
	 *
	 * @param   string  $name  The pusher name.
	 *
	 * @return  callable  The function registered
	 *
	 * @since   12.2
	 */
	public static function getPusher($name)
	{
		return static::$pushers[$name];
	}

	/**
	 * Extract the prefix and the suffix of an accessor class.
	 *
	 * @param   string  $name  The accessor name.
	 *
	 * @return  array  array($prefix, $suffix).
	 *
	 * @since   12.2
	 */
	protected static function extract($name)
	{
		$parts = JString::splitCamelCase($name);
		if (isset($parts[1]))
		{
			return array($parts[0], implode(array_slice($parts, 1)));
		}
		else
		{
			return array ('J', $name);
		}
	}
}
