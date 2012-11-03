<?php

/**
 * @package     Joomla.UnitTest
 * @subpackage  FileSystem
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * A unit test class for JFilesystemAccessorFormatted
 *
 * @package     Joomla.UnitTest
 * @subpackage  FileSystem
 *
 * @since       12.1
 */
abstract class TestCaseFilesystemAccessorFormatted extends TestCaseFilesystem
{
	/**
	 * Test JFilesystemAccessorFormatted::read
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testRead()
	{
		$message = '3.14159';

		$file = JFilesystemElementFile::getInstance(static::$path . '/test.txt', static::$system);
		$file->contents = $message;

		$file->open('r');
		$this->assertThat(
			JFilesystemAccessorFormatted::read($file, "%f"),
			$this->equalTo(array(3.14159)),
			'The content is not correct.'
		);
		$file->close();
	}

	/**
	 * Test JFilesystemAccessorFormatted::write
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testWrite()
	{
		$file = JFilesystemElementFile::getInstance(static::$path . '/test.txt', static::$system)->open('w');
		JFilesystemAccessorFormatted::write($file, "%f", 3.14159);
		$file->close();

		$this->assertThat(
			$file->contents,
			$this->equalTo('3.14159'),
			'The content is not correct.'
		);
	}
}
