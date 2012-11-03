<?php

/**
 * @package     Joomla.UnitTest
 * @subpackage  FileSystem
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * A unit test class for JFilesystemAccessorCharacter
 *
 * @package     Joomla.UnitTest
 * @subpackage  FileSystem
 *
 * @since       12.1
 */
abstract class TestCaseFilesystemAccessorCharacter extends TestCaseFilesystem
{
	/**
	 * Test JFilesystemAccessorCharacter::read
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testRead()
	{
		$message = 'H';

		$file = JFilesystemElementFile::getInstance(static::$path . '/test.txt', static::$system);
		$file->contents = $message;

		$file->open('r');
		$this->assertThat(
			JFilesystemAccessorCharacter::read($file),
			$this->equalTo($message),
			'The content is not correct.'
		);
		$file->close();
	}

	/**
	 * Test JFilesystemAccessorCharacter::write
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testWrite()
	{
		$message = 'H';

		$file = JFilesystemElementFile::getInstance(static::$path . '/test.txt', static::$system)->open('w');
		JFilesystemAccessorCharacter::write($file, $message);
		$file->close();

		$this->assertThat(
			$file->contents,
			$this->equalTo('H'),
			'The content is not correct.'
		);
	}
}
