<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * Test class for JForm.
 *
 * @package		Joomla.UnitTest
 * @subpackage  Form
 *
 */
class JFormRuleBooleanTest extends TestCase
{
	/**
	 * set up for testing
	 *
	 * @return void
	 */
	public function setUp()
	{
		require_once JPATH_PLATFORM.'/joomla/form/rules/boolean.php';
	}

	/**
	 * Test the JFormRuleBoolean::test method.
	 */
	public function testBoolean()
	{
		$rule = new JFormRuleBoolean;
		$xml = simplexml_load_string('<form><field name="foo" /></form>');

		// Test fail conditions.

		$this->assertThat(
			$rule->test($xml->field, 'bogus'),
			$this->isFalse(),
			'Line:'.__LINE__.' The rule should fail and return false.'
		);

		$this->assertThat(
			$rule->test($xml->field, '0_anything'),
			$this->isFalse(),
			'Line:'.__LINE__.' The rule should fail and return false.'
		);

		$this->assertThat(
			$rule->test($xml->field, 'anything_1_anything'),
			$this->isFalse(),
			'Line:'.__LINE__.' The rule should fail and return false.'
		);

		$this->assertThat(
			$rule->test($xml->field, 'anything_true_anything'),
			$this->isFalse(),
			'Line:'.__LINE__.' The rule should fail and return false.'
		);

		$this->assertThat(
			$rule->test($xml->field, 'anything_false'),
			$this->isFalse(),
			'Line:'.__LINE__.' The rule should fail and return false.'
		);

		// Test pass conditions.

		$this->assertThat(
			$rule->test($xml->field, 0),
			$this->isTrue(),
			'Line:'.__LINE__.' The rule should pass and return true.'
		);

		$this->assertThat(
			$rule->test($xml->field, '0'),
			$this->isTrue(),
			'Line:'.__LINE__.' The rule should pass and return true.'
		);

		$this->assertThat(
			$rule->test($xml->field, 1),
			$this->isTrue(),
			'Line:'.__LINE__.' The rule should pass and return true.'
		);

		$this->assertThat(
			$rule->test($xml->field, '1'),
			$this->isTrue(),
			'Line:'.__LINE__.' The rule should pass and return true.'
		);

		$this->assertThat(
			$rule->test($xml->field, 'true'),
			$this->isTrue(),
			'Line:'.__LINE__.' The rule should pass and return true.'
		);

		$this->assertThat(
			$rule->test($xml->field, 'false'),
			$this->isTrue(),
			'Line:'.__LINE__.' The rule should pass and return true.'
		);
	}
}
