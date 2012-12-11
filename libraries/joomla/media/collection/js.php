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
 * Javascript combiner class.
 *
 * @package     Joomla.Platform
 * @subpackage  Compress
 * @since       12.1
 */
class JMediaCollectionJs extends JMediaCollection
{

	public static $DEFAULT_OPTIONS = array('COMPRESS' => false, 'FILE_COMMENTS' => true, 'COMPRESS_OPTIONS' => array(), 'COMPRESSOR' => null);

	/**
	 * Constructor
	 *
	 * @param   Array  $options  options
	 *
	 * @since   12.1
	 */
	public function __construct($options = array())
	{
		$this->options = self::$DEFAULT_OPTIONS;
		parent::__construct($options);
	}

	/**
	 * Method to combine content of a set of js files
	 *
	 * @since  12.1
	 *
	 * @return	void
	 */
	public function combine()
	{
		$this->combined = '';

		foreach ($this->sources as $file)
		{
			if ($this->options['FILE_COMMENTS'])
			{
				$this->combined .= '/** File : ' . basename($file) . ' : Start **/' . "\n\n";
			}

			if ($this->options['COMPRESS'])
			{
				$this->options['COMPRESS_OPTIONS']['type'] = 'js';

				if ($this->options['COMPRESSOR'] != null && $this->options['COMPRESSOR']->isSupported($file))
				{
					$compressor = $this->options['COMPRESSOR'];
					$compressor->setUncompressed(file_get_contents($file));
					$compressor->compress();

					$this->combined .= $compressor->getCompressed();
				}
				else
				{
					$this->combined .= JMediaCompressor::compressString(file_get_contents($file), $this->options['COMPRESS_OPTIONS']) . "\n\n";
				}
			}
			else
			{
				$this->combined .= file_get_contents($file) . "\n\n";
			}

			if ($this->options['FILE_COMMENTS'])
			{
				$this->combined .= '/** File : ' . basename($file) . ' : End **/' . "\n\n";
			}
		}

		$this->combined .= '/** ' . $this->sourceCount . ' js files are combined **/';
	}
}
