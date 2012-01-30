<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Database
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

require_once JPATH_PLATFORM.'/joomla/log/log.php';
require_once JPATH_PLATFORM.'/joomla/database/iterator.php';
require_once JPATH_PLATFORM.'/joomla/database/database.php';
require_once JPATH_PLATFORM.'/joomla/database/database/mysql.php';
require_once JPATH_PLATFORM.'/joomla/database/query.php';
require_once JPATH_PLATFORM.'/joomla/database/database/mysqlquery.php';

/**
 * Test class for JDatabaseIterator using MySQL engine.
 *
 * @package     Joomla.UnitTest
 * @subpackage  Database
 */
class JDatabaseIteratorMySQLTest extends JoomlaDatabaseTestCase
{
	/**
	 * @var  JDatabaseMySQL
	 */
	protected $object;

	/**
	 * Gets the data set to be loaded into the database during setup
	 *
	 * @return  xml dataset
	 *
	 * @since   12.1
	 */
	protected function getDataSet()
	{
		return $this->createXMLDataSet(__DIR__.'/stubs/database.xml');
	}

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	protected function setUp()
	{
		@include_once JPATH_TESTS . '/config_mysql.php';
		if (class_exists('JMySQLTestConfig')) {
			$config = new JMySQLTestConfig;
		} else {
			$this->markTestSkipped('There is no MySQL test config file present.');
		}
		$this->object = JDatabase::getInstance(
			array(
				'driver' => $config->dbtype,
				'database' => $config->db,
				'host' => $config->host,
				'user' => $config->user,
				'password' => $config->password
			)
		);

		parent::setUp();
	}

	/**
	 * Test foreach control
	 *
	 * @covers JDatabaseIterator::__construct
	 * @covers JDatabaseIterator::__destruct
	 * @covers JDatabaseIterator::rewind
	 * @covers JDatabaseIterator::current
	 * @covers JDatabaseIterator::key
	 * @covers JDatabaseIterator::next
	 * @covers JDatabaseIterator::valid
	 * @covers JDatabaseIterator::freeResult
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testForEach()
	{
		$iterator = new JDatabaseIterator($this->object->getQuery(true)->select('title')->from('jos_dbtest'));
		$results = array(
			array('title' => 'Testing'),
			array('title' => 'Testing2'),
			array('title' => 'Testing3'),
			array('title' => 'Testing4')
		);
		foreach ($iterator as $i => $result)
		{
			$this->assertThat(
				$result,
				$this->equalTo($results[$i]),
				__LINE__
			);
		}
		foreach ($iterator as $i => $result)
		{
			$this->assertThat(
				$result,
				$this->equalTo($results[$i]),
				__LINE__
			);
		}

		$iterator = new JDatabaseIterator($this->object->getQuery(true)->select('title')->from('jos_dbtest'), array('offset' => 2));
		$results = array(
			array('title' => 'Testing3'),
			array('title' => 'Testing4')
		);
		foreach ($iterator as $i => $result)
		{
			$this->assertThat(
				$result,
				$this->equalTo($results[$i]),
				__LINE__
			);
		}
		foreach ($iterator as $i => $result)
		{
			$this->assertThat(
				$result,
				$this->equalTo($results[$i]),
				__LINE__
			);
		}

		$iterator = new JDatabaseIterator($this->object->getQuery(true)->select('title')->from('jos_dbtest'), array('limit' => 2));
		$results = array(
			array('title' => 'Testing'),
			array('title' => 'Testing2')
		);
		foreach ($iterator as $i => $result)
		{
			$this->assertThat(
				$result,
				$this->equalTo($results[$i]),
				__LINE__
			);
		}
		foreach ($iterator as $i => $result)
		{
			$this->assertThat(
				$result,
				$this->equalTo($results[$i]),
				__LINE__
			);
		}

		$iterator = new JDatabaseIterator($this->object->getQuery(true)->select('title')->from('jos_dbtest'), array('dbo' => $this->object));
		$results = array(
			array('title' => 'Testing'),
			array('title' => 'Testing2'),
			array('title' => 'Testing3'),
			array('title' => 'Testing4')
		);
		foreach ($iterator as $i => $result)
		{
			$this->assertThat(
				$result,
				$this->equalTo($results[$i]),
				__LINE__
			);
		}
		foreach ($iterator as $i => $result)
		{
			$this->assertThat(
				$result,
				$this->equalTo($results[$i]),
				__LINE__
			);
		}

		$iterator = new JDatabaseIterator($this->object->getQuery(true)->select('title')->from('jos_dbtest'), array('type' => 'stdClass'));
		$results = array(
			(object) array('title' => 'Testing'),
			(object) array('title' => 'Testing2'),
			(object) array('title' => 'Testing3'),
			(object) array('title' => 'Testing4')
		);
		foreach ($iterator as $i => $result)
		{
			$this->assertThat(
				$result,
				$this->equalTo($results[$i]),
				__LINE__
			);
		}
		foreach ($iterator as $i => $result)
		{
			$this->assertThat(
				$result,
				$this->equalTo($results[$i]),
				__LINE__
			);
		}

		$iterator = new JDatabaseIterator($this->object->getQuery(true)->select('title')->from('jos_dbtest'), array('type' => 'array'));
		$results = array(
			array(0 => 'Testing'),
			array(0 => 'Testing2'),
			array(0 => 'Testing3'),
			array(0 => 'Testing4')
		);
		foreach ($iterator as $i => $result)
		{
			$this->assertThat(
				$result,
				$this->equalTo($results[$i]),
				__LINE__
			);
		}
		foreach ($iterator as $i => $result)
		{
			$this->assertThat(
				$result,
				$this->equalTo($results[$i]),
				__LINE__
			);
		}

		$iterator = new JDatabaseIterator($this->object->getQuery(true)->select('id, title')->from('jos_dbtest'), array('key' => 'title'));
		$results = array(
			'Testing' => array('title' => 'Testing', 'id' => '1'),
			'Testing2' => array('title' => 'Testing2', 'id' => '2'),
			'Testing3' => array('title' => 'Testing3', 'id' => '3'),
			'Testing4' => array('title' => 'Testing4', 'id' => '4')
		);
		foreach ($iterator as $i => $result)
		{
			$this->assertThat(
				$result,
				$this->equalTo($results[$i]),
				__LINE__
			);
		}
		foreach ($iterator as $i => $result)
		{
			$this->assertThat(
				$result,
				$this->equalTo($results[$i]),
				__LINE__
			);
		}

		$iterator = new JDatabaseIterator($this->object->getQuery(true)->select('id, title')->from('jos_dbtest'), array('type' => 'stdClass', 'key' => 'title'));
		$results = array(
			'Testing' => (object) array('title' => 'Testing', 'id' => '1'),
			'Testing2' => (object) array('title' => 'Testing2', 'id' => '2'),
			'Testing3' => (object) array('title' => 'Testing3', 'id' => '3'),
			'Testing4' => (object) array('title' => 'Testing4', 'id' => '4')
		);
		foreach ($iterator as $i => $result)
		{
			$this->assertThat(
				$result,
				$this->equalTo($results[$i]),
				__LINE__
			);
		}
		foreach ($iterator as $i => $result)
		{
			$this->assertThat(
				$result,
				$this->equalTo($results[$i]),
				__LINE__
			);
		}

		$iterator = new JDatabaseIterator($this->object->getQuery(true)->select('id, title')->from('jos_dbtest'), array('type' => 'array', 'key' => 1));
		$results = array(
			'Testing' => array(0 => '1', 1=> 'Testing'),
			'Testing2' => array(0 => '2', 1=> 'Testing2'),
			'Testing3' => array(0 => '3', 1=> 'Testing3'),
			'Testing4' => array(0 => '4', 1=> 'Testing4')
		);
		foreach ($iterator as $i => $result)
		{
			$this->assertThat(
				$result,
				$this->equalTo($results[$i]),
				__LINE__
			);
		}
		foreach ($iterator as $i => $result)
		{
			$this->assertThat(
				$result,
				$this->equalTo($results[$i]),
				__LINE__
			);
		}

		// Testing exception
		try
		{
			$iterator = new JDatabaseIterator($this->object->getQuery(true)->select('id, title')->from('jos_dbtest'), array('type' => 'UnexistingClass'));
			$error = true;
		}
		catch (InvalidArgumentException $expected)
		{
			$error = false;
		}
		if ($error)
		{
			$this->fail('An expected exception has not been raised.');
		}
		try
		{
			$iterator = new JDatabaseIterator($this->object->getQuery(true)->select('id, title')->from('jos_dbtest'), array('dbo' => 'SomeError'));
			$error = true;
		}
		catch (InvalidArgumentException $expected)
		{
			$error = false;
		}
		if ($error)
		{
			$this->fail('An expected exception has not been raised.');
		}
		try
		{
			$iterator = new JDatabaseIterator($this->object->getQuery(true)->select('id, title')->from('jos_dbtest'), array('type' => 'array', 'key' => 'error'));
			$error = true;
		}
		catch (InvalidArgumentException $expected)
		{
			$error = false;
		}
		if ($error)
		{
			$this->fail('An expected exception has not been raised.');
		}
		try
		{
			$iterator = new JDatabaseIterator($this->object->getQuery(true)->select('id, title')->from('jos_dbtest'), array('key' => 0));
			$error = true;
		}
		catch (InvalidArgumentException $expected)
		{
			$error = false;
		}
		if ($error)
		{
			$this->fail('An expected exception has not been raised.');
		}
		try
		{
			$iterator = new JDatabaseIterator($this->object->getQuery(true)->select('id, title')->from('jos_dbtest'), array('offset' => 'bad value'));
			$error = true;
		}
		catch (InvalidArgumentException $expected)
		{
			$error = false;
		}
		if ($error)
		{
			$this->fail('An expected exception has not been raised.');
		}
		try
		{
			$iterator = new JDatabaseIterator($this->object->getQuery(true)->select('id, title')->from('jos_dbtest'), array('limit' => 'bas value'));
			$error = true;
		}
		catch (InvalidArgumentException $expected)
		{
			$error = false;
		}
		if ($error)
		{
			$this->fail('An expected exception has not been raised.');
		}
	}
}
