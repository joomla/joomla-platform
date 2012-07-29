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
	public $_uncompressed = null;

	/**
     * @var    int  size of uncompressed Code.
     * @since  12.1
     */
	public $_uncompressedSize = null;

	/**
     * @var    String  To hold compressed Code.
     * @since  12.1
     */
	protected  $_compressed = null;

	/**
     * @var    int  size of compressed Code.
     * @since  12.1
     */
	public $_compressedSize = null;

	/**
     * @var    Array  Compression options for CSS Minifier.
     * @since  12.1
     */
	protected  $_options = array();

	/**
     * @var    array  JMediaCompressor instances container.
     * @since  11.1
     */
	protected static $instances = array();

	/**
     * Method to set uncompressed code.
     *
	 * @param   string  $uncompressed  Uncomressed Code.
     *
     * @return  void
     * 
     * @since  12.1
	*/
	public function setUncompressed($uncompressed)
	{
		$this->_uncompressed = $uncompressed;
		$this->_uncompressedSize	= strlen($this->_uncompressed);
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
		return $this->_uncompressed;
	}

	/**
     * Method to set uncompressed code.
     *
	 * @param   string  $compressed  compressed Code.
     *
     * @return  void
     *
     * @since  12.1
     */
	public function setCompressed($compressed)
	{
		$this->_compressed = $compressed;
		$this->_compressedSize	= strlen($this->_compressed);
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
		return $this->_compressed;
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
		// Merge user defined options with default options
		$this->_options = array_merge($options, $this->_options);
	}

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
		$this->_options = array_merge($options, $this->_options);
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
		return round(($this->_compressedSize / $this->_uncompressedSize * 100), 2);
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
		$types = JFolder::files(__DIR__ . '/compressor');

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
			jimport('joomla.media.compressor.' . $class);

			// If the class doesn't exist we have nothing left to do but look at the next type.  We did our best.
			if (!class_exists('JMediaCompressor' . ucfirst($class)))
			{
				continue;
			}

			// Sweet!  Our class exists, so now we just need to know if it passes its test method.
			if ($class::isSupported())
			{
				// Connector names should not have file extensions.
				$compressors[] = $class;
			}
		}

		return $compressors;
	}

	/**
     * Compress a CSS/JS file with given options
     *
	 * @param   string  $uncompressed  The full file path of the source file.
     * @param   array   $options       An asssociative array with options. Eg: force overwirte, prefix for minified files
     *
     * @return  string  compressed string
     *
     * @since  12.1
     */
	public static function compressString( $uncompressed, $options = array())
	{
		$compressor = self::getInstance($options);
		$compressor->setUncompressed($uncompressed);

		try
		{
			$compressor->compress();
		}
		catch (Exception $e)
		{
			return false;
		}
		return $compressor->getCompressed;
	}

	/**
	 * Compress a CSS/JS file with given options
	 *
	 * @param   string  $sourcefile   The full file path of the source file.
	 * @param   array   $options      An asssociative array with options. Eg: force overwirte, prefix for minified files
	 * @param   string  $destination  The full file path of the destination file. If left empty the compressed file will be returned as string
	 * 
	 * @return  boolean  false on failure.
	 *
	 * @since  12.1
	 */
	public static function compressFile( $sourcefile, $options = array(),  $destination = null )
	{
		$compressor = self::getInstance($options);
		$uncompressed = JFile::read($sourcefile);

		if ($destination === null)
		{
			$type = $extension = pathinfo($sourcefile, PATHINFO_EXTENSION);
			if (array_key_exists('PREFIX', $options) && !empty($options['PREFIX']))
			{
				$destination = str_ireplace('.' . $type, '.' . $options['PREFIX'] . '.' . $type, $sourcefile);
			}
			else
			{
				$destination = str_ireplace('.' . $type, '.min.' . $type, $sourcefile);
			}
		}

		if (!$uncompressed)
		{
			throw new JMediaException("Error reading the file (" . $sourcefile . ") contents");
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

		if (!JFile::exists($destination) || (JFile::exists($destination) && $force))
		{
			if (JFile::write($destination, $compressor->getCompressed()))
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

			// Load the class
			jimport('joomla.media.compressor.' . $class);

			// If the class still doesn't exist we have nothing left to do but throw an exception.  We did our best.
			if (!class_exists($class))
			{
				throw new RuntimeException(JText::sprintf('JMEDIA_ERROR_LOAD_COMPRESSOR', $options['type']));
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
			if (strtolower(str_ireplace('JMediaCompressor', '', $class)) === strtolower(JFile::getExt($sourceFile)))
			{
				return true;
			}
			else
			{
				return false;
			}
		}
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
		$this->_compressed = null;
		$this->_compressedSize = null;
		$this->_uncompressed = null;
		$this->_uncompressedSize = null;
	}
}
