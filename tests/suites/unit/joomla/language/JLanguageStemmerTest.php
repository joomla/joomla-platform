<?php
/**
 * @package    Joomla.UnitTest
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

/**
 * Test class for JLanguageStemmer.
 * Generated by PHPUnit on 2012-03-21 at 21:29:32.
 *
 * @package     Joomla.UnitTest
 * @subpackage  Language
 * @since       11.1
 */
class JLanguageStemmerTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var JLanguageStemmer
	 */
	protected $object;

	/**
	 * Test...
	 *
	 * @covers  JLanguageStemmer::getInstance
	 *
	 * @return void
	 */
	public function testGetInstance()
	{
		$instance = JLanguageStemmer::getInstance('porteren');

		$this->assertInstanceof(
			'JLanguageStemmer',
			$instance
		);

		$this->assertInstanceof(
			'JLanguageStemmerPorteren',
			$instance
		);

		$instance2 = JLanguageStemmer::getInstance('porteren');

		$this->assertSame(
			$instance,
			$instance2
		);
	}

	/**
	 * Test...
	 *
	 * @covers             JLanguageStemmer::getInstance
	 * @expectedException  RuntimeException
	 *
	 * @return void
	 */
	public function testGetInstanceException()
	{
		$instance = JLanguageStemmer::getInstance('unexisting');
	}
}
