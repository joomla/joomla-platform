<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

jimport('joomla.form.formrule');

/**
 * General inspector class for JFormRule.
 *
 * @package Joomla.UnitTest
 * @subpackage HTML
 * @since 11.3
 */
class JFormRuleInspector extends JFormRule
{
	/**
	* Sets any property from the class.
	*
	* @param string $property The name of the class property.
	* @param string $value The value of the class property.
	*
	* @return void
	*/
	public function __set($property, $value)
	{
		$this->$property = $value;
	}
}

/**
 * Test class for JForm.
 *
 * @package		Joomla.UnitTest
 * @subpackage  Form
 */
class JFormRuleTest extends JoomlaTestCase {
	/**
	 * Test JFormRule::test().
	 * 
	 * @return  void
	 * 
	 * @since   11.3
	 */
	public function testTest() {
		jimport('joomla.utilities.xmlelement');
		
		$rule = new JFormRuleInspector;
		$xml = simplexml_load_string('<form><field name="foo" /></form>', 'JXMLElement');

		$rule->regex = '^[a-zA-Z]+$';
		
		$this->assertThat(
			$rule->test($xml, 'truestring'),
			$this->equalTo(true)
		);
		
		$this->assertThat(
			$rule->test($xml, '%wrongstring%'),
			$this->equalTo(false)
		);
		
		//Test illegal regular expression
		$rule->regex = null;
		try
		{
			$rule->test($xml->field, 'bogus');
		}
		catch (JException $e)
		{
			return;
		}
		$this->fail('JFormRule::test() should throw a JException when no regexp is present');
	}
}
