<?php
/**
 * @version		$Id: JDatabaseTest.php 20196 2011-01-09 02:40:25Z ian $
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

require_once JPATH_PLATFORM . '/joomla/database/database.php';
require_once __DIR__ . '/stubs/nosqldriver.php';

/**
 * Test class for JDatabase.
 * Generated by PHPUnit on 2009-10-08 at 22:00:38.
 */
class JDatabaseTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var	   JDatabase
	 * @since  11.4
	 */
	protected $db;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp()
	{
		$this->db = JDatabase::getInstance(
			array(
				'driver' => 'nosql',
				'database' => 'europa',
				'prefix' => '&',
			)
		);
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown()
	{
	}

	/**
	 * @todo Implement testGetInstance().
	 */
	public function testGetInstance()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @todo Implement test__destruct().
	 */
	public function test__destruct()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * Tests the JDatabase::getConnection method.
	 *
	 * @return  void
	 *
	 * @since   11.4
	 */
	public function testGetConnection()
	{
		ReflectionHelper::setValue($this->db, 'connection', 'foo');

		$this->assertThat(
			$this->db->getConnection(),
			$this->equalTo('foo')
		);
	}

	/**
	 * @todo Implement testGetConnectors().
	 */
	public function testGetConnectors()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * Tests the JDatabase::getCount method.
	 *
	 * @return  void
	 *
	 * @since   11.4
	 */
	public function testGetCount()
	{
		ReflectionHelper::setValue($this->db, 'count', 42);

		$this->assertThat(
			$this->db->getCount(),
			$this->equalTo(42)
		);
	}

	/**
	 * Tests the JDatabase::getDatabase method.
	 *
	 * @return  void
	 *
	 * @since   11.4
	 */
	public function testGetDatabase()
	{
		$this->assertThat(
			ReflectionHelper::invoke($this->db, 'getDatabase'),
			$this->equalTo('europa')
		);
	}

	/**
	 * Tests the JDatabase::getDateFormat method.
	 *
	 * @return  void
	 *
	 * @since   11.4
	 */
	public function testGetDateFormat()
	{
		$this->assertThat(
			$this->db->getDateFormat(),
			$this->equalTo('Y-m-d H:i:s')
		);
	}

	/**
	 * @todo Implement testAddQuoted().
	 */
	public function testAddQuoted()
	{
		// Remove the following lines when you implement this test.
		$this->markTestSkipped('Deprecated method');
	}

	/**
	 * @todo Implement testSplitSql().
	 */
	public function testSplitSql()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @todo Implement testIsQuoted().
	 */
	public function testIsQuoted()
	{
		// Remove the following lines when you implement this test.
		$this->markTestSkipped('Deprecated method');
	}

	/**
	 * @todo Implement testDebug().
	 */
	public function testDebug()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @todo Implement testGetUTFSupport().
	 */
	public function testGetUTFSupport()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @todo Implement testGetErrorNum().
	 */
	public function testGetErrorNum()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @todo Implement testGetErrorMsg().
	 */
	public function testGetErrorMsg()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * Tests the JDatabase::getLog method.
	 *
	 * @return  void
	 *
	 * @since   11.4
	 */
	public function testGetLog()
	{
		ReflectionHelper::setValue($this->db, 'log', 'foo');

		$this->assertThat(
			$this->db->getLog(),
			$this->equalTo('foo')
		);
	}

	/**
	 * @todo Implement testGetTicker().
	 */
	public function testGetTicker()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @todo Implement testNameQuote().
	 */
	public function testNameQuote()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * Tests the JDatabase::getDateFormat method.
	 *
	 * @return  void
	 *
	 * @since   11.4
	 */
	public function testGetPrefix()
	{
		$this->assertThat(
			$this->db->getPrefix(),
			$this->equalTo('&')
		);
	}

	/**
	 * Tests the JDatabase::getDateFormat method.
	 *
	 * @return  void
	 *
	 * @since   11.4
	 */
	public function testGetNullDate()
	{
		$this->assertThat(
			$this->db->getNullDate(),
			$this->equalTo('1BC')
		);
	}

	/**
	 * @todo Implement testSetQuery().
	 */
	public function testSetQuery()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @todo Implement testReplacePrefix().
	 */
	public function testReplacePrefix()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @todo Implement testGetQuery().
	 */
	public function testGetQuery()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @todo Implement testStderr().
	 */
	public function testStderr()
	{
		// Remove the following lines when you implement this test.
		$this->markTestSkipped('Deprecated method');
	}

	/**
	 * @todo Implement testGetVersion().
	 */
	public function testGetVersion()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * Tests the JDatabase::quote method.
	 *
	 * @return  void
	 *
	 * @since   11.4
	 */
	public function testQuote()
	{
		$this->assertThat(
			$this->db->quote('test', false),
			$this->equalTo("'test'"),
			'Tests the without escaping.'
		);

		$this->assertThat(
			$this->db->quote('test'),
			$this->equalTo("'-test-'"),
			'Tests the with escaping (default).'
		);
	}

	/**
	 * Tests the JDatabase::quoteName method.
	 *
	 * @return  void
	 *
	 * @since   11.4
	 */
	public function testQuoteName()
	{
		$this->assertThat(
			$this->db->quoteName('test'),
			$this->equalTo('[test]'),
			'Tests the left-right quotes on a string.'
		);

		$this->assertThat(
			$this->db->quoteName('a.test'),
			$this->equalTo('[a].[test]'),
			'Tests the left-right quotes on a dotted string.'
		);

		$this->assertThat(
			$this->db->quoteName(array('a', 'test')),
			$this->equalTo(array('[a]', '[test]')),
			'Tests the left-right quotes on an array.'
		);

		$this->assertThat(
			$this->db->quoteName(array('a.b', 'test.quote')),
			$this->equalTo(array('[a].[b]', '[test].[quote]')),
			'Tests the left-right quotes on an array.'
		);

		$this->assertThat(
			$this->db->quoteName(array('a.b', 'test.quote'), array(null, 'alias')),
			$this->equalTo(array('[a].[b]', '[test].[quote] AS [alias]')),
			'Tests the left-right quotes on an array.'
		);

		$this->assertThat(
			$this->db->quoteName(array('a.b', 'test.quote'), array('alias1', 'alias2')),
			$this->equalTo(array('[a].[b] AS [alias1]', '[test].[quote] AS [alias2]')),
			'Tests the left-right quotes on an array.'
		);

		$this->assertThat(
			$this->db->quoteName((object) array('a', 'test')),
			$this->equalTo(array('[a]', '[test]')),
			'Tests the left-right quotes on an object.'
		);

		ReflectionHelper::setValue($this->db, 'nameQuote', '/');

		$this->assertThat(
			$this->db->quoteName('test'),
			$this->equalTo('/test/'),
			'Tests the uni-quotes on a string.'
		);
	}

	/**
	 * @todo Implement testTruncate().
	 */
	public function testTruncateTable()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}
}
