<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Input
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Joomla! Input Files Class
 *
 * @package     Joomla.Platform
 * @subpackage  Input
 * @since       11.1
 */
class JInputFiles extends JInput
{
	protected $decodedData = array();

	/**
	 * Constructor.
	 *
	 * @param   array  $source   Ignored.
	 * @param   array  $options  Array of configuration parameters (Optional)
	 *
	 * @since   12.1
	 */
	public function __construct(array $source = null, array $options = array())
	{
		if (isset($options['filter']))
		{
			$this->filter = $options['filter'];
		}
		else
		{
			$this->filter = JFilterInput::getInstance();
		}

		// Set the data source.
		$this->data = & $_FILES;

		// Set the options for the class.
		$this->options = $options;
	}

	/**
	 * Gets a value from the input data.
	 *
	 * @param   string  $name     Name of the value to get.
	 * @param   mixed   $default  Default value to return if variable does not exist.
	 * @param   string  $filter   Filter to apply to the value.
	 *
	 * @return  mixed  The filtered input value.
	 *
	 * @since   11.1
	 */
	public function get($name, $default = null, $filter = 'cmd')
	{
		if (isset($this->data[$name]))
		{
			$results = $this->decodeData(
				array(
					$this->data[$name]['name'],
					$this->data[$name]['type'],
					$this->data[$name]['tmp_name'],
					$this->data[$name]['error'],
					$this->data[$name]['size']
				)
			);
			return $results;
		}

		return $default;

	}

	/**
	 * Method to decode a data array.
	 *
	 * @param   array  $data  The data array to decode.
	 *
	 * @return  array
	 *
	 * @since   11.1
	 */
	protected function decodeData(array $data)
	{
		$result = array();

		if (is_array($data[0]))
		{
			foreach ($data[0] as $k => $v)
			{
				$result[$k] = $this->decodeData(array($data[0][$k], $data[1][$k], $data[2][$k], $data[3][$k], $data[4][$k]));
			}
			return $result;
		}

		return array('name' => $data[0], 'type' => $data[1], 'tmp_name' => $data[2], 'error' => $data[3], 'size' => $data[4]);
	}

	/**
	 * Sets a value
	 *
	 * @param   string  $name   Name of the value to set.
	 * @param   mixed   $value  Value to assign to the input.
	 *
	 * @return  void
	 *
	 * @throws  InvalidArgumentException
	 *
	 * @since   12.2
	 */
	public function set($name, $value)
	{
		$pattern = array(
			'name' => 0,
			'type' => 0,
			'tmp_name' => 0,
			'error' => 0,
			'size' => 0
		);

		// We just consider the needed keys.
		$intersection = array_intersect_key((array) $value, $pattern);

		// If there are less keys (or value is not an array) we throw an exception.
		if (count($intersection) !== 5)
		{
			throw new InvalidArgumentException("The file's value is not formatted correctly.");
		}

		// If there are more keys we ignore them.
		$this->data[$name] = $intersection;
	}
}
