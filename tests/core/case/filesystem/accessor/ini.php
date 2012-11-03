<?php

/**
 * @package     Joomla.UnitTest
 * @subpackage  FileSystem
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * A unit test class for JFilesystemAccessorIni
 *
 * @package     Joomla.UnitTest
 * @subpackage  FileSystem
 *
 * @since       12.1
 */
abstract class TestCaseFilesystemAccessorIni extends TestCaseFilesystem
{
	/**
	 * Test JFilesystemAccessorIni::pull
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testPull()
	{
		$file = JFilesystemElementFile::getInstance(static::$path . '/test.ini', static::$system);
		$file->contents =
'KEY1=yes
KEY2="yes"

[section]

KEY3=5.6
KEY4="My value"
KEY5="My long \\
\"paragraph\""
';
		$this->assertThat(
			$file->pullIni(),
			$this->equalTo(array('KEY1' => '1', 'KEY2' => 'yes', 'KEY3' => '5.6', 'KEY4' => 'My value', 'KEY5' => 'My long \\' . "\n" . '"paragraph"')),
			'The ini content is not corrrect'
		);		

		$this->assertThat(
			$file->pullIni(true),
			$this->equalTo(array(
				'KEY1' => '1',
				'KEY2' => 'yes',
				'section' => array('KEY3' => '5.6', 'KEY4' => 'My value', 'KEY5' => 'My long \\' . "\n" . '"paragraph"')
			)),
			'The ini content is not corrrect'
		);		
	}

	/**
	 * Test JFilesystemAccessorIni::push
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testPush()
	{
		$file = JFilesystemElementFile::getInstance(static::$path . '/test.ini', static::$system);
		$file->pushIni(array(
				'KEY1' => true,
				'KEY12' => false,
				'KEY2' => 'yes',
				'section' => array('KEY3' => 5.6, 'KEY4' => 'My value', 'KEY5' => 'My long \\' . "\n" . '"paragraph"')
		));

		$this->assertThat(
			$file->contents,
			$this->equalTo(
'KEY1=true
KEY12=false
KEY2="yes"

[section]

KEY3=5.6
KEY4="My value"
KEY5="My long \\
\"paragraph\""
'),
			'The ini content is not corrrect'
		);
	}
}
