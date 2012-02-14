<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Error
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Utility class to assist in the process of benchmarking the execution
 * of sections of code to understand where time is being spent.
 *
 * @package     Joomla.Platform
 * @subpackage  Error
 * @since       11.1
 */
class JProfiler
{
	/**
	 * @var    integer  The start time.
	 * @since  11.1
	 */
	protected $start = 0;

	/**
	 * @var    string  The prefix to use in the output
	 * @since  11.1
	 */
	protected $prefix = '';

	/**
	 * @var    array  The buffer of profiling messages.
	 * @since  11.1
	 */
	protected $buffer = null;

	/**
	 * @var    float
	 * @since  11.1
	 */
	protected $previous_time = 0.0;

	/**
	 * @var    float
	 * @since  11.1
	 */
	protected $previous_mem = 0.0;

	/**
	 * @var    boolean  Boolean if the OS is Windows.
	 * @since  11.1
	 */
	protected $iswin = false;

	/**
	 * @var    array  JProfiler instances container.
	 * @since  11.3
	 */
	protected static $instances = array();

	/**
	 * Constructor
	 *
	 * @param   string  $prefix  Prefix for mark messages
	 *
	 * @since  11.1
	 */
	public function __construct($prefix = '')
	{
		$this->start = $this->getmicrotime();
		$this->prefix = $prefix;
		$this->buffer = array();
		$this->iswin = (substr(PHP_OS, 0, 3) == 'WIN');
	}

	/**
	 * magic get method
	 *
	 * @param   $propertyName  Property name
	 *
	 * @return  mixed  the property value
	 *
	 * @since   12.1
	 * @deprecated  12.3
	 */
	public function __get($propertyName)
	{
		if ($propertyName[0] == '_' && property_exists($this, $newPropertyName = substr($propertyName, 1)))
		{
			JLog::add(get_called_class() . '::$' . $propertyName . ' is deprecated. Use ' . get_called_class() . '::$'. $newPropertyName . ' instead.', JLog::WARNING, 'deprecated');
			return $this->$newPropertyName;
		}
		else
		{
			// Trigger an error
			return $this->$propertyName;
		}
	}

	/**
	 * magic set method
	 *
	 * @param   $propertyName  Property name
	 * @param   $value         Property name
	 *
	 * @return  void
	 *
	 * @since   12.1
	 * @deprecated  12.3
	 */
	public function __set($propertyName, $value)
	{
		if ($propertyName[0] == '_' && property_exists($this, $newPropertyName = substr($propertyName, 1)))
		{
			JLog::add(get_called_class() . '::$' . $propertyName . ' is deprecated. Use ' . get_called_class() . '::$'. $newPropertyName . ' instead.', JLog::WARNING, 'deprecated');
			$this->$newPropertyName = $value;
		}
		else
		{
			$this->$propertyName = $value;
		}
	}

	/**
	 * Returns the global Profiler object, only creating it
	 * if it doesn't already exist.
	 *
	 * @param   string  $prefix  Prefix used to distinguish profiler objects.
	 *
	 * @return  JProfiler  The Profiler object.
	 *
	 * @since   11.1
	 */
	public static function getInstance($prefix = '')
	{
		if (empty(self::$instances[$prefix]))
		{
			self::$instances[$prefix] = new JProfiler($prefix);
		}

		return self::$instances[$prefix];
	}

	/**
	 * Output a time mark
	 *
	 * The mark is returned as text enclosed in <div> tags
	 * with a CSS class of 'profiler'.
	 *
	 * @param   string  $label  A label for the time mark
	 *
	 * @return  string  Mark enclosed in <div> tags
	 *
	 * @since   11.1
	 */
	public function mark($label)
	{
		$current = self::getmicrotime() - $this->start;
		if (function_exists('memory_get_usage'))
		{
			$current_mem = memory_get_usage() / 1048576;
			$mark = sprintf(
				'<code>%s %.3f seconds (+%.3f); %0.2f MB (%s%0.3f) - %s</code>',
				$this->prefix,
				$current,
				$current - $this->previous_time,
				$current_mem,
				($current_mem > $this->previous_mem) ? '+' : '', $current_mem - $this->previous_mem,
				$label
			);
		}
		else
		{
			$mark = sprintf('<code>%s %.3f seconds (+%.3f) - %s</code>', $this->prefix, $current, $current - $this->previous_time, $label);
		}

		$this->previous_time = $current;
		$this->previous_mem = $current_mem;
		$this->buffer[] = $mark;

		return $mark;
	}

	/**
	 * Get the current time.
	 *
	 * @return  float The current time
	 *
	 * @since   11.1
	 */
	public static function getmicrotime()
	{
		list ($usec, $sec) = explode(' ', microtime());

		return ((float) $usec + (float) $sec);
	}

	/**
	 * Get information about current memory usage.
	 *
	 * @return  integer  The memory usage
	 *
	 * @link    PHP_MANUAL#memory_get_usage
	 * @since   11.1
	 */
	public function getMemory()
	{
		if (function_exists('memory_get_usage'))
		{
			return memory_get_usage();
		}
		else
		{
			// Initialise variables.
			$output = array();
			$pid = getmypid();

			if ($this->iswin)
			{
				// Windows workaround
				@exec('tasklist /FI "PID eq ' . $pid . '" /FO LIST', $output);
				if (!isset($output[5]))
				{
					$output[5] = null;
				}
				return substr($output[5], strpos($output[5], ':') + 1);
			}
			else
			{
				@exec("ps -o rss -p $pid", $output);
				return $output[1] * 1024;
			}
		}
	}

	/**
	 * Get all profiler marks.
	 *
	 * Returns an array of all marks created since the Profiler object
	 * was instantiated.  Marks are strings as per {@link JProfiler::mark()}.
	 *
	 * @return  array  Array of profiler marks
	 */
	public function getBuffer()
	{
		return $this->_buffer;
	}
}
