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
jimport('joomla.filesystem.path');

/**
 * Test class for JMediaCompressor.
 *
 * @package     Joomla.UnitTest
 * @subpackage  Media
 *
 * @since       12.1
 */
class JMediaCompressorJsTest extends TestCase
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
		$this->object = JMediaCompressor::getInstance(array('type' => 'js'));
		$this->pathToTestFiles = JPATH_TESTS . DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR . 'js';
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
			array('.min.js', '.php', '.html', '.combined.js')
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

			// Getting the expected result from filename.min.js file.
			$expected = file_get_contents(str_ireplace('.js', '.' . $this->suffix . '.js', $file));

			$this->object->compress();

			$result = $this->object->getCompressed();

			$this->assertEquals($expected, $result);

			$this->object->clear();
		}

	}

	/**
	 * Test _checkAlphaNum Method
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function test_checkAlphaNum()
	{
		$method = new ReflectionMethod('JMediaCompressorJs', '_checkAlphaNum');
		$method->setAccessible(true);

		// Check whether _checkAlphaNum() return true on numbers
		$this->assertTrue($method->invoke($this->object, rand(0, 9)));

		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_$";
		$rand_letter = $chars[rand(0, 53)];

		// Check whether _checkAlphaNum() return true on alphabetical chars and '_' , '$'
		$this->assertTrue($method->invoke($this->object, $rand_letter));

		$rand_extended_char = chr(rand(127, 255));

		// Check whether _checkAlphaNum() return true on extended ascii chars
		$this->assertTrue($method->invoke($this->object, $rand_extended_char));


		$non_alpha_chars = '~`{}[]|\/-()&*%^#@!,.<>?=+"' . "'";
		$rand_non_alpha_char = $non_alpha_chars[rand(0, 27)];

		// Check whether _checkAlphaNum() return false on non alpha numeric chars
		$this->assertFalse($method->invoke($this->object, $rand_non_alpha_char));
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
		$sourceJs = $this->pathToTestFiles . DIRECTORY_SEPARATOR . 'case1.js';

		$this->object->setUncompressed(file_get_contents($sourceJs));
		$this->object->compress();
		$this->object->clear();

		$this->assertEquals(null, $this->object->getUncompressed());

		$this->assertEquals(null, $this->object->getcompressed());

		$this->assertAttributeEquals(null, 'compressedSize', $this->object);

		$this->assertAttributeEquals(null, 'uncompressedSize', $this->object);

		$this->assertAttributeEquals("\n", '_a', $this->object);

		$this->assertAttributeEquals('', '_b', $this->object);

		$this->assertAttributeEquals(0, '_nextIndex', $this->object);

		$this->assertAttributeEquals(0, '_startLength', $this->object);

		$this->assertAttributeEquals('', '_preLoaded', $this->object);

		$this->assertAttributeEquals('', '_previousChar', $this->object);
	}

}
