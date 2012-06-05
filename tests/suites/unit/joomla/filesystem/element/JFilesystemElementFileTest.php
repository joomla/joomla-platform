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
 * @since       12.2
 */
class JFilesystemElementFilePhpTest extends TestCaseFilesystemElementFile
{
	/**
	 * Test JFilesystemElementFile::getInstance
	 *
	 * @return  void
	 *
	 * @since   12.2
	 */
	public function testGetInstance()
	{
		$file = JFilesystemElementFile::getInstance(static::$path . '/test.txt', static::$system);
		$this->assertThat(
			$file,
			$this->logicalNot($this->identicalTo(JFilesystemElementFile::getInstance('..' . static::$path . '/test.txt', static::$system))),
			'The object is not the same.'
		);
		parent::testGetInstance();
	}

	/**
	 * Test JFilesystemElementFile::__set
	 *
	 * @return  void
	 *
	 * @since   12.2
	 */
	public function test__Set_permissions()
	{
		$file = JFilesystemElementFile::getInstance(static::$path . '/test.txt', static::$system, 'w')->close();

		$file->permissions = 0444;
		$this->assertThat(
			$file->permissions,
			$this->equalTo(0444),
			'The permissions are not correct.'
		);

		$file->permissions = 'u+w,o-r,g=w,g+x,o+w';
		$this->assertThat(
			$file->permissions,
			$this->equalTo(0632),
			'The permissions are not correct.'
		);

		$file->permissions = 'u=rx,o=r';
		$this->assertThat(
			$file->permissions,
			$this->equalTo(0534),
			'The permissions are not correct.'
		);

		$file->permissions = 'u-r,g-x';
		$this->assertThat(
			$file->permissions,
			$this->equalTo(0124),
			'The permissions are not correct.'
		);

		$file->permissions = 'a=rw';
		$this->assertThat(
			$file->permissions,
			$this->equalTo(0666),
			'The permissions are not correct.'
		);

		$file->permissions = 'a=-,u=rw';
		$this->assertThat(
			$file->permissions,
			$this->equalTo(0600),
			'The permissions are not correct.'
		);

		$file->permissions = 'g=u,u=-';
		$this->assertThat(
			$file->permissions,
			$this->equalTo(0060),
			'The permissions are not correct.'
		);

		$file->permissions = 'o=g,g=-';
		$this->assertThat(
			$file->permissions,
			$this->equalTo(0006),
			'The permissions are not correct.'
		);

		$file->permissions = 'u=o,o=-';
		$this->assertThat(
			$file->permissions,
			$this->equalTo(0600),
			'The permissions are not correct.'
		);

		$this->setExpectedException('InvalidArgumentException');
		$file->permissions = 'error';
	}

	/**
	 * Test JFilesystemElementFile::__get
	 *
	 * @return  void
	 *
	 * @since   12.2
	 */
	public function test__Get_realpath()
	{
		$file = JFilesystemElementFile::getInstance(static::$path . '/test.txt', static::$system, 'w')->close();

		$this->assertThat(
			$file->realpath,
			$this->equalTo(static::$path . '/test.txt'),
			'The realpath is not correct.'
		);
	}

	/**
	 * Test JFilesystemElementFile::__get
	 *
	 * @return  void
	 *
	 * @since   12.2
	 */
	public function test__Get_is_writable()
	{
		$file = JFilesystemElementFile::getInstance(static::$path . '/test.txt', static::$system, 'w')->close();

		$this->assertThat(
			$file->is_writable,
			$this->equalTo(true),
			'The file is not writable.'
		);
	}

	/**
	 * Test JFilesystemElementFile::__get
	 *
	 * @return  void
	 *
	 * @since   12.2
	 */
	public function test__Get_position()
	{
		$file = JFilesystemElementFile::getInstance(static::$path . '/test.txt', static::$system, 'w');
		$message = 'Hello World!';

		$this->assertThat(
			$file->position,
			$this->equalTo(0),
			'The file is not at the beginning.'
		);

		$file->writeContents($message);

		$this->assertThat(
			$file->position,
			$this->equalTo(strlen($message)),
			'The file is not at the right position.'
		);

		$file->close();
	}

	/**
	 * Test JFilesystemElementFile::__get
	 *
	 * @return  void
	 *
	 * @since   12.2
	 */
	public function test__Get_link()
	{
		$file = JFilesystemElementFile::getInstance(static::$path . '/test.txt', static::$system);
		symlink(static::$path . '/to.txt', (string) $file);
		$this->assertThat(
			$file->link,
			$this->equalTo(static::$path . '/to.txt'),
			'The link is not correct'
		);
	}

	/**
	 * Test JFilesystemElementFile::__set
	 *
	 * @return  void
	 *
	 * @since   12.2
	 */
	public function test__Set_owner()
	{
		$file = JFilesystemElementFile::getInstance(static::$path . '/test.txt', static::$system, 'w')->close();

		$file->owner = $file->owner;
	}

	/**
	 * Test JFilesystemElementFile::__set
	 *
	 * @return  void
	 *
	 * @since   12.2
	 */
	public function test__Set_group()
	{
		$file = JFilesystemElementFile::getInstance(static::$path . '/test.txt', static::$system, 'w')->close();

		$file->group = $file->group;
	}

	/**
	 * Test JFilesystemElementFile::__set
	 *
	 * @return  void
	 *
	 * @since   12.2
	 */
	public function test__Set_position()
	{
		$file = JFilesystemElementFile::getInstance(static::$path . '/test.txt', static::$system, 'w');
		$file->writeContents('Hello world!');
		$file->close();

		$file->open('r');
		$file->position = 6;
		$this->assertThat(
			$file->readContents(20),
			$this->equalTo('world!'),
			'The file position is not correct'
		);
		$file->close();

		$file->open('r');
		$file->position = true;
		$this->assertThat(
			$file->readContents(20),
			$this->equalTo(''),
			'The file position is not correct'
		);
		$file->close();

		$file->open('r');
		$file->position = -3;
		$this->assertThat(
			$file->readContents(20),
			$this->equalTo('ld!'),
			'The file position is not correct'
		);
		$file->close();

		$file->open('r');
		$file->position = false;
		$this->assertThat(
			$file->readContents(20),
			$this->equalTo('Hello world!'),
			'The file position is not correct'
		);
		$file->close();

		$file->open('r');
		$file->position = 7;
		$file->position = 'C-1';
		$this->assertThat(
			$file->readContents(20),
			$this->equalTo('world!'),
			'The file position is not correct'
		);
		$file->close();

		$file->open('r');
		$file->position = 5;
		$file->position = 'C+1';
		$this->assertThat(
			$file->readContents(20),
			$this->equalTo('world!'),
			'The file position is not correct'
		);
		$file->close();

		$file->open('r');
		$file->position = 5;
		$file->position = 'C1';
		$this->assertThat(
			$file->readContents(20),
			$this->equalTo('world!'),
			'The file position is not correct'
		);
		$file->close();

		$this->setExpectedException('InvalidArgumentException');
		$file->open('r');
		$file->position = 5;
		$file->position = 'S+1';
		$this->assertThat(
			$file->readContents(20),
			$this->equalTo('world!'),
			'The file position is not correct'
		);
		$file->close();
	}

	/**
	 * Test JFilesystemElementFile::__set
	 *
	 * @return  void
	 *
	 * @since   12.2
	 */
	public function test__Set_link()
	{
		$file = JFilesystemElementFile::getInstance(static::$path . '/test.txt', static::$system);
		$file->link = static::$path . '/to.txt';
		$this->assertThat(
			readlink(static::$path . '/test.txt'),
			$this->equalTo(static::$path . '/to.txt'),
			'The link is not correct'
		);
	}

	/**
	 * Test JFilesystemElementFile::__call
	 *
	 * @return  void
	 *
	 * @since   12.2
	 */
	public function test__Call_truncate()
	{
		$file = JFilesystemElementFile::getInstance(static::$path . '/test.txt', static::$system);
		$message = 'Hello world!';
		$truncate = 5;

		$file->open('w');
		$file->writeContents($message);
		$file->truncate($truncate);

		$this->assertThat(
			$file->open('r')->readLine(),
			$this->equalTo(substr($message, 0, $truncate)),
			'The truncate is not correct.'
		);

		$file->close();
	}

	/**
	 * Test JFilesystemElementFile::__call
	 *
	 * @return  void
	 *
	 * @since   12.2
	 */
	public function test__Call_lock()
	{
		$file = JFilesystemElementFile::getInstance(static::$path . '/test.txt', static::$system);
		$file->contents = 'Hello world';

		$file->open('r');

		$this->assertThat(
			$file->lock(LOCK_SH),
			$this->equalTo(true),
			'The lock is not correct.'
		);

		$file->close();
	}

	/**
	 * Test JFilesystemElementFile::__call
	 *
	 * @return  void
	 *
	 * @since   12.2
	 */
	public function test__Call_filters()
	{
		parent::test__Call_filters();
		$message = 'Hello world!';
		$file = JFilesystemElementFile::getInstance(static::$path . '/test.txt', static::$system);
		$file->contents = $message;

		$file->open('r')->appendFilter('string.toupper');
		$filter = $file->appendFilter('string.tolower');
		$this->assertThat(
			$file->readContents(50),
			$this->equalTo(strtolower($message)),
			'The message is not lower case'
		);

		$file->position = 0;
		$file->removeFilter($filter);
		$this->assertThat(
			$file->readContents(50),
			$this->equalTo(strtoupper($message)),
			'The message is not upper case'
		);
		$file->close();
	}
}
