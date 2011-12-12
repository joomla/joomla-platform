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
class JFormRuleBooleanTest extends JoomlaTestCase
{
	/**
	 * set up for testing
	 *
	 * @return void
	 */
	public function setUp()
	{
		$this->saveFactoryState();
		jimport('joomla.utilities.xmlelement');
		require_once JPATH_PLATFORM.'/joomla/form/rules/boolean.php';
		$this->rule = new JFormRuleBoolean;
		$this->xml = simplexml_load_string('<form><field name="foo" /></form>', 'JXMLElement');
	}

	/**
	 * Tear down test
	 *
	 * @return void
	 */
	function tearDown()
	{
		$this->restoreFactoryState();
	}

	public function _test($value)
	{
		try {
			$this->rule->test($this->xml->field, $value);
		}
		catch(Exception $e) {
			return $e;
		}
		return true;
	}

	/**
	 * Test the JFormRuleBoolean::test method.
	 *
	 * @dataProvider provider
	 */
	public function testBoolean($value, $expected)
	{
		// Test fail conditions.
		if ($expected == false){
			// Test fail conditions.
			$this->assertThat(
				$this->_test($value),
				$this->isInstanceOf('Exception'),
				'Line:'.__LINE__.' The rule should fail and throw an exception.'
			);
		}
		else
		{
			// Test pass conditions.
			$this->assertThat(
				$this->_test($value),
				$this->isTrue(),
				'Line:'.__LINE__.' The rule should return true.'
			);
		}
	}

	public function provider()
	{
		return
		array(
			array(0, true),
			array('0', true),
			array(1, true),
			array('1', true),
			array('true', true),
			array('false', true),
			array('bogus', false),
			array('0_anything', false),
			array('anything_1_anything', false),
			array('anything_true_anything', false),
			array('anything_false', false)
		);
	}
}
