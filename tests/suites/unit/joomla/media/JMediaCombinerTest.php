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
 */
class JMediaCombinerTest extends TestCase
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
		$this->object = JMediaCombiner::getInstance(array('type' => 'css'));
	}

	public function testGetInstance()
	{
		$compressor1 = JMediaCompressor::getInstance(array('type'=>'css'));

		$this->assertInstanceOf('JMediaCompressorCss', $compressor1);

		$compressor2 = JMediaCompressor::getInstance(array('type'=>'js'));

		$this->assertInstanceOf('JMediaCompressorJs', $compressor2);
	}

	public function testCombineFiles()
	{
	}

	public function  testIsSupported()
	{
		$file1 = JPATH_BASE . '/test_files/css/comments.css';

		$this->assertTrue(JMediaCombiner::isSupported($file1));

		$file2 = JPATH_BASE . '/test_files/js/case2.js';

		$this->assertTrue(JMediaCombiner::isSupported($file2));
	}
}