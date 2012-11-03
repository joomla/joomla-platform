<?php

/**
 * @package     Joomla.UnitTest
 * @subpackage  FileSystem
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * A unit test class for JFilesystemAccessorSerializable
 *
 * @package     Joomla.UnitTest
 * @subpackage  FileSystem
 *
 * @since       12.1
 */
abstract class TestCaseFilesystemAccessorSerializable extends TestCaseFilesystem
{
	/**
	 * Test JFilesystemAccessorSerializable::read
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testRead()
	{
		$message = 'a:2:{i:0;s:7:"Joomla!";i:1;s:35:"Content Management System:\nThe best";}' . "\n";

		$file = JFilesystemElementFile::getInstance(static::$path . '/test.txt', static::$system);
		$file->contents = $message;

		$file->open('r');
		$this->assertThat(
			JFilesystemAccessorSerializable::read($file),
			$this->equalTo(array('Joomla!', 'Content Management System:' . "\n" . 'The best')),
			'The content is not correct.'
		);
		$file->close();
	}

	/**
	 * Test JFilesystemAccessorSerializable::write
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testWrite()
	{
		$file = JFilesystemElementFile::getInstance(static::$path . '/test.txt', static::$system)->open('w');
		JFilesystemAccessorSerializable::write($file, array('Joomla!', 'Content Management System:' . "\n" . 'The best'));
		$file->close();

		$this->assertThat(
			$file->contents,
			$this->equalTo('a:2:{i:0;s:7:"Joomla!";i:1;s:35:"Content Management System:\nThe best";}' . "\n"),
			'The content is not correct.'
		);
	}

	/**
	 * Test JFilesystemAccessorSerializable::pull
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testPull()
	{
		$messages[] = 'a:2:{i:0;s:7:"Joomla!";i:1;s:35:"Content Management System:\nThe best";}' . "\n";
		$messages[] = 'a:2:{i:0;s:3:"PHP";i:1;s:24:"Web programming language";}' . "\n";

		$file = JFilesystemElementFile::getInstance(static::$path . '/test.txt', static::$system);
		$file->contents = $messages;

		$this->assertThat(
			JFilesystemAccessorSerializable::pull($file),
			$this->equalTo(
				array(
					array('Joomla!', 'Content Management System:' . "\n" . 'The best'),
					array('PHP', 'Web programming language')
				)
			),
			'The content is not correct.'
		);
	}

	/**
	 * Test JFilesystemAccessorSerializable::push
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testPush()
	{
		$file = JFilesystemElementFile::getInstance(static::$path . '/test.txt', static::$system);
		JFilesystemAccessorSerializable::push(
			$file,
			array(
				array('Joomla!', 'Content Management System:' . "\n" . 'The best'),
				array('PHP', 'Web programming language')
			)
		);

		$this->assertThat(
			$file->contents,
			$this->equalTo(
'a:2:{i:0;s:7:"Joomla!";i:1;s:35:"Content Management System:\nThe best";}
a:2:{i:0;s:3:"PHP";i:1;s:24:"Web programming language";}
'),
			'The content is not correct.'
		);
	}
}
