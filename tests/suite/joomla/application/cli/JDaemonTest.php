<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Application
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

require_once JPATH_PLATFORM.'/joomla/application/cli/daemon.php';
include_once __DIR__.'/stubs/JDaemonInspector.php';

/**
 * Test class for JDaemon.
 *
 * @package     Joomla.UnitTest
 * @subpackage  Application
 * @since       11.1
 */
class JDaemonTest extends JoomlaTestCase
{
	/**
	 * An instance of a JDaemon inspector.
	 *
	 * @var    JDaemonInspector
	 * @since  11.3
	 */
	protected $inspector;

	/**
	 * Setup for testing.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function setUp()
	{
		parent::setUp();

		// Skip this test suite if PCNTL  extension is not available
		if (!extension_loaded("PCNTL")){
			$this->markTestSkipped('The PCNTL extension is not available.');
		}

		// Get a new JDaemonInspector instance.
		$this->inspector = new JDaemonInspector;
		$this->inspector->setClassInstance($this->inspector);

		//$this->config->set('max_memory_limit', '2048M');

		// We are only coupled to Document and Language in JFactory.
		$this->saveFactoryState();
	}

	/**
	 * Overrides the parent tearDown method.
	 *
	 * @return  void
	 *
	 * @see     PHPUnit_Framework_TestCase::tearDown()
	 * @since   11.3
	 */
	protected function tearDown()
	{
		// Reset some daemon inspector static settings.
		JDaemonInspector::$pcntlChildExitStatus = 0;
		JDaemonInspector::$pcntlFork = 0;
		JDaemonInspector::$pcntlSignal = true;
		JDaemonInspector::$pcntlWait = 0;

		// Check if the inspector was instantiated.
		if (isset($this->inspector))
		{
			$this->inspector->setClassInstance(null);
		}

		$this->restoreFactoryState();

		parent::tearDown();
	}

	/**
	 * Overrides the parent tearDown method.
	 *
	 * @return  void
	 *
	 * @see     PHPUnit_Framework_TestCase::tearDownAfterClass()
	 * @since   11.3
	 */
	 public static function tearDownAfterClass()
	 {
		$pidPath = JPATH_BASE . '/jdaemontest.pid';

		if (file_exists($pidPath))
		{
			unlink($pidPath);
		}

		ini_restore('memory_limit');
		parent::tearDownAfterClass();
	 }

	/**
	 * Tests the JDaemon::changeIdentity method.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function testChangeIdentity()
	{
		$this->markTestIncomplete();
	}

	/**
	 * Tests the JDaemon::daemonize method.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function testDaemonize()
	{
		$this->markTestIncomplete();
	}

	/**
	 * Tests the JDaemon::fork method.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function testFork()
	{
		$this->markTestIncomplete();
	}

	/**
	 * Tests the JDaemon::gc method.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function testGc()
	{
		$this->markTestIncomplete();
	}

	/**
	 * Tests the JDaemon::isActive method.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function testIsActive()
	{
		$this->markTestIncomplete();
	}

	/**
	 * Tests the JDaemon::loadConfiguration method.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function testLoadConfiguration()
	{
		$this->markTestIncomplete();
	}

	/**
	 * Tests the JDaemon::setupSignalHandlers method.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function testSetupSignalHandlers()
	{
		$this->inspector->setClassSignals(array('SIGTERM', 'SIGHUP', 'SIGFOOBAR123'));
		$return = $this->inspector->setupSignalHandlers();

		$this->assertThat(
			count($this->inspector->setupSignalHandlers),
			$this->equalTo(2),
			'Check that only the two valid signals are setup.'
		);
		$this->assertThat(
			$return,
			$this->equalTo(true),
			'Check that only setupSignalHandlers return is true.'
		);
	}

	/**
	 * Tests the JDaemon::setupSignalHandlers method.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function testSetupSignalHandlersFailure()
	{
		JDaemonInspector::$pcntlSignal = false;
		$this->inspector->setClassSignals(array('SIGTERM', 'SIGHUP', 'SIGFOOBAR123'));
		$return = $this->inspector->setupSignalHandlers();

		$this->assertThat(
			count($this->inspector->setupSignalHandlers),
			$this->equalTo(0),
			'Check that no signals are setup.'
		);
		$this->assertThat(
			$return,
			$this->equalTo(false),
			'Check that only setupSignalHandlers return is false.'
		);
	}

	/**
	 * Tests the JDaemon::shutdown method.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function testShutdown()
	{
		$this->markTestIncomplete();
	}

	/**
	 * Tests the JDaemon::signal method.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function testSignal()
	{
		$this->markTestIncomplete();
	}

	/**
	 * Tests the JDaemon::start method.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function testStart()
	{
		$this->markTestIncomplete();
	}

	/**
	 * Tests the JDaemon::writeProcessIdFile method.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function testWriteProcessIdFile()
	{
		$pidPath = JPATH_BASE . '/jdaemontest.pid';

		if (file_exists($pidPath))
		{
			unlink($pidPath);
		}

		// we set a custom process id file path so that we don't interfere
		// with other tests that are running on a system
		$this->inspector->set('application_pid_file', $pidPath);

		// Get the current process id and set it to the daemon instance.
		$pid = (int) posix_getpid();
		$this->inspector->setClassProperty('processId', $pid);

		// Execute the writeProcessIdFile method.
		$this->inspector->writeProcessIdFile();

		// Check the value of the file.
		$this->assertEquals(
			$pid,
			(int) file_get_contents($this->inspector->getClassProperty('config')->get('application_pid_file')),
			'Line: '.__LINE__
		);

		// Check the permissions on the file.
		$this->assertEquals(
			'0644',
			substr(decoct(fileperms($this->inspector->getClassProperty('config')->get('application_pid_file'))), 1),
			'Line: '.__LINE__
		);
	}
}
