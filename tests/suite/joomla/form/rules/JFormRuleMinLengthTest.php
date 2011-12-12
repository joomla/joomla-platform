<?php
/**
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 * @package     Joomla.UnitTest
 */

defined('JPATH_PLATFORM') or die;

/**
 * Test class for JFormRuleMinLength.
 *
 * @package		Joomla.UnitTest
 * @subpackage	Form
 *
 */
class JFormRuleMinLengthTest extends JoomlaTestCase
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
		require_once JPATH_PLATFORM.'/joomla/form/rules/minlength.php';
		$this->rule = new JFormRuleMinLength;
		$this->xml = simplexml_load_string('<form><field name="MinLength" minLength="5" /></form>', 'JXMLElement');
	}

	private function _test($value)
	{
		try {
			$this->rule->test($this->xml->field[0], $value);
		}
		catch(Exception $e) {
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
	 * Test the JFormRuleMinLength::test method.
	 *
     * @dataProvider provider
     */
	public function testMinLength($value, $expected)
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
			array('日本語', false),
			array('abdé', false),
			array('tiny', false),
			array('bogus', true),
			array('foobar', true),
			array('áéíóú123456', true),
		);
	}
}