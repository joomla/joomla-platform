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
 * @since       11.3
 */
class JFormFieldCheckboxesTest extends TestCase
{
	/**
	 * Sets up dependencies for the test.
	 *
	 * @since       11.3
	 */
	protected function setUp()
	{
		require_once JPATH_PLATFORM.'/joomla/form/fields/checkboxes.php';
		include_once dirname(__DIR__).'/inspectors.php';
	}

	/**
	 * Test the getInput method.
	 *
	 * @since       11.3
	 */
	public function testGetInput()
	{
		$form = new JFormInspector('form1');

		$this->assertThat(
			$form->load('<form><field name="checkboxes" type="checkboxes" /></form>'),
			$this->isTrue(),
			'Line:'.__LINE__.' XML string should load successfully.'
		);

		$field = new JFormFieldCheckboxes($form);

		$this->markTestIncomplete();

		// TODO: Should check all the attributes have come in properly.
	}

	/**
	 * Data provider for testGetInputDefaultPreset
	 */
	public function caseGetInputDefaultPreset()
	{
		return array(
			// no default, no preset, existing value
			array(
				'',
				'<option value="1">1</option><option value="2">2</option><option value="3">3</option>',
				'1',
				'<fieldset id="checkboxes" class="checkboxes"><ul><li><input type="checkbox" id="checkboxes0" name="checkboxes[]" value="1"/><label for="checkboxes0">1</label></li><li><input type="checkbox" id="checkboxes1" name="checkboxes[]" value="2"/><label for="checkboxes1">2</label></li><li><input type="checkbox" id="checkboxes2" name="checkboxes[]" value="3"/><label for="checkboxes2">3</label></li></ul></fieldset>',
				'<fieldset id="checkboxes" class="checkboxes"><ul><li><input type="checkbox" id="checkboxes0" name="checkboxes[]" value="1" checked="checked"/><label for="checkboxes0">1</label></li><li><input type="checkbox" id="checkboxes1" name="checkboxes[]" value="2"/><label for="checkboxes1">2</label></li><li><input type="checkbox" id="checkboxes2" name="checkboxes[]" value="3"/><label for="checkboxes2">3</label></li></ul></fieldset>',
			),

			// one default, no preset, existing value
			array(
				'default="1"',
				'<option value="1">1</option><option value="2">2</option><option value="3">3</option>',
				'2',
				'<fieldset id="checkboxes" class="checkboxes"><ul><li><input type="checkbox" id="checkboxes0" name="checkboxes[]" value="1" checked="checked"/><label for="checkboxes0">1</label></li><li><input type="checkbox" id="checkboxes1" name="checkboxes[]" value="2"/><label for="checkboxes1">2</label></li><li><input type="checkbox" id="checkboxes2" name="checkboxes[]" value="3"/><label for="checkboxes2">3</label></li></ul></fieldset>',
				'<fieldset id="checkboxes" class="checkboxes"><ul><li><input type="checkbox" id="checkboxes0" name="checkboxes[]" value="1"/><label for="checkboxes0">1</label></li><li><input type="checkbox" id="checkboxes1" name="checkboxes[]" value="2" checked="checked"/><label for="checkboxes1">2</label></li><li><input type="checkbox" id="checkboxes2" name="checkboxes[]" value="3"/><label for="checkboxes2">3</label></li></ul></fieldset>',
			),

			// one default, no preset, empty value
			array(
				'default="1"',
				'<option value="1">1</option><option value="2">2</option><option value="3">3</option>',
				'',
				'<fieldset id="checkboxes" class="checkboxes"><ul><li><input type="checkbox" id="checkboxes0" name="checkboxes[]" value="1" checked="checked"/><label for="checkboxes0">1</label></li><li><input type="checkbox" id="checkboxes1" name="checkboxes[]" value="2"/><label for="checkboxes1">2</label></li><li><input type="checkbox" id="checkboxes2" name="checkboxes[]" value="3"/><label for="checkboxes2">3</label></li></ul></fieldset>',
				'<fieldset id="checkboxes" class="checkboxes"><ul><li><input type="checkbox" id="checkboxes0" name="checkboxes[]" value="1" checked="checked"/><label for="checkboxes0">1</label></li><li><input type="checkbox" id="checkboxes1" name="checkboxes[]" value="2"/><label for="checkboxes1">2</label></li><li><input type="checkbox" id="checkboxes2" name="checkboxes[]" value="3"/><label for="checkboxes2">3</label></li></ul></fieldset>',
			),

			// no default, one preset, existing value
			array(
				'preset="1"',
				'<option value="1">1</option><option value="2">2</option><option value="3">3</option>',
				'2',
				'<fieldset id="checkboxes" class="checkboxes"><ul><li><input type="checkbox" id="checkboxes0" name="checkboxes[]" value="1" checked="checked"/><label for="checkboxes0">1</label></li><li><input type="checkbox" id="checkboxes1" name="checkboxes[]" value="2"/><label for="checkboxes1">2</label></li><li><input type="checkbox" id="checkboxes2" name="checkboxes[]" value="3"/><label for="checkboxes2">3</label></li></ul></fieldset>',
				'<fieldset id="checkboxes" class="checkboxes"><ul><li><input type="checkbox" id="checkboxes0" name="checkboxes[]" value="1"/><label for="checkboxes0">1</label></li><li><input type="checkbox" id="checkboxes1" name="checkboxes[]" value="2" checked="checked"/><label for="checkboxes1">2</label></li><li><input type="checkbox" id="checkboxes2" name="checkboxes[]" value="3"/><label for="checkboxes2">3</label></li></ul></fieldset>',
			),

			// one default, one preset, empty value
			array(
				'preset="1" default="2"',
				'<option value="1">1</option><option value="2">2</option><option value="3">3</option>',
				'',
				'<fieldset id="checkboxes" class="checkboxes"><ul><li><input type="checkbox" id="checkboxes0" name="checkboxes[]" value="1" checked="checked"/><label for="checkboxes0">1</label></li><li><input type="checkbox" id="checkboxes1" name="checkboxes[]" value="2"/><label for="checkboxes1">2</label></li><li><input type="checkbox" id="checkboxes2" name="checkboxes[]" value="3"/><label for="checkboxes2">3</label></li></ul></fieldset>',
				'<fieldset id="checkboxes" class="checkboxes"><ul><li><input type="checkbox" id="checkboxes0" name="checkboxes[]" value="1"/><label for="checkboxes0">1</label></li><li><input type="checkbox" id="checkboxes1" name="checkboxes[]" value="2" checked="checked"/><label for="checkboxes1">2</label></li><li><input type="checkbox" id="checkboxes2" name="checkboxes[]" value="3"/><label for="checkboxes2">3</label></li></ul></fieldset>',
			),

			// one default, one preset, existing value
			array(
				'preset="1" default="2"',
				'<option value="1">1</option><option value="2">2</option><option value="3">3</option>',
				'3',
				'<fieldset id="checkboxes" class="checkboxes"><ul><li><input type="checkbox" id="checkboxes0" name="checkboxes[]" value="1" checked="checked"/><label for="checkboxes0">1</label></li><li><input type="checkbox" id="checkboxes1" name="checkboxes[]" value="2"/><label for="checkboxes1">2</label></li><li><input type="checkbox" id="checkboxes2" name="checkboxes[]" value="3"/><label for="checkboxes2">3</label></li></ul></fieldset>',
				'<fieldset id="checkboxes" class="checkboxes"><ul><li><input type="checkbox" id="checkboxes0" name="checkboxes[]" value="1"/><label for="checkboxes0">1</label></li><li><input type="checkbox" id="checkboxes1" name="checkboxes[]" value="2"/><label for="checkboxes1">2</label></li><li><input type="checkbox" id="checkboxes2" name="checkboxes[]" value="3" checked="checked"/><label for="checkboxes2">3</label></li></ul></fieldset>',
			),

			// many default, many preset, empty value
			array(
				'',
				'<option value="1" preset="true">1</option><option value="2" preset="true">2</option><option value="3" default="true">3</option>',
				'',
				'<fieldset id="checkboxes" class="checkboxes"><ul><li><input type="checkbox" id="checkboxes0" name="checkboxes[]" value="1" checked="checked"/><label for="checkboxes0">1</label></li><li><input type="checkbox" id="checkboxes1" name="checkboxes[]" value="2" checked="checked"/><label for="checkboxes1">2</label></li><li><input type="checkbox" id="checkboxes2" name="checkboxes[]" value="3"/><label for="checkboxes2">3</label></li></ul></fieldset>',
				'<fieldset id="checkboxes" class="checkboxes"><ul><li><input type="checkbox" id="checkboxes0" name="checkboxes[]" value="1"/><label for="checkboxes0">1</label></li><li><input type="checkbox" id="checkboxes1" name="checkboxes[]" value="2"/><label for="checkboxes1">2</label></li><li><input type="checkbox" id="checkboxes2" name="checkboxes[]" value="3" checked="checked"/><label for="checkboxes2">3</label></li></ul></fieldset>',
			),

			// many default, many preset, existing value
			array(
				'',
				'<option value="1" preset="true">1</option><option value="2" preset="true">2</option><option value="3" default="true">3</option>',
				'2',
				'<fieldset id="checkboxes" class="checkboxes"><ul><li><input type="checkbox" id="checkboxes0" name="checkboxes[]" value="1" checked="checked"/><label for="checkboxes0">1</label></li><li><input type="checkbox" id="checkboxes1" name="checkboxes[]" value="2" checked="checked"/><label for="checkboxes1">2</label></li><li><input type="checkbox" id="checkboxes2" name="checkboxes[]" value="3"/><label for="checkboxes2">3</label></li></ul></fieldset>',
				'<fieldset id="checkboxes" class="checkboxes"><ul><li><input type="checkbox" id="checkboxes0" name="checkboxes[]" value="1"/><label for="checkboxes0">1</label></li><li><input type="checkbox" id="checkboxes1" name="checkboxes[]" value="2" checked="checked"/><label for="checkboxes1">2</label></li><li><input type="checkbox" id="checkboxes2" name="checkboxes[]" value="3"/><label for="checkboxes2">3</label></li></ul></fieldset>',
			),

			// many default, many preset, existing value
			array(
				'',
				'<option value="1" preset="true">1</option><option value="2" preset="true">2</option><option value="3" default="true">3</option>',
				array('2', '3'),
				'<fieldset id="checkboxes" class="checkboxes"><ul><li><input type="checkbox" id="checkboxes0" name="checkboxes[]" value="1" checked="checked"/><label for="checkboxes0">1</label></li><li><input type="checkbox" id="checkboxes1" name="checkboxes[]" value="2" checked="checked"/><label for="checkboxes1">2</label></li><li><input type="checkbox" id="checkboxes2" name="checkboxes[]" value="3"/><label for="checkboxes2">3</label></li></ul></fieldset>',
				'<fieldset id="checkboxes" class="checkboxes"><ul><li><input type="checkbox" id="checkboxes0" name="checkboxes[]" value="1"/><label for="checkboxes0">1</label></li><li><input type="checkbox" id="checkboxes1" name="checkboxes[]" value="2" checked="checked"/><label for="checkboxes1">2</label></li><li><input type="checkbox" id="checkboxes2" name="checkboxes[]" value="3" checked="checked"/><label for="checkboxes2">3</label></li></ul></fieldset>',
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
			$form->load('<form><field name="checkboxes" type="checkboxes" ' . $attributes . ' >' . $options . '</field></form>'),
			$this->isTrue(),
			'Line:'.__LINE__.' XML string should load successfully.'
		);

		$this->assertThat(
			$form->getField('checkboxes')->input,
			$this->equalTo($before),
			'Line:'.__LINE__.' The getInput method should return correct input.'
		);

		$this->assertThat(
			$form->bind(array('checkboxes' => $bind)),
			$this->isTrue(),
			'Line:'.__LINE__.' The data should bind successfully.'
		);

		$this->assertThat(
			$form->getField('checkboxes')->input,
			$this->equalTo($after),
			'Line:'.__LINE__.' The getInput method should return correct input.'
		);
	}
}
