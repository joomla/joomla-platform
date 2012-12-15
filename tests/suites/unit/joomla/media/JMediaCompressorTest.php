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

	protected $files;

	protected $pathToTestFiles;

	protected $suffix;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp()
	{
		$this->object = JMediaCompressor::getInstance(array('type' => 'css'));
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

	/**
	 * Test getInstance Method
	 */
	public function testGetInstance()
	{
		$compressor1 = JMediaCompressor::getInstance(array('type' => 'css'));

		$this->assertInstanceOf('JMediaCompressorCss', $compressor1);

		$compressor2 = JMediaCompressor::getInstance(array('type' => 'js'));

		$this->assertInstanceOf('JMediaCompressorJs', $compressor2);
	}

	/**
	 * Test setCompressed Method
	 */
	public function testSetCompressed()
	{
		$random = rand();
		$this->object->setCompressed($random);
		$test = $this->object->getCompressed();
		$this->assertEquals($random, $test);
		$this->object->clear();
	}

	/**
	 * Test setUncompressed Method
	 */
	public function testSetUncompressed()
	{
		$random = rand();
		$this->object->setUncompressed($random);
		$test = $this->object->getUncompressed();
		$this->assertEquals($random, $test);
		$this->assertAttributeEquals($random, 'uncompressed', $this->object);
	}

	/**
	 * Test getRatio
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
	 */
	public function testGetCompressors()
	{
		$expected = array('css','js');

		$test = JMediaCompressor::getCompressors();

		$this->assertEquals($expected, $test);

	}

	/**
	 * Test setOptions
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
	 * Test compressString Method
	 */
	public function testCompressString()
	{
		$sourceCss = JPATH_BASE . '/test_files/css/comments.css';
		$expectedCss = file_get_contents(str_ireplace('.css', '.min.css', $sourceCss));

		$testCss = JMediaCompressor::compressString(file_get_contents($sourceCss), array('type' => 'css'));

		$this->assertEquals($expectedCss, $testCss);

		$sourceJs = JPATH_BASE . '/test_files/js/case1.js';
		$expectedJs = file_get_contents(str_ireplace('.js', '.min.js', $sourceJs));

		$testJs = JMediaCompressor::compressString(file_get_contents($sourceJs), array('type' => 'js'));

		$this->assertEquals($expectedJs, $testJs);
	}

	/**
	 * Test isSupported Method
	 */
	public function  testIsSupported()
	{
		$file1 = JPATH_BASE . '/test_files/css/comments.css';

		$this->assertTrue(JMediaCompressor::isSupported($file1));

		$file2 = JPATH_BASE . '/test_files/js/case2.js';

		$this->assertTrue(JMediaCompressor::isSupported($file2));

		$this->assertFalse(JMediaCompressor::isSupported("index.php"));
	}


	/**
	 * test clear Method
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
