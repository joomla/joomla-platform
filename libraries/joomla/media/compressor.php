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
 * Javascript and CSS Compressor Class.
 *
 * @package     Joomla.Platform
 * @subpackage  Media
 * @since       12.1
 */
abstract class JMediaCompressor
{
	/**
	 * @var    String  To hold uncompressed Code.
	 * @since  12.1
	 */
	public $uncompressed = null;

	/**
	 * @var    int  size of uncompressed Code.
	 * @since  12.1
	 */
	public $uncompressedSize = null;

	/**
	 * @var    String  To hold compressed Code.
	 * @since  12.1
	 */
	protected  $compressed = null;

	/**
	 * @var    int  size of compressed Code.
	 * @since  12.1
	 */
	public $compressedSize = null;

	/**
	 * @var    Array  Compression options for CSS Minifier.
	 * @since  12.1
	 */
	protected  $options = array();

	/**
	 * @var    array  JMediaCompressor instances container.
	 * @since  11.1
	 */
	protected static $instances = array();

	/**
	 * Object Constructor takes two parameters.
	 *
	 * @param   Array  $options  Compression options for Minifier.
	 *
	 * @since  12.1
	 */
	public function __construct($options = array())
	{
		// Merge user defined options with default options
		$this->options = array_merge($this->options, $options);
	}

	/**
	 * Method to compress the code.
	 *
	 * @return   Void
	 *
	 * @since  12.1
	 */
	public abstract function compress();

	/**
	 * Method to set uncompressed code.
	 *
	 * @param   string  $uncompressed  Uncompressed Code.
	 *
	 * @return  void
	 *
	 * @since  12.1
	 */
	public function setUncompressed($uncompressed)
	{
		$this->uncompressed = $uncompressed;
		$this->uncompressedSize	= strlen($this->uncompressed);
	}

	/**
	 * Method to get uncompressed code.
	 *
	 * @return  String  uncompressed code.
	 *
	 * @since  12.1
	 */
	public function getUncompressed()
	{
		return $this->uncompressed;
	}

	/**
	 * Method to set compressed code.
	 *
	 * @param   string  $compressed  compressed Code.
	 *
	 * @return  void
	 *
	 * @since  12.1
	 */
	public function setCompressed($compressed)
	{
		$this->compressed = $compressed;
		$this->compressedSize	= strlen($this->compressed);
	}

	/**
	 * Method to get compressed code.
	 *
	 * @return  String  compressed code.
	 *
	 * @since  12.1
	 */
	public function getCompressed()
	{
		if ($this->compressed === null && $this->uncompressed != null)
		{
			$this->compress();
		}
		return $this->compressed;
	}

	/**
	 * Method to set compression options.
	 *
	 * @param   Array  $options  options to compress.
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
	 * Method to get compressed ratio.
	 *
	 * @return  double  Compressed ratio.
	 *
	 * @since  12.1
	 */
	public function getRatio()
	{
		return round(($this->compressedSize / $this->uncompressedSize * 100), 2);
	}

	/**
	 * Get a list of available compressors
	 *
	 * @return  array  An array of available compressors
	 *
	 * @since   11.1
	 */
	public static function getCompressors()
	{
		// Instantiate variables.
		$compressors = array();

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
			if (!class_exists('JMediaCompressor' . ucfirst($class)))
			{
				continue;
			}

			// Compressor names should not have file extensions.
			$compressors[] = $class;

		}

		return $compressors;
	}

	/**
	 * Method to get compressor options
	 * 
	 * @return  array  Options for the compressor
	 * 
	 * @since   12.1
	 */
	public function getOptions()
	{
		return $this->options;
	}

	/**
	 * Compress a CSS/JS file with given options
	 *
	 * @param   string  $uncompressed  The String to be compressed
	 * @param   array   $options       An associative array with options. Eg: type, force overwrite, prefix for minimised files
	 *
	 * @return  string  compressed string
	 *
	 * @throws  RuntimeException
	 *
	 * @since  12.1
	 */
	public static function compressString( $uncompressed, $options)
	{
		if (!array_key_exists('type', $options))
		{
			throw new RuntimeException(sprintf("File Type is not defined in options array"));
		}
		$compressor = self::getInstance($options);
		$compressor->clear();
		$compressor->setUncompressed($uncompressed);

		try
		{
			$compressor->compress();
		}
		catch (Exception $e)
		{
			return false;
		}
		return $compressor->getCompressed();
	}

	/**
	 * Compress a CSS/JS file with given options
	 *
	 * @param   string  $sourceFile   The full file path of the source file.
	 * @param   array   $options      An associative array with options. Eg: type, force overwrite, prefix for minified files
	 * @param   string  $destination  The full file path of the destination file. If left empty the compressed file will be returned as string
	 * 
	 * @return  boolean  false on failure.
	 *
	 * @throws  RuntimeException
	 *
	 * @since  12.1
	 */
	public static function compressFile( $sourceFile, $options = array(),  $destination = null )
	{
		$options['type'] = strtolower(pathinfo($sourceFile, PATHINFO_EXTENSION));

		if (!self::isSupported($sourceFile))
		{
			throw new RuntimeException(sprintf("The file type of the source file - %s is not supported by the Compressors", $options['type']));
		}
		$compressor = self::getInstance($options);
		$uncompressed = file_get_contents($sourceFile);

		if ($destination === null)
		{
			$type = $extension = pathinfo($sourceFile, PATHINFO_EXTENSION);
			if (array_key_exists('PREFIX', $options) && !empty($options['PREFIX']))
			{
				$destination = str_ireplace('.' . $type, '.' . $options['PREFIX'] . '.' . $type, $sourceFile);
			}
			else
			{
				$destination = str_ireplace('.' . $type, '.min.' . $type, $sourceFile);
			}
		}

		if (!$uncompressed)
		{
			throw new RuntimeException("Error reading the file (" . $sourceFile . ") contents");
		}

		$compressor->setUncompressed($uncompressed);

		try
		{
			$compressor->compress();
		}
		catch (Exception $e)
		{
			return false;
		}

		// Sets force overwrite option
		$force = array_key_exists('overwrite', $options) && !empty($options['overwrite']) ? $options['overwrite'] : false;

		if (!file_exists($destination) || (file_exists($destination) && $force))
		{
			if (file_put_contents($destination, $compressor->getCompressed()))
			{
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
	 * Gives a compressor object for CSS/JS
	 * 
	 * @param   array  $options  options for the compressor
	 * 
	 * @return  JMediaCompressor  Returns a JMediaCompressor object
	 *
	 * @throws  RuntimeException
	 * 
	 * @since   12.1
	 */
	public static function getInstance($options = array())
	{

		// Get the options signature for the compressor.
		$signature = md5(serialize($options));

		// If we already have a compressor instance for these options then just use that.
		if (empty(self::$instances[$signature]))
		{
			// Derive the class name from the type.
			$class = 'JMediaCompressor' . ucfirst(strtolower($options['type']));

			// If the class still doesn't exist we have nothing left to do but throw an exception.  We did our best.
			if (!class_exists($class))
			{
				throw new RuntimeException(sprintf("Error Loading Compressor class for %s file type", $options['type']));
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
				throw new RuntimeException(sprintf("Error Loading Collection class for %s file type", $options['type']));
			}

			// Set the new connector to the global instances based on signature.
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
		$compressors = self::getCompressors();

		foreach ($compressors as $class)
		{
			if (strtolower(str_ireplace('JMediaCompressor', '', $class)) === strtolower(pathinfo($sourceFile, PATHINFO_EXTENSION)))
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Method to clear compressor data
	 * 
	 * @return  void
	 * 
	 * @since  12.1
	 */
	public function clear()
	{
		$this->compressed = null;
		$this->compressedSize = null;
		$this->uncompressed = null;
		$this->uncompressedSize = null;
	}
}
