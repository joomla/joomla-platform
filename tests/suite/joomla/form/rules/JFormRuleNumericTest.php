<?php
/**
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 * @package     Joomla.UnitTest
 */

defined('JPATH_PLATFORM') or die;

/**
 * Test class for JFormRuleNumeric.
 *
 * @package		Joomla.UnitTest
 * @subpackage	Form
 *
 */
class JFormRuleNumericTest extends JoomlaTestCase
{

	/**
	 * set up for testing
	 *
	 * @return void
	 */
	public function setUp()
	{
		$this->saveFactoryState();
		jimport('joomla.form.formrule');
		jimport('joomla.utilities.xmlelement');
		require_once JPATH_PLATFORM.'/joomla/form/rules/numeric.php';
		$this->rule = new JFormRuleNumeric;
		$this->xml = simplexml_load_string('<form><field name="Numeric" /></form>', 'JXMLElement');
	}

	private function _test($value)
	{
		try {
			$this->rule->test($this->xml->field[0], $value);
		}
		catch(JException $e) {
			return $e;
		}
		return true;
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

	/**
	 * Test the JFormRuleNumeric::test method.
	 *
     * @dataProvider provider
     */
	public function testNumeric($value, $expected)
	{
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
			array('123', true),
			array('-123', true),
			array('12.34', true),
			array('-12.34', true),
			array('0.1', true),
			array('0.', false),
			array('.0', false),
			array('*123', false),
			array('abc', false)
		);
	}
}