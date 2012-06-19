<?php
/**
 * @package     Joomla.UnitTest
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

include_once JPATH_PLATFORM . '/joomla/archive/bzip2.php';

/**
 * Test class for JArchiveBzip2.
 * Generated by PHPUnit on 2011-10-26 at 19:34:29.
 */
class JArchiveBzip2Test extends PHPUnit_Framework_TestCase {
	protected static $outputPath;

	/**
     * @var JArchiveBzip2
     */
	protected $object;

	/**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
	 */
	protected function setUp() {
		self::$outputPath = __DIR__ . '/output';

		if (!is_dir(self::$outputPath))
		{
			mkdir(self::$outputPath, 0777);
		}

		$this->object = new JArchiveBzip2;
	}

	/**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
	 */
	protected function tearDown() {

	}

	/**
	 * Tests the extract Method.
	 *
	 * @group   JArchive
	 * @return  void
	 */
	public function testExtract()
	{
		if (!JArchiveBzip2::isSupported())
		{
			$this->markTestSkipped('Bzip2 files can not be extracted.');
			return;
		}

		$this->object->extract(__DIR__ . '/logo.bz2', self::$outputPath . '/logo-bz2.png');
		$this->assertTrue(is_file(self::$outputPath . '/logo-bz2.png'));

		if (is_file(self::$outputPath . '/logo-bz2.png'))
		{
			unlink(self::$outputPath . '/logo-bz2.png');
		}
	}

}

?>
