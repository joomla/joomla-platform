<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Archive
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.filesystem.stream');

/**
 * Bzip2 format adapter for the JArchive class
 *
 * @package     Joomla.Platform
 * @subpackage  Archive
 * @since       11.1
 */
class JArchiveBzip2 implements JArchiveExtractable
{
	/**
	 * Bzip2 file data buffer
	 *
	 * @var    string
	 * @since  11.1
	 */
	private $_data = null;

	/**
	 * Constructor tries to load the bz2 extension if not loaded
	 *
	 * @since   11.1
	 */
	public function __construct()
	{
		self::loadExtension();
	}

	/**
	 * Extract a Bzip2 compressed file to a given path
	 *
	 * @param   string  $archive      Path to Bzip2 archive to extract
	 * @param   string  $destination  Path to extract archive to
	 * @param   array   $options      Extraction options [unused]
	 *
	 * @return  mixed  True if successful or exception
	 * @throws  RuntimeException
	 * @since   11.1
	 */
	public function extract($archive, $destination, array $options = array ())
	{
		// Initialise variables.
		$this->_data = null;

		if (!extension_loaded('bz2'))
		{
			throw new RuntimeException('The bz2 extension is not available.');
		}

		if (!isset($options['use_streams']) || $options['use_streams'] == false)
		{
			// Old style: read the whole file and then parse it
			if (!$this->_data = JFile::read($archive))
			{
				throw new RuntimeException('Unable to read archive');
			}

			$buffer = bzdecompress($this->_data);
			unset($this->_data);
			if (empty($buffer))
			{
				throw new RuntimeException('Unable to decompress data');
			}

			if (JFile::write($destination, $buffer) === false)
			{
				throw new RuntimeException('Unable to write archive');
			}

		}
		else
		{
			// New style! streams!
			$input = JFactory::getStream();

			// Use bzip
			$input->set('processingmethod', 'bz');

			if (!$input->open($archive))
			{
				throw new RuntimeException('Unable to read archive (bz2)');
			}

			$output = JFactory::getStream();

			if (!$output->open($destination, 'w'))
			{
				$input->close();
				throw new RuntimeException('Unable to write archive (bz2)');
			}

			do
			{
				$this->_data = $input->read($input->get('chunksize', 8196));
				if ($this->_data)
				{
					if (!$output->write($this->_data))
					{
						$input->close();
						throw new RuntimeException('Unable to write archive (bz2)');
					}
				}
			}
			while ($this->_data);

			$output->close();
			$input->close();
		}

		return true;
	}

	/**
	 * Tests whether this adapter can unpack files on this computer.
	 *
	 * @return  boolean  True if supported
	 *
	 * @since   11.3
	 */
	public static function isSupported()
	{
		self::loadExtension();

		return extension_loaded('bz2');
	}

	/**
	 * Load the bzip2 extension
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	private static function loadExtension()
	{
		// Is bz2 extension loaded?  If not try to load it
		if (!extension_loaded('bz2'))
		{
			if (JPATH_ISWIN)
			{
				@ dl('php_bz2.dll');
			}
			else
			{
				@ dl('bz2.so');
			}
		}
	}
}
