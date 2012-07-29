
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
        $this->object->clear();
    }
    
    public function testGetRatio()
    {
    	$this->object->setUncompressed("TestUncompressed");
    	$this->object->setCompressed("TestCompressed");
    	
    	$expected = round ((14/16)*100 , 2);
    	$test = $this->object->getRatio();
    	
    	$this->assertEquals($expected, $test);
    	
    	$this->object->clear();
    }
    
    public function testCompressString()
    {
    	
    }
    
    public function  testIsSupported()
    {
    	$file1 = JPATH_BASE . '/test_files/css/comments.css';
    	
    	$this->assertTrue(JMediaCompressor::isSupported($file1));
    	
    	$file2 = JPATH_BASE . '/test_files/js/case1.js';
    	 
    	$this->assertTrue(JMediaCompressor::isSupported($file2));
    }

}