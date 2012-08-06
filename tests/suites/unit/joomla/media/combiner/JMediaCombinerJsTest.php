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
class JMediaCombinerJsTest extends TestCase
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
		$this->object = JMediaCombiner::getInstance(array('type' => 'js'));
	}


	public function testCombine()
	{
		$this->object->setSources($this->loadJsFiles());
		
		$this->object->combine();
		
		
		
		
	}

	public function loadJsFiles()
	{
		// Path to source css files
		$path = JPATH_BASE . '/test_files/js';
	
		$files = JFolder::files($path,'.',false,true, array(), array('.min.js', '.php', '.html','.combined.js'));//get full path
	
		return $files;
	}

}