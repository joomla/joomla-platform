<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * Test class for JFormFieldGroupedList.
 *
 * @package     Joomla.UnitTest
 * @subpackage  Form
 * @since       12.1
 */
class JFormFieldGroupedListTest extends TestCase
{
	/**
	 * Sets up dependencies for the test.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	protected function setUp()
	{
		require_once JPATH_PLATFORM . '/joomla/form/fields/groupedlist.php';
		include_once dirname(__DIR__) . '/inspectors.php';
	}

	/**
	 * Test the getInput method.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testGetInput()
	{
		$form = new JFormInspector('form1');

		$this->assertThat(
			$form->load('<form><field name="groupedlist" type="groupedlist" /></form>'),
			$this->isTrue(),
		'Line:'.__LINE__.' XML string should load successfully.'
		);

		$field = new JFormFieldGroupedList($form);

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
				'<option value="1">1</option><option value="2">2</option><group name="Group"><option value="3">3</option></group>',
				'',
				''
			),
			array(
				'default="1"',
				'<option value="1">1</option><option value="2">2</option><group name="Group"><option value="3">3</option></group>',
				'1',
				'1'
			),
			array(
				'preset="1"',
				'<option value="1">1</option><option value="2">2</option><group name="Group"><option value="3">3</option></group>',
				'',
				'1'
			),
			array(
				'preset="1" default="2"',
				'<option value="1">1</option><option value="2">2</option><group name="Group"><option value="3">3</option></group>',
				'2',
				'1'
			),
			array(
				'multiple="true"',
				'<option value="1">1</option><option value="2">2</option><group name="Group"><option value="3">3</option></group>',
				array(),
				array()
			),
			array(
				'multiple="true"',
				'<option value="1" selected="true">1</option><option value="2" selected="true" default="true">2</option><group name="Group"><option value="3" default="true">3</option></group>',
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
			$form->load('<form><field name="groupedlist" type="groupedlist" ' . $attributes . ' >' . $options . '</field></form>'),
			$this->isTrue(),
			'Line:'.__LINE__.' XML string should load successfully.'
		);

		$this->assertThat(
			$form->getField('groupedlist')->default,
			$this->equalTo($default),
			'Line:'.__LINE__.' The getDefault method should return correct default.'
		);

		$this->assertThat(
			$form->getField('groupedlist')->preset,
			$this->equalTo($preset),
			'Line:'.__LINE__.' The getDefault method should return correct preset.'
		);
	}
}
