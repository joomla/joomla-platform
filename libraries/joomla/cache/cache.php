<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Cache
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

// Register the storage class with the loader
JLoader::register('JCacheStorage', __DIR__ . '/storage.php');

// Register the controller class with the loader
JLoader::register('JCacheController', __DIR__ . '/controller.php');

// Almost everything must be public here to allow overloading.

/**
 * Joomla! Cache base object
 *
 * @package     Joomla.Platform
 * @subpackage  Cache
 * @since       11.1
 */
class JCache extends JObject
{
	/**
	 * @var    object  Storage handler
	 * @since  11.1
	 */
	public static $_handler = array();

	/**
	 * @var    array  Options
	 * @since  11.1
	 */
	public $_options;

	/**
	 * Constructor
	 *
	 * @param   array  $options  options
	 *
	 * @since   11.1
	 */
	public function __construct($options)
	{
		$conf = JFactory::getConfig();

		$this->_options = array(
			'cachebase' => $conf->get('cache_path', JPATH_CACHE),
			'lifetime' => (int) $conf->get('cachetime'),
			'language' => $conf->get('language', 'en-GB'),
			'storage' => $conf->get('cache_handler', ''),
			'defaultgroup' => 'default',
			'locking' => true,
			'locktime' => 15,
			'checkTime' => true,
			'caching' => ($conf->get('caching') >= 1) ? true : false);

		// Overwrite default options with given options
		foreach ($options as $option => $value)
		{
			if (isset($options[$option]) && $options[$option] !== '')
			{
				$this->_options[$option] = $options[$option];
			}
		}

		if (empty($this->_options['storage']))
		{
			$this->_options['caching'] = false;
		}
	}

	/**
	 * Returns a reference to a cache adapter object, always creating it
	 *
	 * @param   string  $type     The cache object type to instantiate
	 * @param   array   $options  The array of options
	 *
	 * @return  JCache  A JCache object
	 *
	 * @since   11.1
	 */
	public static function getInstance($type = 'output', $options = array())
	{
		return JCacheController::getInstance($type, $options);
	}

	/**
	 * Get the storage handlers
	 *
	 * @return  array    An array of available storage handlers
	 *
	 * @since   11.1
	 */
	public static function getStores()
	{
		jimport('joomla.filesystem.folder');
		$handlers = JFolder::files(__DIR__ . '/storage', '.php');

		$names = array();
		foreach ($handlers as $handler)
		{
			$name = substr($handler, 0, strrpos($handler, '.'));
			$class = 'JCacheStorage' . $name;

			if (!class_exists($class))
			{
				include_once __DIR__ . '/storage/' . $name . '.php';
			}

			if (call_user_func_array(array(trim($class), 'test'), array()))
			{
				$names[] = $name;
			}
		}

		return $names;
	}

	/**
	 * Set caching enabled state
	 *
	 * @param   boolean  $enabled  True to enable caching
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	public function setCaching($enabled)
	{
		$this->_options['caching'] = $enabled;
	}

	/**
	 * Get caching state
	 *
	 * @return  boolean  Caching state
	 *
	 * @since   11.1
	 */
	public function getCaching()
	{
		return $this->_options['caching'];
	}

	/**
	 * Set cache lifetime
	 *
	 * @param   integer  $lt  Cache lifetime
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	public function setLifeTime($lt)
	{
		$this->_options['lifetime'] = $lt;
	}

	/**
	 * Get cached data by id and group
	 *
	 * @param   string  $id     The cache data id
	 * @param   string  $group  The cache data group
	 *
	 * @return  mixed  boolean  False on failure or a cached data string
	 *
	 * @since   11.1
	 */
	public function get($id, $group = null)
	{
		// Get the default group
		$group = ($group) ? $group : $this->_options['defaultgroup'];

		// Get the storage
		$handler = $this->_getStorage();
		if (!($handler instanceof Exception) && $this->_options['caching'])
		{
			return $handler->get($id, $group, $this->_options['checkTime']);
		}
		return false;
	}

	/**
	 * Get a list of all cached data
	 *
	 * @return  mixed    Boolean false on failure or an object with a list of cache groups and data
	 *
	 * @since   11.1
	 */
	public function getAll()
	{
		// Get the storage
		$handler = $this->_getStorage();
		if (!($handler instanceof Exception) && $this->_options['caching'])
		{
			return $handler->getAll();
		}
		return false;
	}

	/**
	 * Store the cached data by id and group
	 *
	 * @param   mixed   $data   The data to store
	 * @param   string  $id     The cache data id
	 * @param   string  $group  The cache data group
	 *
	 * @return  boolean  True if cache stored
	 *
	 * @since   11.1
	 */
	public function store($data, $id, $group = null)
	{
		// Get the default group
		$group = ($group) ? $group : $this->_options['defaultgroup'];

		// Get the storage and store the cached data
		$handler = $this->_getStorage();
		if (!($handler instanceof Exception) && $this->_options['caching'])
		{
			$handler->_lifetime = $this->_options['lifetime'];
			return $handler->store($id, $group, $data);
		}
		return false;
	}

	/**
	 * Remove a cached data entry by id and group
	 *
	 * @param   string  $id     The cache data id
	 * @param   string  $group  The cache data group
	 *
	 * @return  boolean  True on success, false otherwise
	 *
	 * @since   11.1
	 */
	public function remove($id, $group = null)
	{
		// Get the default group
		$group = ($group) ? $group : $this->_options['defaultgroup'];

		// Get the storage
		$handler = $this->_getStorage();
		if (!($handler instanceof Exception))
		{
			return $handler->remove($id, $group);
		}
		return false;
	}

	/**
	 * Clean cache for a group given a mode.
	 *
	 * group mode       : cleans all cache in the group
	 * notgroup mode    : cleans all cache not in the group
	 *
	 * @param   string  $group  The cache data group
	 * @param   string  $mode   The mode for cleaning cache [group|notgroup]
	 *
	 * @return  boolean  True on success, false otherwise
	 *
	 * @since   11.1
	 */
	public function clean($group = null, $mode = 'group')
	{
		// Get the default group
		$group = ($group) ? $group : $this->_options['defaultgroup'];

		// Get the storage handler
		$handler = $this->_getStorage();
		if (!($handler instanceof Exception))
		{
			return $handler->clean($group, $mode);
		}
		return false;
	}

	/**
	 * Garbage collect expired cache data
	 *
	 * @return  boolean  True on success, false otherwise.
	 *
	 * @since   11.1
	 */
	public function gc()
	{
		// Get the storage handler
		$handler = $this->_getStorage();
		if (!($handler instanceof Exception))
		{
			return $handler->gc();
		}
		return false;
	}

	/**
	 * Set lock flag on cached item
	 *
	 * @param   string  $id        The cache data id
	 * @param   string  $group     The cache data group
	 * @param   string  $locktime  The default locktime for locking the cache.
	 *
	 * @return  object  Properties are lock and locklooped
	 *
	 * @since   11.1
	 */
	public function lock($id, $group = null, $locktime = null)
	{
		$returning = new stdClass;
		$returning->locklooped = false;

		// Get the default group
		$group = ($group) ? $group : $this->_options['defaultgroup'];

		// Get the default locktime
		$locktime = ($locktime) ? $locktime : $this->_options['locktime'];

		// Allow storage handlers to perform locking on their own
		// NOTE drivers with lock need also unlock or unlocking will fail because of false $id
		$handler = $this->_getStorage();
		if (!($handler instanceof Exception) && $this->_options['locking'] == true && $this->_options['caching'] == true)
		{
			$locked = $handler->lock($id, $group, $locktime);
			if ($locked !== false)
			{
				return $locked;
			}
		}

		// Fallback
		$curentlifetime = $this->_options['lifetime'];

		// Set lifetime to locktime for storing in children
		$this->_options['lifetime'] = $locktime;

		$looptime = $locktime * 10;
		$id2 = $id . '_lock';

		if ($this->_options['locking'] == true && $this->_options['caching'] == true)
		{
			$data_lock = $this->get($id2, $group);

		}
		else
		{
			$data_lock = false;
			$returning->locked = false;
		}

		if ($data_lock !== false)
		{
			$lock_counter = 0;

			// Loop until you find that the lock has been released.
			// That implies that data get from other thread has finished
			while ($data_lock !== false)
			{

				if ($lock_counter > $looptime)
				{
					$returning->locked = false;
					$returning->locklooped = true;
					break;
				}

				usleep(100);
				$data_lock = $this->get($id2, $group);
				$lock_counter++;
			}
		}

		if ($this->_options['locking'] == true && $this->_options['caching'] == true)
		{
			$returning->locked = $this->store(1, $id2, $group);
		}

		// Revert lifetime to previous one
		$this->_options['lifetime'] = $curentlifetime;

		return $returning;
	}

	/**
	 * Unset lock flag on cached item
	 *
	 * @param   string  $id     The cache data id
	 * @param   string  $group  The cache data group
	 *
	 * @return  boolean  True on success, false otherwise.
	 *
	 * @since   11.1
	 */
	public function unlock($id, $group = null)
	{
		$unlock = false;

		// Get the default group
		$group = ($group) ? $group : $this->_options['defaultgroup'];

		// Allow handlers to perform unlocking on their own
		$handler = $this->_getStorage();
		if (!($handler instanceof Exception) && $this->_options['caching'])
		{
			$unlocked = $handler->unlock($id, $group);
			if ($unlocked !== false)
			{
				return $unlocked;
			}
		}

		// Fallback
		if ($this->_options['caching'])
		{
			$unlock = $this->remove($id . '_lock', $group);
		}

		return $unlock;
	}

	/**
	 * Get the cache storage handler
	 *
	 * @return  JCacheStorage   A JCacheStorage object
	 *
	 * @since   11.1
	 */
	public function &_getStorage()
	{
		$hash = md5(serialize($this->_options));

		if (isset(self::$_handler[$hash]))
		{
			return self::$_handler[$hash];
		}

		self::$_handler[$hash] = JCacheStorage::getInstance($this->_options['storage'], $this->_options);

		return self::$_handler[$hash];
	}

	/**
	 * Perform workarounds on retrieved cached data
	 *
	 * @param   string  $data     Cached data
	 * @param   array   $options  Array of options
	 *
	 * @return  string  Body of cached data
	 *
	 * @since   11.1
	 */
	public static function getWorkarounds($data, $options = array())
	{
		// Initialise variables.
		$app = JFactory::getApplication();
		$document = JFactory::getDocument();
		$body = null;

		// Get the document head out of the cache.
		if (isset($options['mergehead']) && $options['mergehead'] == 1 && isset($data['head']) && !empty($data['head']))
		{
			$document->mergeHeadData($data['head']);
		}
		elseif (isset($data['head']) && method_exists($document, 'setHeadData'))
		{
			$document->setHeadData($data['head']);
		}

		// If the pathway buffer is set in the cache data, get it.
		if (isset($data['pathway']) && is_array($data['pathway']))
		{
			// Push the pathway data into the pathway object.
			$pathway = $app->getPathWay();
			$pathway->setPathway($data['pathway']);
		}

		// @todo check if the following is needed, seems like it should be in page cache
		// If a module buffer is set in the cache data, get it.
		if (isset($data['module']) && is_array($data['module']))
		{
			// Iterate through the module positions and push them into the document buffer.
			foreach ($data['module'] as $name => $contents)
			{
				$document->setBuffer($contents, 'module', $name);
			}
		}

		if (isset($data['body']))
		{
			// The following code searches for a token in the cached page and replaces it with the
			// proper token.
			$token = JSession::getFormToken();
			$search = '#<input type="hidden" name="[0-9a-f]{32}" value="1" />#';
			$replacement = '<input type="hidden" name="' . $token . '" value="1" />';
			$data['body'] = preg_replace($search, $replacement, $data['body']);
			$body = $data['body'];
		}

		// Get the document body out of the cache.
		return $body;
	}

	/**
	 * Create workarounded data to be cached
	 *
	 * @param   string  $data     Cached data
	 * @param   array   $options  Array of options
	 *
	 * @return  string  Data to be cached
	 *
	 * @since   11.1
	 */
	public static function setWorkarounds($data, $options = array())
	{
		$loptions = array();
		$loptions['nopathway'] = 0;
		$loptions['nohead'] = 0;
		$loptions['nomodules'] = 0;
		$loptions['modulemode'] = 0;

		if (isset($options['nopathway']))
		{
			$loptions['nopathway'] = $options['nopathway'];
		}

		if (isset($options['nohead']))
		{
			$loptions['nohead'] = $options['nohead'];
		}

		if (isset($options['nomodules']))
		{
			$loptions['nomodules'] = $options['nomodules'];
		}

		if (isset($options['modulemode']))
		{
			$loptions['modulemode'] = $options['modulemode'];
		}

		// Initialise variables.
		$app = JFactory::getApplication();
		$document = JFactory::getDocument();

		// Get the modules buffer before component execution.
		$buffer1 = $document->getBuffer();
		if (!is_array($buffer1))
		{
			$buffer1 = array();
		}

		// Make sure the module buffer is an array.
		if (!isset($buffer1['module']) || !is_array($buffer1['module']))
		{
			$buffer1['module'] = array();
		}

		// View body data
		$cached['body'] = $data;

		// Document head data
		if ($loptions['nohead'] != 1 && method_exists($document, 'getHeadData'))
		{

			if ($loptions['modulemode'] == 1)
			{
				$headnow = $document->getHeadData();
				$unset = array('title', 'description', 'link', 'links', 'metaTags');

				foreach ($unset as $un)
				{
					unset($headnow[$un]);
					unset($options['headerbefore'][$un]);
				}

				$cached['head'] = array();

				// Only store what this module has added
				foreach ($headnow as $now => $value)
				{
					$newvalue = array_diff_assoc($headnow[$now], isset($options['headerbefore'][$now]) ? $options['headerbefore'][$now] : array());
					if (!empty($newvalue))
					{
						$cached['head'][$now] = $newvalue;
					}
				}

			}
			else
			{
				$cached['head'] = $document->getHeadData();
			}
		}

		// Pathway data
		if ($app->isSite() && $loptions['nopathway'] != 1)
		{
			$pathway = $app->getPathWay();
			$cached['pathway'] = isset($data['pathway']) ? $data['pathway'] : $pathway->getPathway();
		}

		if ($loptions['nomodules'] != 1)
		{
			// @todo Check if the following is needed, seems like it should be in page cache
			// Get the module buffer after component execution.
			$buffer2 = $document->getBuffer();
			if (!is_array($buffer2))
			{
				$buffer2 = array();
			}

			// Make sure the module buffer is an array.
			if (!isset($buffer2['module']) || !is_array($buffer2['module']))
			{
				$buffer2['module'] = array();
			}

			// Compare the second module buffer against the first buffer.
			$cached['module'] = array_diff_assoc($buffer2['module'], $buffer1['module']);
		}

		return $cached;
	}

	/**
	 * Create safe id for cached data from url parameters set by plugins and framework
	 *
	 * @return  string   md5 encoded cacheid
	 *
	 * @since   11.1
	 */
	public static function makeId()
	{
		$app = JFactory::getApplication();

		// Get url parameters set by plugins
		$registeredurlparams = $app->get('registeredurlparams');

		if (empty($registeredurlparams))
		{
			/*
			$registeredurlparams = new stdClass;
			$registeredurlparams->Itemid 	= 'INT';
			$registeredurlparams->catid 	= 'INT';
			$registeredurlparams->id 		= 'INT';
			*/

			// Provided for backwards compatibility - THIS IS NOT SAFE!!!!
			return md5(serialize(JRequest::getURI()));
		}
		// Platform defaults
		$registeredurlparams->format = 'WORD';
		$registeredurlparams->option = 'WORD';
		$registeredurlparams->view = 'WORD';
		$registeredurlparams->layout = 'WORD';
		$registeredurlparams->tpl = 'CMD';
		$registeredurlparams->id = 'INT';

		$safeuriaddon = new stdClass;

		foreach ($registeredurlparams as $key => $value)
		{
			$safeuriaddon->$key = JRequest::getVar($key, null, 'default', $value);
		}

		return md5(serialize($safeuriaddon));
	}

	/**
	 * Add a directory where JCache should search for handlers. You may
	 * either pass a string or an array of directories.
	 *
	 * @param   string  $path  A path to search.
	 *
	 * @return  array   An array with directory elements
	 *
	 * @since   11.1
	 */
	public static function addIncludePath($path = '')
	{
		static $paths;

		if (!isset($paths))
		{
			$paths = array();
		}
		if (!empty($path) && !in_array($path, $paths))
		{
			jimport('joomla.filesystem.path');
			array_unshift($paths, JPath::clean($path));
		}
		return $paths;
	}
}
