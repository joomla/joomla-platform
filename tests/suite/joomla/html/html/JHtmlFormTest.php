<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  HTML
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

require_once JPATH_PLATFORM.'/joomla/html/html/form.php';

/**
 * Test class for JHtmlForm.
 *
 * @package     Joomla.UnitTest
 * @subpackage  HTML
 * @since       11.1
 */
class JHtmlFormTest extends JoomlaTestCase
{
	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	protected function setUp()
	{
		parent::setUp();

		jimport('joomla.utilities.utility');
		jimport('joomla.filter.filterinput');

		// Need a mock session
		$this->saveFactoryState();

		JFactory::$session = $this->getMockSession();
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	protected function tearDown()
	{
		$this->restoreFactoryState();

		parent::tearDown();
	}

	/**
	 * Tests the JHtmlForm::token method.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function testToken()
	{
		$this->assertThat(
			strlen(JHtmlForm::token()),
			$this->greaterThan(0),
			'Line:'.__LINE__.' The token method should return something without error.'
		);
	}
}