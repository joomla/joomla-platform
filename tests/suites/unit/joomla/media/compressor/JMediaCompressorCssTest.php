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
 * Test class for JMediaCompressorCss.
 *
 * @package     Joomla.UnitTest
 * @subpackage  Media
 *
 * @since       12.1
 */
class JMediaCompressorCssTest extends TestCase
{
	/**
	 * @var JMediaCompressor
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
		$this->object = JMediaCompressor::getInstance(array('type' => 'css'));
		$this->pathToTestFiles = JPATH_TESTS . DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR . 'css';
		$this->loadFiles();
		$this->suffix = 'min';
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
	 * Test compress Method
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testCompress()
	{
		foreach ($this->files as $file)
		{
			$this->object->setUncompressed(file_get_contents($file));

			// Getting the expected result from filename.min.css file.
			$expected = file_get_contents(str_ireplace('.css', '.' . $this->suffix . '.css', $file));

			$this->object->compress();

			$result = $this->object->getCompressed();

			$this->assertEquals($expected, $result);

			$this->object->clear();
		}

	}

	/**
	 * test clear Method
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testClear()
	{
		$sourceCss = $this->pathToTestFiles . DIRECTORY_SEPARATOR . 'comments.css';

		$this->object->setUncompressed(file_get_contents($sourceCss));
		$this->object->compress();
		$this->object->clear();
		$this->assertEquals(null, $this->object->getUncompressed());
		$this->assertEquals(null, $this->object->getcompressed());
		$this->assertAttributeEquals(null, 'compressedSize', $this->object);
		$this->assertAttributeEquals(null, 'uncompressedSize', $this->object);
	}
}
