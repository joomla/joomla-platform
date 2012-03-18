<?php

/**
 * @package     Joomla.UnitTest
 * @subpackage  FileSystem
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * A unit test class for JFilesystemAccessorCsv
 *
 * @package     Joomla.UnitTest
 * @subpackage  FileSystem
 *
 * @since       12.1
 */
abstract class TestCaseFilesystemAccessorCsv extends TestCaseFilesystem
{
	/**
	 * Test JFilesystemAccessorCsv::read
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testRead()
	{
		$line = 'Joomla!,"Content Management System"' . "\n";

		$file = JFilesystemElementFile::getInstance(JPATH_TESTS . '/tmp/filesystem/test.csv', static::$system);
		$file->contents = $line;

		$file->open('r');
		$this->assertThat(
			JFilesystemAccessorCsv::read($file),
			$this->equalTo(array('Joomla!', 'Content Management System')),
			'The content is not correct.'
		);
		$file->close();
	}

	/**
	 * Test JFilesystemAccessorCsv::write
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testWrite()
	{
		$file = JFilesystemElementFile::getInstance(JPATH_TESTS . '/tmp/filesystem/test.csv', static::$system)->open('w');
		JFilesystemAccessorCsv::write($file, array('Joomla!', 'Content Management System'));
		$file->close();

		$this->assertThat(
			$file->contents,
			$this->equalTo('Joomla!,"Content Management System"' . "\n"),
			'The content is not correct.'
		);
	}

	/**
	 * Test JFilesystemAccessorCsv::pull
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testPull()
	{
		$lines[] = 'Joomla!,"Content Management System"' . "\n";
		$lines[] = 'PHP,"Web programming language"' . "\n";

		$file = JFilesystemElementFile::getInstance(JPATH_TESTS . '/tmp/filesystem/test.csv', static::$system);
		$file->contents = $lines;

		$results = array(
			array('Joomla!', 'Content Management System'),
			array('PHP', 'Web programming language')
		);
		foreach (JFilesystemAccessorCsv::pull($file) as $i => $result)
		{
			$this->assertThat(
				$result,
				$this->equalTo($results[$i]),
				'The content is not correct.'
			);
		}
	}

	/**
	 * Test JFilesystemAccessorCsv::push
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testPush()
	{
		$file = JFilesystemElementFile::getInstance(JPATH_TESTS . '/tmp/filesystem/test.csv', static::$system);
		$csv = array(
			array('Joomla!', 'Content Management System'),
			array('PHP', 'Web programming language')
		);

		JFilesystemAccessorCsv::push($file, $csv);
		$this->assertThat(
			$file->contents,
			$this->equalTo(
'Joomla!,"Content Management System"
PHP,"Web programming language"
'),
			'The content is not correct'
		);
	}
}
