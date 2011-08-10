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
class JFormRuleEmailTest extends JoomlaTestCase
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
		require_once JPATH_PLATFORM.'/joomla/form/rules/email.php';
		$this->rule = new JFormRuleEmail;
		$this->xml = simplexml_load_string('<form><field name="email1" /><field name="email2" unique="true" /></form>', 'JXMLElement');
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
			$this->rule->test($this->xml->field, $value);
		}
		catch(JException $e) {
			return $e;
		}
		return true;
	}

	/**
	 * Test the JFormRuleEmail::test method.
	 */
	public function testEmail()
	{
		// Test fail conditions.

		$result = $this->_test('bogus');
		$this->assertThat(
			$result,
			$this->isInstanceOf('Exception'),
			'Line:'.__LINE__.' The rule should fail and throw an exception.'
		);

		// Test pass conditions.
		$this->assertThat(
			$this->_test('me@example.com'),
			$this->isTrue(),
			'Line:'.__LINE__.' The basic rule should pass and return true.'
		);

		$this->markTestIncomplete('More tests required');

		// TODO: Need to test the "field" attribute which adds to the unqiue test where clause.
		// TODO: Is the regex as robust/same as the mail class validation check?
		// TODO: Database error is prevents the following tests from working properly.
		// TODO:

		$this->assertThat(
			$this->_test('me@example.com'),
			$this->isTrue(),
			'Line:'.__LINE__.' The unique rule should pass and return true.'
		);
	}

	public function emailData()
	{
		return array(
			array('test@example.com'),
			array('firstnamelastname@domain.tld'),
			array('firstname+lastname@domain.tld'),
			array('firstname+middlename+lastname@domain.tld'),
			array('firstnamelastname@subdomain.domain.tld'),
			array('firstname+lastname@subdomain.domain.tld'),
			array('firstname+middlename+lastname@subdomain.domain.tld')
		);
	}

	/**
	 * @dataProvider emailData
	 */
	public function testEmailData($emailAddress)
	{
		$this->assertThat(
			$this->_test($emailAddress),
			$this->isTrue(),
			$emailAddress.' should have returned true but did not'
		);
	}
}
