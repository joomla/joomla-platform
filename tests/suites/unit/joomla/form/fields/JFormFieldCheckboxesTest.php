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
	}

	/**
	 * Test the getInput method with no value and no checked attribute.
	 *
	 * @since       12.2
	 */
	public function testGetInputNoValueNoChecked()
	{
		$formFieldCheckboxes = $this->getMock('JFormFieldCheckboxes', array('getOptions'));

		$option1 = new JObject;
		$option1->set('value','red');
		$option1->set('text','red');

		$option2 = new JObject;
		$option2->set('value','blue');
		$option2->set('text','blue');

		$optionsReturn = array($option1,$option2);
		$formFieldCheckboxes->expects($this->any())
							->method('getOptions')
							->will($this->returnValue($optionsReturn));

		// Test with no value, no checked element
		$element = simplexml_load_string(
			'<field name="color" type="checkboxes">
			<option value="red">red</option>
			<option value="blue">blue</option>
			</field>');
		TestReflection::setValue($formFieldCheckboxes, 'element', $element);
		TestReflection::setValue($formFieldCheckboxes, 'id', 'myTestId');
		TestReflection::setValue($formFieldCheckboxes, 'name', 'myTestName');

		$this->assertEquals(
			'<fieldset id="myTestId" class="checkboxes"><ul><li><input type="checkbox" id="myTestId0" name="myTestName" value="red"/><label for="myTestId0">red</label></li><li><input type="checkbox" id="myTestId1" name="myTestName" value="blue"/><label for="myTestId1">blue</label></li></ul></fieldset>',
			TestReflection::invoke($formFieldCheckboxes, 'getInput'),
			'The field with no value and no checked values did not produce the right html'
		);
	}

	/**
	 * Test the getInput method with one value selected and no checked attribute.
	 *
	 * @since       12.2
	 */
	public function testGetInputValueNoChecked()
	{
		$formFieldCheckboxes = $this->getMock('JFormFieldCheckboxes', array('getOptions'));

		$option1 = new JObject;
		$option1->set('value','red');
		$option1->set('text','red');

		$option2 = new JObject;
		$option2->set('value','blue');
		$option2->set('text','blue');

		$optionsReturn = array($option1,$option2);
		$formFieldCheckboxes->expects($this->any())
							->method('getOptions')
							->will($this->returnValue($optionsReturn));

		// Test with one value checked, no checked element
		$element = simplexml_load_string(
			'<field name="color" type="checkboxes">
			<option value="red">red</option>
			<option value="blue">blue</option>
			</field>');
		TestReflection::setValue($formFieldCheckboxes, 'element', $element);
		TestReflection::setValue($formFieldCheckboxes, 'id', 'myTestId');
		TestReflection::setValue($formFieldCheckboxes, 'value', 'red');
		TestReflection::setValue($formFieldCheckboxes, 'name', 'myTestName');

		$this->assertEquals(
			'<fieldset id="myTestId" class="checkboxes"><ul><li><input type="checkbox" id="myTestId0" name="myTestName" value="red" checked="checked"/><label for="myTestId0">red</label></li><li><input type="checkbox" id="myTestId1" name="myTestName" value="blue"/><label for="myTestId1">blue</label></li></ul></fieldset>',
			TestReflection::invoke($formFieldCheckboxes, 'getInput'),
			'The field with one value did not produce the right html'
		);
	}

	/**
	 * Test the getInput method with one value that is an array and no checked attribute.
	 *
	 * @since       12.2
	 */
	public function testGetInputValueArrayNoChecked()
	{
		$formFieldCheckboxes = $this->getMock('JFormFieldCheckboxes', array('getOptions'));

		$option1 = new JObject;
		$option1->set('value','red');
		$option1->set('text','red');

		$option2 = new JObject;
		$option2->set('value','blue');
		$option2->set('text','blue');

		$optionsReturn = array($option1,$option2);
		$formFieldCheckboxes->expects($this->any())
							->method('getOptions')
							->will($this->returnValue($optionsReturn));

		// Test with one value checked, no checked element
		$element = simplexml_load_string(
			'<field name="color" type="checkboxes">
			<option value="red">red</option>
			<option value="blue">blue</option>
			</field>');
		$valuearray = array ('red');
		TestReflection::setValue($formFieldCheckboxes, 'element', $element);
		TestReflection::setValue($formFieldCheckboxes, 'id', 'myTestId');
		TestReflection::setValue($formFieldCheckboxes, 'value', $valuearray);
		TestReflection::setValue($formFieldCheckboxes, 'name', 'myTestName');

		$this->assertEquals(
			'<fieldset id="myTestId" class="checkboxes"><ul><li><input type="checkbox" id="myTestId0" name="myTestName" value="red" checked="checked"/><label for="myTestId0">red</label></li><li><input type="checkbox" id="myTestId1" name="myTestName" value="blue"/><label for="myTestId1">blue</label></li></ul></fieldset>',
			TestReflection::invoke($formFieldCheckboxes, 'getInput'),
			'The field with one value did not produce the right html'
		);
	}

	/**
	 * Test the getInput method  with no value and one value in checked.
	 *
	 * @since       12.2
	 */
	public function testGetInputNoValueOneChecked()
	{
		$formFieldCheckboxes = $this->getMock('JFormFieldCheckboxes', array('getOptions'));

		$option1 = new JObject;
		$option1->set('value','red');
		$option1->set('text','red');

		$option2 = new JObject;
		$option2->set('value','blue');
		$option2->set('text','blue');

		$optionsReturn = array($option1,$option2);
		$formFieldCheckboxes->expects($this->any())
							->method('getOptions')
							->will($this->returnValue($optionsReturn));
		
		// Test with nothing checked, one value in checked element
		$element = simplexml_load_string(
			'<field name="color" type="checkboxes" checked="blue">
			<option value="red">red</option>
			<option value="blue">blue</option>
			</field>');
		TestReflection::setValue($formFieldCheckboxes, 'element', $element);
		TestReflection::setValue($formFieldCheckboxes, 'id', 'myTestId');
		TestReflection::setValue($formFieldCheckboxes, 'name', 'myTestName');

		$this->assertEquals(
			'<fieldset id="myTestId" class="checkboxes"><ul><li><input type="checkbox" id="myTestId0" name="myTestName" value="red"/><label for="myTestId0">red</label></li><li><input type="checkbox" id="myTestId1" name="myTestName" value="blue" checked="checked"/><label for="myTestId1">blue</label></li></ul></fieldset>',
			TestReflection::invoke($formFieldCheckboxes, 'getInput'),
			'The field with no values and one value in the checked element did not produce the right html'
		);
	}

	/**
	 * Test the getInput method with no value and two values in the checked element.
	 *
	 * @since       12.2
	 */
	public function testGetInputNoValueTwoChecked()
	{
		$formFieldCheckboxes = $this->getMock('JFormFieldCheckboxes', array('getOptions'));

		$option1 = new JObject;
		$option1->set('value','red');
		$option1->set('text','red');

		$option2 = new JObject;
		$option2->set('value','blue');
		$option2->set('text','blue');

		$optionsReturn = array($option1,$option2);
		$formFieldCheckboxes->expects($this->any())
							->method('getOptions')
							->will($this->returnValue($optionsReturn));
		
		// Test with nothing checked, two values in checked element
		$element = simplexml_load_string(
			'<field name="color" type="checkboxes" checked="red,blue">
			<option value="red">red</option>
			<option value="blue">blue</option>
			</field>');
		TestReflection::setValue($formFieldCheckboxes, 'element', $element);
		TestReflection::setValue($formFieldCheckboxes, 'id', 'myTestId');
		TestReflection::setValue($formFieldCheckboxes, 'name', 'myTestName');
		TestReflection::setValue($formFieldCheckboxes, 'value', '""');

		$this->assertEquals(
			'<fieldset id="myTestId" class="checkboxes"><ul><li><input type="checkbox" id="myTestId0" name="myTestName" value="red"/><label for="myTestId0">red</label></li><li><input type="checkbox" id="myTestId1" name="myTestName" value="blue"/><label for="myTestId1">blue</label></li></ul></fieldset>',
			TestReflection::invoke($formFieldCheckboxes, 'getInput'),
			'The field with no values and two items in the checked element did not produce the right html'
		);
	}

	/**
	 * Test the getInput method with one value and a different checked value.
	 *
	 * @since       12.2
	 */
	public function testGetInputValueChecked()
	{
		$formFieldCheckboxes = $this->getMock('JFormFieldCheckboxes', array('getOptions'));

		$option1 = new JObject;
		$option1->set('value','red');
		$option1->set('text','red');

		$option2 = new JObject;
		$option2->set('value','blue');
		$option2->set('text','blue');

		$optionsReturn = array($option1,$option2);
		$formFieldCheckboxes->expects($this->any())
							->method('getOptions')
							->will($this->returnValue($optionsReturn));

		// Test with one item checked, a different value in checked element
		$element = simplexml_load_string(
			'<field name="color" type="checkboxes" checked="blue">
			<option value="red">red</option>
			<option value="blue">blue</option>
			</field>');
		TestReflection::setValue($formFieldCheckboxes, 'element', $element);
		TestReflection::setValue($formFieldCheckboxes, 'id', 'myTestId');
		TestReflection::setValue($formFieldCheckboxes, 'value', 'red');
		TestReflection::setValue($formFieldCheckboxes, 'name', 'myTestName');

		$this->assertEquals(
			'<fieldset id="myTestId" class="checkboxes"><ul><li><input type="checkbox" id="myTestId0" name="myTestName" value="red" checked="checked"/><label for="myTestId0">red</label></li><li><input type="checkbox" id="myTestId1" name="myTestName" value="blue"/><label for="myTestId1">blue</label></li></ul></fieldset>',
			TestReflection::invoke($formFieldCheckboxes, 'getInput'),
			'The field with one value and a different value in the checked element did not produce the right html'
		);
	}

	/**
	 * Test the getInput method with multiple values, no checked.
	 *
	 * @since       12.2
	 */
	public function testGetInputValuesNoChecked()
	{
	$formFieldCheckboxes = $this->getMock('JFormFieldCheckboxes', array('getOptions'));

		$option1 = new JObject;
		$option1->set('value','red');
		$option1->set('text','red');

		$option2 = new JObject;
		$option2->set('value','blue');
		$option2->set('text','blue');

		$optionsReturn = array($option1,$option2);
		$formFieldCheckboxes->expects($this->any())
							->method('getOptions')
							->will($this->returnValue($optionsReturn));

		// Test with two values checked, no checked element
		$element = simplexml_load_string(
			'<field name="color" type="checkboxes">
			<option value="red">red</option>
			<option value="blue">blue</option>
			</field>');
		TestReflection::setValue($formFieldCheckboxes, 'element', $element);
		TestReflection::setValue($formFieldCheckboxes, 'id', 'myTestId');
		TestReflection::setValue($formFieldCheckboxes, 'value', 'yellow,green');
		TestReflection::setValue($formFieldCheckboxes, 'name', 'myTestName');

		$this->assertEquals(
			'<fieldset id="myTestId" class="checkboxes"><ul><li><input type="checkbox" id="myTestId0" name="myTestName" value="red"/><label for="myTestId0">red</label></li><li><input type="checkbox" id="myTestId1" name="myTestName" value="blue"/><label for="myTestId1">blue</label></li></ul></fieldset>',
			TestReflection::invoke($formFieldCheckboxes, 'getInput'),
			'The field with two values did not produce the right html'
		);
	}

	/**
	 * Test the getOptions method.
	 *
	 * @since       12.2
	 */
	public function testGetOptions()
	{
		$formFieldCheckboxes = new JFormFieldCheckboxes;

		$option1 = new JObject;
		$option1->set('value','yellow');
		$option1->set('text','yellow');
		$option1->set('disable',false);
		$option1->set('class','');
		$option1->set('onclick','');

		$option2 = new JObject;
		$option2->set('value','green');
		$option2->set('text','green');
		$option2->set('disable',false);
		$option2->set('class','');
		$option2->set('onclick','');

		$optionsExpected = array($option1,$option2);

		// Test with two values checked, no checked element
		TestReflection::setValue($formFieldCheckboxes, 'element', simplexml_load_string(
			'<field name="color" type="checkboxes">
			<option value="yellow">yellow</option>
			<option value="green">green</option>
			</field>'));

		$this->assertEquals(
			$optionsExpected,
			TestReflection::invoke($formFieldCheckboxes, 'getOptions'),
			'The field with two values did not produce the right options'
		);
	}
}
