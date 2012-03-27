<?php

/**
 * @package     Joomla.UnitTest
 * @subpackage  FileSystem
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * A unit test class for JFilesystem
 *
 * @package     Joomla.UnitTest
 * @subpackage  FileSystem
 *
 * @since       12.1
 */
class JFilesystemTest extends TestCase
{
	/**
     * Sets up the fixture.
     * This method is called before a test is executed.
     *
     * @return  void
	 *
	 * @since       12.1
     */
	protected function setUp()
	{
		// Make sure previous test files are cleaned up
		$directory = JFilesystemElementDirectory::getInstance(JPATH_TESTS . '/tmp/filesystem');

		// Make some test files and folders
		$directory->create();
	}

	/**
	 * Remove created files
	 *
	 * @return  void
	 *
	 * @since       12.1
	 */
	protected function tearDown()
	{
		// Make sure previous test files are cleaned up
		$directory = JFilesystemElementDirectory::getInstance(JPATH_TESTS . '/tmp/filesystem');
		if ($directory->exists)
		{
			$directory->delete();
		}
	}

	/**
	 * Test JFilesystem::getInstance
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testGetInstance()
	{
		$filesystem = JFilesystem::getInstance();

		// Test first time
		$this->assertThat(
			$filesystem,
			$this->isInstanceOf('JFilesystem'),
			'The object is not instance of JFilesystem.'
		);

		// Test singleton
		$this->assertThat(
			$filesystem,
			$this->identicalTo(JFilesystem::getInstance()),
			'The object is not the same'
		);
	}

	/**
	 * Test JFilesystem::getFile
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testGetFile()
	{
		$file = JFilesystem::getInstance()->getFile(JPATH_TESTS . '/tmp/filesystem/test');

		// Test first time
		$this->assertThat(
			$file,
			$this->isInstanceOf('JFilesystemElementFile'),
			'The object is not instance of JFilesystemElementFile.'
		);

		// Test singleton
		$this->assertThat(
			$file,
			$this->identicalTo(JFilesystem::getInstance()->getFile(JPATH_TESTS . '/tmp/filesystem/test')),
			'The object is not the same.'
		);

		// Test Exception
		touch(JPATH_TESTS . '/tmp/filesystem/test');
		$this->setExpectedException('RuntimeException');
		$this->assertThat(
			JFilesystem::getInstance()->getDirectory(JPATH_TESTS . '/tmp/filesystem/test'),
			$this->isInstanceOf('JFilesystemElementFolder'),
			'The object is not instance of JFilesystemElementFile.'
		);

	}

	/**
	 * Test JFilesystem::getDirectory
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testGetDirectory()
	{
		$folder = JFilesystem::getInstance()->getDirectory(JPATH_TESTS . '/tmp/filesystem/test');

		// Test first time
		$this->assertThat(
			$folder,
			$this->isInstanceOf('JFilesystemElementDirectory'),
			'The object is not instance of JFilesystemElementDirectory.'
		);

		// Test singleton
		$this->assertThat(
			$folder,
			$this->identicalTo(JFilesystem::getInstance()->getDirectory(JPATH_TESTS . '/tmp/filesystem/test')),
			'The object is not the same.'
		);
	}

	/**
	 * Test JFilesystem::__get
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function test__Get()
	{
		$filesystem = JFilesystem::getInstance('myprotocol://');

		// Test *prefix* property
		$this->assertThat(
			$filesystem->prefix,
			$this->equalTo('myprotocol://'),
			'The prefix is not correct.'
		);

		// Test *context* property
		$this->assertTrue(
			is_resource($filesystem->context),
			'The context is not correct.'
		);

		// Test *unknown* property
		$this->setExpectedException('InvalidArgumentException');
		$filesystem->unknown;
	}
}
