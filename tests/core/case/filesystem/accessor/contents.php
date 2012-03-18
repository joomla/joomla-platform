<?php

/**
 * @package     Joomla.UnitTest
 * @subpackage  FileSystem
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * A unit test class for JFilesystemAccessorContents
 *
 * @package     Joomla.UnitTest
 * @subpackage  FileSystem
 *
 * @since       12.1
 */
abstract class TestCaseFilesystemAccessorContents extends TestCaseFilesystem
{
	/**
	 * Test JFilesystemAccessorContents::read
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testRead()
	{
		$message = 'Hello world!';

		$file = JFilesystemElementFile::getInstance(JPATH_TESTS . '/tmp/filesystem/test.txt', static::$system);
		$file->contents = $message;

		$file->open('r');
		$this->assertThat(
			JFilesystemAccessorContents::read($file, 20),
			$this->equalTo($message),
			'The content is not correct.'
		);
		$file->close();
	}

	/**
	 * Test JFilesystemAccessorContents::write
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testWrite()
	{
		$message = 'Hello world!';

		$file = JFilesystemElementFile::getInstance(JPATH_TESTS . '/tmp/filesystem/test.txt', static::$system)->open('w');
		JFilesystemAccessorContents::write($file, $message);
		$file->close();

		$this->assertThat(
			$file->contents,
			$this->equalTo($message),
			'The content is not correct.'
		);

		$file = JFilesystemElementFile::getInstance(JPATH_TESTS . '/tmp/filesystem/test.txt', static::$system)->open('w');
		JFilesystemAccessorContents::write($file, $message, 5);
		$file->close();

		$this->assertThat(
			$file->contents,
			$this->equalTo(substr($message, 0, 5)),
			'The content is not correct.'
		);
	}

	/**
	 * Test JFilesystemAccessorContents::pull
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testPull()
	{
		$message = 'Hello world!';

		$file = JFilesystemElementFile::getInstance(JPATH_TESTS . '/tmp/filesystem/test.txt', static::$system);
		$file->contents = $message;

		$this->assertThat(
			JFilesystemAccessorContents::pull($file),
			$this->equalTo($message),
			'The content is not correct.'
		);
	}
}
