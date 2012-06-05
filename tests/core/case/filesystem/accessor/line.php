<?php

/**
 * @package     Joomla.UnitTest
 * @subpackage  FileSystem
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * A unit test class for JFilesystemAccessorLine
 *
 * @package     Joomla.UnitTest
 * @subpackage  FileSystem
 *
 * @since       12.1
 */
abstract class TestCaseFilesystemAccessorLine extends TestCaseFilesystem
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

		$file = JFilesystemElementFile::getInstance(static::$path . '/test.txt', static::$system);
		$file->contents = $message;

		$file->open('r');
		$this->assertThat(
			JFilesystemAccessorLine::read($file),
			$this->equalTo($message),
			'The content is not correct.'
		);
		$file->close();

		$file->open('r');
		$this->assertThat(
			JFilesystemAccessorLine::read($file, 6),
			$this->equalTo(substr($message, 0, 5)),
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

		$file = JFilesystemElementFile::getInstance(static::$path . '/test.txt', static::$system)->open('w');
		JFilesystemAccessorLine::write($file, $message);
		$file->close();

		$this->assertThat(
			$file->contents,
			$this->equalTo($message . "\n"),
			'The content is not correct.'
		);

		$file = JFilesystemElementFile::getInstance(static::$path . '/test.txt', static::$system)->open('w');
		JFilesystemAccessorLine::write($file, $message, 5);
		$file->close();

		$this->assertThat(
			$file->contents,
			$this->equalTo(substr($message, 0, 5) . "\n"),
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
		$messages = 'Hello world!' . "\n" . 'Welcome to Joomla!' . "\n";

		$file = JFilesystemElementFile::getInstance(static::$path . '/test.txt', static::$system);
		$file->contents = $messages;

		$this->assertThat(
			JFilesystemAccessorLine::pull($file),
			$this->equalTo(array('Hello world!' . "\n", 'Welcome to Joomla!' . "\n")),
			'The content is not correct'
		);
	}

	/**
	 * Test JFilesystemAccessorContents::push
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testPush()
	{
		$messages = array('Hello world!', 'Welcome to Joomla!');
		'Hello world!' . "\n" . 'Welcome to Joomla!' . "\n";

		$file = JFilesystemElementFile::getInstance(static::$path . '/test.txt', static::$system);
		JFilesystemAccessorLine::push($file, $messages);

		$this->assertThat(
			$file->contents,
			$this->equalTo('Hello world!' . "\n" . 'Welcome to Joomla!' . "\n"),
			'The content is not correct'
		);
	}
}
