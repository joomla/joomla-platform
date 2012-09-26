<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Client
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

require_once JPATH_PLATFORM . '/joomla/mediawiki/http.php';
require_once JPATH_PLATFORM . '/joomla/http/transport/stream.php';

/**
 * Test class for JMediawiki.
 */
class JMediawikiHttpTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var    JRegistry  Options for the Mediawiki object.
	 * @since  12.1
	 */
	protected $options;

	/**
	 * @var    JHttpTransportStream object.
	 * @since  12.1
	 */
	protected $transport;

	/**
     * @var JMediawikiHttp object under test.
     */
	protected  $object;

	/**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
	 *
	 * @access protected
     */
	protected function setUp()
	{
		$this->options = new JRegistry;
		$this->transport = $this->getMock('JHttpTransportStream', array('request'), array($this->options), 'CustomTransport', false);
		$this->object = new JMediawikiHttp($this->options, $this->transport);
	}

	/**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
	 *
	 *  @access protected
     */
	protected function tearDown()
	{
	}
}
