<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Client
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

require_once JPATH_PLATFORM . '/joomla/github/github.php';

/**
 * Test class for JGithub.
 *
 * @package     Joomla.UnitTest
 * @subpackage  Github
 *
 * @since       11.1
 */
class JGithubTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var    JRegistry  Options for the GitHub object.
	 * @since  11.4
	 */
	protected $options;

	/**
	 * @var    JGithubHttp  Mock client object.
	 * @since  11.4
	 */
	protected $client;

	/**
	 * @var    JGithubIssues  Object under test.
	 * @since  11.4
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
		$this->client = $this->getMock('JGithubHttp', array('get', 'post', 'delete', 'patch', 'put'));

		$this->object = new JGithub($this->options, $this->client);
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
	 * Tests the magic __get method - gists
	 *
	 * @since  11.3
	 *
	 * @return void
	 */
	public function test__GetGists()
	{
		$this->assertThat(
			$this->object->gists,
			$this->isInstanceOf('JGithubGists')
		);
	}

	/**
	 * Tests the magic __get method - issues
	 *
	 * @since  11.3
	 *
	 * @return void
	 */
	public function test__GetIssues()
	{
		$this->assertThat(
			$this->object->issues,
			$this->isInstanceOf('JGithubIssues')
		);
	}

	/**
	 * Tests the magic __get method - pulls
	 *
	 * @since  11.3
	 *
	 * @return void
	 */
	public function test__GetPulls()
	{
		$this->assertThat(
			$this->object->pulls,
			$this->isInstanceOf('JGithubPulls')
		);
	}

	/**
	 * Tests the magic __get method - refs
	 *
	 * @since  11.3
	 *
	 * @return void
	 */
	public function test__GetRefs()
	{
		$this->assertThat(
			$this->object->refs,
			$this->isInstanceOf('JGithubRefs')
		);
	}

	/**
	 * Tests the magic __get method - forks
	 *
	 * @since  11.4
	 *
	 * @return void
	 */
	public function test__GetForks()
	{
		$this->assertThat(
			$this->object->forks,
			$this->isInstanceOf('JGithubForks')
		);
	}

	/**
	 * Tests the magic __get method - commits
	 *
	 * @since  12.1
	 *
	 * @return void
	 */
	public function test__GetCommits()
	{
		$this->assertThat(
			$this->object->commits,
			$this->isInstanceOf('JGithubCommits')
		);
	}

	/**
	 * Tests the magic __get method - milestones
	 *
	 * @since  12.3
	 *
	 * @return void
	 */
	public function test__GetMilestones()
	{
		$this->assertThat(
			$this->object->milestones,
			$this->isInstanceOf('JGithubMilestones')
		);
	}

	/**
	 * Tests the magic __get method - refs
	 *
	 * @since  11.3
	 *
	 * @return void
	 */
	public function test__GetOther()
	{
		$this->assertThat(
			$this->object->other,
			$this->isNull()
		);
	}

	/**
	 * Tests the setOption method
	 *
	 * @since  11.3
	 *
	 * @return void
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
	 * Tests the getOption method
	 *
	 * @since  11.3
	 *
	 * @return void
	 */
	public function testGetOption()
	{
		$this->options->set('api.url', 'https://example.com/gettest');

		$this->assertThat(
			$this->object->getOption('api.url', 'https://example.com/gettest'),
			$this->equalTo('https://example.com/gettest')
		);
	}
}
