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
class JMediaCollectionCss extends JMediaCollection
{
	/**
	 * Constructor
	 *
	 * @param   Array  $options  options
	 *
	 * @since   12.1
	 */
	public function __construct($options = array())
	{
		$this->options = array('COMPRESS' => false, 'FILE_COMMENTS' => true, 'COMPRESS_OPTIONS' => array());
		parent::__construct($options);
	}

	/**
	 * Method to combine content of a set of css files
	 * 
	 * @return  Void
	 * 
	 * @since  12.1
	 */
	public function combine()
	{
		$this->combined = '';

		foreach ($this->sources as $file)
		{
			if ($this->options['FILE_COMMENTS'])
			{
				$this->combined .= '/** File : ' . JFile::getName($file) . ' : Start **/' . "\n\n";
			}

			if ($this->options['COMPRESS'])
			{
				$this->options['COMPRESS_OPTIONS']['type'] = 'css';

				if ($this->options['COMPRESSOR'] != null && $this->options['COMPRESSOR']->isSupported($file))
				{
					$compressor = $this->options['COMPRESSOR'];
					$compressor->setUncompressed(file_get_contents($file));
					$compressor->compress();

					$this->combined .= $compressor->getCompressed();
				}
				else
				{
					$this->combined .= JMediaCompressor::compressString(JFile::read($file), $this->options['COMPRESS_OPTIONS']) . "\n\n";
				}
			}
			else
			{
				$this->combined .= JFile::read($file) . "\n\n";
			}

			if ($this->options['FILE_COMMENTS'])
			{
				$this->combined .= '/** File : ' . JFile::getName($file) . ' : End **/' . "\n\n";
			}
		}

		$this->combined .= '/** ' . $this->sourceCount . ' css files are combined **/';

	}
}
