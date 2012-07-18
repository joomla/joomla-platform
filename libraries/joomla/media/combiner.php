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

	protected $_combined = null;

	protected $_options = array();

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
		$this->_options = array_merge($options, $this->_options);
	}

	/**
	 * Gives a combiner object for CSS/JS
	 *
	 * @param   string  $type     Type of compressor needed
	 * @param   array   $options  options for the compressor
	 *
	 * @return  JMediaCombiner  Returns a JMediaCombiner object
	 *
	 * @since   12.1
	 */
	public static function getInstance($type, $options = array())
	{

		// Derive the class name from the type.
		$class = 'JMediaCombiner' . ucfirst(strtolower($type));

		// Load the class
		jimport('joomla.media.combiner.' . $class);

		// If the class still doesn't exist we have nothing left to do but throw an exception.  We did our best.
		if (!class_exists($class))
		{
			throw new RuntimeException(JText::sprintf('JMEDIA_ERROR_LOAD_COMBINOR', $type));
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

		return $instance;
	}

	/**
	 * 
	 * @param unknown_type $files
	 * @param unknown_type $options
	 * @param unknown_type $destination
	 * @throws RuntimeException
	 */
	public static function combineFiles($files, $options = array(), $destination = null)
	{
		$type = JFile::getExt($files[0]);

		foreach ($files as $file)
		{
			if ($type != JFile::getExt($file))
			{
				throw new RuntimeException(JText::sprintf('JMEDIA_COMBINE_ERROR_MULTIPLE_FILE_TYPES'), $type);
			}
		}

		$combiner = self::getInstance($type);

		$combiner->combine($files);
	}
}
