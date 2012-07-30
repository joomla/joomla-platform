<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Media
 * 
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Interface for Javascript/CSS combiner classes.
 *
 * @package     Joomla.Platform
 * @subpackage  Media
 * @since       12.1 
 */
abstract class JMediaCombiner
{
	public $sources = array();

	public $sourceCount = 0;

	protected $_combined = null;

	protected $_options = array();

	/**
	 * @var    array  JMediaCombiner instances container.
	 * @since  11.1
	 */
	protected static $instances = array();

	/**
	 * Constructor
	 * 
	 * @param   Array  $options  options for the combiner
	 * 
	 * @since   12.1 
	 */
	public function __construct($options = array())
	{
		// Merge user defined options with default options
		$this->_options = array_merge($this->_options, $options);
	}

	/**
	 * Method to set combiner options.
	 *
	 * @param   Array  $options  options to combiner.
	 *
	 * @return  void
	 *
	 * @since  12.1
	 */
	public function setOptions($options)
	{
		// Merge user defined options with default options
		$this->_options = array_merge($this->_options, $options);
	}

	/**
	 * Method to set source files to combine
	 * 
	 * @param   array  $files  array of source files
	 * 
	 * @throws RuntimeException
	 * 
	 * @return  void
	 * 
	 * @since  12.1
	 */
	public function setSources($files =array())
	{
		// Get combiner object type
		$type = $this->_options['type'];

		foreach ($files as $file)
		{
			// Check file ext for compability
			if (JFile::getExt($file) == $type)
			{
				$this->sources[] = $file;
				$this->sourceCount++;
			}
			else
			{
				throw new RuntimeException(JText::sprintf('JMEDIA_COMBINE_ERROR_MULTIPLE_FILE_TYPES'), $type);
			}

		}
	}

	/**
	 * Method to get combined string
	 * 
	 * @return  String  Combined String
	 */
	public function getCombined()
	{
		return  $this->_combined;
	}

	/**
	 * Get a list of available combiners
	 *
	 * @return  array  An array of available combiners
	 *
	 * @since   12.1
	 */
	public static function getCombiners()
	{
		// Instantiate variables.
		$combiners = array();

		// Get a list of types.
		$types = JFolder::files(__DIR__ . '/combiner');

		// Loop through the types and find the ones that are available.
		foreach ($types as $type)
		{
			// Ignore some files.
			if ($type == 'index.html')
			{
				continue;
			}

			// Derive the class name from the type.
			$class = str_ireplace('.php', '', trim($type));

			// Load the class
			jimport('joomla.media.combiner.' . $class);

			// If the class doesn't exist we have nothing left to do but look at the next type.  We did our best.
			if (!class_exists('JMediaCombiner' . ucfirst($class)))
			{
				continue;
			}

			// Combiner names should not have file extensions.
			$combiners[] = $class;

		}

		return $combiners;
	}

	/**
	 * Method to get combiner options
	 *
	 * @return  array  Options for the combinor
	 *
	 * @since   12.1
	 */
	public function getOptions()
	{
		return $this->_options;
	}

	/**
	 * Gives a combiner object for CSS/JS
	 *
	 * @param   array  $options  options for the compressor
	 *
	 * @return  JMediaCombiner  Returns a JMediaCombiner object
	 *
	 * @since   12.1
	 */
	public static function getInstance( $options = array())
	{

		// Get the options signature for the database connector.
		$signature = md5(serialize($options));

		// If we already have a database connector instance for these options then just use that.
		if (empty(self::$instances[$signature]))
		{
			// Derive the class name from the type.
			$class = 'JMediaCompressor' . ucfirst(strtolower($options['type']));

			// Load the class
			jimport('joomla.media.compressor.' . $class);

			// If the class still doesn't exist we have nothing left to do but throw an exception.  We did our best.
			if (!class_exists($class))
			{
				throw new RuntimeException(JText::sprintf('JMEDIA_ERROR_LOAD_COMBINER', $options['type']));
			}

			// Create our new JMediaCompressor class based on the options given.
			try
			{
				$instance = new $class($options);
			}
			catch (RuntimeException $e)
			{
				throw new RuntimeException(JText::sprintf('JLIB_DATABASE_ERROR_CONNECT_DATABASE', $e->getMessage()));
			}

			// Set the new combinerr to the global instances based on signature.
			self::$instances[$signature] = $instance;
		}

		return self::$instances[$signature];
	}

	/**
	 * Static method to get a set of files combined
	 * 
	 * @param   array   $files        Set of source files
	 * @param   array   $options      Options for combiner
	 * @param   string  $destination  Destination file
	 * 
	 * @return  boolean  True on success
	 * 
	 * @since  12.1
	 */
	public static function combineFiles($files, $options = array(), $destination = null)
	{
		// Detect file type
		$type = JFile::getExt($files[0]);

		if (!self::isSupported($files[0]))
		{
			throw new RuntimeException(JText::sprintf('JMEDIA_ERROR_FILE_TYPE_NOT_SUPPORTED'));
		}

		// Checks for the destination
		if ($destination === null)
		{
			$type = $extension = pathinfo($files[0], PATHINFO_EXTENSION);

			// Check for the file prefix in options, assign default prefix if not dound
			if (array_key_exists('PREFIX', $options) && !empty($options['PREFIX']))
			{
				$destination = str_ireplace('.' . $type, '.' . $options['PREFIX'] . '.' . $type, $files[0]);
			}
			else
			{
				$destination = str_ireplace('.' . $type, '.combined.' . $type, $files[0]);
			}
		}

		$options['type'] = $type;

		$combiner = self::getInstance($options);

		$combiner->setSources($files);

		if (!empty($combiner->_combined))
		{
			$force = array_key_exists('overwrite', $options) && !empty($options['overwrite']) ? $options['overwrite'] : false;

			if (!JFile::exists($destination) || (JFile::exists($destination) && $force))
			{
				JFile::write($destination, $combiner->getCombined());
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}

	/**
	 * Method to test if supported
	 *
	 * @param   string  $sourceFile  file to test
	 *
	 * @return  boolean   true or false
	 *
	 * @since  12.1
	 */
	public static function isSupported($sourceFile)
	{
		$combiners = self::getCombiners();

		foreach ($combiners as $class)
		{
			if (strtolower(str_ireplace('JMediaCombiner', '', $class)) === strtolower(JFile::getExt($sourceFile)))
			{
				return true;
			}
		}

		return true;
	}
}
