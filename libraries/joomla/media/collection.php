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
 * SuperClass for Javascript/CSS combiner classes.
 *
 * @package     Joomla.Platform
 * @subpackage  Media
 * @since       12.1 
 */
abstract class JMediaCollection
{
	public $sources = array();

	public $sourceCount = 0;

	protected $combined = null;

	protected $options = array();

	/**
	 * @var    array  JMediaCollection instances container.
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
		$this->options = array_merge($this->options, $options);
	}

	/**
	 * Method to combine content of a set of files.
	 *
	 * @return  Void
	 *
	 * @since  12.1
	 */
	public abstract function combine();

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
		$prevSignature = md5(serialize($this->options));

		// Merge old options with new options
		$this->options = array_merge($this->options, $options);

		$newSignature = md5(serialize($this->options));

		if (strcmp($prevSignature, $newSignature) !== 0)
		{
			// Remove old signature from instance array
			unset(self::$instances[$prevSignature]);

			// Set new instance signature
			if (!array_key_exists($newSignature, self::$instances))
			{
				self::$instances[$newSignature] = $this;
			}
		}
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
	public function addFiles($files =array())
	{
		// Get combiner object type
		$type = $this->options['type'];

		foreach ($files as $file)
		{
			// Check file ext for compatibility
			if (pathinfo($file, PATHINFO_EXTENSION) == $type)
			{
				if (!file_exists($file))
				{
					throw new RuntimeException(sprintf("%s File not exists", $file));
				}
				// Check whether file already registered
				if (!in_array($file, $this->sources))
				{
					$this->sources[] = $file;
					$this->sourceCount++;
				}
			}
			else
			{
				throw new RuntimeException(sprintf("Multiple File types detected in files array. %s", $type));
			}

		}
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
	 * @throws  RuntimeException
	 *
	 * @since 12.1
	 */
	public static function combineFiles($files, $options = array(), $destination = null)
	{
		// Detect file type
		$type = pathinfo($files[0], PATHINFO_EXTENSION);

		if (!self::isSupported($files[0]))
		{
			throw new RuntimeException(sprintf("Error Loading Collection class for %s file type", $type));
		}

		// Checks for the destination
		if ($destination === null)
		{
			$type = $extension = pathinfo($files[0], PATHINFO_EXTENSION);

			// Check for the file prefix in options, assign default prefix if not found
			if (array_key_exists('PREFIX', $options) && !empty($options['PREFIX']))
			{
				$destination = str_ireplace('.' . $type, '.' . $options['PREFIX'] . '.' . $type, $files[0]);
			}
			else
			{
				$destination = str_ireplace('.' . $type, '.combined.' . $type, $files[0]);
			}
		}

		$options['type'] = (!empty($options['type'])) ? $options['type'] : $type;

		$combiner = self::getInstance($options);

		$combiner->addFiles($files);

		$combiner->combine();

		if (!empty($combiner->combined))
		{
			$force = array_key_exists('OVERWRITE', $options) && !empty($options['OVERWRITE']) ? $options['OVERWRITE'] : false;

			if (!file_exists($destination) || (file_exists($destination) && $force))
			{
				file_put_contents($destination, $combiner->getCombined());
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
	 * Method to get source files
	 * 
	 * @return  array  Source File
	 * 
	 * @since   12.1
	 */
	public function getSources()
	{
		return $this->sources;
	}

	/**
	 * Method to get combined string
	 * 
	 * @return  String  Combined String
	 */
	public function getCombined()
	{
		if ($this->combined == null)
		{
			$this->combine();
		}
		return  $this->combined;
	}

	/**
	 * Get a list of available collection classes
	 *
	 * @return  array  An array of available collection classes
	 *
	 * @since   12.1
	 */
	public static function getCollectionTypes()
	{
		// Instantiate variables.
		$collectionTypes = array();

		// Get a list of types.
		$types = glob(__DIR__ . '/collection/*');

		// Loop through the types and find the ones that are available.
		foreach ($types as $type)
		{
			$type = basename($type);

			// Ignore some files.
			if ($type == 'index.html')
			{
				continue;
			}

			// Derive the class name from the type.
			$class = str_ireplace('.php', '', trim($type));

			// If the class doesn't exist we have nothing left to do but look at the next type.  We did our best.
			if (!class_exists('JMediaCollection' . ucfirst($class)))
			{
				continue;
			}

			// Combiner names should not have file extensions.
			$collectionTypes[] = $class;

		}

		return $collectionTypes;
	}

	/**
	 * Method to get options
	 *
	 * @return  array  Options for the collection object
	 *
	 * @since   12.1
	 */
	public function getOptions()
	{
		return $this->options;
	}

	/**
	 * Gives a collection object for CSS/JS
	 *
	 * @param   array  $options  options for the compressor
	 *
	 * @return  JMediaCollection  Returns a JMediaCollection object
	 *
	 * @throws  RuntimeException
	 *
	 * @since   12.1
	 */
	public static function getInstance( $options = array())
	{

		// Get the options signature for the instance
		$signature = md5(serialize($options));

		// If we already have a Collection instance for these options then just use that.
		if (empty(self::$instances[$signature]))
		{
			// Derive the class name from the type.
			$class = 'JMediaCollection' . ucfirst(strtolower($options['type']));

			// If the class still doesn't exist we have nothing left to do but throw an exception.  We did our best.
			if (!class_exists($class))
			{
				throw new RuntimeException(sprintf("Error Loading Collection class for %s file type", $options['type']));
			}

			// Modify the signature with all the options
			$signature = md5(serialize(array_merge($class::$DEFAULT_OPTIONS, $options)));

			if (!empty(self::$instances[$signature]))
			{
				return self::$instances[$signature];
			}
			// Create our new JMediaCompressor class based on the options given.
			try
			{
				$instance = new $class($options);
			}
			catch (RuntimeException $e)
			{
				throw new RuntimeException(sprintf("Error Loading Collection class for %s file type", $e->getMessage()));
			}

			// Set the new combiner to the global instances based on signature.
			self::$instances[$signature] = $instance;
		}
		else
		{
			$instance = self::$instances[$signature];
			$instance->clear();
		}

		return self::$instances[$signature];
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
		$collectionTypes = self::getCollectionTypes();

		foreach ($collectionTypes as $class)
		{
			if (strtolower(str_ireplace('JMediaCollection', '', $class)) === strtolower(pathinfo($sourceFile, PATHINFO_EXTENSION)))
			{
				return true;
			}
		}

		return true;
	}

	/**
	 * Method to clear combiner data
	 *
	 * @return  void
	 *
	 * @since  12.1
	 */
	public function clear()
	{
		$this->sources = array();
		$this->sourceCount = 0;
		$this->combined = null;
	}
}
