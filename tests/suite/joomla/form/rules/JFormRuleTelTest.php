<?php
/**
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 * @package     Joomla.UnitTest
 */

/**
 * Test class for JForm.
 *
 * @package		Joomla.UnitTest
 * @subpackage  Form
 *
 */
class JFormRuleTelTest extends JoomlaTestCase
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
		require_once JPATH_PLATFORM.'/joomla/form/rules/tel.php';
		$this->rule = new JFormRuleTel;
		$this->xml = simplexml_load_string('<form><field name="tel1" plan="NANP" />
			<field name="tel2" plan="ITU-T" /><field name="tel3" plan="EPP" />
			<field name="tel4" /></form>',
			'JXMLElement');
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

	private function _test($field, $value)
	{
		try {
			$this->rule->test($this->xml->field[$field], $value);
		}
		catch(Exception $e) {
			return $e;
		}
		return true;
	}

	/**
	 * Test the JFormRuleTel::test method.
	 * 
	 * @dataProvider testTelData
	 */
	public function testTel($field, $value, $expected)
	{
		if ($expected == false){
			// Test fail conditions.
			$this->assertThat(
				$this->_test($field, $value),
				$this->isInstanceOf('Exception'),
				'Line:'.__LINE__.' The rule should fail and throw an exception.'
			);
		}
		else
		{
			// Test pass conditions.
			$this->assertThat(
				$this->_test($field, $value),
				$this->isTrue(),
				'Line:'.__LINE__.' The rule should return true.'
			);
		}
	}
	
	public function testTelData()
	{
		return
		array(
			// Test fail conditions NANP.
			array(0, 'bogus', false),
			array(0, '123451234512', false),
			array(0, 'anything_5555555555', false),
			array(0, '5555555555_anything', false),
	
			// Test fail conditions ITU-T.
			array(1, 'bogus', false),
			array(1, '123451234512', false),
			array(1, 'anything_5555555555', false),
			array(1, '5555555555_anything', false),
			array(1, '1 2 3 4 5 6 ', false),
			array(1, '5552345678', false),
			array(1, 'anything_555.5555555', false),
			array(1, '555.5555555_anything', false),
	
			// Test fail conditions EPP.
			array(2, 'bogus', false),
			array(2, '12345123451234512345', false),
			array(2, '123.1234', false),
			array(2, '23.1234', false),
			array(2, '3.1234', false),
			// Test fail conditions no plan.
			array(3, 'bogus', false),
	
			array(3, 'anything_555.5555555', false),
			array(3, '555.5555555x555_anything', false),
			array(3, '.5555555', true),
			array(3, '555.', false),
			array(3, '1 2 3 4 5 6 ', false),
			// Test pass conditions.
			//For NANP
			array(0, '(555) 234-5678', true),
			array(0, '1-555-234-5678', true),
			array(0, '+1-555-234-5678', true),
			array(0, '555-234-5678', true),
			array(0, '1-555-234-5678', true),
			array(0, '1 555 234 5678', true),
			//For ITU-T
			array(1, '+555 234 5678', true),
			array(1, '+123 555 234 5678', true),
			array(1, '+2 52 34 55', true),
			array(1, '+5552345678', true),
	
			//For EPP
			array(2, '+123.1234', true),
			array(2, '+23.1234', true),
			array(2, '+3.1234', true),
			array(2, '+3.1234x555', true),
	
			//For no plan
			array(3, '555 234 5678', true),
			array(3, '+123 555 234 5678', true),
			array(3, '+2 52 34 55', true),
			array(3, '5552345678', true),
			array(3, '+5552345678', true),
			array(3, '1 2 3 4 5 6 7', true),
			array(3, '123451234512', true)
		);
	} 
}
