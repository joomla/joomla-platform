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
class JFilesystemAccessorContentsPhpTest extends TestCaseFilesystemAccessorContents
{
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

		$file = JFilesystemElementFile::getInstance(static::$path . '/test.txt', static::$system);
		$file->contents = $message;

		$this->assertThat(
			JFilesystemAccessorContents::pull($file),
			$this->equalTo($message),
			'The content is not correct.'
		);

		$this->assertThat(
			JFilesystemAccessorContents::pull($file, 2, 5),
			$this->equalTo(substr($message, 2, 5)),
			'The content is not correct.'
		);
	}
}
