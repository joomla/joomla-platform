<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * Test class for JFormFieldUrl.
 *
 * @package		Joomla.UnitTest
 * @subpackage  Form
 * @since       11.3
 */
class JFormFieldUrlTest extends JoomlaTestCase
{
	/**
	 * Sets up dependencies for the test.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	protected function setUp()
	{
		jimport('joomla.form.form');
		jimport('joomla.form.formfield');
		jimport('joomla.form.helper');
		require_once JPATH_PLATFORM.'/joomla/form/fields/url.php';
		include_once dirname(__DIR__).'/inspectors.php';
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
			$form->load('<form><field name="url" type="url" /></form>'),
			$this->isTrue(),
			'Line:'.__LINE__.' XML string should load successfully.'
		);

		$field = new JFormFieldUrl($form);

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
}
