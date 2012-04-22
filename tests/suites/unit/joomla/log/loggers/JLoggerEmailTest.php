<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Log
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

require_once JPATH_PLATFORM.'/joomla/log/loggers/email.php';
require_once __DIR__.'/stubs/email/mock.mail.php';

/**
 * Test class for JLoggerEmail.
 */
class JLoggerEmailTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var   mixed  The main mail object from JFactory while we mock it out.
	 * @since 11.1
	 */
	protected $mailer;

	/**
	 * Setup for testing.
	 *
	 * @return void
	 */
	public function setUp()
	{
		$this->mailer = JFactory::$mailer;
		JFactory::$mailer = new JMailMock();
	}

	/**
	 * Tear down.
	 *
	 * @return void
	 */
	public function tearDown()
	{
		JFactory::$mailer = $this->mailer;
	}

	/**
	 * Test the JLoggerMail::addEntry method.
	 */
	public function testSendEmail01()
	{
		// Create config.
		$config = array(
			'categories' => 'Emergency Alerts',
			'priorities' => JLog::EMERGENCY,
			'from' => 'Person@example.com',
			'to' => 'Person@example.com'
		);

		// Get an instance of the email logger, passing in configuration
		$logger = new JLoggerEmail($config);

		// Add log entry
		$logger->addEntry(new JLogEntry('Database connection failed', JLog::EMERGENCY, 'Alert'));

		// Expected results
		$expected = array(
			'Person@example.com',
			NULL,
			'Person@example.com',
			'EMERGENCY: Database connection failed [alert]',
			'EMERGENCY: Database connection failed [alert]'
		);

		// Verify results
		$this->assertEquals(TestReflection::getValue($logger, 'mailer')->Sent, $expected);
	}

	/**
	 * Verify a long Subject is shortened
	 */
	public function testSendEmail02()
	{
		// Create config.
		$config = array(
			'categories' => 'Emergency Alerts',
			'priorities' => JLog::EMERGENCY,
			'from' => 'Person@example.com',
			'to' => 'Person@example.com'
		);

		// Get an instance of the email logger, passing in configuration
		$logger = new JLoggerEmail($config);

		// Add log entry
		$longMessage = "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.";
		$logger->addEntry(new JLogEntry($longMessage, JLog::EMERGENCY, 'Alert'));

		$expected = 'EMERGENCY: Lorem Ipsum is simply dummy text of the...';

		// Verify results
		$this->assertEquals(TestReflection::getValue($logger, 'mailer')->Subject, $expected);
	}

	/**
	 * Verify email is not sent if From is not specified
	 */
	public function testSendEmail03()
	{
		// Create config.
		$config = array(
			'categories' => 'Emergency Alerts',
			'priorities' => JLog::EMERGENCY,
			'to' => 'Person@example.com',
			'from' => null
		);

		// Get an instance of the email logger, passing in configuration
		$logger = new JLoggerEmail($config);

		// Test should fail due to null value for from
		$this->setExpectedException('RuntimeException');

		// Add log entry
		$logger->addEntry(new JLogEntry('This should fail', JLog::EMERGENCY, 'Alert'));
	}
}
