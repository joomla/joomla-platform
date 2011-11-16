<?php
/**
 * @package     Joomla.UnitTest
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

JLoader::register('JElement', JPATH_PLATFORM . '/joomla/html/parameter/element.php');
include_once JPATH_PLATFORM . '/joomla/html/parameter/element/imagelist.php';

/**
 * Test class for JElementImageList.
 * Generated by PHPUnit on 2011-10-26 at 19:38:20.
 */
class JElementImageListTest extends PHPUnit_Framework_TestCase {

    /**
     * @var JElementImageList
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->object = new JElementImageList;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {

    }

    /**
     * @todo Implement testFetchElement().
     */
    public function testFetchElement() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

}

?>
