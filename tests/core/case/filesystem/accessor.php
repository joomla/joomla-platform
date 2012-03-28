<?php

/**
 * @package     Joomla.UnitTest
 * @subpackage  FileSystem
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

require_once __DIR__ . '/stubs/testcontent.php';

/**
 * A unit test class for JFilesystemAccessor
 *
 * @package     Joomla.UnitTest
 * @subpackage  FileSystem
 *
 * @since       12.1
 */
abstract class TestCaseFilesystemAccessor extends TestCaseFilesystem
{
	/**
	 * Test JFilesystemAccessor::read
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testRead()
	{
		$message = 'Hello world!';

		$file = JFilesystemElementFile::getInstance(static::$path . '/test.txt', static::$system)->open('w');
		JFilesystemAccessor::write('Contents', array($file, $message));
		$file->close();

		$file = JFilesystemElementFile::getInstance(static::$path . '/test.txt', static::$system)->open('r');
		$this->assertThat(
			JFilesystemAccessor::read('Contents', array($file, 20)),
			$this->equalTo($message),
			'The content is not correct.'
		);
		$file->close();
	}

	/**
	 * Test JFilesystemAccessor::write
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testWrite()
	{
		$message = 'Hello world!';

		$file = JFilesystemElementFile::getInstance(static::$path . '/test.txt', static::$system)->open('w');
		JFilesystemAccessor::write('Contents', array($file, $message));
		$file->close();

		$this->assertThat(
			$file->contents,
			$this->equalTo($message),
			'The content is not correct.'
		);
	}

	/**
	 * Test JFilesystemAccessor::pull
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testPull()
	{
		$message = 'Hello world!';

		$file = JFilesystemElementFile::getInstance(static::$path . '/test.txt', static::$system)->open('w');
		JFilesystemAccessor::write('Contents', array($file, $message));
		$file->close();

		$this->assertThat(
			JFilesystemAccessor::pull('Contents', array($file)),
			$this->equalTo($message),
			'The content is not correct.'
		);
	}

	/**
	 * Test JFilesystemAccessor::push
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testPush()
	{
		$message = 'Hello world!';

		$file = JFilesystemElementFile::getInstance(static::$path . '/test.txt', static::$system);
		JFilesystemAccessor::push('Contents', array($file, $message));

		$this->assertThat(
			$file->contents,
			$this->equalTo($message),
			'The content is not correct.'
		);
	}

	/**
	 * Test JFilesystemAccessor::xxxAccessor
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testAccessor()
	{
		JFilesystemAccessor::registerAccessor('TestContents', 'TestFilesystemAccessorContents');

		$this->assertThat(
			JFilesystemAccessor::isAccessor('TestContents'),
			$this->equalTo(true),
			'TestContents is not an accessor.'
		);

		$this->assertThat(
			JFilesystemAccessor::getAccessor('TestContents'),
			$this->equalTo('TestFilesystemAccessorContents'),
			'TestContents is not the accessor TestFilesystemAccessorContents.'
		);

		$message = 'Hello world!';

		$file = JFilesystemElementFile::getInstance(static::$path . '/test.txt', static::$system)->open('w');
		$this->assertThat(
			JFilesystemAccessor::write('TestContents', array($file, $message)),
			$this->equalTo(strlen($message)),
			'The content is not correct.'
		);
		$file->close();

		$file = JFilesystemElementFile::getInstance(static::$path . '/test.txt', static::$system)->open('r');
		$this->assertThat(
			JFilesystemAccessor::read('TestContents', array($file, $message)),
			$this->equalTo($message),
			'The content is not correct.'
		);
		$file->close();

		JFilesystemAccessor::unregisterAccessor('TestContents');

		$this->assertThat(
			JFilesystemAccessor::isAccessor('TestContents'),
			$this->equalTo(false),
			'TestContents is an accessor.'
		);

		try
		{
			$file = JFilesystemElementFile::getInstance(static::$path . '/test.txt', static::$system)->open('w');
			JFilesystemAccessor::write('Unknown', array($file, $message));
			$this->fail('An expected exception has not been raised.');
		}
		catch (RuntimeException $e)
		{
		}

		try
		{
			JFilesystemAccessor::registerWriter('Unknown', 'unknown_writer');
			$file = JFilesystemElementFile::getInstance(static::$path . '/test.txt', static::$system)->open('w');
			JFilesystemAccessor::write('Unknown', array($file, $message));
			$this->fail('An expected exception has not been raised.');
		}
		catch (RuntimeException $e)
		{
		}

		try
		{
			JFilesystemAccessor::registerWriter('Unknown', array(new TestFilesystemAccessorContents, 'unknown_writer'));
			$file = JFilesystemElementFile::getInstance(static::$path . '/test.txt', static::$system)->open('w');
			JFilesystemAccessor::write('Unknown', array($file, $message));
			$this->fail('An expected exception has not been raised.');
		}
		catch (RuntimeException $e)
		{
		}
	}

	/**
	 * Test JFilesystemAccessor::xxxReader
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testReader()
	{
		JFilesystemAccessor::registerReader('TestContents', 'test_reader_contents');

		$this->assertThat(
			JFilesystemAccessor::isReader('TestContents'),
			$this->equalTo(true),
			'TestContents is not a reader.'
		);

		$this->assertThat(
			JFilesystemAccessor::getReader('TestContents'),
			$this->equalTo('test_reader_contents'),
			'TestContents is not the reader test_reader_contents.'
		);

		$file = JFilesystemElementFile::getInstance(static::$path . '/test.txt', static::$system)->open('w')->open('r');
		$this->assertThat(
			JFilesystemAccessor::read('TestContents', array($file)),
			$this->equalTo('Hello'),
			'The content is not correct.'
		);
		$file->close();

		JFilesystemAccessor::unregisterReader('TestContents');

		$this->assertThat(
			JFilesystemAccessor::isReader('TestContents'),
			$this->equalTo(false),
			'TestContents is a reader.'
		);
	}

	/**
	 * Test JFilesystemAccessor::xxxWriter
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testWriter()
	{
		JFilesystemAccessor::registerWriter('TestContents', 'test_writer_contents');

		$this->assertThat(
			JFilesystemAccessor::isWriter('TestContents'),
			$this->equalTo(true),
			'TestContents is not a writer.'
		);

		$this->assertThat(
			JFilesystemAccessor::getWriter('TestContents'),
			$this->equalTo('test_writer_contents'),
			'TestContents is not the reader test_writer_contents.'
		);

		$message = 'Hello world!';

		$file = JFilesystemElementFile::getInstance(static::$path . '/test.txt', static::$system)->open('w');
		$this->assertThat(
			JFilesystemAccessor::write('TestContents', array($file, $message)),
			$this->equalTo(strlen($message)),
			'The content is not correct.'
		);
		$file->close();

		JFilesystemAccessor::unregisterWriter('TestContents');

		$this->assertThat(
			JFilesystemAccessor::isWriter('TestContents'),
			$this->equalTo(false),
			'TestContents is a writer.'
		);
	}

	/**
	 * Test JFilesystemAccessor::xxxPuller
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testPuller()
	{
		JFilesystemAccessor::registerPuller('TestContents', 'test_puller_contents');

		$this->assertThat(
			JFilesystemAccessor::isPuller('TestContents'),
			$this->equalTo(true),
			'TestContents is not a puller.'
		);

		$this->assertThat(
			JFilesystemAccessor::getPuller('TestContents'),
			$this->equalTo('test_puller_contents'),
			'TestContents is not the puller test_puller_contents.'
		);

		$file = JFilesystemElementFile::getInstance(static::$path . '/test.txt', static::$system)->open('w')->open('r');
		$this->assertThat(
			JFilesystemAccessor::pull('TestContents', array($file)),
			$this->equalTo('Hello'),
			'The content is not correct.'
		);
		$file->close();

		JFilesystemAccessor::unregisterPuller('TestContents');

		$this->assertThat(
			JFilesystemAccessor::isPuller('TestContents'),
			$this->equalTo(false),
			'TestContents is a puller.'
		);
	}

	/**
	 * Test JFilesystemAccessor::xxxPusher
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testPusher()
	{
		JFilesystemAccessor::registerPusher('TestContents', 'test_pusher_contents');

		$this->assertThat(
			JFilesystemAccessor::isPusher('TestContents'),
			$this->equalTo(true),
			'TestContents is not a pusher.'
		);

		$this->assertThat(
			JFilesystemAccessor::getPusher('TestContents'),
			$this->equalTo('test_pusher_contents'),
			'TestContents is not the reader test_pusher_contents.'
		);

		$message = 'Hello world!';

		$file = JFilesystemElementFile::getInstance(static::$path . '/test.txt', static::$system)->open('w');
		$this->assertThat(
			JFilesystemAccessor::write('TestContents', array($file, $message)),
			$this->equalTo(strlen($message)),
			'The content is not correct.'
		);
		$file->close();

		JFilesystemAccessor::unregisterPusher('TestContents');

		$this->assertThat(
			JFilesystemAccessor::isPusher('TestContents'),
			$this->equalTo(false),
			'TestContents is a pusher.'
		);
	}

	/**
	 * Test JFilesystemAccessor::extractKey
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testExtractKey()
	{
		$message = 'Hello world!';

		$file = JFilesystemElementFile::getInstance(static::$path . '/test.txt', static::$system)->open('w');
		$this->assertThat(
			JFilesystemAccessor::write('TestContents', array($file, $message)),
			$this->equalTo(strlen($message)),
			'The content is not correct.'
		);
		$file->close();

		$file = JFilesystemElementFile::getInstance(static::$path . '/test.txt', static::$system)->open('r');
		$this->assertThat(
			JFilesystemAccessor::read('TestContents', array($file, $message)),
			$this->equalTo($message),
			'The content is not correct.'
		);
		$file->close();
	}
}
