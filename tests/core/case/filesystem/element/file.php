<?php

/**
 * @package     Joomla.UnitTest
 * @subpackage  FileSystem
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * A unit test class for JFilesystemElementFile
 *
 * @package     Joomla.UnitTest
 * @subpackage  FileSystem
 *
 * @since       12.1
 */
abstract class TestCaseFilesystemElementFile extends TestCaseFilesystem
{
	/**
	 * Test JFilesystemElementFile::getInstance
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testGetInstance()
	{
		$file = JFilesystemElementFile::getInstance(JPATH_TESTS . '/tmp/filesystem/test.txt', static::$system);

		// Test first time
		$this->assertThat(
			$file,
			$this->isInstanceOf('JFilesystemElementFile'),
			'The object is not instance of JFilesystemElementFile.'
		);

		// Test singleton
		$this->assertThat(
			$file,
			$this->identicalTo(JFilesystemElementFile::getInstance(JPATH_TESTS . '/tmp/filesystem/test.txt', static::$system)),
			'The object is not the same.'
		);
		$this->assertThat(
			$file,
			$this->identicalTo(JFilesystemElementFile::getInstance(JPATH_TESTS . '/tmp\\filesystem\\..//filesystem/\\./test.txt', static::$system)),
			'The object is not the same.'
		);

		// Test opening file for writing
		$file = JFilesystemElementFile::getInstance(JPATH_TESTS . '/tmp/filesystem/test.txt', static::$system, 'w');
		$this->assertTrue(
			file_exists(JPATH_TESTS . '/tmp/filesystem/test.txt'),
			'The file does not exist.'
		);

		// Test reopening file for writing
		$file = JFilesystemElementFile::getInstance(JPATH_TESTS . '/tmp/filesystem/test.txt', static::$system, 'w');
		$this->assertTrue(
			file_exists(JPATH_TESTS . '/tmp/filesystem/test.txt'),
			'The file does not exist.'
		);

		// Test opening file for reading
		$file = JFilesystemElementFile::getInstance(JPATH_TESTS . '/tmp/filesystem/test.txt', static::$system, 'r');
		$this->assertThat(
			$file->mode,
			$this->equalTo('r'),
			'The mode is not correct.'
		);

		$this->assertThat(
			$file->use_include_path,
			$this->equalTo(false),
			'The use_include_path is not correct.'
		);

		try
		{
			mkdir(static::$system->prefix . JPATH_TESTS . '/tmp/filesystem/dir');
			$file = JFilesystemElementFile::getInstance(JPATH_TESTS . '/tmp/filesystem/dir', static::$system);
			$this->fail('Runtime exception has not been thrown');
		}
		catch (RuntimeException $e)
		{
		}

		try
		{
			$file = JFilesystemElementFile::getInstance(JPATH_TESTS . '/tmp/filesystem/unexisting', static::$system, 'r');
			$file->close();
			$this->fail('Runtime exception has not been thrown');
		}
		catch (ErrorException $e)
		{
		}
	}

	/**
	 * Test JFilesystemElementFile::__get
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function test__Get_mode()
	{
		$file = JFilesystemElementFile::getInstance(JPATH_TESTS . '/tmp/filesystem/test.txt', static::$system, 'w')->close();

		$this->assertThat(
			$file->mode,
			$this->equalTo(null),
			'The mode is not correct.'
		);
	}

	/**
	 * Test JFilesystemElementFile::__get
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function test__Get_use_include_path()
	{
		$file = JFilesystemElementFile::getInstance(JPATH_TESTS . '/tmp/filesystem/test.txt', static::$system, 'w')->close();

		$this->assertThat(
			$file->use_include_path,
			$this->equalTo(false),
			'The use_include_path is not correct.'
		);
	}

	/**
	 * Test JFilesystemElementFile::__get
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function test__Get_exists()
	{
		$file = JFilesystemElementFile::getInstance(JPATH_TESTS . '/tmp/filesystem/test.txt', static::$system, 'w')->close();

		$this->assertThat(
			$file->exists,
			$this->equalTo(true),
			'The file does not exists.'
		);
	}

	/**
	 * Test JFilesystemElementFile::__get
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function test__Get_name()
	{
		$file = JFilesystemElementFile::getInstance(JPATH_TESTS . '/tmp/filesystem/test.txt', static::$system, 'w')->close();

		$this->assertThat(
			$file->name,
			$this->equalTo('test.txt'),
			'The basename is not correct.'
		);
	}

	/**
	 * Test JFilesystemElementFile::__get
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function test__Get_basename()
	{
		$file = JFilesystemElementFile::getInstance(JPATH_TESTS . '/tmp/filesystem/test.txt', static::$system, 'w')->close();

		$this->assertThat(
			$file->basename,
			$this->equalTo('test'),
			'The basename is not correct.'
		);

		$file = JFilesystemElementFile::getInstance(JPATH_TESTS . '/tmp/filesystem/test', static::$system, 'w')->close();

		$this->assertThat(
			$file->basename,
			$this->equalTo('test'),
			'The basename is not correct.'
		);
	}

	/**
	 * Test JFilesystemElementFile::__get
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function test__Get_dirpath()
	{
		$file = JFilesystemElementFile::getInstance(JPATH_TESTS . '/tmp/filesystem/test.txt', static::$system, 'w')->close();

		$this->assertThat(
			$file->dirpath,
			$this->equalTo(JPATH_TESTS . '/tmp/filesystem'),
			'The dirname is not correct.'
		);
	}

	/**
	 * Test JFilesystemElementFile::__get
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function test__Get_extension()
	{
		$file = JFilesystemElementFile::getInstance(JPATH_TESTS . '/tmp/filesystem/test.txt', static::$system, 'w')->close();

		$this->assertThat(
			$file->extension,
			$this->equalTo('txt'),
			'The extension is not correct.'
		);
	}

	/**
	 * Test JFilesystemElementFile::__get
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function test__Get_access_time()
	{
		$file = JFilesystemElementFile::getInstance(JPATH_TESTS . '/tmp/filesystem/test.txt', static::$system, 'w')->close();

		$this->assertTrue(
			is_int($file->access_time),
			'The access time is not an integer.'
		);
	}

	/**
	 * Test JFilesystemElementFile::__get
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function test__Get_change_time()
	{
		$file = JFilesystemElementFile::getInstance(JPATH_TESTS . '/tmp/filesystem/test.txt', static::$system, 'w')->close();

		$this->assertTrue(
			is_int($file->change_time),
			'The change time is not an integer.'
		);
	}

	/**
	 * Test JFilesystemElementFile::__get
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function test__Get_modification_time()
	{
		$file = JFilesystemElementFile::getInstance(JPATH_TESTS . '/tmp/filesystem/test.txt', static::$system, 'w')->close();

		$this->assertTrue(
			is_int($file->modification_time),
			'The modification time is not an integer.'
		);
	}

	/**
	 * Test JFilesystemElementFile::__get
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function test__Get_group()
	{
		$file = JFilesystemElementFile::getInstance(JPATH_TESTS . '/tmp/filesystem/test.txt', static::$system, 'w')->close();

		$this->assertTrue(
			is_int($file->group),
			'The group is not an integer.'
		);
	}

	/**
	 * Test JFilesystemElementFile::__get
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function test__Get_owner()
	{
		$file = JFilesystemElementFile::getInstance(JPATH_TESTS . '/tmp/filesystem/test.txt', static::$system, 'w')->close();

		$this->assertTrue(
			is_int($file->owner),
			'The owner is not an integer.'
		);
	}

	/**
	 * Test JFilesystemElementFile::__get
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function test__Get_permissions()
	{
		$file = JFilesystemElementFile::getInstance(JPATH_TESTS . '/tmp/filesystem/test.txt', static::$system, 'w')->close();

		$this->assertTrue(
			is_int($file->permissions),
			'The permissions are not an integer.'
		);
	}

	/**
	 * Test JFilesystemElementFile::__get
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function test__Get_size()
	{
		$file = JFilesystemElementFile::getInstance(JPATH_TESTS . '/tmp/filesystem/test.txt', static::$system, 'w')->close();

		$this->assertThat(
			$file->size,
			$this->equalTo(0),
			'The size is not equal to 0.'
		);
	}

	/**
	 * Test JFilesystemElementFile::__get
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function test__Get_is_dir()
	{
		$file = JFilesystemElementFile::getInstance(JPATH_TESTS . '/tmp/filesystem/test.txt', static::$system, 'w')->close();

		$this->assertThat(
			$file->is_dir,
			$this->equalTo(false),
			'The file is a directory.'
		);
	}

	/**
	 * Test JFilesystemElementFile::__get
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function test__Get_is_file()
	{
		$file = JFilesystemElementFile::getInstance(JPATH_TESTS . '/tmp/filesystem/test.txt', static::$system, 'w')->close();

		$this->assertThat(
			$file->is_file,
			$this->equalTo(true),
			'The file is not a file.'
		);
	}

	/**
	 * Test JFilesystemElementFile::__get
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function test__Get_is_link()
	{
		$file = JFilesystemElementFile::getInstance(JPATH_TESTS . '/tmp/filesystem/test.txt', static::$system, 'w')->close();

		$this->assertThat(
			$file->is_link,
			$this->equalTo(false),
			'The file is a link.'
		);
	}

	/**
	 * Test JFilesystemElementFile::__get
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function test__Get_is_readable()
	{
		$file = JFilesystemElementFile::getInstance(JPATH_TESTS . '/tmp/filesystem/test.txt', static::$system, 'w')->close();

		$this->assertThat(
			$file->is_readable,
			$this->equalTo(true),
			'The file is not readable.'
		);
	}

	/**
	 * Test JFilesystemElementFile::__get
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function test__Get_opened()
	{
		$file = JFilesystemElementFile::getInstance(JPATH_TESTS . '/tmp/filesystem/test.txt', static::$system, 'w');

		$this->assertThat(
			$file->opened,
			$this->equalTo(true),
			'The file is not opened.'
		);

		$file->close();

		$this->assertThat(
			$file->opened,
			$this->equalTo(false),
			'The file is opened.'
		);
	}

	/**
	 * Test JFilesystemElementFile::__get
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function test__Get_eof()
	{
		$file = JFilesystemElementFile::getInstance(JPATH_TESTS . '/tmp/filesystem/test.txt', static::$system, 'w')->close()->open('r');
		$data = $file->readContents(20);

		$this->assertThat(
			$file->eof,
			$this->equalTo(true),
			'The file is not at the end.'
		);

		$file->close();
	}

	/**
	 * Test JFilesystemElementFile::__get
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function test__Get_contents()
	{
		$file = JFilesystemElementFile::getInstance(JPATH_TESTS . '/tmp/filesystem/test.txt', static::$system, 'w');
		$message = 'Hello World!';
		$file->writeContents($message);
		$file->close();

		$this->assertThat(
			$file->contents,
			$this->equalTo($message),
			'The file content is not correct.'
		);
	}

	/**
	 * Test JFilesystemElementFile::__get
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function test__Get_unknown()
	{
		$file = JFilesystemElementFile::getInstance(JPATH_TESTS . '/tmp/filesystem/test.txt', static::$system, 'w')->close();

		$this->setExpectedException('InvalidArgumentException');
		$file->unknown;
	}

	/**
	 * Test JFilesystemElementFile::__set
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function test__Set_mode()
	{
		$file = JFilesystemElementFile::getInstance(JPATH_TESTS . '/tmp/filesystem/test.txt', static::$system, 'w')->close();

		$file->mode = 'r';
		$this->assertThat(
			$file->mode,
			$this->equalTo('r'),
			'The mode is not correct.'
		);

		$file->mode = null;
		$this->assertThat(
			$file->mode,
			$this->equalTo(null),
			'The mode is not correct.'
		);
	}

	/**
	 * Test JFilesystemElementFile::__set
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function test__Set_contents()
	{
		$file = JFilesystemElementFile::getInstance(JPATH_TESTS . '/tmp/filesystem/test.txt', static::$system);
		$message = 'Hello world!';
		$file->contents = $message;

		$file->open('r');
		$this->assertThat(
			$file->readContents(20),
			$this->equalTo($message),
			'The file content is not correct'
		);
		$file->close();
	}

	/**
	 * Test JFilesystemElementFile::__set
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function test__Set_path()
	{
		$message = 'Hello world';
		$file = JFilesystemElementFile::getInstance(JPATH_TESTS . '/tmp/filesystem/test.txt', static::$system);
		$file->contents = $message;
		$file->path = JPATH_TESTS . '/tmp/filesystem/test2.txt';
		$this->assertThat(
			(string) $file,
			$this->equalTo(JPATH_TESTS . '/tmp/filesystem/test2.txt'),
			'The path name is not correct'
		);
		$this->assertThat(
			$file->contents,
			$this->equalTo($message),
			'The content is not correct'
		);
		$this->assertThat(
			JFilesystemElementFile::getInstance(JPATH_TESTS . '/tmp/filesystem/test.txt', static::$system)->exists,
			$this->equalTo(false),
			'The file exists'
		);
	}

	/**
	 * Test JFilesystemElementFile::__set
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function test__Set_dirpath()
	{
		$message = 'Hello world';
		$file = JFilesystemElementFile::getInstance(JPATH_TESTS . '/tmp/filesystem/from/test.txt', static::$system);
		JFilesystemElementDirectory::getInstance(JPATH_TESTS . '/tmp/filesystem/to', static::$system)->create();
		$file->contents = $message;
		$file->dirpath = JPATH_TESTS . '/tmp/filesystem/to';
		$this->assertThat(
			(string) $file,
			$this->equalTo(JPATH_TESTS . '/tmp/filesystem/to/test.txt'),
			'The path name is not correct'
		);
		$this->assertThat(
			$file->contents,
			$this->equalTo($message),
			'The content is not correct'
		);
		$this->assertThat(
			JFilesystemElementFile::getInstance(JPATH_TESTS . '/tmp/filesystem/from/test.txt', static::$system)->exists,
			$this->equalTo(false),
			'The file exists'
		);
	}

	/**
	 * Test JFilesystemElementFile::__set
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function test__Set_name()
	{
		$message = 'Hello world';
		$file = JFilesystemElementFile::getInstance(JPATH_TESTS . '/tmp/filesystem/test.txt', static::$system);
		$file->contents = $message;
		$file->name = 'test2.txt';
		$this->assertThat(
			(string) $file,
			$this->equalTo(JPATH_TESTS . '/tmp/filesystem/test2.txt'),
			'The path name is not correct'
		);
		$this->assertThat(
			$file->contents,
			$this->equalTo($message),
			'The content is not correct'
		);
		$this->assertThat(
			JFilesystemElementFile::getInstance(JPATH_TESTS . '/tmp/filesystem/test.txt', static::$system)->exists,
			$this->equalTo(false),
			'The file exists'
		);
	}

	/**
	 * Test JFilesystemElementFile::__set
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function test__Set_basename()
	{
		$message = 'Hello world';
		$file = JFilesystemElementFile::getInstance(JPATH_TESTS . '/tmp/filesystem/test.txt', static::$system);
		$file->contents = $message;
		$file->basename = 'test2';
		$this->assertThat(
			(string) $file,
			$this->equalTo(JPATH_TESTS . '/tmp/filesystem/test2.txt'),
			'The path name is not correct'
		);
		$this->assertThat(
			$file->contents,
			$this->equalTo($message),
			'The content is not correct'
		);
		$this->assertThat(
			JFilesystemElementFile::getInstance(JPATH_TESTS . '/tmp/filesystem/test.txt', static::$system)->exists,
			$this->equalTo(false),
			'The file exists'
		);

		$file = JFilesystemElementFile::getInstance(JPATH_TESTS . '/tmp/filesystem/test', static::$system);
		$file->contents = $message;
		$file->basename = 'test2';
		$this->assertThat(
			(string) $file,
			$this->equalTo(JPATH_TESTS . '/tmp/filesystem/test2'),
			'The path name is not correct'
		);
		$this->assertThat(
			$file->contents,
			$this->equalTo($message),
			'The content is not correct'
		);
		$this->assertThat(
			JFilesystemElementFile::getInstance(JPATH_TESTS . '/tmp/filesystem/test', static::$system)->exists,
			$this->equalTo(false),
			'The file exists'
		);
	}

	/**
	 * Test JFilesystemElementFile::__set
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function test__Set_extension()
	{
		$message = 'Hello world';
		$file = JFilesystemElementFile::getInstance(JPATH_TESTS . '/tmp/filesystem/test.txt', static::$system);
		$file->contents = $message;
		$file->extension = 'msg';
		$this->assertThat(
			(string) $file,
			$this->equalTo(JPATH_TESTS . '/tmp/filesystem/test.msg'),
			'The path name is not correct'
		);
		$this->assertThat(
			$file->contents,
			$this->equalTo($message),
			'The content is not correct'
		);
		$this->assertThat(
			JFilesystemElementFile::getInstance(JPATH_TESTS . '/tmp/filesystem/test.txt', static::$system)->exists,
			$this->equalTo(false),
			'The file exists'
		);

		$file->extension = '';
		$this->assertThat(
			(string) $file,
			$this->equalTo(JPATH_TESTS . '/tmp/filesystem/test'),
			'The path name is not correct'
		);
		$this->assertThat(
			$file->contents,
			$this->equalTo($message),
			'The content is not correct'
		);
		$this->assertThat(
			JFilesystemElementFile::getInstance(JPATH_TESTS . '/tmp/filesystem/test.msg', static::$system)->exists,
			$this->equalTo(false),
			'The file exists'
		);
	}

	/**
	 * Test JFilesystemElementFile::__set
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function test__Set_unknown()
	{
		$file = JFilesystemElementFile::getInstance(JPATH_TESTS . '/tmp/filesystem/test.txt', static::$system, 'w')->close();

		$this->setExpectedException('InvalidArgumentException');
		$file->unknown = true;
	}

	/**
	 * Test JFilesystemElementFile::__call
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function test__Call_open_close()
	{
		$file = JFilesystemElementFile::getInstance(JPATH_TESTS . '/tmp/filesystem/test.txt', static::$system);

		$file->open('w');
		$this->assertThat(
			$file->mode,
			$this->equalTo('w'),
			'The mode is not correct.'
		);

		$file->close();
	}

	/**
	 * Test JFilesystemElementFile::__call
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function test__Call_copy()
	{
		$file = JFilesystemElementFile::getInstance(JPATH_TESTS . '/tmp/filesystem/test.txt', static::$system);
		$message = 'Hello world!';

		$file->contents = $message;

		$file->copy($file2 = JFilesystemElementFile::getInstance(JPATH_TESTS . '/tmp/filesystem/test2.txt', static::$system));
		$this->assertThat(
			$file2->contents,
			$this->equalTo($message),
			'The content is not correct.'
		);

		$file->copy($directory = JFilesystemElementDirectory::getInstance(JPATH_TESTS . '/tmp/filesystem/test', static::$system));
		$this->assertThat(
			JFilesystemElementFile::getInstance(JPATH_TESTS . '/tmp/filesystem/test/test.txt', static::$system)->contents,
			$this->equalTo($message),
			'The content is not correct.'
		);
		
	}

	/**
	 * Test JFilesystemElementFile::__call
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function test__Call_delete()
	{
		$file = JFilesystemElementFile::getInstance(JPATH_TESTS . '/tmp/filesystem/test.txt', static::$system);
		$message = 'Hello world!';

		$file->open('w');
		$file->contents = $message;
		$file->close();

		$file->delete();
		$this->assertThat(
			$file->exists,
			$this->equalTo(false),
			'The file exists.'
		);
	}

	/**
	 * Test JFilesystemElementFile::__call
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function test__Call_flush()
	{
		$file = JFilesystemElementFile::getInstance(JPATH_TESTS . '/tmp/filesystem/test.txt', static::$system);
		$message = 'Hello world!';

		$file->open('w');
		$file->writeContents($message);
		$file->flush();

		$this->assertThat(
			$file->size,
			$this->equalTo(strlen($message)),
			'The size is not correct.'
		);

		$file->close();
	}

	/**
	 * Test JFilesystemElementFile::__call
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function test__Call_iterate()
	{
		$file = JFilesystemElementFile::getInstance(JPATH_TESTS . '/tmp/filesystem/test.txt', static::$system);

		$message = 'Hello world!';

		$file->open('w');
		$file->writeLine($message);
		$file->writeLine($message);
		$file->writeLine($message);
		$file->close();

		foreach ($file->open('r')->iterateLine() as $i => $line)
		{
			$this->assertThat(
				$line,
				$this->equalTo($message),
				'The line is not correct'
			);
		}
		$file->close();

		$file->open('w');
		$file->writeContents($message);
		$file->close();

		foreach ($file->open('r')->iterateCharacter() as $i => $char)
		{
			if ($char !== false)
			{
				$this->assertThat(
					$char,
					$this->equalTo($message[$i]),
					'The character is not correct'
				);
			}
		}
		$file->close();
	}

	/**
	 * Test JFilesystemElementFile::__call
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function test__Call_filters()
	{
		$message = 'Hello world!';
		$file = JFilesystemElementFile::getInstance(JPATH_TESTS . '/tmp/filesystem/test.txt', static::$system);
		$file->contents = $message;
		$file->open('r')->prependFilter('string.toupper');
		$this->assertThat(
			$file->readContents(50),
			$this->equalTo(strtoupper($message)),
			'The message is not upper case'
		);
		$file->close();

		$filter = $file->open('r')->appendFilter('string.toupper');
		$this->assertThat(
			$file->readContents(50),
			$this->equalTo(strtoupper($message)),
			'The message is not upper case'
		);
		$file->close();

		$file->open('r')->appendFilter('string.toupper');
		$filter = $file->appendFilter('string.tolower');
		$this->assertThat(
			$file->readContents(50),
			$this->equalTo(strtolower($message)),
			'The message is not lower case'
		);
		$file->close();
	}

	/**
	 * Test JFilesystemElementFile::__call
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function test__Call_unknown()
	{
		$file = JFilesystemElementFile::getInstance(JPATH_TESTS . '/tmp/filesystem/test.txt', static::$system);
		$this->setExpectedException('InvalidArgumentException');
		$file->unknown();
	}

	/**
	 * Test JFilesystemElementFile::__toString
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function test__Call_toString()
	{
		$file = JFilesystemElementFile::getInstance(JPATH_TESTS . '/tmp/filesystem/test.txt', static::$system);
		$this->assertThat(
			(string)$file,
			$this->equalTo(JPATH_TESTS . '/tmp/filesystem/test.txt'),
			'The string representation is not correct'
		);
	}
}
