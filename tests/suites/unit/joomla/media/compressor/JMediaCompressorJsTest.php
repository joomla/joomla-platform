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
 */
class JMediaCompressorTest extends TestCase
{
	/**
	 * @var JMediaCompressor
	 */
	protected $object;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp()
	{
		$this->object = JMediaCompressor::getInstance(array('type' => 'js'));
	}

	public function testSetCompressed()
	{
		$random = rand();
		$this->object->setCompressed($random);
		$test = $this->object->getCompressed();
		$this->assertEquals($random,$test);
		$this->object->clear();
	}

	public function testSetUncompressed()
	{
		$random = rand();
		$this->object->setUncompressed($random);
		$test = $this->object->getUncompressed();
		$this->assertEquals($random,$test);
		$this->object->clear();
	}

	public function  testGetCompressed()
	{
		$random = rand();
		$this->object->setCompressed($random);
		$test = $this->object->getCompressed();
		$this->assertEquals($random,$test);
		$this->object->clear();
	}

	public function testSetOptions()
	{
		$existing_options = $this->object->getOptions();

		$expected = array('REMOVE_COMMENTS' => false, 'CHANGE_ENCODING' => false);
	
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

	public function testCompress()
	{

		//Put the path to test files for java script compressor.    	
		$path = JPATH_BASE . '/test_files/js';

		$files = JFolder::files($path,'.',false,true, array(),array('.min.js','.php','.html','.combined.js'));

		foreach ($files as $file)
		{
			$this->object->setUncompressed(JFile::read($file));

			// Getting the expected result from filename.min.js file.
			$expected = JFile::read(str_ireplace('.js', '.min.js', $file));

			$this->object->compress();

			$result = $this->object->getCompressed();

			$this->assertEquals($expected, $result);

			$this->object->clear();
		}

	}

	public function test_checkAlphaNum()
	{
		$method = new ReflectionMethod('JMediaCompressorJs', '_checkAlphaNum');
		$method->setAccessible(true);
		
		// Check whether _checkAlphaNum() return true on numbers
		$this->assertTrue($method->invoke($this->object, rand(0,9)));

		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_$";
		$rand_letter = $chars[rand(0,53)];

		// Check whether _checkAlphaNum() return true on alphabatical chars and '_' , '$'
		$this->assertTrue($method->invoke($this->object, $rand_letter));

		$rand_extended_char = chr(rand(127, 255));

		// Check whether _checkAlphaNum() return true on extended aschii chars
		$this->assertTrue($method->invoke($this->object, $rand_extended_char));

		
		$non_alpha_chars = '~`{}[]|\/-()&*%^#@!,.<>?=+"' . "'" ;
		$rand_non_alpha_char = $non_alpha_chars[rand(0, 27)];

		// Check whether _checkAlphaNum() return false on non alpha numeric chars
		$this->assertFalse($method->invoke($this->object, $rand_non_alpha_char));
	}

	public function testClear()
	{
		$sourceJs = JPATH_BASE . '/test_files/js/case1.js';
		
		$this->object->setUncompressed(JFile::read($sourceJs));
		$this->object->compress();
		$this->object->clear();

		$this->assertEquals(null, $this->object->getUncompressed());

		$this->assertEquals(null, $this->object->getcompressed());

		$this->assertAttributeEquals(null, '_compressedSize', $this->object);

		$this->assertAttributeEquals(null, '_uncompressedSize', $this->object);

		$this->assertAttributeEquals("\n", '_a', $this->object);

		$this->assertAttributeEquals('', '_b', $this->object);

		$this->assertAttributeEquals(0, '_nextIndex', $this->object);

		$this->assertAttributeEquals(0, '_startLength', $this->object);

		$this->assertAttributeEquals('', '_preLoaded', $this->object);

		$this->assertAttributeEquals('', '_previousChar', $this->object);
	}
}