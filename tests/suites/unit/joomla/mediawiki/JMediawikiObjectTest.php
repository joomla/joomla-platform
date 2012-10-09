<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Client
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

require_once JPATH_PLATFORM . '/joomla/mediawiki/http.php';
require_once JPATH_PLATFORM . '/joomla/mediawiki/object.php';
require_once __DIR__ . '/stubs/JMediawikiObjectMock.php';

/**
 * Test class for JGithub.
 */
class JMediawikiObjectTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var    JRegistry  Options for the Mediawiki object.
	 * @since  12.1
	 */
	protected $options;

	/**
	 * @var    JGithubHttp  Mock client object.
	 * @since  12.1
	 */
	protected $client;

	/**
	 * @var    JMediawikiObject  Object under test.
	 * @since  12.1
	 */
	protected $object;

	/**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
	protected function setUp()
	{
		$this->options = new JRegistry;
		$this->client = $this->getMock('JMediawikiHttp', array('get', 'post'));

		$this->object = new JMediawikiObjectMock($this->options, $this->client);
	}

	/**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
	protected function tearDown()
	{
	}

	/**
     * @covers JMediawikiObject::buildParameter
     * @todo   Implement testBuildParameter().
     */
	public function testBuildParameter()
	{
		$this->assertThat(
			$this->object->buildParameter(array('mango', 'apple', 'orange')),
			$this->equalTo('mango|apple|orange')
		);
	}

	/**
     * @covers JMediawikiObject::validateResponse
     * @todo   Implement testValidateResponse().
    */
	public function testValidateResponse()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}
}
