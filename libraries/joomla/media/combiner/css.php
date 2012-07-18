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
 * CSS combiner classes.
 *
 * @package     Joomla.Platform
 * @subpackage  Media
 * @since       12.1 
 */
class JMediaCombinerCss extends JMediaCombiner
{
	/**
	 * Constructor of css combiner
	 * 
	 * @param   Array  $options  options
	 * 
	 * @since  12.1 
	 */
	public function __construct($options = array())
	{
		$this->_options = array('COMPRESS' => false);
		parent::__construct($options);
	}

	/**
	 * Method to combine a set of files and save to single file.
	 * 
	 * @param   Array   $files        Paths of files to combine.
	 * @param   string  $destination  Path to the destination file.
	 *
	 * @return  string  Combined css code
	 * 
	 * @since  12.1
	 */
	public function combine($files, $destination = null)
	{
		foreach ($files as $file)
		{
			$this->_combined .= JFile::read($file) . "\n\n";
		}

		if ($this->_options['COMPRESS'])
		{
			$compressor = JMediaCompressorCss::getInstance('css', $this->_options);
			$compressor->setUncompressed($this->_combined);
			$compressor->compress();

			$this->_combined = $compressor->getCompressed();
		}

		return $this->_combined;
	}
}
