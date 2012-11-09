<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Object
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

// @codeCoverageIgnoreStart
defined('JPATH_PLATFORM') or die;
// @codeCoverageIgnoreEnd

/**
 * Joomla Platform Object List Class
 *
 * @package     Joomla.Platform
 * @subpackage  Object
 * @since       12.3
 */
class JObjectList implements ArrayAccess, Countable, Iterator
{
	/**
	 * The current position.
	 *
	 * @var    integer
	 * @since  12.3
	 */
	private $_current = false;

	/**
	 * The iterator objects.
	 *
	 * @var    array
	 * @since  12.3
	 */
	private $_objects = array();

	/**
	 * Method to instantiate the object list.
	 *
	 * @param   array  $objects  An array of objects.
	 *
	 * @since   12.3
	 */
	public function __construct(array $objects = array())
	{
		// Set the objects.
		$this->_initialise($objects);
	}

	/**
	 * The magic call method is used to call object methods using the iterator.
	 *
	 * Example: $array = $objectList->foo('bar');
	 *
	 * The object list will iterate over it's objects and see if each object has a callable 'foo' method.
	 * If so, it will pass the argument list and assemble any return values. If an object does not have
	 * a callable method no return value is recorded.
	 * The keys of the objects and the result array are maintained.
	 *
	 * @param   string  $method     The called method.
	 * @param   array   $arguments  The method arguments.
	 *
	 * @return  array   An array of returns.
	 *
	 * @since   12.3
	 */
	public function __call($method, $arguments = array())
	{
		$return = array();

		// Iterate through the objects.
		foreach ($this->_objects as $key => $object)
		{
			// Create the object callback.
			$callback = array($object, $method);

			// Check if the callback is callable.
			if (is_callable($callback))
			{
				// Call the method for the object.
				$return[$key] = call_user_func_array($callback, $arguments);
			}
		}

		return $return;
	}

	/**
	 * The magic get method is used to get an object property using the iterator.
	 *
	 * Example: $array = $objectList->foo;
	 *
	 * This will return a column of the values of the 'foo' property in all the objects.
	 * (or values determined by custom property setters in the individual JObject's).
	 * The result array will contain an entry for each object in the list (compared to __call which may not).
	 * The keys of the objects and the result array are maintained.
	 *
	 * @param   string  $property  The property name.
	 *
	 * @return  array  An array of return values.
	 *
	 * @since   12.3
	 */
	public function __get($property)
	{
		$return = array();

		// Iterate through the objects.
		foreach ($this->_objects as $key => $object)
		{
			// Get the property.
			$return[$key] = $object->$property;
		}

		return $return;
	}

	/**
	 * The magic isset method is used to check the state of an object property using the iterator.
	 *
	 * Example: $array = isset($objectList->foo);
	 *
	 * @param   string  $property  The property name.
	 *
	 * @return  boolean  True if the property is set in any of the objects.
	 *
	 * @since   12.3
	 */
	public function __isset($property)
	{
		$return = array();

		// Iterate through the objects.
		foreach ($this->_objects as $key => $object)
		{
			// Check the property.
			$return[] = isset($object->$property);
		}

		return in_array(true, $return, true) ? true : false;
	}

	/**
	 * The magic set method is used to set an object property using the iterator.
	 *
	 * Example: $objectList->foo = 'bar';
	 *
	 * This will set the 'foo' property to 'bar' in all of the objects
	 * (or a value determined by custom property setters in the JObject).
	 *
	 * @param   string  $property  The property name.
	 * @param   mixed   $value     The property value.
	 *
	 * @return  void
	 *
	 * @since   12.3
	 */
	public function __set($property, $value)
	{
		$return = array();

		// Iterate through the objects.
		foreach ($this->_objects as $key => $object)
		{
			// Set the property.
			$object->$property = $value;
		}
	}

	/**
	 * The magic unset method is used to unset an object property using the iterator.
	 *
	 * Example: unset($objectList->foo);
	 *
	 * This will unset all of the 'foo' properties in the list of JObject's.
	 *
	 * @param   string  $property  The property name.
	 *
	 * @return  void
	 *
	 * @since   12.3
	 */
	public function __unset($property)
	{
		// Iterate through the objects.
		foreach ($this->_objects as $object)
		{
			unset($object->$property);
		}
	}

	/**
	 * Method to get the number of objects in the iterator.
	 *
	 * @return  integer  The number of objects.
	 *
	 * @since   12.3
	 */
	public function count()
	{
		return count($this->_objects);
	}

	/**
	 * Method to get the current object.
	 *
	 * @return  JObject  The current object, or false if the array is empty or the pointer is beyond the end of the elements.
	 *
	 * @since   12.3
	 */
	public function current()
	{
		return is_scalar($this->_current) ? $this->_objects[$this->_current] : false;
	}

	/**
	 * Dump the object properties recursively if appropriate.
	 *
	 * @param   integer  $depth   The maximum recursion depth.
	 * @param   array    $dumped  An array of already serialized object to avoid infinite loops.
	 *
	 * @return  stdClass  A standard class object ready to be encoded.
	 *
	 * @since   12.3
	 */
	public function dump($depth = 3, $dumped = null)
	{
		$objects = array();

		// Check if we should initialise the recursion tracker.
		if ($dumped === null)
		{
			$dumped = array();
		}

		// Add this object to the serialized stack.
		$dumped[] = spl_object_hash($this);

		// Make sure that we have not reached our maximum depth.
		if ($depth > 0)
		{
			// Handle JSON serialization recursively.
			foreach ($this->_objects as $key => $object)
			{
				$objects[$key] = $object->dump($depth, $dumped);
			}
		}

		return $objects;
	}

	/**
	 * Get the objects to serialize with JSON.
	 *
	 * @param   mixed  $serialized  An array of objects that have already been serialized to avoid recursion, null on first call.
	 *
	 * @return  array  An array that can be serialised by json_encode().
	 *
	 * @since   12.3
	 */
	public function jsonSerialize($serialized = null)
	{
		// Check if we should initialise the recursion tracker.
		if ($serialized === null)
		{
			$serialized = array();
		}

		// Add this object to the serialized stack.
		$serialized[] = spl_object_hash($this);
		$return = array();

		// Iterate through the objects.
		foreach ($this->_objects as $key => $object)
		{
			// Call the method for the object.
			$return[$key] = $object->jsonSerialize($serialized);
		}

		return $return;
	}

	/**
	 * Method to get the key of the current object.
	 *
	 * @return  scalar  The object key on success, null on failure.
	 *
	 * @since   12.3
	 */
	public function key()
	{
		return $this->_current;
	}

	/**
	 * Method to advance the iterator to the next object.
	 *
	 * @return  void
	 *
	 * @since   12.3
	 */
	public function next()
	{
		// Get the object offsets.
		$keys = array_keys($this->_objects);

		// Get the current key.
		$position = array_search($this->_current, $keys);

		// Check if there is an object after the current object.
		if (isset($keys[$position + 1]))
		{
			// Get the next id.
			$this->_current = $keys[$position + 1];
		}
		else
		{
			// That was the last object.
			$this->_current = null;
		}
	}

	/**
	 * Method to check whether an offset exists.
	 *
	 * @param   mixed  $offset  The object offset.
	 *
	 * @return  boolean  True if the object exists, false otherwise.
	 *
	 * @since   12.3
	 */
	public function offsetExists($offset)
	{
		return isset($this->_objects[$offset]);
	}

	/**
	 * Method to get an offset.
	 *
	 * @param   mixed  $offset  The object offset.
	 *
	 * @return  JObject  The object if it exists, null otherwise.
	 *
	 * @since   12.3
	 */
	public function offsetGet($offset)
	{
		return isset($this->_objects[$offset]) ? $this->_objects[$offset] : null;
	}

	/**
	 * Method to set an offset.
	 *
	 * @param   mixed    $offset  The object offset.
	 * @param   JObject  $object  The object object.
	 *
	 * @return  void
	 *
	 * @since   12.3
	 * @throws  InvalidArgumentException
	 */
	public function offsetSet($offset, $object)
	{
		// Check if the object is a JObject object.
		if (!($object instanceOf JObject))
		{
			throw new InvalidArgumentException(sprintf('%s("%s", *%s*)', __METHOD__, $offset, gettype($object)));
		}

		// Set the offset.
		$this->_objects[$offset] = $object;
	}

	/**
	 * Method to unset an offset.
	 *
	 * @param   mixed  $offset  The object offset.
	 *
	 * @return  void
	 *
	 * @since   12.3
	 */
	public function offsetUnset($offset)
	{
		unset($this->_objects[$offset]);
	}

	/**
	 * Method to rewind the iterator.
	 *
	 * @return  void
	 *
	 * @since   12.3
	 */
	public function rewind()
	{
		// Set the current position to the first object.
		$this->_current = empty($this->_objects) ? false : array_shift(array_keys($this->_objects));
	}

	/**
	 * Method to validate the iterator.
	 *
	 * @return  boolean  True if valid, false otherwise.
	 *
	 * @since   12.3
	 */
	public function valid()
	{
		// Check the current position.
		if (!is_scalar($this->_current) || !isset($this->_objects[$this->_current]))
		{
			return false;
		}

		return true;
	}

	/**
	 * Initialises the list with an array of objects.
	 *
	 * @param   array  $input  An array of objects.
	 *
	 * @return  void
	 *
	 * @since   12.3
	 * @throws  InvalidArgumentException
	 */
	private function _initialise(array $input = array())
	{
		foreach ($input as $key => $object)
		{
			if (!is_null($object))
			{
				$this->offsetSet($key, $object);
			}
		}

		$this->rewind();
	}
}
