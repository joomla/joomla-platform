<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Authorisation
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * @package     Joomla.UnitTest
 * @subpackage  Authorisation
 * @since       12.1
 */
class JAuthorisationAuthoriserRootTest extends JoomlaTestCase
{
	/**
	 * @var    JAuthorisationRequestor
	 * @since  12.1
	 */
	protected $requestor;

	/**
	 * Sets up the fixture.
	 *
	 * This method is called before a test is executed.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	protected function setUp()
	{
		// Create the mock.
		$this->requestor = $this->getMock(
			'JAuthorisationRequestor',
			array('getIdentities'),
			// Constructor arguments.
			array(),
			// Mock class name.
			'',
			// Call original constructor.
			false
		);
	}

	/**
	 * Tests construction of the class object.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testConstruction()
	{
		$a = new JAuthorisationAuthoriserRoot;

		$this->assertThat(
			$a,
			$this->isInstanceOf('JAuthorisationAuthoriser')
		);
	}

	/**
	 * Tests the JAuthorisationAuthoriserDefault::isAllowed method.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testIsAllowed()
	{
		$a = new JAuthorisationAuthoriserRoot;

		$this->assertThat(
			$a->isAllowed('anything', $this->requestor),
			$this->isTrue(),
			'Check an arbitrary action is allowed.'
		);
	}

	/**
	 * Tests the JAuthoriserDefault::isAllowed method.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testSetRules()
	{
		$a = new JAuthorisationAuthoriserRoot;

		$this->assertThat(
			$a->setRules(),
			$this->identicalTo($a),
			'Checks chaining.'
		);
	}
}
