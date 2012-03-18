<?php

/**
 * @package     Joomla.UnitTest
 * @subpackage  FileSystem
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * A unit test class for JFilesystemElementDirectory
 *
 * @package     Joomla.UnitTest
 * @subpackage  FileSystem
 *
 * @since       12.1
 */
class JFilesystemElementDirectoryPhpTest extends TestCaseFilesystemElementDirectory
{
	/**
	 * Test JFilesystemElementDirectory::__set
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function test__Set_permissions()
	{
		$directory = JFilesystemElementDirectory::getInstance(JPATH_TESTS . '/tmp/filesystem/test')->create();

		$directory->permissions = 0444;
		$this->assertThat(
			$directory->permissions,
			$this->equalTo(0444),
			'The permissions are not correct.'
		);

		$directory->permissions = 'u+w,o-r,g=w,g+x,o+w';
		$this->assertThat(
			$directory->permissions,
			$this->equalTo(0632),
			'The permissions are not correct.'
		);

		$directory->permissions = 'u=rx,o=r';
		$this->assertThat(
			$directory->permissions,
			$this->equalTo(0534),
			'The permissions are not correct.'
		);

		$directory->permissions = 'u-r,g-x';
		$this->assertThat(
			$directory->permissions,
			$this->equalTo(0124),
			'The permissions are not correct.'
		);

		$directory->permissions = 'a=rw';
		$this->assertThat(
			$directory->permissions,
			$this->equalTo(0666),
			'The permissions are not correct.'
		);

		$directory->permissions = 'a=-,u=rw';
		$this->assertThat(
			$directory->permissions,
			$this->equalTo(0600),
			'The permissions are not correct.'
		);

		$directory->permissions = 'g=u,u=-';
		$this->assertThat(
			$directory->permissions,
			$this->equalTo(0060),
			'The permissions are not correct.'
		);

		$directory->permissions = 'o=g,g=-';
		$this->assertThat(
			$directory->permissions,
			$this->equalTo(0006),
			'The permissions are not correct.'
		);

		$directory->permissions = 'u=o,o=-,u+x';
		$this->assertThat(
			$directory->permissions,
			$this->equalTo(0700),
			'The permissions are not correct.'
		);

		$file = JFilesystemElementFile::getInstance(JPATH_TESTS . '/tmp/filesystem/test/test.txt')->create();
		$directory->permissions = 'f:g=rwx,f:u=rw,f:o=r';
		$this->assertThat(
			$directory->permissions,
			$this->equalTo(0700),
			'The permissions are not correct.'
		);
		$this->assertThat(
			$file->permissions,
			$this->equalTo(0674),
			'The permissions are not correct.'
		);

		$file = JFilesystemElementFile::getInstance(JPATH_TESTS . '/tmp/filesystem/test/test.txt')->create();
		$subfile = JFilesystemElementFile::getInstance(JPATH_TESTS . '/tmp/filesystem/test/subtest/test.txt')->create();
		$directory->permissions = 'f:u=rw,f:g=rw,f:o=rw,f[0]:g=-,f[0]:u=rw,f[0]:o=-';
		$this->assertThat(
			$file->permissions,
			$this->equalTo(0600),
			'The permissions are not correct.'
		);
		$this->assertThat(
			$subfile->permissions,
			$this->equalTo(0666),
			'The permissions are not correct.'
		);

		$subtest = JFilesystemElementDirectory::getInstance(JPATH_TESTS . '/tmp/filesystem/test/subtest')->create();
		$subsubtest = JFilesystemElementDirectory::getInstance(JPATH_TESTS . '/tmp/filesystem/test/subtest/subsubtest')->create();
		$directory->permissions = 'd:g=rwx,d:u=rx,d:o=x';
		$this->assertThat(
			$directory->permissions,
			$this->equalTo(0700),
			'The permissions are not correct.'
		);
		$this->assertThat(
			$subtest->permissions,
			$this->equalTo(0571),
			'The permissions are not correct.'
		);
		$this->assertThat(
			$subsubtest->permissions,
			$this->equalTo(0571),
			'The permissions are not correct.'
		);

		$subtest = JFilesystemElementDirectory::getInstance(JPATH_TESTS . '/tmp/filesystem/test/subtest')->create();
		$subsubtest = JFilesystemElementDirectory::getInstance(JPATH_TESTS . '/tmp/filesystem/test/subtest/subsubtest')->create();
		$directory->permissions = 'd:u=rwx,d[0]:g=rw';
		$this->assertThat(
			$directory->permissions,
			$this->equalTo(0700),
			'The permissions are not correct.'
		);
		$this->assertThat(
			$subtest->permissions,
			$this->equalTo(0761),
			'The permissions are not correct.'
		);
		$this->assertThat(
			$subsubtest->permissions,
			$this->equalTo(0771),
			'The permissions are not correct.'
		);

		$this->setExpectedException('InvalidArgumentException');
		$directory->permissions = 'error:error';
	}
}
