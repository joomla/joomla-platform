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
class JMediaCompressorTest extends PHPUnit_Framework_TestCase
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
    }

    public function testSetUncompressed()
    {
        $random = rand();
        $this->object->setUncompressed($random);
        $test = $this->object->getUncompressed();
        $this->assertEquals($random,$test);
    }

    public function testCompress()
    {

        echo 'Starting Media Compression Test
';

        $path = dirname(JPATH_PLATFORM).'\media\system\css';
        $files = JFolder::files($path,'.',false,true);
        foreach($files as $file)
        {
            $start = microtime();
            echo ' File: ' . basename($file);
            $uncompressed= JFile::read($file);
            $uncompressed_size = strlen($uncompressed);
            echo ' Before: ' . $uncompressed_size . ' bytes';
            $this->object->setUncompressed($uncompressed);
            $this->object->compress();
            $compressed = $this->object->getCompressed();
            $compressed_size = strlen($compressed);
            echo ' After: ' . $compressed_size . ' bytes';
            echo ' Ratio: ' . round(($compressed_size / $uncompressed_size),2);
            echo ' Time: '. round((microtime()-$start),4). ' ms';
            echo '
';
        }

    }
}