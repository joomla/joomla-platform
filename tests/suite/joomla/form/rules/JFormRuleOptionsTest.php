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
 * @package     Joomla.UnitTest
 * @subpackage  Form
 *
 */
class JFormRuleOptionsTest extends JoomlaTestCase
{
	/**
	 * Set up for testing
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	public function setUp()
	{
		$this->saveFactoryState();
		jimport('joomla.utilities.xmlelement');
		require_once JPATH_PLATFORM.'/joomla/form/rules/options.php';
		$this->rule = new JFormRuleOptions;
		$this->xml = simplexml_load_string(
			'<form><field name="field1"><option value="value1">Value1</option><option value="value2">Value2</option></field></form>',
			'JXMLElement'
		);
	}

	/**
	 * Tear down test
	 *
	 * @return void
	 *
	 * @since   11.1
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
		catch(JException $e) {
			return $e;
		}
		return true;
	}

	/**
	 * Test the JFormRuleEmail::test method.
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	public function testEmail()
	{
		// Initialise variables.
		

		// Test fail conditions.

		$result = $this->_test('bogus');
		$this->assertThat(
			$result,
			$this->isInstanceOf('Exception'),
			'Line:'.__LINE__.' The rule should fail and throw an exception.'
		);

		// Test pass conditions.

		$this->assertThat(
			$this->_test('value1'),
			$this->isTrue(),
			'Line:'.__LINE__.' value1 should pass and return true.'
		);

		$this->assertThat(
			$this->_test('value2'),
			$this->isTrue(),
			'Line:'.__LINE__.' value2 should pass and return true.'
		);
	}
}
