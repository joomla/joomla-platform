#!/usr/bin/php
<?php
/**
 * A "hello world" command line application built on the Joomla Platform.
 *
 * To run this example, adjust the executable path above to suite your operating system,
 * make this file executable and run the file.
 *
 * Alternatively, run the file using:
 *
 * php -f run.php
 *
 * @package    Joomla.Examples
 * @copyright  Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

// We are a valid Joomla entry point.
// This is required to load the Joomla Platform import.php file.
define('_JEXEC', 1);

// Setup the base path related constant.
// This is one of the few, mandatory constants needed for the Joomla Platform.
define('JPATH_BASE', dirname(__FILE__));
define('JPATH_SITE', JPATH_BASE);

// Bootstrap the application.
require dirname(dirname(dirname(__FILE__))).'/libraries/import.php';


// Import the JCli class from the platform.
jimport('joomla.application.cli');

/**
 * A "hello world" command line application class.
 *
 * Simple command line applications extend the JCli class.
 *
 * @package  Joomla.Examples
 * @since    11.3
 */
class SnifferTestsRunTests extends JCli
{
	/**
	 * Execute the application.
	 *
	 * The 'execute' method is the entry point for a command line application.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function execute()
	{
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');

		$this->out('Running the Joomla! CodeSniffer Tests...');

		$folders = JFolder::folders(JPATH_BASE.'/tests');

		$files = array('good', 'bad');

		foreach ($folders as $folder)
		{
			foreach ($files as $file)
			{
				$path = 'tests/'.$folder.'/'.$file.'.php';
				$result = 'tests/'.$folder.'/'.$file.'.xml';

				if ( ! JFile::exists($path))
				{
					$this->out('File not found: '.$path);

					continue;
				}

				if ( ! JFile::exists($result))
				{
					$this->out('File not found: '.$result);

					continue;
				}

				$this->out('Processing: '.$path.'...', false);

				$arguments = array();

				$arguments[] = '--report=xml';
				$arguments[] = '--standard=Joomla';
				$arguments[] = $path;

				$args = implode(' ', $arguments);

				$command = 'phpcs';

				$cmd = $command.' '.$args.' 2>&1';

				$test = shell_exec($cmd);

				$xmlTest = $this->getXML($test, false);

				$xmlResult = $this->getXML($result);

				$resTest = $this->extract($xmlTest);
				$resResult = $this->extract($xmlResult);

				if ($resTest !== $resResult)
				{
					$this->out('*** ERROR ***');
				}
				else
				{
					$this->out('ok');
				}
			}//foreach
		}//foreach

		$this->out('... Finished =;)');
	}//function

	/**
	 * Extract information from a XML string.
	 *
	 * @param   string  $xml  The XML string
	 *
	 * @return string
	 */
	protected function extract($xml)
	{
		$res = '';

		if ( ! isset($xml->file->error))
		{
			return $res;
		}

		foreach ($xml->file->error as $error)
		{
			$res .= $error->asXml();
		}//foreach

		return $res;
	}//function

	/**
	 * Reads a XML file.
	 *
	 * This method has been "stolen" from JFactory.
	 * We don't use JError...
	 *
	 * @param   string   $data    Full path and file name.
	 * @param   boolean  $isFile  true to load a file or false to load a string.
	 *
	 * @return  object   JXMLElement on success or false on error.
	 *
	 * @see     JXMLElement
	 * @since   11.1
	 * @todo    This may go in a separate class - error reporting may be improved.
	 */
	protected function getXML($data, $isFile = true)
	{
		jimport('joomla.utilities.xmlelement');

		// Disable libxml errors and allow to fetch error information as needed
		libxml_use_internal_errors(true);

		if ($isFile)
		{
			// Try to load the XML file
			$xml = simplexml_load_file($data, 'JXMLElement');
		}
		else
		{
			// Try to load the XML string
			$xml = simplexml_load_string($data, 'JXMLElement');
		}

		if (empty($xml))
		{
			// There was an error
			$this->out(JText::_('JLIB_UTIL_ERROR_XML_LOAD'));

			if ($isFile)
			{
				$this->out('File: '.$data);
			}

			foreach (libxml_get_errors() as $error)
			{
				$this->out($error->message);
			}
		}

		return $xml;
	}//function

}//class

// Instantiate the application object, passing the class name to JCli::getInstance
// and use chaining to execute the application.
try
{
	JCli::getInstance('SnifferTestsRunTests')
	->execute();
}
catch (Exception $e)
{
	echo $e->getMessage();

	exit($e->getCode());
}//try
