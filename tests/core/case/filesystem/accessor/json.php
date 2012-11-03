<?php

/**
 * @package     Joomla.UnitTest
 * @subpackage  FileSystem
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * A unit test class for JFilesystemAccessorJson
 *
 * @package     Joomla.UnitTest
 * @subpackage  FileSystem
 *
 * @since       12.1
 */
abstract class TestCaseFilesystemAccessorJson extends TestCaseFilesystem
{
	/**
	 * Test JFilesystemAccessorJson::read
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testRead()
	{
		$message = '{"cms":"Joomla!"}' . "\n";

		$file = JFilesystemElementFile::getInstance(static::$path . '/test.json', static::$system);
		$file->contents = $message;

		$file->open('r');
		$this->assertThat(
			JFilesystemAccessorJson::read($file),
			$this->equalTo((object) array('cms' => 'Joomla!')),
			'The content is not correct.'
		);
		$file->close();
	}

	/**
	 * Test JFilesystemAccessorJson::write
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testWrite()
	{
		$file = JFilesystemElementFile::getInstance(static::$path . '/test.json', static::$system)->open('w');
		JFilesystemAccessorJson::write($file, (object) array('cms' => 'Joomla!'));
		$file->close();

		$this->assertThat(
			$file->contents,
			$this->equalTo('{"cms":"Joomla!"}' . "\n"),
			'The content is not correct.'
		);
	}

	/**
	 * Test JFilesystemAccessorJson::pull
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testPull()
	{
		$file = JFilesystemElementFile::getInstance(static::$path . '/test.json', static::$system)->open('w');
		JFilesystemAccessorJson::write($file, (object) array('cms' => 'Joomla!'));
		$file->close();

		$this->assertThat(
			JFilesystemAccessorJson::pull($file),
			$this->equalTo(array((object) array('cms' => 'Joomla!'))),
			'The content is not correct.'
		);
	}

	/**
	 * Test JFilesystemAccessorJson::push
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testPush()
	{
		$file = JFilesystemElementFile::getInstance(static::$path . '/test.json', static::$system);
		JFilesystemAccessorJson::push($file, array((object) array('cms' => 'Joomla!')));

		$this->assertThat(
			$file->contents,
			$this->equalTo('{"cms":"Joomla!"}' . "\n"),
			'The content is not correct.'
		);
	}
}
