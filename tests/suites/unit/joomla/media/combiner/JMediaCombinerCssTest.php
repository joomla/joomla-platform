<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Media
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

/**
 * Test class for JMediaCollectionCss.
 */
class JMediaCombinerCssTest extends TestCase
{
	/**
	* @var JMediaCollectionCss
	*/
	protected $object;

	protected $files;

	protected $pathToTestFiles;

	protected $suffix;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp()
	{
		$this->object = JMediaCollection::getInstance(array('type' => 'css'));
		$this->pathToTestFiles = JPATH_BASE . '/test_files/css';
		$this->loadFiles();
		$this->suffix = 'min';
	}

	/**
	 * Loads Necessary files
	 */
	protected function loadFiles()
	{
		//
		$this->files = glob($this->pathToTestFiles . DIRECTORY_SEPARATOR . '*.css');
	}


	public function testCombine()
	{
		$this->object->addFiles($this->files);

		$this->object->combine();

	}

}
