<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Cache
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * Test class for JCacheStorageEaccelerator.
 * Generated by PHPUnit on 2009-10-08 at 21:45:12.
 *
 * @package	Joomla.UnitTest
 * @subpackage Cache
 *
 */
class JCacheStorageEacceleratorTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var	JCacheStorageEaccelerator
	 * @access protected
	 */
	protected $object;

	/**
	 * @var	eacceleratorAvailable
	 * @access protected
	 */
	protected $eacceleratorAvailable;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return void
	 * @access protected
	 */
	protected function setUp()
	{
		include_once JPATH_PLATFORM.'/joomla/cache/storage.php';
		include_once JPATH_PLATFORM.'/joomla/cache/storage/eaccelerator.php';

		$this->eacceleratorAvailable = (extension_loaded('eaccelerator') && function_exists('eaccelerator_get'));
		$this->object = JCacheStorage::getInstance('eaccelerator');
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @return void
	 * @access protected
	 */
	protected function tearDown()
	{
	}

	/**
	 * @return void
	 * @todo Implement testGet().
	 */
	public function testGet()
	{
		if ($this->eacceleratorAvailable)
		{
			$this->markTestIncomplete('This test has not been implemented yet.');
		}
		else
		{
			$this->markTestSkipped('This caching method is not supported on this system.');
		}
	}

	/**
	 * @return void
	 * @todo Implement testStore().
	 */
	public function testStore()
	{
		if ($this->eacceleratorAvailable)
		{
			$this->markTestIncomplete('This test has not been implemented yet.');
		}
		else
		{
			$this->markTestSkipped('This caching method is not supported on this system.');
		}
	}

	/**
	 * @return void
	 * @todo Implement testRemove().
	 */
	public function testRemove()
	{
		if ($this->eacceleratorAvailable)
		{
			$this->markTestIncomplete('This test has not been implemented yet.');
		}
		else
		{
			$this->markTestSkipped('This caching method is not supported on this system.');
		}
	}

	/**
	 * @return void
	 * @todo Implement testClean().
	 */
	public function testClean()
	{
		if ($this->eacceleratorAvailable)
		{
			$this->markTestIncomplete('This test has not been implemented yet.');
		}
		else
		{
			$this->markTestSkipped('This caching method is not supported on this system.');
		}
	}

	/**
	 * @return void
	 * @todo Implement testGc().
	 */
	public function testGc()
	{
		if ($this->eacceleratorAvailable)
		{
			$this->markTestIncomplete('This test has not been implemented yet.');
		}
		else
		{
			$this->markTestSkipped('This caching method is not supported on this system.');
		}
	}

	/**
	 * Testing isSupported().
	 *
	 * @return void
	 */
	public function testIsSupported()
	{
		$this->assertThat(
			$this->object->isSupported(),
			$this->equalTo($this->eacceleratorAvailable),
			'Claims Eaccelerator is not loaded.'
		);
	}

	/**
	 * @return void
	 * @todo Implement test_setExpire().
	 */
	public function testSetExpire()
	{
		if ($this->eacceleratorAvailable)
		{
			$this->markTestIncomplete('This test has not been implemented yet.');
		}
		else
		{
			$this->markTestSkipped('This caching method is not supported on this system.');
		}
	}

	/**
	 * @return void
	 * @todo Implement test_getCacheId().
	 */
	public function testGetCacheId()
	{
		if ($this->eacceleratorAvailable)
		{
			$this->markTestIncomplete('This test has not been implemented yet.');
		}
		else
		{
			$this->markTestSkipped('This caching method is not supported on this system.');
		}
	}
}
?>
