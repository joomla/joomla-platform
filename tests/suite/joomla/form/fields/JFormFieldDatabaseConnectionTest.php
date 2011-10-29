<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * Test class for JFormFieldDatabaseconnection.
 *
 * @package		Joomla.UnitTest
 * @subpackage  Form
 * @since       11.3
 */
class JFormFieldDatabaseConnectionTest extends JoomlaTestCase
{
	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp()
	{
		jimport('joomla.form.form');
		jimport('joomla.form.formfield');
		require_once JPATH_PLATFORM.'/joomla/form/fields/databaseconnection.php';
		include_once dirname(dirname(__FILE__)).'/inspectors.php';
	}

	/**
	 * Test the getInput method.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function testGetInput()
	{
		$form = new JFormInspector('form1');

		$this->assertThat(
			$form->load('<form><field name="databaseconnection" type="databaseconnection" supported="mysqli" /></form>'),
			$this->isTrue(),
			'Line:'.__LINE__.' XML string should load successfully.'
		);

		$field = new JFormFieldDatabaseconnection($form);

		$this->assertThat(
			$field->setup($form->getXml()->field, 'value'),
			$this->isTrue(),
			'Line:'.__LINE__.' The setup method should return true.'
		);

		$this->assertThat(
			strlen($field->input),
			$this->greaterThan(0),
			'Line:'.__LINE__.' The getInput method should return something without error; in this case, a "Mysqli" option.'
		);

		$this->assertThat(
			$form->load('<form><field name="databaseconnection" type="databaseconnection" supported="non-existing" /></form>'),
			$this->isTrue(),
			'Line:'.__LINE__.' XML string should load successfully.'
		);

		$field = new JFormFieldDatabaseconnection($form);

		$this->assertThat(
			$field->setup($form->getXml()->field, 'value'),
			$this->isTrue(),
			'Line:'.__LINE__.' The setup method should return true.'
		);

		$this->assertThat(
			strlen($field->input),
			$this->greaterThan(0),
			'Line:'.__LINE__.' The getInput method should return something without error; in this case, a "None" option.'
		);
	}
}
