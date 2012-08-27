<?php
/**
 * @package     Joomla.UnitTest
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * Test class for JArchiveGzip.
 * Generated by PHPUnit on 2011-10-26 at 19:34:32.
 */
class JArchiveGzipTest extends PHPUnit_Framework_TestCase
{
	protected static $outputPath;

	/**
     * @var JArchiveGzip
     */
	protected $object;

	/**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
	protected function setUp()
	{
		self::$outputPath = __DIR__ . '/output';

		if (!is_dir(self::$outputPath))
		{
			mkdir(self::$outputPath, 0777);
		}

		$this->object = new JArchiveGzip;
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown()
	{

	}

	/**
	 * Tests the extract Method.
	 *
	 * @group   JArchive
	 * @return  void
	 * @covers  JArchiveGzip::extract
	 */
	public function testExtract()
	{
		if (!JArchiveGzip::isSupported())
		{
			$this->markTestSkipped('Gzip files can not be extracted.');
			return;
		}

		$this->object->extract(__DIR__ . '/logo.gz', self::$outputPath . '/logo-gz.png');
		$this->assertTrue(is_file(self::$outputPath . '/logo-gz.png'));

		if (is_file(self::$outputPath . '/logo-gz.png'))
		{
			unlink(self::$outputPath . '/logo-gz.png');
		}
	}

	/**
	 * Tests the extract Method.
	 *
	 * @group   JArchive
	 * @return  void
	 * @covers  JArchiveGzip::extract
	 */
	public function testExtractWithStreams()
	{
		if (!JArchiveGzip::isSupported())
		{
			$this->markTestSkipped('Gzip files can not be extracted.');
			return;
		}

		$this->object->extract(__DIR__ . '/logo.gz', self::$outputPath . '/logo-gz.png', array('use_streams' => true));
		$this->assertTrue(is_file(self::$outputPath . '/logo-gz.png'));

		if (is_file(self::$outputPath . '/logo-gz.png'))
		{
			unlink(self::$outputPath . '/logo-gz.png');
		}
	}

	/**
	 * Tests the isSupported Method.
	 *
	 * @group   JArchive
	 * @return  void
	 * @covers  JArchiveGzip::isSupported
	 */
	public function testIsSupported()
	{
		$this->assertEquals(
			extension_loaded('zlib'),
			JArchiveGzip::isSupported()
		);
	}

	/**
     * @todo Implement test_getFilePosition().
     */
	public function test_getFilePosition()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
				'This test has not been implemented yet.'
		);
	}
}
