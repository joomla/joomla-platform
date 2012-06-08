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
	 * Data provider for testGetInputDefaultPreset
	 */
	public function caseGetInputDefaultPreset()
	{
		return array(
			// no multiple, no default, no preset, existing value
			array(
				'',
				'<option value="1">1</option><option value="2">2</option><option value="3">3</option>',
				'1',
'<select id="list" name="list">
	<option value="1">1</option>
	<option value="2">2</option>
	<option value="3">3</option>
</select>
',
'<select id="list" name="list">
	<option value="1" selected="selected">1</option>
	<option value="2">2</option>
	<option value="3">3</option>
</select>
'
			),

			// multiple, no default, no preset, existing value
			array(
				'multiple="true"',
				'<option value="1">1</option><option value="2">2</option><option value="3">3</option>',
				'1',
'<select id="list" name="list[]" multiple="multiple">
	<option value="1">1</option>
	<option value="2">2</option>
	<option value="3">3</option>
</select>
',
'<select id="list" name="list[]" multiple="multiple">
	<option value="1" selected="selected">1</option>
	<option value="2">2</option>
	<option value="3">3</option>
</select>
'
			),

			// no multiple, one default, no preset, existing value
			array(
				'default="1"',
				'<option value="1">1</option><option value="2">2</option><option value="3">3</option>',
				'2',
'<select id="list" name="list">
	<option value="1" selected="selected">1</option>
	<option value="2">2</option>
	<option value="3">3</option>
</select>
',
'<select id="list" name="list">
	<option value="1">1</option>
	<option value="2" selected="selected">2</option>
	<option value="3">3</option>
</select>
'
			),

			// no multiple, one default, no preset, empty value
			array(
				'default="1"',
				'<option value="1">1</option><option value="2">2</option><option value="3">3</option>',
				'',
'<select id="list" name="list">
	<option value="1" selected="selected">1</option>
	<option value="2">2</option>
	<option value="3">3</option>
</select>
',
'<select id="list" name="list">
	<option value="1" selected="selected">1</option>
	<option value="2">2</option>
	<option value="3">3</option>
</select>
'
			),

			// no multiple, no default, one preset, existing value
			array(
				'preset="1"',
				'<option value="1">1</option><option value="2">2</option><option value="3">3</option>',
				'2',
'<select id="list" name="list">
	<option value="1" selected="selected">1</option>
	<option value="2">2</option>
	<option value="3">3</option>
</select>
',
'<select id="list" name="list">
	<option value="1">1</option>
	<option value="2" selected="selected">2</option>
	<option value="3">3</option>
</select>
'
			),

			// no multiple, no default, no preset, existing value, readonly
			array(
				'readonly="true"',
				'<option value="1">1</option><option value="2">2</option><option value="3">3</option>',
				'2',
'<select id="list" name="" disabled="disabled">
	<option value="1">1</option>
	<option value="2">2</option>
	<option value="3">3</option>
</select>
<input type="hidden" name="list" value=""/>',
'<select id="list" name="" disabled="disabled">
	<option value="1">1</option>
	<option value="2" selected="selected">2</option>
	<option value="3">3</option>
</select>
<input type="hidden" name="list" value="2"/>'
			),

			// multiple, no default, one preset, existing value, readonly
			array(
				'multiple="true" readonly="true"',
				'<option value="1">1</option><option value="2">2</option><option value="3">3</option>',
				'2',
'<select id="list" name="" disabled="disabled" multiple="multiple">
	<option value="1">1</option>
	<option value="2">2</option>
	<option value="3">3</option>
</select>
',
'<select id="list" name="" disabled="disabled" multiple="multiple">
	<option value="1">1</option>
	<option value="2" selected="selected">2</option>
	<option value="3">3</option>
</select>
<input type="hidden" name="list[]" value="2"/>'
			),

			// multiple, no default, one preset, existing value, readonly
			array(
				'multiple="true" readonly="true"',
				'<option value="1">1</option><option value="2">2</option><option value="3">3</option>',
				array('1', '2'),
'<select id="list" name="" disabled="disabled" multiple="multiple">
	<option value="1">1</option>
	<option value="2">2</option>
	<option value="3">3</option>
</select>
',
'<select id="list" name="" disabled="disabled" multiple="multiple">
	<option value="1" selected="selected">1</option>
	<option value="2" selected="selected">2</option>
	<option value="3">3</option>
</select>
<input type="hidden" name="list[]" value="1"/><input type="hidden" name="list[]" value="2"/>'
			),

			// no multiple, one default, one preset, empty value
			array(
				'preset="1" default="2"',
				'<option value="1">1</option><option value="2">2</option><option value="3">3</option>',
				'',
'<select id="list" name="list">
	<option value="1" selected="selected">1</option>
	<option value="2">2</option>
	<option value="3">3</option>
</select>
',
'<select id="list" name="list">
	<option value="1">1</option>
	<option value="2" selected="selected">2</option>
	<option value="3">3</option>
</select>
'
			),

			// no multiple, one default, one preset, existing value
			array(
				'preset="1" default="2"',
				'<option value="1">1</option><option value="2">2</option><option value="3">3</option>',
				'3',
'<select id="list" name="list">
	<option value="1" selected="selected">1</option>
	<option value="2">2</option>
	<option value="3">3</option>
</select>
',
'<select id="list" name="list">
	<option value="1">1</option>
	<option value="2">2</option>
	<option value="3" selected="selected">3</option>
</select>
'
			),

			// multiple, many default, many preset, empty value
			array(
				'multiple="true"',
				'<option value="1" preset="true">1</option><option value="2" preset="true">2</option><option value="3" default="true">3</option>',
				'',
'<select id="list" name="list[]" multiple="multiple">
	<option value="1" selected="selected">1</option>
	<option value="2" selected="selected">2</option>
	<option value="3">3</option>
</select>
',
'<select id="list" name="list[]" multiple="multiple">
	<option value="1">1</option>
	<option value="2">2</option>
	<option value="3" selected="selected">3</option>
</select>
'
			),

			// multiple, many default, many preset, existing value
			array(
				'multiple="true"',
				'<option value="1" preset="true">1</option><option value="2" preset="true">2</option><option value="3" default="true">3</option>',
				'2',
'<select id="list" name="list[]" multiple="multiple">
	<option value="1" selected="selected">1</option>
	<option value="2" selected="selected">2</option>
	<option value="3">3</option>
</select>
',
'<select id="list" name="list[]" multiple="multiple">
	<option value="1">1</option>
	<option value="2" selected="selected">2</option>
	<option value="3">3</option>
</select>
'
			),

			// multiple, many default, many preset, existing value
			array(
				'multiple="true"',
				'<option value="1" preset="true">1</option><option value="2" preset="true">2</option><option value="3" default="true">3</option>',
				array('2', '3'),
'<select id="list" name="list[]" multiple="multiple">
	<option value="1" selected="selected">1</option>
	<option value="2" selected="selected">2</option>
	<option value="3">3</option>
</select>
',
'<select id="list" name="list[]" multiple="multiple">
	<option value="1">1</option>
	<option value="2" selected="selected">2</option>
	<option value="3" selected="selected">3</option>
</select>
'
			),
		);
	}

	/**
	 * Test the getInput method.
	 *
	 * @dataProvider caseGetInputDefaultPreset
	 */
	public function testGetInputDefaultPreset($attributes, $options, $bind, $before, $after)
	{
		$form = new JFormInspector('form1');

		$this->assertThat(
			$form->load('<form><field name="list" type="list" ' . $attributes . ' >' . $options . '</field></form>'),
			$this->isTrue(),
			'Line:'.__LINE__.' XML string should load successfully.'
		);

		$this->assertThat(
			$form->getField('list')->input,
			$this->equalTo($before),
			'Line:'.__LINE__.' The getInput method should return correct input.'
		);

		$this->assertThat(
			$form->bind(array('list' => $bind)),
			$this->isTrue(),
			'Line:'.__LINE__.' The data should bind successfully.'
		);

		$this->assertThat(
			$form->getField('list')->input,
			$this->equalTo($after),
			'Line:'.__LINE__.' The getInput method should return correct input.'
		);
	}
}
