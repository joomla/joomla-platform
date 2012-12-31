<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Openstreetmap
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

include_once __DIR__ . '/stubs/JOpenstreetmapObjectMock.php';

/**
 * Test class for JOpenstreetmapObject.
 *
 * @package     Joomla.UnitTest
 * @subpackage  Openstreetmap
 *
 * @since       12.3
 */
class JOpenstreetmapObjectTest extends TestCase
{
	/**
	 * @var    JRegistry  Options for the Openstreetmap object.
	 * @since  12.3
	 */
	protected $options;

	/**
	 * @var    JHttp  Mock client object.
	 * @since  12.3
	 */
	protected $client;

	/**
	 * @var    JOpenstreetmapObjectMock  Object under test.
	 * @since  12.3
	 */
	protected $object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 *
	 * @return void
	 */
	protected function setUp()
	{
		$this->options = new JRegistry;
		$this->client = $this->getMock('JHttp', array('get', 'post', 'delete', 'put'));

		$this->object = new JOpenstreetmapObjectMock($this->options, $this->client);
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 *
	 * @return void
	 */
	protected function tearDown()
	{
	}

	/**
	 * Tests the setOption method
	 *
	 * @return void
	 *
	 * @since 12.3
	 */
	public function testSetOption()
	{
		$this->object->setOption('api.url', 'https://example.com/settest');

		$this->assertThat(
				$this->options->get('api.url'),
				$this->equalTo('https://example.com/settest')
		);
	}

	/**
	 * Tests the getSendRequest method
	 *
	 * @return  void
	 *
	 * @since   12.3
	 */
	public function testSendRequest()
	{
		// Method tested via requesting classes
		$this->markTestSkipped('This method is tested via requesting classes.');
	}
}
