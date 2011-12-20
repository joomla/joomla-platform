<?php
/**
 * @package     Joomla.UnitTest
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

include_once JPATH_PLATFORM . '/joomla/updater/updater.php';

/**
 * Test class for JUpdater.
 * Generated by PHPUnit on 2011-10-26 at 19:33:24.
 */
class JUpdaterTest extends PHPUnit_Framework_TestCase {

    /**
     * @var JUpdater
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->object = new JUpdater;
    }
    
    /**
     * Test JUpdater::__construct().
     * 
     * @since 11.3
     */
    public function test__construct() {
    	
    	$this->assertThat(
    		$this->object->get('_adapters'),
    		$this->equalTo(array())
    	);
    	
    	$this->assertThat(
    		$this->object->get('_adapterfolder'),
    		$this->equalTo('adapters')
    	);

    	$this->assertThat(
    		realpath($this->object->get('_basepath')),
    		$this->equalTo(realpath(JPATH_PLATFORM.'/joomla/updater'))
    	);

    	$this->assertThat(
    		$this->object->get('_classprefix'),
    		$this->equalTo('JUpdater')
    	);

		$this->assertTrue(
    		is_a($this->object->get('_db'), 'JDatabase')
    	);
    }

    /**
     * Test JUpdater::getInstance().
     * 
     * @return  void
     * 
     * @since   11.3
     */
    public function testGetInstance() {
        $this->assertInstanceOf('JUpdater', JUpdater::getInstance());
    }

    /**
     * @todo Implement testFindUpdates().
     */
    public function testFindUpdates() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testUpdate().
     */
    public function testUpdate() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

}

?>
