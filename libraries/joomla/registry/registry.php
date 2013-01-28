<?php
/**
 * @package		 Joomla.Platform
 * @subpackage	Registry
 *
 * @copyright	 Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		 GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.utilities.arrayhelper');

/**
 * JRegistry class
 *
 * @package		 Joomla.Platform
 * @subpackage	Registry
 * @since			 11.1
 */
class JRegistry implements JsonSerializable
{
	/**
	 * Registry Object
	 *
	 * @var		object
	 * @since	11.1
	 */
	protected $data;

	/**
	 * @var		array	JRegistry instances container.
	 * @since	11.3
	 */
	protected static $instances = array();

	/**
	 * Constructor
	 *
	 * @param	 mixed	$data	The data to bind to the new JRegistry object.
	 *
	 * @since	 11.1
	 */
	public function __construct($data = null)
	{
		// Instantiate the internal data object.
		$this->data = new stdClass;

		// Optionally load supplied data.
		if (is_array($data) || is_object($data))
		{
			$this->bindData($this->data, $data);
		}
		elseif (!empty($data) && is_string($data))
		{
			$this->loadString($data);
		}
	}

	/**
	 * Magic function to clone the registry object.
	 *
	 * @return	JRegistry
	 *
	 * @since	 11.1
	 */
	public function __clone()
	{
		$this->data = unserialize(serialize($this->data));
	}

	/**
	 * Magic function to render this object as a string using default args of toString method.
	 *
	 * @return	string
	 *
	 * @since	 11.1
	 */
	public function __toString()
	{
		return $this->toString();
	}

	/**
	 * Implementation for the JsonSerializable interface.
	 * Allows us to pass JRegistry objects to json_encode.
	 *
	 * @return	object
	 *
	 * @since	 12.2
	 * @note		The interface is only present in PHP 5.4 and up.
	 */
	public function jsonSerialize()
	{
		return $this->data;
	}

	/**
	 * Sets a default value if not already assigned.
	 *
	 * @param	 string	$key			The name of the parameter.
	 * @param	 mixed	 $default	An optional value for the parameter.
	 *
	 * @return	mixed	The value set, or the default if the value was not previously set (or null).
	 *
	 * @since	 11.1
	 */
	public function def($key, $default = '')
	{
		$value = $this->get($key, $default);
		$this->set($key, $value);

		return $value;
	}

	/**
	 * Check if a registry path exists.
	 *
	 * @param	 string	$path	Registry path (e.g. joomla.content.showauthor)
	 *
	 * @return	boolean
	 *
	 * @since	 11.1
	 */
	public function exists($path)
	{
		// Explode the registry path into an array
		if ($nodes = explode('.', $path))
		{
			// Initialize the current node to be the registry root.
			$node = $this->data;

			// Traverse the registry to find the correct node for the result.
			for ($i = 0, $n = count($nodes); $i < $n; $i++)
			{
				if (isset($node->$nodes[$i]))
				{
					$node = $node->$nodes[$i];
				}
				else
				{
					break;
				}

				if ($i + 1 == $n)
				{
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Get a registry value.
	 *
	 * @param	 string	$path		 Registry path (e.g. joomla.content.showauthor)
	 * @param	 mixed	 $default	Optional default value, returned if the internal value is null.
	 *
	 * @return	mixed	Value of entry or null
	 *
	 * @since	 11.1
	 */
	public function get($path, $default = null)
	{
		$result = $default;

		if (!strpos($path, '.'))
		{
			return (isset($this->data->$path) && $this->data->$path !== null && $this->data->$path !== '') ? $this->data->$path : $default;
		}
		// Explode the registry path into an array
		$nodes = explode('.', $path);

		// Initialize the current node to be the registry root.
		$node = $this->data;
		$found = false;

		// Traverse the registry to find the correct node for the result.
		foreach ($nodes as $n)
		{
			if (isset($node->$n))
			{
				$node = $node->$n;
				$found = true;
			}
			else
			{
				$found = false;
				break;
			}
		}
		if ($found && $node !== null && $node !== '')
		{
			$result = $node;
		}

		return $result;
	}

	/**
	 * Returns a reference to a global JRegistry object, only creating it
	 * if it doesn't already exist.
	 *
	 * This method must be invoked as:
	 * <pre>$registry = JRegistry::getInstance($id);</pre>
	 *
	 * @param	 string	$id	An ID for the registry instance
	 *
	 * @return	JRegistry	The JRegistry object.
	 *
	 * @since	 11.1
	 */
	public static function getInstance($id)
	{
		if (empty(self::$instances[$id]))
		{
			self::$instances[$id] = new JRegistry;
		}

		return self::$instances[$id];
	}

	/**
	 * Load a associative array of values into the default namespace
	 *
	 * @param	 array	$array	Associative array of value to load
	 *
	 * @return	boolean	True on success
	 *
	 * @since	 11.1
	 */
	public function loadArray($array)
	{
		$this->bindData($this->data, $array);

		return true;
	}

	/**
	 * Load the public variables of the object into the default namespace.
	 *
	 * @param	 object	$object	The object holding the publics to load
	 *
	 * @return	boolean	True on success
	 *
	 * @since	 11.1
	 */
	public function loadObject($object)
	{
		$this->bindData($this->data, $object);

		return true;
	}

	/**
	 * Load the contents of a file into the registry
	 *
	 * @param	 string	$file		 Path to file to load
	 * @param	 string	$format	 Format of the file [optional: defaults to JSON]
	 * @param	 array	 $options	Options used by the formatter
	 *
	 * @return	boolean	True on success
	 *
	 * @since	 11.1
	 */
	public function loadFile($file, $format = 'JSON', $options = array())
	{
		$data = file_get_contents($file);

		return $this->loadString($data, $format, $options);
	}

	/**
	 * Load a string into the registry
	 *
	 * @param	 string	$data		 String to load into the registry
	 * @param	 string	$format	 Format of the string
	 * @param	 array	 $options	Options used by the formatter
	 *
	 * @return	boolean	True on success
	 *
	 * @since	 11.1
	 */
	public function loadString($data, $format = 'JSON', $options = array())
	{
		// Load a string into the given namespace [or default namespace if not given]
		$handler = JRegistryFormat::getInstance($format);

		$obj = $handler->stringToObject($data, $options);
		$this->loadObject($obj);

		return true;
	}

	/**
	 * Merge a JRegistry object into this one
	 *
	 * @param	 JRegistry	$source	Source JRegistry object to merge.
	 *
	 * @return	boolean	True on success
	 *
	 * @since	 11.1
	 */
	public function merge($source)
	{
		if (!$source instanceof JRegistry)
		{
			return false;
		}

		// Load the variables into the registry's default namespace.
		foreach ($source->toArray() as $k => $v)
		{
			if (($v !== null) && ($v !== ''))
			{
				$this->data->$k = $v;
			}
		}
		return true;
	}

	/**
	 * Set a registry value.
	 *
	 * @param	 string	$path	 Registry Path (e.g. joomla.content.showauthor)
	 * @param	 mixed	 $value	Value of entry
	 *
	 * @return	mixed	The value of the that has been set.
	 *
	 * @since	 11.1
	 */
	public function set($path, $value)
	{
		$result = null;

		// Explode the registry path into an array
		if ($nodes = explode('.', $path))
		{
			// Initialize the current node to be the registry root.
			$node = $this->data;

			// Traverse the registry to find the correct node for the result.
			for ($i = 0, $n = count($nodes) - 1; $i < $n; $i++)
			{
				if (!isset($node->$nodes[$i]) && ($i != $n))
				{
					$node->$nodes[$i] = new stdClass;
				}
				$node = $node->$nodes[$i];
			}

			// Get the old value if exists so we can return it
			$result = $node->$nodes[$i] = $value;
		}

		return $result;
	}

	/**
	 * Transforms a namespace to an array
	 *
	 * @return	array	An associative array holding the namespace data
	 *
	 * @since	 11.1
	 */
	public function toArray()
	{
		return (array) $this->asArray($this->data);
	}

	/**
	 * Transforms a namespace to an object
	 *
	 * @return	object	 An an object holding the namespace data
	 *
	 * @since	 11.1
	 */
	public function toObject()
	{
		return $this->data;
	}

	/**
	 * Get a namespace in a given string format
	 *
	 * @param	 string	$format	 Format to return the string in
	 * @param	 mixed	 $options	Parameters used by the formatter, see formatters for more info
	 *
	 * @return	string	 Namespace in string format
	 *
	 * @since	 11.1
	 */
	public function toString($format = 'JSON', $options = array())
	{
		// Return a namespace in a given format
		$handler = JRegistryFormat::getInstance($format);

		return $handler->objectToString($this->data, $options);
	}

	/**
	 * Method to recursively bind data to a parent object.
	 *
	 * @param	 object	$parent	The parent object on which to attach the data values.
	 * @param	 mixed	 $data		An array or object of data to bind to the parent object.
	 *
	 * @return	void
	 *
	 * @since	 11.1
	 */
	protected function bindData($parent, $data)
	{
		// Ensure the input data is an array.
		if (is_object($data))
		{
			$data = get_object_vars($data);
		}
		else
		{
			$data = (array) $data;
		}

		foreach ($data as $k => $v)
		{
			if ((is_array($v) && JArrayHelper::isAssociative($v)) || is_object($v))
			{
				$parent->$k = new stdClass;
				$this->bindData($parent->$k, $v);
			}
			else
			{
				$parent->$k = $v;
			}
		}
	}

	/**
	 * Method to recursively convert an object of data to an array.
	 *
	 * @param	 object	$data	An object of data to return as an array.
	 *
	 * @return	array	Array representation of the input object.
	 *
	 * @since	 11.1
	 */
	protected function asArray($data)
	{
		$array = array();

		foreach (get_object_vars((object) $data) as $k => $v)
		{
			if (is_object($v))
			{
				$array[$k] = $this->asArray($v);
			}
			else
			{
				$array[$k] = $v;
			}
		}

		return $array;
	}
}
