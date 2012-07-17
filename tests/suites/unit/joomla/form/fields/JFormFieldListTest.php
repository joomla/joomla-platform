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
class JFormFieldListTest extends TestCase
{
	/**
	 * Sets up dependancies for the test.
	 */
	protected function setUp()
	{
		require_once JPATH_PLATFORM.'/joomla/form/fields/list.php';
		include_once dirname(__DIR__).'/inspectors.php';
	}

	/**
	 * Test the getInput method.
	 */
	public function testGetInput()
	{
		$form = new JFormInspector('form1');

		$this->assertThat(
			$form->load('<form><field name="list" type="list" /></form>'),
			$this->isTrue(),
			'Line:'.__LINE__.' XML string should load successfully.'
		);

		$field = new JFormFieldList($form);

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
	 * Data provider for testGetDefaultPreset
	 *
	 * @since  12.2
	 */
	public function caseGetDefaultPreset()
	{
		// $attributes, $options, $default, $preset
		return array(
			array(
				'',
				'<option value="1">1</option><option value="2">2</option><option value="3">3</option>',
				'',
				''
			),
			array(
				'default="1"',
				'<option value="1">1</option><option value="2">2</option><option value="3">3</option>',
				'1',
				'1'
			),
			array(
				'preset="1"',
				'<option value="1">1</option><option value="2">2</option><option value="3">3</option>',
				'',
				'1'
			),
			array(
				'preset="1" default="2"',
				'<option value="1">1</option><option value="2">2</option><option value="3">3</option>',
				'2',
				'1'
			),
			array(
				'multiple="true"',
				'<option value="1">1</option><option value="2">2</option><option value="3">3</option>',
				array(),
				array()
			),
			array(
				'multiple="true"',
				'<option value="1" selected="true">1</option><option value="2" selected="true" default="true">2</option><option value="3" default="true">3</option>',
				array(2, 3),
				array(1, 2)
			),
		);
	}

	/**
	 * Test the getDefault and getPreset method.
	 *
	 * @dataProvider caseGetDefaultPreset
	 *
	 * @since  12.2
	 */
	public function testGetDefaultPreset($attributes, $options, $default, $preset)
	{
		$form = new JFormInspector('form1');

		$this->assertThat(
			$form->load('<form><field name="list" type="list" ' . $attributes . ' >' . $options . '</field></form>'),
			$this->isTrue(),
			'Line:'.__LINE__.' XML string should load successfully.'
		);

		$this->assertThat(
			$form->getField('list')->default,
			$this->equalTo($default),
			'Line:'.__LINE__.' The getDefault method should return correct default.'
		);

		$this->assertThat(
			$form->getField('list')->preset,
			$this->equalTo($preset),
			'Line:'.__LINE__.' The getDefault method should return correct preset.'
		);
	}
}
