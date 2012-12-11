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
 */
class JMediaCombinerCssTest extends TestCase
{
	/**
	* @var JMediaCollectionCss
	*/
	protected $object;


	/**
	* Sets up the fixture, for example, opens a network connection.
	* This method is called before a test is executed.
	*/
	protected function setUp()
	{
		$this->object = JMediaCollection::getInstance(array('type' => 'css'));
	}


	public function testCombine()
	{
	}

	public function loadCssFiles()
	{
		// Path to source css files
		$path = JPATH_BASE . '/test_files/css';
	
		$files = JFolder::files($path,'.',false,true, array(), array('.min.css', '.php', '.html','.combined.css'));//get full path
	
		return $files;
	}

}