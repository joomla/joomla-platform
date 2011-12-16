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
class JAuthorisationRuleTest extends PHPUnit_Framework_TestCase
{
	/**
	 * Tests the JPermission::__construct method.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function test__construct()
	{
		$p = new JAuthorisationRule(
			array(
				'edit' => array('foo' => true),
			)
		);

		$this->assertThat(
			(string) $p,
			$this->equalTo('{"edit":{"foo":1}}'),
			'Checks the constructor initialises input from an array.'
		);
	}

	/**
	 * Tests the JPermission::merge method.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testMerge()
	{
		$p = new JAuthorisationRule;

		$p->merge(
			array(
				'create' => array(-42 => true, 2 => true),
				'edit' => array('-42' => false, 3 => true),
			)
		);

		// Use the magic toString to get to the internal data.
		$this->assertThat(
			(string) $p,
			$this->equalTo('{"create":{"-42":1,"2":1},"edit":{"-42":0,"3":1}}'),
			'Tests merge from an array.'
		);

		$p = new JAuthorisationRule;
		$p->merge('{"foo":{"bar":1}}');

		// Use the magic toString to get to the internal data.
		$this->assertThat(
			(string) $p,
			$this->equalTo('{"foo":{"bar":1}}'),
			'Tests merge from a JSON string.'
		);
	}

	/**
	 * Tests the JPermission::merge method for an exception.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testMergeException()
	{
		$this->setExpectedException('UnexpectedValueException');

		$p = new JAuthorisationRule;
		$p->merge(new stdClass);
	}

	/**
	 * Tests the JPermission::set method for an exception.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testSet()
	{
		$p = new JAuthorisationRule;

		// Simple allow.
		$p->set('create', 42, true);

		// Do it again for code coverage.
		$p->set('create', 42, true);

		// Simple deny.
		$p->set('create', 43, false);

		// Change back to true, but deny should win.
		$p->set('create', 43, true);

		// Simple allow, second action.
		$p->set('edit', 43, true);

		$this->assertThat(
			(string) $p,
			$this->equalTo('{"create":{"42":1,"43":0},"edit":{"43":1}}')
		);
	}
}
