<?php

/**
 * @package     Joomla.UnitTest
 * @subpackage  FileSystem
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * A unit test class for JFilesystemAccessorXml
 *
 * @package     Joomla.UnitTest
 * @subpackage  FileSystem
 *
 * @since       12.1
 */
abstract class TestCaseFilesystemAccessorXml extends TestCaseFilesystem
{
	/**
	 * Test JFilesystemAccessorXml::pull
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testPull()
	{
		$file = JFilesystemElementFile::getInstance(JPATH_TESTS . '/tmp/filesystem/test.xml', static::$system);
		$file->contents =
'<?xml version="1.0" encoding="utf-8"?>
	<extension type="component" version="2.5" method="upgrade">
	<name>com_test</name>
</extension>';
		$this->assertThat(
			$file->pullXml(),
			$this->isInstanceOf('JXMLElement'),
			'The xml content is not corrrect'
		);
	}

	/**
	 * Test JFilesystemAccessorXml::push
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testPush()
	{
		$file = JFilesystemElementFile::getInstance(JPATH_TESTS . '/tmp/filesystem/test.xml', static::$system);
		$file->contents =
'<?xml version="1.0" encoding="utf-8"?>
<extension type="component" version="2.5" method="upgrade">
	<name>com_test</name>
</extension>
';

		$file2 = JFilesystemElementFile::getInstance(JPATH_TESTS . '/tmp/filesystem/test2.xml', static::$system);
		$file2->pushXml($file->pullXml());
		$this->assertThat(
			$file2->contents,
			$this->equalTo($file->contents),
			'The xml content is not corrrect'
		);
	}
}
