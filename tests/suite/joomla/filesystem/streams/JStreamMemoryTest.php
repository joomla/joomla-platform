<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage Filesystem
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

require_once JPATH_PLATFORM . '/joomla/filesystem/streams/memory.php';

/**
 * Test class for JStreamMemory.
 *
 * @package	Joomla.UnitTest
 * @subpackage Filesystem
 */
class JStreamMemoryTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var JStreamMemory
	 */
	protected $object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return void
	 */
	protected function setUp()
	{
		$this->object = new JStreamMemory;
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @return void
	 */
	protected function tearDown()
	{
	}

	/**
	 * Test cases for the stream_open test
	 *
	 * @return array
	 */
	function casesOpen()
	{
		return array(
			'write' => array (
				'memory:///path/to/file',
				'w',
				null,
				null,
				true,
				'memory:///path/to/file',
			),
			'read' => array (
				'memory:///path/to/unexisting',
				'r',
				null,
				null,
				null,
				false
			),
			'append' => array (
				'memory:///path/to/file',
				'a',
				null,
				null,
				true,
				'memory:///path/to/file',
			),
			'append2' => array (
				'memory:///path/to/unexisting',
				'a',
				null,
				null,
				true,
				'memory:///path/to/unexisting',
			),
		);
	}

	/**
	 * testing stream_open().
	 *
	 * @param string $path		The path to buffer
	 * @param string $mode		The mode of the buffer
	 * @param string $options	The options
	 * @param string $opened_path The path
	 * @param string $expected	The expected test return
	 *
	 * @return void
	 * @dataProvider casesOpen
	 */
	public function testStreamOpen($path, $mode, $options, $opened_path, $expected, $name)
	{
		$files = ReflectionHelper::getValue($this->object, 'files');
		$existing = isset($files[md5($path)]);
		$return = $this->object->stream_open($path, $mode, $options, $opened_path);
		$this->assertThat(
			$return,
			$this->equalTo($expected)
		);
		if ($return)
		{
			$this->assertThat(
				md5($name),
				$this->equalTo(ReflectionHelper::getValue($this->object, 'name'))
			);
		}
		if ($mode == 'a' && $existing)
		{//var_dump($files);var_dump($path);var_dump(md5($path));exit();
			$files = ReflectionHelper::getValue($this->object, 'files');
			$this->assertThat(
				$files[md5($name)]->mtime,
				$this->logicalOr($this->greaterThan($files[md5($name)]->ctime), $this->equalTo($files[md5($name)]->ctime))
			);
		}
	}

	/**
	 * Test cases for the stream_read test
	 *
	 * @return array
	 */
	function casesRead()
	{
		return array(
			'basic' => array (
				'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
				'memory:///path/to/file',
				30,
				10,
				'EFGHIJKLMN',
			),
		);
	}

	/**
	 * testing stream_read().
	 *
	 * @param string $buffer   The buffer to perform the operation upon
	 * @param string $name	The name of the buffer
	 * @param int	$position The position in the buffer of the current pointer
	 * @param int	$count	The movement of the pointer
	 * @param bool   $expected The expected test return
	 *
	 * @return void
	 * @dataProvider casesRead
	 */
	public function testStreamRead($buffer, $name, $position, $count, $expected)
	{
		ReflectionHelper::setValue($this->object, 'name', md5($name));
		ReflectionHelper::setValue($this->object, 'position', $position);
		ReflectionHelper::setValue($this->object, 'files', array(md5($name) => (object)(array('buffer' => $buffer))));

		$this->assertThat(
			$expected,
			$this->equalTo($this->object->stream_read($count))
		);
	}

	/**
	 * Test cases for the stream_write test
	 *
	 * @return array
	 */
	function casesWrite()
	{
		return array(
			'basic' => array (
				'abcdefghijklmnop',
				'memory:///path/to/file',
				5,
				'ABCDE',
				'abcdeABCDEklmnop',
			),
		);
	}

	/**
	 * testing stream_write().
	 *
	 * @param string $buffer   The buffer to perform the operation upon
	 * @param string $name	The name of the buffer
	 * @param int	$position The position in the buffer of the current pointer
	 * @param string $write	The data to write
	 * @param bool   $expected The expected test return
	 *
	 * @return void
	 * @dataProvider casesWrite
	 */
	public function testStreamWrite( $buffer, $name, $position, $write, $expected )
	{
		ReflectionHelper::setValue($this->object, 'name', md5($name));
		ReflectionHelper::setValue($this->object, 'position', $position);
		ReflectionHelper::setValue($this->object, 'files', array(md5($name) => (object)(array('buffer' => $buffer))));

		$output = $this->object->stream_write($write);

		$this->assertThat(
			array(md5($name) => (object)array('buffer' => $expected)),
			$this->equalTo(ReflectionHelper::getValue($this->object, 'files'))
		);
	}

	/**
	 * Testing stream_tell.
	 *
	 * @return void
	 */
	public function testStreamTell()
	{
		$pos = 10;
		ReflectionHelper::setValue($this->object, 'position', $pos);

		$this->assertThat(
			$pos,
			$this->equalTo($this->object->stream_tell())
		);
	}

	/**
	 * Test cases for the stream_eof test
	 *
	 * @return array
	 */
	function casesEOF()
	{
		return array(
			'~EOF' => array (
				'abcdefghijklmnop',
				'memory:///path/to/file',
				5,
				false,
			),
			'EOF' => array (
				'abcdefghijklmnop',
				'memory:///path/to/file',
				17,
				true,
			),
		);
	}

	/**
	 * Testing stream_eof.
	 *
	 * @param string $buffer   The buffer to perform the operation upon
	 * @param string $name	The name of the buffer
	 * @param int	$position The position in the buffer of the current pointer
	 * @param bool   $expected The expected test return
	 *
	 * @return void
	 * @dataProvider casesEOF
	 */
	public function testStreamEOF( $buffer, $name, $position, $expected )
	{
		ReflectionHelper::setValue($this->object, 'name', md5($name));
		ReflectionHelper::setValue($this->object, 'position', $position);
		ReflectionHelper::setValue($this->object, 'files', array(md5($name) => (object)(array('buffer' => $buffer))));

		$this->assertThat(
			$expected,
			$this->equalTo($this->object->stream_eof())
		);
	}

	/**
	 * Test cases for the stream_seek test
	 *
	 * @return array
	 */
	function casesSeek()
	{
		return array(
			'basic' => array (
				'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
				'memory:///path/to/file',
				5,
				10,
				SEEK_SET,
				true,
				10,
			),
			'too_early' => array (
				'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
				'memory:///path/to/file',
				5,
				-10,
				SEEK_SET,
				false,
				5,
			),
			'off_end' => array (
				'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
				'memory:///path/to/file',
				5,
				100,
				SEEK_SET,
				false,
				5,
			),
			'is_pos' => array (
				'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
				'memory:///path/to/file',
				5,
				10,
				SEEK_CUR,
				true,
				15,
			),
			'is_neg' => array (
				'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
				'memory:///path/to/file',
				5,
				-100,
				SEEK_CUR,
				false,
				5,
			),
			'from_end' => array (
				'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
				'memory:///path/to/file',
				5,
				-10,
				SEEK_END,
				true,
				42,
			),
			'before_beg' => array (
				'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
				'memory:///path/to/file',
				5,
				-100,
				SEEK_END,
				false,
				5,
			),
			'bad_seek_code' => array (
				'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
				'memory:///path/to/file',
				5,
				-100,
				100,
				false,
				5,
			),
		);
	}

	/**
	 * Testing stream_seek.
	 *
	 * @param string $buffer	The buffer to perform the operation upon
	 * @param string $name		The name of the buffer
	 * @param int	$position	The position in the buffer of the current pointer
	 * @param int	$offset	The movement of the pointer
	 * @param int	$whence	The buffer seek op code
	 * @param bool   $expected	The expected test return
	 * @param int	$expectedPos The new buffer position pointer
	 *
	 * @return void
	 * @dataProvider casesSeek
	 */
	public function testStreamSeek( $buffer, $name, $position, $offset, $whence, $expected, $expectedPos )
	{
		ReflectionHelper::setValue($this->object, 'name', md5($name));
		ReflectionHelper::setValue($this->object, 'position', $position);
		ReflectionHelper::setValue($this->object, 'files', array(md5($name) => (object)(array('buffer' => $buffer))));

		$this->assertThat(
			$expected,
			$this->equalTo($this->object->stream_seek($offset, $whence))
		);
		$this->assertThat(
			$expectedPos,
			$this->equalTo(ReflectionHelper::getValue($this->object, 'position'))
		);
	}

	/**
	 * Test cases for the stream_stat test
	 *
	 * @return array
	 */
	function casesStat()
	{
		return array(
			'basic' => array (
				'abcdefghijklmnop',
				'memory:///path/to/file',
			),
		);
	}

	/**
	 * Testing stream_stat
	 *
	 * @return void
	 * @dataProvider casesStat
	 */
	public function testStreamStat($buffer, $name)
	{
		$now = time();
		ReflectionHelper::setValue($this->object, 'name', md5($name));
		ReflectionHelper::setValue(
			$this->object,
			'files',
			array(md5($name) => (object)(array('buffer' => $buffer, 'atime' => $now, 'mtime' => $now, 'ctime' => $now)))
		);
		$size = strlen($buffer);

		$stat = $this->object->stream_stat();
		$this->assertThat(
			$size,
			$this->equalTo($stat['size'])
		);
		$this->assertThat(
			array(
				1 => 0,
				2 => 0,
				3 => 1,
				4 => 0,
				5 => 0,
				6 => 0,
				7 => 16,
				8 => $now,
				9 => $now,
				10 => $now,
				11 => 512,
				12 => (int)ceil($size / 512),
				'ino' => 0,
				'mode' => 0,
				'nlink' => 1,
				'uid' => 0,
				'gid' => 0,
				'rdev' => 0,
				'size' => $size,
				'atime' => $now,
				'mtime' => $now,
				'ctime' => $now,
				'blksize' => 512,
				'blocks' => (int)ceil($size / 512)
			),
			$this->equalTo($stat)
		);
	}

	/**
	 * Testing url_stat
	 *
	 * @return void
	 * @dataProvider casesStat
	 */
	public function testUrlStat($buffer, $name)
	{
		$now = time();
		ReflectionHelper::setValue($this->object, 'name', md5($name));
		ReflectionHelper::setValue(
			$this->object,
			'files',
			array(md5($name) => (object)(array('buffer' => $buffer, 'atime' => $now, 'mtime' => $now, 'ctime' => $now)))
		);
		$size = strlen($buffer);

		$stat = $this->object->url_stat($name, null);
		$this->assertThat(
			$size,
			$this->equalTo($stat['size'])
		);
		$this->assertThat(
			array(
				1 => 0,
				2 => 0,
				3 => 1,
				4 => 0,
				5 => 0,
				6 => 0,
				7 => 16,
				8 => $now,
				9 => $now,
				10 => $now,
				11 => 512,
				12 => (int)ceil($size / 512),
				'ino' => 0,
				'mode' => 0,
				'nlink' => 1,
				'uid' => 0,
				'gid' => 0,
				'rdev' => 0,
				'size' => $size,
				'atime' => $now,
				'mtime' => $now,
				'ctime' => $now,
				'blksize' => 512,
				'blocks' => (int)ceil($size / 512)
			),
			$this->equalTo($stat)
		);
	}

	/**
	 * Testing unlink
	 *
	 * @return void
	 */
	public function testUnlink()
	{
		ReflectionHelper::setValue($this->object, 'name', md5('memory://dummy'));
		ReflectionHelper::setValue($this->object, 'files', array(md5('memory://dummy') => (object) array()));

		$this->assertThat(
			true,
			$this->equalTo($this->object->unlink('memory://dummy'))
		);
		$this->assertThat(
			false,
			$this->equalTo($this->object->unlink('memory://dummy'))
		);

		$files = ReflectionHelper::getValue($this->object, 'files');
		$this->assertThat(
			false,
			$this->equalTo(isset($files[md5('memory://dummy')]))
		);
	}

	/**
	 * Testing register
	 *
	 * @return void
	 */
	public function testRegister()
	{
		$this->assertThat(
			JStreamMemory::register('memory.test'),
			$this->equalTo(true)
		);
		$this->assertThat(
			JStreamMemory::register('memory.test'),
			$this->equalTo(false)
		);

		file_put_contents('memory.test://tmp', 'Hello');
		$this->assertThat(
			'Hello',
			$this->equalTo(file_get_contents('memory.test://tmp'))
		);

		$this->assertThat(
			file_exists('memory.test://unexisting'),
			$this->equalTo(false)
		);


		stream_wrapper_unregister('memory.test');
		$this->assertThat(
			JStreamMemory::register('memory.test'),
			$this->equalTo(true)
		);
		stream_wrapper_unregister('memory.test');
	}
}

