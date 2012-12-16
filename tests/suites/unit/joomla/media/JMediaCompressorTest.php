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
 * Test class for JMediaCompressor.
 * 
 * @package     Joomla.UnitTest
 * @subpackage  Media
 * 
 * @since       12.1
 */
class JMediaCompressorTest extends TestCase
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
	 * Test setUncompressed Method
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testSetUncompressed()
	{
		$random = rand();
		$this->object->setUncompressed($random);
		$this->assertAttributeEquals(strlen($random), 'uncompressedSize', $this->object);
		$this->assertAttributeEquals($random, 'uncompressed', $this->object);
		$this->object->clear();
	}

	/**
	 * Test getUncompressed Method
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testGetUncompressed()
	{
		$random = rand();
		$this->object->setUncompressed($random);
		$test = $this->object->getUncompressed();
		$this->assertEquals($random, $test);
		$this->object->clear();
	}

	/**
	 * Test setCompressed Method
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testSetCompressed()
	{
		$random = rand();
		$this->object->setCompressed($random);
		$this->assertAttributeEquals(strlen($random), 'compressedSize', $this->object);
		$this->assertAttributeEquals($random, 'compressed', $this->object);
		$this->object->clear();
	}

	/**
	 * Test getCompressed Method
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testGetCompressed()
	{
		$random = rand();
		$this->object->setCompressed($random);
		$this->assertEquals($random, $this->object->getCompressed());
		$this->object->clear();
	}

	/**
	 * Test setOptions and getOptions
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testSetOptions()
	{
		$existing_options = $this->object->getOptions();

		$expected = array('REMOVE_COMMENTS' => false, 'MIN_COLOR_CODES' => false, 'LIMIT_LINE_LENGTH' => false);

		$this->object->setOptions($expected);

		$test = $this->object->getOptions();

		foreach ($expected as $key => $value)
		{
			$this->arrayHasKey($key, $test);
			$this->assertEquals($value, $test[$key]);
		}
		// Replace the existed options to avoid any harm to other tests
		$this->object->setOptions($existing_options);

	}

	/**
	 * Test getRatio
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testGetRatio()
	{
		$this->object->setUncompressed("TestUncompressed");
		$this->object->setCompressed("TestCompressed");

		$expected = round((14 / 16) * 100, 2);
		$test = $this->object->getRatio();

		$this->assertEquals($expected, $test);
		$this->object->clear();
	}

	/**
	 * Test getCompressors Method
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testGetCompressors()
	{
		$expected = array('css','js');

		$test = JMediaCompressor::getCompressors();

		$this->assertEquals($expected, $test);

	}

	/**
	 * Test JMediaCompressor::compressString Method
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testCompressString()
	{
		// Get the file contents of comments.css file in css test files folder
		$sourceCss = $this->pathToTestFiles . DIRECTORY_SEPARATOR . 'comments.css';

		// Get the compressed contents in comment.min.css to compare with
		$expectedCss = file_get_contents(str_ireplace('.css', '.' . $this->suffix . '.css', $sourceCss));

		$testCss = JMediaCompressor::compressString(file_get_contents($sourceCss), array('type' => 'css'));

		$this->assertEquals($expectedCss, $testCss);

		// Get the file contents of case1.js file in js test files folder
		$sourceJs = JPATH_TESTS . DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . 'case1.js';

		// Get the compressed contents in case1.min.js to compare with
		$expectedJs = file_get_contents(str_ireplace('.js', '.' . $this->suffix . '.js', $sourceJs));

		$testJs = JMediaCompressor::compressString(file_get_contents($sourceJs), array('type' => 'js'));

		$this->assertEquals($expectedJs, $testJs);
	}

	/**
	 * Test JMediaCompressor::compressFile Method
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testCompressFile()
	{
		// Get the comments.css file in css test files folder
		$sourceCss = $this->pathToTestFiles . DIRECTORY_SEPARATOR . 'comments.css';

		$expectedFile = str_ireplace('.css', '.' . $this->suffix . '.css', $sourceCss);

		$destinationFile = str_ireplace('.css', '.' . $this->suffix . '.tmp.css', $sourceCss);

		$this->assertTrue(JMediaCompressor::compressFile($sourceCss, array('type' => 'css', 'OVERWRITE' => true), $destinationFile));

		$this->assertFileExists($destinationFile);

		$this->assertFileEquals($expectedFile, $destinationFile);

		unlink($destinationFile);

	}

	/**
	 * Test JMediaCompressor::getInstance Method
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testGetInstance()
	{
		$compressor1 = JMediaCompressor::getInstance(array('type' => 'css'));

		$this->assertInstanceOf('JMediaCompressorCss', $compressor1);

		$compressor2 = JMediaCompressor::getInstance(array('type' => 'js'));

		$this->assertInstanceOf('JMediaCompressorJs', $compressor2);
	}


	/**
	 * Test JMediaCompressor::isSupported Method
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function  testIsSupported()
	{
		$file1 = $this->pathToTestFiles . DIRECTORY_SEPARATOR . 'comments.css';

		$this->assertTrue(JMediaCompressor::isSupported($file1));

		$file2 = JPATH_TESTS . DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . 'case2.js';

		$this->assertTrue(JMediaCompressor::isSupported($file2));

		$this->assertFalse(JMediaCompressor::isSupported('index.php'));
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
		$this->object->setUncompressed("Compress This");
		$this->object->compress();
		$this->object->clear();
		$this->assertEquals(null, $this->object->getUncompressed());
		$this->assertEquals(null, $this->object->getcompressed());
		$this->assertAttributeEquals(null, 'compressedSize', $this->object);
		$this->assertAttributeEquals(null, 'uncompressedSize', $this->object);
	}

}
