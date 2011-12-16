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
class JAuthorisationAuthoriserDefaultTest extends JoomlaTestCase
{
	/**
	 * @var    array
	 * @since  12.1
	 */
	protected $identities = array();

	/**
	 * @var    JAuthorisationRequestor
	 * @since  12.1
	 */
	protected $requestor;

	/**
	 * Mocks the getIdentities method of a JAuthorisationRequestor interface.
	 *
	 * @return  array
	 *
	 * @since   12.1
	 */
	public function mockGetIdentities()
	{
		return $this->identities;
	}

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
		parent::setUp();

		$this->identities = array();

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

		$this->assignMockCallbacks(
			$this->requestor,
			array(
				'getIdentities' => array($this, 'mockGetIdentities'),
			)
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
		$a = new JAuthorisationAuthoriserDefault;

		$this->assertThat(
			$a,
			$this->isInstanceOf('JAuthorisationAuthoriser')
		);
	}

	/**
	 * Tests the JAuthoriserDefault::isAllowed method.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testIsAllowed()
	{
		$a = new JAuthorisationAuthoriserDefault;

		$a->setRules(array());
		$this->assertThat(
			$a->isAllowed('unknown', $this->requestor),
			$this->isNull(),
			'Check empty input.'
		);

		$a->setRules(array(1, 2, 3));
		$this->assertThat(
			$a->isAllowed('unknown', $this->requestor),
			$this->isNull(),
			'Check crazy intput 1.'
		);

		$a->setRules(array(new stdClass));
		$this->assertThat(
			$a->isAllowed('unknown', $this->requestor),
			$this->isNull(),
			'Check crazy intput 2.'
		);

		$a->setRules(array('create' => new stdClass));
		$this->assertThat(
			$a->isAllowed('unknown', $this->requestor),
			$this->isNull(),
			'Check crazy intput 3.'
		);

		$a->setRules(json_decode('{"create":{"42":1,"43":0},"edit":{"44":1}}', true));

		$this->identities = array();
		$this->assertThat(
			$a->isAllowed('unknown', $this->requestor),
			$this->isNull(),
			'Check an unknown action'
		);

		$this->identities = array(42);
		$this->assertThat(
			$a->isAllowed('create', $this->requestor),
			$this->isTrue(),
			'Checks simple case, one identity.'
		);

		$this->identities = array(42, 44);
		$this->assertThat(
			$a->isAllowed('create', $this->requestor),
			$this->isTrue(),
			'Checks simple case, two identities with one allowed.'
		);

		$this->identities = array(42, 43);
		$this->assertThat(
			$a->isAllowed('create', $this->requestor),
			$this->isFalse(),
			'Checks explicit deny.'
		);

		$this->identities = array(86, 99);
		$this->assertThat(
			$a->isAllowed('create', $this->requestor),
			$this->isNull(),
			'Checks unknown identities.'
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
		$a = new JAuthorisationAuthoriserDefault;

		$this->assertThat(
			$a->setRules(),
			$this->identicalTo($a),
			'Checks chaining.'
		);
	}
}
