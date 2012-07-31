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
 * Javascript combiner classe.
 *
 * @package     Joomla.Platform
 * @subpackage  Compress
 * @since  12.1 
 */
class JMediaCombinerJs extends JMediaCombiner
{
	
	public function __construct($options = array())
	{
		$this->_options = array('COMPRESS' => false, 'FILE_COMMENTS' => true, 'COMPRESS_OPTIONS' => array());
		parent::__construct($options);
	}
	
	/**
	 * Method to combine a set of files and save to single file.
	 * 
	 * @param  Array   $files        Paths of files to combine.
	 * @param  string  $destination  Path to the destination file.
	 *
	 * @since  12.1
	 */
	public function combine()
	{
		foreach ($this->sources as $file)
		{
			if($this->_options['FILE_COMMENTS'])
			{
				$this->_combined .= '// File : ' . JFile::getName($file) . ' : Start' . "\n\n";
			}
			
			if ($this->_options['COMPRESS'])
			{
				$this->_options['COMPRESS_OPTIONS']['type'] = 'js';
					
				$this->_combined .= JMediaCompressor::compressString(JFile::read($file), $this->_options['COMPRESS_OPTIONS']) . "\n\n";
			}
			else 
			{
				$this->_combined .= JFile::read($file)."\n\n";
			}
			
			if($this->_options['FILE_COMMENTS'])
			{
				$this->_combined .= '// File : ' . JFile::getName($file) . ' : End' . "\n\n";
			}
		}

		$this->_combined .= '// ' . $this->sourceCount . ' js files are combined';
	}
}
