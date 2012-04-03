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
class JFormFieldTemplateStyleTest extends TestCase
{
	/**
	 * Test the getInput method.
	 * @covers JFormFieldTemplateStyle::getGroups
	 */
	public function testGetInput()
	{
		$form = new JForm('form1');

		$this->assertThat($form->load('<form><field name="templatestyle" type="templatestyle" /></form>'), $this->isTrue(),
			'Line:' . __LINE__ . ' XML string should load successfully.');

		$field = new JFormFieldTemplatestyle($form);

		$this->assertThat($field->setup(TestReflection::getValue($form, 'xml')->field, 'value'), $this->isTrue(),
			'Line:' . __LINE__ . ' The setup method should return true.');

		$this->markTestIncomplete('Problems encountered in next assertion');

		$this->assertThat(strlen($field->input), $this->greaterThan(0),
			'Line:' . __LINE__ . ' The getInput method should return something without error.');

		// TODO: Should check all the attributes have come in properly.
	}
}
