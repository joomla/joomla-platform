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
 */
class JFormFieldIntegersTest extends JoomlaTestCase
{
	/**
	 * Sets up dependancies for the test.
	 */
	protected function setUp()
	{
		require_once JPATH_PLATFORM.'/joomla/form/fields/integer.php';
		include_once dirname(__DIR__).'/inspectors.php';
	}

	/**
	 * Test the getInput method.
	 */
	public function testGetInput()
	{
		$form = new JFormInspector('form1');

		$this->assertThat(
			$form->load('<form><field name="integer" type="integer" /></form>'),
			$this->isTrue(),
			'Line:'.__LINE__.' XML string should load successfully.'
		);

		$field = new JFormFieldInteger($form);

		$this->assertThat(
			$field->setup($form->getXml()->field, 'value'),
			$this->isTrue(),
			'Line:'.__LINE__.' The setup method should return true.'
		);

		$this->assertThat(
			strlen($field->input),
			$this->greaterThan(0),
			'Line:'.__LINE__.' The getInput method should return something without error.'
		);

		// TODO: Should check all the attributes have come in properly.
	}

	/**
	 * Test the getOptions method.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function testGetOptions()
	{
		$form = new JFormInspector('form1');

		$this->assertThat(
			$form->load('<form><field name="integer" type="integer" first="1" last="-5" step="1"/></form>'),
			$this->isTrue(),
			'Line:'.__LINE__.' XML string should load successfully.'
		);

		$field = new JFormFieldInteger($form);

		$this->assertThat(
			$field->setup($form->getXml()->field, 'value'),
			$this->isTrue(),
			'Line:'.__LINE__.' The setup method should return true.'
		);

		$this->assertThat(
			$field->input,
			$this->logicalNot(
				$this->StringContains('<option')
			),
			'Line:'.__LINE__.' The field should not contain any options.'
		);

		$this->assertThat(
			$form->load('<form><field name="integer" type="integer" first="-7" last="-5" step="1"/></form>'),
			$this->isTrue(),
			'Line:'.__LINE__.' XML string should load successfully.'
		);

		$field = new JFormFieldInteger($form);

		$this->assertThat(
			$field->setup($form->getXml()->field, 'value'),
			$this->isTrue(),
			'Line:'.__LINE__.' The setup method should return true.'
		);

		$this->assertThat(
			$field->input,
			$this->StringContains('<option value="-7">-7</option>'),
			'Line:'.__LINE__.' The field should contain -7 through -5 as options.'
		);

		$this->assertThat(
			$form->load('<form><field name="integer" type="integer" first="-7" last="-5" step="-1"/></form>'),
			$this->isTrue(),
			'Line:'.__LINE__.' XML string should load successfully.'
		);

		$field = new JFormFieldInteger($form);

		$this->assertThat(
			$field->setup($form->getXml()->field, 'value'),
			$this->isTrue(),
			'Line:'.__LINE__.' The setup method should return true.'
		);

		$this->assertThat(
			$field->input,
			$this->logicalNot(
				$this->StringContains('<option')
			),
			'Line:'.__LINE__.' The field should not contain any options.'
		);
	}
}
