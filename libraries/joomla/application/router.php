<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Application
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die();

/**
 * Set the available masks for the routing mode
 */
define('JROUTER_MODE_RAW', 0);
define('JROUTER_MODE_SEF', 1);
define('JROUTER_MODE', 1);

/**
 * Class to create and parse routes
 *
 * @package     Joomla.Platform
 * @subpackage  Application
 * @since       11.1
 */
class JRouter extends JObject
{
	/**
	 * An array of variables
	 *
	 * @var     array
	 * @since   11.1
	 */
	protected $_vars = array();

	/**
	 * Array of buildrules
	 * 
	 * @var array
	 * @since 11.3
	 */
	protected $buildrules = array();

	/**
	 * Array of parserules
	 * 
	 * @var array
	 * @since 11.3
	 */
	protected $parserules = array();

	/**
	 * Router-Options
	 * 
	 * @var array
	 * @since 11.3
	 */
	protected $options = array();

	/**
	 * Cache for processed URLs
	 * 
	 * @var array Array of processed URLs, defined by hash of the original parameters
	 * @since 11.3
	 */
	protected $cache = array();

	/**
	 * @var    array  An array of JRouter instances.
	 * @since  11.3
	 */
	protected static $instances = array();

	/**
	 * Returns the global JRouter object, only creating it if it
	 * doesn't already exist.
	 *
	 * @param   string  $client   The name of the client
	 * @param   array   $options  An associative array of options
	 *
	 * @return  JRouter A JRouter object.
	 *
	 * @since   11.1
	 */
	public static function getInstance($client, $options = array())
	{
		if (empty(JRouter::$instances[$client]))
		{
			if (!class_exists('JRouter'.ucfirst($client)))
			{
				//Load the router object
				$info = JApplicationHelper::getClientInfo($client, true);

				$path = $info->path.'/includes/router.php';
				if (file_exists($path))
				{
					include_once $path;
				}
				else
				{
					$error = JError::raiseError(500, JText::sprintf('JLIB_APPLICATION_ERROR_ROUTER_LOAD', $client));
					return $error;
				}
			}

			// Create a JRouter object
			$classname = 'JRouter'.ucfirst($client);

			JRouter::$instances[$client] = new $classname($options);
		}

		return JRouter::$instances[$client];
	}

	/**
	 * Constructor for JRouter
	 * 
	 * @param   array  $options  Associative array of options
	 * 
	 * @return void
	 * 
	 * @since 11.3
	 */
	public function __construct($options = array())
	{
		$this->options = (array) $options;
	}

	/**
	 * Function to convert a route to an internal URI
	 *
	 * @param   JURI  &$uri  The uri.
	 *
	 * @return  array
	 *
	 * @since   11.1
	 */
	public function parse(&$uri)
	{
		// Process the parsed variables based on custom defined rules
		foreach ($this->parserules as $rule)
		{
			call_user_func_array($rule, array(&$this, &$uri));
		}
		$this->setVars($uri->getQuery(true));

		return $uri->getQuery(true);
	}

	/**
	 * Function to convert an internal URI to a route
	 *
	 * @param   string  $url  The internal URL as a string or associative array
	 *
	 * @return JURI  The search engine friendly URL
	 *
	 * @since   11.1
	 */
	public function build($url)
	{
		if (!is_array($url))
		{
			//Read the URL into an array
			$temp = array();
			if (strpos($url, '&amp;') !== false)
			{
				$url = str_replace('&amp;', '&', $url);
			}

			if (substr($url, 0, 10) == 'index.php?')
			{
				$url = substr($url, 10);
			}

			parse_str($url, $temp);

			foreach ($temp as $key => $var)
			{
				if ($var == "")
				{
					unset($temp[$key]);
				}
			}
			$url = $temp;
		}

		$key = md5(serialize($url));
		if (isset($this->cache[$key]))
		{
			return $this->cache[$key];
		}

		$uri = new JURI;
		$uri->setQuery($url);

		//Process the uri information based on custom defined rules
		foreach ($this->buildrules as $rule)
		{
			call_user_func_array($rule, array(&$this, &$uri));
		}

		// Get the path data
		$route = $uri->getPath();
		if (!$route)
		{
			$route = 'index.php';
		}

		//Add basepath to the uri
		$uri->setPath(JURI::base(true).'/'.$route);

		$this->cache[$key] = $uri;

		return $uri;
	}

	/**
	 * Get Options of the router
	 * 
	 * @param   string  $key    Name of the setting to retrieve
	 * @param   mixed   $value  Default value if the setting has not been set
	 * 
	 * @return mixed Array of the options of the router or value of the setting
	 * 
	 * @since 11.3
	 */
	public function getOptions($key = null, $value = null)
	{
		if ($key)
		{
			if (isset($this->options[$key]))
			{
				return $this->options[$key];
			}
			else
			{
				return $value;
			}
		}
		return $this->options;
	}

	/**
	 * Set the options for the router
	 * 
	 * @param   string  $key    Name of the setting
	 * @param   string  $value  Value of the setting
	 * 
	 * @return void
	 * 
	 * @since 11.3
	 */
	public function setOption($key, $value)
	{
		$this->options[$key] = $value;
	}

	/**
	 * Set the options for the router
	 * 
	 * @param   array  $options  Associative array of options for the router
	 * 
	 * @return void
	 * 
	 * @since 11.3
	 */
	public function setOptions($options)
	{
		$this->options = $options;
	}

	/**
	 * Returns the vars
	 * 
	 * @param   string  $key    Key of the variable to set
	 * @param   mixed   $value  Defaultvalue of the variable
	 * 
	 * @return array Associative array of URL variables
	 * 
	 * @since 11.1
	 */
	public function getVars($key = null, $value = null)
	{
		if ($key)
		{
			if (isset($this->_vars[$key]))
			{
				return $this->_vars[$key];
			}
			else
			{
				return $value;
			}
		}
		return $this->_vars;
	}

	/**
	 * Get a router variable
	 *
	 * @param   string  $key  The name of the variable
	 *
	 * @return  mixed  Value of the variable
	 *
	 * @since   11.1
	 * @deprecated
	 */
	public function getVar($key)
	{
		return $this->getvars($key);
	}

	/**
	 * Set the current URL variables
	 * 
	 * @param   array  $query  Associative Array of URL parameters
	 * 
	 * @return void
	 * 
	 * @since 11.1
	 */
	public function setVars($query)
	{
		$this->_vars = $query;
	}

	/**
	 * Set a current URL variable
	 * 
	 * @param   string  $key    The name of the variable
	 * @param   mixed   $value  The value of the variable
	 * 
	 * @return void
	 * 
	 * @since 11.3
	 */
	public function setVar($key, $value)
	{
		$this->_vars[$key] = $value;
	}

	/**
	 * Returns the current Mode of the Router
	 * 
	 * @return string
	 * 
	 * @deprecated
	 * @since 11.1
	 */
	public function getMode()
	{
		if (defined(JROUTER_MODE))
		{
			return JROUTER_MODE;
		}
		return 'undefined';
	}
	
	/**
	 * Set the router mode
	 *
	 * @param   integer  $mode  The routing mode.
	 *
	 * @return  void
	 *
	 * @deprecated
	 * @since   11.1
	 */
	public function setMode($mode)
	{
	}

	/**
	 * Attach a build rule
	 *
	 * @param   callback  $callback  The function to be called.
	 * @param   string    $position  The position where this function is supposed
	 * 							   to be executed. Valid values: 'first', 'last'
	 * 
	 * @return void
	 * 
	 * @since 11.3
	 */
	public function attachBuildRule($callback, $position = 'last')
	{
		if ($position == 'last')
		{
			$this->buildrules[] = $callback;
		}
		elseif ($position == 'first')
		{
			array_unshift($this->buildrules, $callback);
		}
	}

	/**
	 * Attach a parse rule
	 *
	 * @param   callback  $callback  The function to be called.
	 * @param   string    $position  The position where this	function is supposed
	 * 								to be executed.	Valid values: 'first', 'last'
	 * 
	 * @return void
	 * 
	 * @since 11.3
	 */
	public function attachParseRule($callback, $position = 'last')
	{
		if ($position == 'last')
		{
			$this->parserules[] = $callback;
		}
		elseif ($position == 'first')
		{
			array_unshift($this->parserules, $callback);
		}
	}
}