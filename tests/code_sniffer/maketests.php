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
class SnifferTestsMakeTests extends JCli
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

		$this->out('Building the Joomla! CodeSniffer Test Tests...');

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

				$this->out('Processing: '.$path.'...', false);

				$arguments = array();

				$arguments[] = '--report=xml';
				$arguments[] = '--standard=Joomla';
				$arguments[] = $path;

				$args = implode(' ', $arguments);

				$command = 'phpcs';

				$cmd = $command.' '.$args.' 2>&1 > '.$result;

				$results = shell_exec($cmd);

				if ($results)
				{
					// In case of an error..
					echo $results;
				}
				else
				{
					$this->out('OK');
				}
			}//foreach
		}//foreach

		$this->out('Finished =;)');
	}//function
}//class

// Instantiate the application object, passing the class name to JCli::getInstance
// and use chaining to execute the application.
try
{
	JCli::getInstance('SnifferTestsMakeTests')
	->execute();
}
catch (Exception $e)
{
	echo $e->getMessage();

	exit($e->getCode());
}//try
