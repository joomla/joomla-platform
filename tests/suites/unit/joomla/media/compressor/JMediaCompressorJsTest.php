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

    public function testCompress()
    {

    	//Put the path to test files for java script compressor.    	
    	$path = JPATH_BASE . '/test_files/js';
    	    	
    	$files = JFolder::files($path,'.',false,true, array(),array('.min.js','.php','.html'));
  
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
}