<?php
/**
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 * @package     Joomla.UnitTest
 */

defined('JPATH_PLATFORM') or die;

/**
 * Test class for JFormRuleAlphanum.
 *
 * @package		Joomla.UnitTest
 * @subpackage	Form
 *
 */
class JFormRuleAlphanumTest extends JoomlaTestCase
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
		require_once JPATH_PLATFORM.'/joomla/form/rules/alphanum.php';
		$this->rule = new JFormRuleAlphanum;
		$this->xml = simplexml_load_string('<form><field name="alphanum" /></form>', 'JXMLElement');
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
	 * Test the JFormRuleAlphanum::test method.
	 *
     * @dataProvider provider
     */
	public function testAlphanum($value, $expected)
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
			array('abcdefghijklmnopqrstuvwxyz', true),
			array('ABCDEFGHIJKLMNOPQRSTUVWXYZ', true),
			array('A B C D E F G H I J K L M N O P Q R S T U V W X Y Z', true),
			array('0123456789', true),
			array('0 1 2 3 4 5 6 789', true),
			array('abc012345def', true),
			array('abc!@#$%¨&**()def', false),
			array('-123', false)
		);
	}
}