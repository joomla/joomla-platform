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
 *
 * @package     Joomla.UnitTest
 * @subpackage  Media
 *
 * @since       12.1
 */
class JMediaCollectionCssTest extends TestCase
{
	/**
	* @var JMediaCollectionCss
	*/
	protected $object;

	/**
	 * @var  array  files needed for tests
	 */
	protected $files;

	/**
	 * @var  string  path to test files
	 */
	protected $pathToTestFiles;

	/**
	 * @var  string  file extension suffix for compressed files
	 */
	protected $suffix;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	protected function setUp()
	{
		$this->object = JMediaCollection::getInstance(array('type' => 'css'));
		$this->pathToTestFiles = JPATH_TESTS . DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR . 'css';
		$this->loadFiles();
		$this->suffix = 'combined';
	}

	/**
	 * Loads Necessary files
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	protected function loadFiles()
	{
		// Load only non compressed css files in to array
		// Skip other files
		$this->files = JFolder::files(
			$this->pathToTestFiles, '.', false, true, array(),
			array('.min.css', '.php', '.html', '.combined.css')
		);
	}
	/**
	 * Test JMediaCollection::combineFiles() Method
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testCombineFiles()
	{
		$this->object->addFiles($this->files);

		// Path to expected combined file without compression turned on
		$expected = file_get_contents($this->pathToTestFiles . DIRECTORY_SEPARATOR . 'all.combined.css');

		$this->object->combine();

		$this->assertEquals($expected, $this->object->getCombined());

		// Path to expected combined file with compression turned on
		$expectedCompressed = file_get_contents($this->pathToTestFiles . DIRECTORY_SEPARATOR . 'all.combined.min.css');

		$this->object->setOptions(array('COMPRESS' => true));

		$this->object->combine();

		// Assert with compression turned on
		$this->assertEquals($expectedCompressed, $this->object->getCombined());
	}


}
