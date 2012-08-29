<?php
/**
 * @package     Joomla.UnitTest
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

require_once __DIR__ . '/JArchiveZipInspector.php';

/**
 * Test class for JArchiveZip.
 * Generated by PHPUnit on 2011-10-26 at 19:34:31.
 */
class JArchiveZipTest extends PHPUnit_Framework_TestCase
{
	protected static $outputPath;

    /**
     * @var JArchiveZip
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        self::$outputPath = __DIR__ . '/output';

		if (!is_dir(self::$outputPath)) {
			mkdir(self::$outputPath, 0777);
		}

        $this->object = new JArchiveZipInspector;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {

    }

    /**
     * @todo Implement testCreate().
     */
    public function testCreate()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
	 * Tests the extractNative Method.
	 *
	 * @group   JArchive
	 * @return  void
	 * @covers  JArchiveZip::extractNative
	 */
	public function testExtractNative()
	{
		if (!JArchiveZip::hasNativeSupport())
		{
			$this->markTestSkipped(
				'ZIP files can not be extracted nativly.'
			);
			return;
		}

		$this->object->accessExtractNative(__DIR__ . '/logo.zip', self::$outputPath);
		$this->assertTrue(is_file(self::$outputPath . '/logo-zip.png'));

		if (is_file(self::$outputPath . '/logo-zip.png'))
		{
			unlink(self::$outputPath . '/logo-zip.png');
		}
	}

	/**
	 * Tests the extractCustom Method.
	 *
	 * @group   JArchive
	 * @return  void
	 * @covers  JArchiveZip::extractCustom
	 * @covers  JArchiveZip::_readZipInfo
	 * @covers  JArchiveZip::_getFileData
	 */
	public function testExtractCustom()
	{
		if (!JArchiveZip::isSupported())
		{
			$this->markTestSkipped(
				'ZIP files can not be extracted.'
			);
			return;
		}

		$this->object->accessExtractCustom(__DIR__ . '/logo.zip', self::$outputPath);
		$this->assertTrue(is_file(self::$outputPath . '/logo-zip.png'));

		if (is_file(self::$outputPath . '/logo-zip.png'))
		{
			unlink(self::$outputPath . '/logo-zip.png');
		}
	}

	/**
	 * Tests the extract Method.
	 *
	 * @group   JArchive
	 * @return  void
	 * @covers  JArchiveZip::extract
	 */
	public function testExtract()
	{
		if (!JArchiveZip::isSupported())
		{
			$this->markTestSkipped(
				'ZIP files can not be extracted.'
			);
			return;
		}

		$this->object->extract(__DIR__ . '/logo.zip', self::$outputPath);
		$this->assertTrue(is_file(self::$outputPath . '/logo-zip.png'));

		if (is_file(self::$outputPath . '/logo-zip.png'))
		{
			unlink(self::$outputPath . '/logo-zip.png');
		}
	}

    /**
	 * Tests the hasNativeSupport Method.
	 *
	 * @group   JArchive
	 * @return  void
	 * @covers  JArchiveZip::hasNativeSupport
	 */
    public function testHasNativeSupport()
    {
        $this->assertEquals(
			(function_exists('zip_open') && function_exists('zip_read')),
			JArchiveZip::hasNativeSupport()
		);
    }

    /**
	 * Tests the isSupported Method.
	 *
	 * @group    JArchive
	 * @return   void
	 * @covers   JArchiveGzip::isSupported
	 * @depends  testHasNativeSupport
	 */
	public function testIsSupported()
	{
		$this->assertEquals(
			(JArchiveZip::hasNativeSupport() || extension_loaded('zlib')),
			JArchiveZip::isSupported()
		);
	}

	/**
	 * @covers  JArchiveZip::checkZipData
	 */
	public function testCheckZipData()
	{
		$dataZip = file_get_contents(__DIR__ . '/logo.zip');
		$this->assertTrue(
			$this->object->checkZipData($dataZip)
		);

		$dataTar = file_get_contents(__DIR__ . '/logo.tar');
		$this->assertFalse(
			$this->object->checkZipData($dataTar)
		);
	}
}
