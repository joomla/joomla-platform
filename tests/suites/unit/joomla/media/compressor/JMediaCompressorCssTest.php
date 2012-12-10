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
 */
class JMediaCompressorCssTest extends TestCase
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
		$this->object = JMediaCompressor::getInstance(array('type' => 'css'));
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

	public function testCompress()
	{

		// Put the path to test files for css compressor. (Include expected result in filename.min.css file)
		$path = JPATH_BASE . '/test_files/css';

		$files = JFolder::files($path,'.',false,true, array(),array('.min.css','.php','.html','.combined.css'));

		foreach ($files as $file)
		{
			$this->object->setUncompressed(JFile::read($file));

			// Getting the expected result from filename.min.js file.
			$expected = JFile::read(str_ireplace('.css', '.min.css', $file));

			$this->object->compress();

			$result = $this->object->getCompressed();

			$this->assertEquals($expected, $result);

			$this->object->clear();
		}

	}

	public function testClear()
	{
		$sourceCss = JPATH_BASE . '/test_files/css/comments.css';
		
		$this->object->setUncompressed(JFile::read($sourceCss));
		$this->object->compress();
		$this->object->clear();
		
		$this->assertEquals(null, $this->object->getUncompressed());
		$this->assertEquals(null, $this->object->getcompressed());
		$this->assertAttributeEquals(null, 'compressedSize', $this->object);
		$this->assertAttributeEquals(null, 'uncompressedSize', $this->object);
	}
}