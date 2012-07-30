
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
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp()
	{
		$this->object = JMediaCompressor::getInstance(array('type' => 'css'));
	}

	public function testGetInstance()
	{
		$compressor1 = JMediaCompressor::getInstance(array('type'=>'css'));

		$this->assertInstanceOf('JMediaCompressorCss', $compressor1);

		$compressor2 = JMediaCompressor::getInstance(array('type'=>'js'));

		$this->assertInstanceOf('JMediaCompressorJs', $compressor2);
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
	}

	public function testGetRatio()
	{
		$this->object->setUncompressed("TestUncompressed");
		$this->object->setCompressed("TestCompressed");

		$expected = round((14 / 16) * 100, 2);
		$test = $this->object->getRatio();

		$this->assertEquals($expected, $test);
		$this->object->clear();
	}

	public function testGetCompressors()
	{
		$expected = array('css','js');

		$test = JMediaCompressor::getCompressors();

		$this->assertEquals($expected, $test);

	}

	public function testSetOptions()
	{
		$expected = array('REMOVE_COMMENTS' => false, 'MIN_COLOR_CODES' => false, 'LIMIT_LINE_LENGTH' => false);

		$this->object->setOptions($expected);

		$test = $this->object->getOptions();

		foreach ($expected as $key => $value)
		{
			$this->arrayHasKey($key, $test);
			$this->assertEquals($value, $test[$key]);
		}

	}

	public function testCompressString()
	{
		$source = JPATH_BASE . '/test_files/css/comments.css';
		$expected = JFile::read(str_ireplace('.css', '.min.css', $source));

		$test = JMediaCompressor::compressString(JFile::read($source), array('type' => 'css'));

		$this->assertEquals($expected, $test);
	}

	public function  testIsSupported()
	{
		$file1 = JPATH_BASE . '/test_files/css/comments.css';

		$this->assertTrue(JMediaCompressor::isSupported($file1));

		$file2 = JPATH_BASE . '/test_files/js/case2.js';

		$this->assertTrue(JMediaCompressor::isSupported($file2));
	}

	public function testClear()
	{
		$this->object->setUncompressed(rand());
		$this->object->compress();
		$this->object->clear();
		$this->assertEquals(null, $this->object->getUncompressed());
		$this->assertEquals(null, $this->object->getcompressed());
		$this->assertAttributeEquals(null, '_compressedSize', $this->object);
		$this->assertAttributeEquals(null, '_uncompressedSize', $this->object);
	}

}