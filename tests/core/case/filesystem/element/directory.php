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
abstract class TestCaseFilesystemElementDirectory extends TestCaseFilesystem
{
	/**
	 * Test JFilesystemElementDirectory::getInstance
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testGetInstance()
	{
		$directory = JFilesystemElementDirectory::getInstance(JPATH_TESTS . '/tmp/filesystem/test', static::$system);

		// Test first time
		$this->assertThat(
			$directory,
			$this->isInstanceOf('JFilesystemElementDirectory'),
			'The object is not instance of JFilesystemElementDirectory.'
		);
	}

	/**
	 * Test JFilesystemElementDirectory::__get
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function test__Get_is_dir()
	{
		$directory = JFilesystemElementDirectory::getInstance(JPATH_TESTS . '/tmp/filesystem/test/subtest', static::$system)->create();

		$this->assertThat(
			$directory->is_dir,
			$this->equalTo(true),
			'The file is not a directory.'
		);
	}

	/**
	 * Test JFilesystemElementDirectory::__get
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function test__Get_is_file()
	{
		$directory = JFilesystemElementDirectory::getInstance(JPATH_TESTS . '/tmp/filesystem/test', static::$system)->create();

		$this->assertThat(
			$directory->is_file,
			$this->equalTo(false),
			'The file is a file.'
		);
	}

	/**
	 * Test JFilesystemElementDirectory::__get
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function test__Get_is_link()
	{
		$directory = JFilesystemElementDirectory::getInstance(JPATH_TESTS . '/tmp/filesystem/test', static::$system)->create();

		$this->assertThat(
			$directory->is_link,
			$this->equalTo(false),
			'The file is a link.'
		);
	}

	/**
	 * Test JFilesystemElementDirectory::__get
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function test__Get_files()
	{
		JFilesystemElementFile::getInstance(JPATH_TESTS . '/tmp/filesystem/test/subtest/subsubtest/test.txt', static::$system)->contents = 'Hello world!';
		JFilesystemElementFile::getInstance(JPATH_TESTS . '/tmp/filesystem/test/subtest/test.csv', static::$system)->contents = 'a,b,c';
		JFilesystemElementFile::getInstance(JPATH_TESTS . '/tmp/filesystem/test/test2.json', static::$system)->contents = '{}';
		JFilesystemElementFile::getInstance(JPATH_TESTS . '/tmp/filesystem/test/test.json', static::$system)->contents = '{}';

		$this->assertThat(
			array_keys(iterator_to_array(JFilesystemElementDirectory::getInstance(JPATH_TESTS . '/tmp/filesystem/test', static::$system)->files)),
			$this->equalTo(array('/test.json', '/test2.json')),
			'The files are not correct.'
		);
	}

	/**
	 * Test JFilesystemElementDirectory::__get
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function test__Get_directories()
	{
		JFilesystemElementDirectory::getInstance(JPATH_TESTS . '/tmp/filesystem/test/subtest/subsubtest', static::$system)->create();
		JFilesystemElementDirectory::getInstance(JPATH_TESTS . '/tmp/filesystem/test/subtest/subsubtest/last', static::$system)->create();
		JFilesystemElementDirectory::getInstance(JPATH_TESTS . '/tmp/filesystem/test/subtest/subsub2', static::$system)->create();
		JFilesystemElementDirectory::getInstance(JPATH_TESTS . '/tmp/filesystem/test/sub2/subsubtest', static::$system)->create();
		JFilesystemElementDirectory::getInstance(JPATH_TESTS . '/tmp/filesystem/test/sub2/subsub2', static::$system)->create();

		$this->assertThat(
			array_keys(iterator_to_array(JFilesystemElementDirectory::getInstance(JPATH_TESTS . '/tmp/filesystem/test', static::$system)->directories)),
			$this->equalTo(array('/sub2', '/subtest')),
			'The directories are not correct.'
		);
	}

	/**
	 * Test JFilesystemElementDirectory::__call
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function test__Call_create()
	{
		$directory = JFilesystemElementDirectory::getInstance(JPATH_TESTS . '/tmp/filesystem/test/subtest/subsubtest', static::$system)->create();
		$this->assertThat(
			$directory->exists,
			$this->equalTo(true),
			'The directory does not exist.'
		);
	}

	/**
	 * Test JFilesystemElementDirectory::__call
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function test__Call_delete()
	{
		$directory = JFilesystemElementDirectory::getInstance(JPATH_TESTS . '/tmp/filesystem/test/subtest/subsubtest', static::$system)->create();
		$file = JFilesystemElementFile::getInstance(JPATH_TESTS . '/tmp/filesystem/test/subtest/subsubtest/test.txt', static::$system)->create();
		JFilesystemElementDirectory::getInstance(JPATH_TESTS . '/tmp/filesystem/test', static::$system)->delete();
		$this->assertThat(
			$directory->exists,
			$this->equalTo(false),
			'The directory does exist.'
		);
		$this->assertThat(
			$file->exists,
			$this->equalTo(false),
			'The file does exist.'
		);
	}

	/**
	 * Data provider for test__Call_files
	 *
	 * @return  array
	 *
	 * @since   12.1
	 */
	public function data__Call_files()
	{
		return array(
			array(
				array(),
				array(
					'/test.json',
					'/test2.json'
				)
			),
			array(
				array(
					'recurse' => true
				),
				array(
					'/test.json',
					'/test2.json',
					'/subtest/test.csv',
					'/subtest/subsubtest/test.txt'
				)
			),
			array(
				array(
					'recurse' => true,
					'mode' => JFilesystemElementDirectoryContents::DEPTH_FIRST
				),
				array(
					'/subtest/subsubtest/test.txt',
					'/subtest/test.csv',
					'/test.json',
					'/test2.json'
				)
			),
			array(
				array(
					'recurse' => 1,
				),
				array(
					'/test.json',
					'/test2.json',
					'/subtest/test.csv',
				)
			),
			array(
				array(
					'recurse' => true,
					'filter' => '#\.csv$#'
				),
				array(
					'/subtest/test.csv',
				)
			),
			array(
				array(
					'recurse' => true,
					'exclude' => '#\.csv$#'
				),
				array(
					'/test.json',
					'/test2.json',
					'/subtest/subsubtest/test.txt'
				)
			),
			array(
				array(
					'recurse' => true,
					'filter_directory' => '#^subtest#'
				),
				array(
					'/test.json',
					'/test2.json',
					'/subtest/test.csv'
				)
			),
			array(
				array(
					'recurse' => true,
					'exclude_directory' => '#^subtest#'
				),
				array(
					'/test.json',
					'/test2.json',
				)
			),
			array(
				array(
					'recurse' => true,
					'accept' => function ($path, $relative, $system) {return $system->getFile($path . $relative)->contents == '{}';}
				),
				array(
					'/test.json',
					'/test2.json',
				)
			),
			array(
				array(
					'recurse' => true,
					'compare' => function($path, $a, $b, $system) {return -strcmp($a, $b);}
				),
				array(
					'/test2.json',
					'/test.json',
					'/subtest/test.csv',
					'/subtest/subsubtest/test.txt'
				)
			),
		);
	}

	/**
	 * Test JFilesystemElementDirectory::__call
	 *
	 * @dataProvider  data__Call_files
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function test__Call_files($options, $results)
	{
		JFilesystemElementFile::getInstance(JPATH_TESTS . '/tmp/filesystem/test/subtest/subsubtest/test.txt', static::$system)->contents = 'Hello world!';
		JFilesystemElementFile::getInstance(JPATH_TESTS . '/tmp/filesystem/test/subtest/test.csv', static::$system)->contents = 'a,b,c';
		JFilesystemElementFile::getInstance(JPATH_TESTS . '/tmp/filesystem/test/test2.json', static::$system)->contents = '{}';
		JFilesystemElementFile::getInstance(JPATH_TESTS . '/tmp/filesystem/test/test.json', static::$system)->contents = '{}';

		$this->assertThat(
			array_keys(iterator_to_array(JFilesystemElementDirectory::getInstance(JPATH_TESTS . '/tmp/filesystem/test', static::$system)->files($options))),
			$this->equalTo($results),
			'The files are not correct'
		);
	}

	/**
	 * Data provider for test__Call_directories
	 *
	 * @return  array
	 *
	 * @since   12.1
	 */
	public function data__Call_directories()
	{
		return array(
			array(
				array(
				),
				array(
					'/sub2',
					'/subtest',
				)
			),
			array(
				array(
					'recurse' => true
				),
				array(
					'/sub2',
					'/sub2/subsub2',
					'/sub2/subsubtest',
					'/subtest',
					'/subtest/subsub2',
					'/subtest/subsubtest',
					'/subtest/subsubtest/last',
				)
			),
			array(
				array(
					'recurse' => 1
				),
				array(
					'/sub2',
					'/sub2/subsub2',
					'/sub2/subsubtest',
					'/subtest',
					'/subtest/subsub2',
					'/subtest/subsubtest',
				)
			),
			array(
				array(
					'recurse' => true,
					'filter' => '#sub2$#'
				),
				array(
					'/sub2',
					'/sub2/subsub2',
				)
			),
			array(
				array(
					'recurse' => true,
					'exclude' => '#a#'
				),
				array(
					'/sub2',
					'/sub2/subsub2',
					'/sub2/subsubtest',
					'/subtest',
					'/subtest/subsub2',
					'/subtest/subsubtest',
				)
			),
			array(
				array(
					'recurse' => true,
					'accept' => function($path, $relative, $system) {return strpos($relative, '2') !== false;}
				),
				array(
					'/sub2',
					'/sub2/subsub2',
					'/sub2/subsubtest',
				)
			),
			array(
				array(
					'recurse' => true,
					'mode' => JFilesystemElementDirectoryContents::DEPTH_FIRST
				),
				array(
					'/sub2/subsub2',
					'/sub2/subsubtest',
					'/sub2',
					'/subtest/subsub2',
					'/subtest/subsubtest/last',
					'/subtest/subsubtest',
					'/subtest',
				)
			),
			array(
				array(
					'recurse' => true,
					'mode' => JFilesystemElementDirectoryContents::BREADTH_FIRST
				),
				array(
					'/sub2',
					'/sub2/subsub2',
					'/sub2/subsubtest',
					'/subtest',
					'/subtest/subsub2',
					'/subtest/subsubtest',
					'/subtest/subsubtest/last',
				)
			),
			array(
				array(
					'recurse' => true,
					'compare' => function($path, $a, $b, $system) {return -strcmp($a, $b);}
				),
				array(
					'/subtest',
					'/subtest/subsubtest',
					'/subtest/subsubtest/last',
					'/subtest/subsub2',
					'/sub2',
					'/sub2/subsubtest',
					'/sub2/subsub2',
				)
			),
		);
	}

	/**
	 * Test JFilesystemElementDirectory::__call
	 *
	 * @dataProvider  data__Call_directories
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function test__Call_directories($options, $results)
	{
		JFilesystemElementFile::getInstance(JPATH_TESTS . '/tmp/filesystem/test/subtest/subsubtest/test.txt', static::$system)->create();
		JFilesystemElementDirectory::getInstance(JPATH_TESTS . '/tmp/filesystem/test/subtest/subsubtest', static::$system)->create();
		JFilesystemElementDirectory::getInstance(JPATH_TESTS . '/tmp/filesystem/test/subtest/subsubtest/last', static::$system)->create();
		JFilesystemElementDirectory::getInstance(JPATH_TESTS . '/tmp/filesystem/test/subtest/subsub2', static::$system)->create();
		JFilesystemElementDirectory::getInstance(JPATH_TESTS . '/tmp/filesystem/test/sub2/subsubtest', static::$system)->create();
		JFilesystemElementDirectory::getInstance(JPATH_TESTS . '/tmp/filesystem/test/sub2/subsub2', static::$system)->create();

		$this->assertThat(
			array_keys(iterator_to_array(JFilesystemElementDirectory::getInstance(JPATH_TESTS . '/tmp/filesystem/test', static::$system)->directories($options))),
			$this->equalTo($results),
			'The directories are not correct'
		);
	}

	/**
	 * Test JFilesystemElementDirectory::__call
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function test__Call_copy()
	{
		JFilesystemElementFile::getInstance(JPATH_TESTS . '/tmp/filesystem/test/subtest/subsubtest/test.txt', static::$system)->contents = 'Hello world';
		JFilesystemElementDirectory::getInstance(JPATH_TESTS . '/tmp/filesystem/test/subtest/subsubtest', static::$system)->create();
		JFilesystemElementDirectory::getInstance(JPATH_TESTS . '/tmp/filesystem/test/subtest/subsubtest/last', static::$system)->create();
		JFilesystemElementDirectory::getInstance(JPATH_TESTS . '/tmp/filesystem/test/subtest/subsub2', static::$system)->create();
		JFilesystemElementDirectory::getInstance(JPATH_TESTS . '/tmp/filesystem/test/sub2/subsubtest', static::$system)->create();
		JFilesystemElementDirectory::getInstance(JPATH_TESTS . '/tmp/filesystem/test/sub2/subsub2', static::$system)->create();

		$dir = JFilesystemElementDirectory::getInstance(JPATH_TESTS . '/tmp/filesystem/test', static::$system);
		$copy = JFilesystemElementDirectory::getInstance(JPATH_TESTS . '/tmp/filesystem/copy', static::$system);
		
		$this->assertThat(
			$dir->copy($copy),
			$this->equalTo(strlen(JFilesystemElementFile::getInstance(JPATH_TESTS . '/tmp/filesystem/test/subtest/subsubtest/test.txt', static::$system)->contents)),
			'The number of bytes is not correct'
		);

		$this->assertThat(
			JFilesystemElementFile::getInstance(JPATH_TESTS . '/tmp/filesystem/copy/subtest/subsubtest/test.txt', static::$system)->contents,
			$this->equalTo(JFilesystemElementFile::getInstance(JPATH_TESTS . '/tmp/filesystem/test/subtest/subsubtest/test.txt', static::$system)->contents),
			'The file content is not correct'
		);

		$this->assertThat(
			JFilesystemElementDirectory::getInstance(JPATH_TESTS . '/tmp/filesystem/copy/subtest', static::$system)->exists,
			$this->equalTo(true),
			'The directory does not exist'
		);
		$this->assertThat(
			JFilesystemElementDirectory::getInstance(JPATH_TESTS . '/tmp/filesystem/copy/subtest/subsubtest', static::$system)->exists,
			$this->equalTo(true),
			'The directory does not exist'
		);
		$this->assertThat(
			JFilesystemElementDirectory::getInstance(JPATH_TESTS . '/tmp/filesystem/copy/subtest/subsubtest/last', static::$system)->exists,
			$this->equalTo(true),
			'The directory does not exist'
		);
		$this->assertThat(
			JFilesystemElementDirectory::getInstance(JPATH_TESTS . '/tmp/filesystem/copy/subtest/subsub2', static::$system)->exists,
			$this->equalTo(true),
			'The directory does not exist'
		);
		$this->assertThat(
			JFilesystemElementDirectory::getInstance(JPATH_TESTS . '/tmp/filesystem/copy/sub2/subsubtest', static::$system)->exists,
			$this->equalTo(true),
			'The directory does not exist'
		);
		$this->assertThat(
			JFilesystemElementDirectory::getInstance(JPATH_TESTS . '/tmp/filesystem/copy/sub2/subsub2', static::$system)->exists,
			$this->equalTo(true),
			'The directory does not exist'
		);
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
		$directory = JFilesystemElementDirectory::getInstance(JPATH_TESTS . '/tmp/filesystem/test', static::$system);
		$this->setExpectedException('InvalidArgumentException');
		$directory->unknown();
	}
}
