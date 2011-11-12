<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  HTML
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

require_once JPATH_PLATFORM.'/joomla/html/parameter.php';

jimport('joomla.log.log');
jimport('joomla.filter.filterinput');

class JParameterInspector extends JParameter
{
	public function getElementPath()
	{
		return $this->_elementPath;
	}
}

/**
 * Test class for JParameter.
 * Generated by PHPUnit on 2009-10-27 at 15:38:18.
 */
class JParameterTest extends PHPUnit_Framework_TestCase
{
	/**
	 * Test the JParameter::addElementPath method.
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	public function testAddElementPath()
	{
		$p = new JParameterInspector('');
		$p->addElementPath(str_replace('\\', '/', __DIR__));

		$this->assertThat(
			$p->getElementPath(),
			$this->equalTo(
				array(
					// addElementPath appends the slash for some reason.
					str_replace('\\', '/', __DIR__.'/'),
					str_replace('\\', '/', JPATH_PLATFORM.'/joomla/html/parameter/element/')
				)
			)
		);
	}

	/**
	 * Test the JParameter::bind method.
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	public function testBind()
	{
		$p = new JParameter('');

		// Check binding an array.
		$p->bind(array(
			'foo1' => 'bar1'
		));
		$this->assertThat(
			$p->get('foo1'),
			$this->equalTo('bar1')
		);

		// Check binding an object.
		$object = new stdClass;
		$object->foo1 = 'bar2';
		$p->bind($object);
		$this->assertThat(
			$p->get('foo1'),
			$this->equalTo('bar2')
		);

		// Check binding a JSON string.
		$p->bind('{"foo1":"bar4"}');
		$this->assertThat(
			$p->get('foo1'),
			$this->equalTo('bar4')
		);

		// Check binding an INI string.
		$p->bind('foo1=bar5');
		$this->assertThat(
			$p->get('foo1'),
			$this->equalTo('bar5')
		);
	}

	/**
	 * Test the JParameter::def method
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	public function testDef()
	{
		$p = new JParameter('');

		$p->set('foo1', 'bar1');

		$this->assertThat(
			$p->def('foo1', 'bar2'),
			$this->equalTo('bar1')
		);

		$this->assertThat(
			$p->def('foo2', 'bar2'),
			$this->equalTo('bar2')
		);
	}

	/**
	 * Test the JParameter::get method.
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	public function testGet()
	{
		$p = new JParameter('{"foo":"bar"}');

		$this->assertThat(
			$p->get('foo'),
			$this->equalTo('bar')
		);

		$this->assertThat(
			$p->get('foo2'),
			$this->equalTo(null)
		);

		$this->assertThat(
			$p->get('foo2', 'bar2'),
			$this->equalTo('bar2')
		);
	}

	/**
	 * Test the JParameter::getGroups method.
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	public function testGetGroups()
	{
		$p = new JParameter('{"foo":"bar"}', __DIR__.'/jparameter.xml');

		$this->assertThat(
			$p->getGroups(),
			$this->equalTo(
				array(
					'basic' => 1,
					'advanced' => 2,
				)
			)
		);
	}

	/**
	 * Test the JParameter::getNumParams() method.
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	public function testGetNumParams()
	{
		$p = new JParameter('{"foo":"bar"}', __DIR__.'/jparameter.xml');

		$this->assertThat(
			$p->getNumParams('unknown'),
			$this->isFalse()
		);

		$this->assertThat(
			$p->getNumParams('basic'),
			$this->equalTo(1)
		);

		$this->assertThat(
			$p->getNumParams('advanced'),
			$this->equalTo(2)
		);
	}

	/**
	 * Test the JParameter::getParam method.
	 */
	public function testGetParam()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
		'This test has not been implemented yet.'
		);
	}

	/**
	 * Test the JParameter::getParams method.
	 */
	public function testGetParams()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
		'This test has not been implemented yet.'
		);
	}

	/**
	 * Test the JParameter::loadElement method.
	 */
	public function testLoadElement()
	{
		$params = new JParameter('');
		
		$this->assertTrue(is_a($params->loadElement('list'), 'JElementList'));
		
		$this->assertFalse($params->loadElement('fake'));
		
		require_once JPATH_TESTS.'/suite/joomla/html/parameter/JElementMock.php';
		
		$el = $params->loadElement('mock');
		
		$this->assertTrue(is_a($el, 'JElementMock'));
		
		$this->assertThat(
			$el->getName(),
			$this->equalTo('Mock')
		);
		
		$el->name = 'Mock2';

		$el = $params->loadElement('mock');

		$this->assertThat(
			$el->getName(),
			$this->equalTo('Mock2')
		);
		
		$el = $params->loadElement('mock', true);
		
		$this->assertThat(
			$el->getName(),
			$this->equalTo('Mock')
		);
	}

	/**
	 * Test the JParameter::loadSetupFile method.
	 */
	public function testLoadSetupFile()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
		'This test has not been implemented yet.'
		);
	}

	/**
	 * Test the JParameter::render method.
	 */
	public function testRender()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
		'This test has not been implemented yet.'
		);
	}

	/**
	 * Test the JParameter::renderToArray method.
	 */
	public function testRenderToArray()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
		'This test has not been implemented yet.'
		);
	}

	/**
	 * Test the JParameter::set method.
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	public function testSet()
	{
		$p = new JParameter('');

		$this->assertThat(
			$p->set('foo', 'bar'),
			$this->equalTo('bar')
		);

		$this->assertThat(
			$p->get('foo'),
			$this->equalTo('bar')
		);
	}

	/**
	 * Test the JParameter::setXML method.
	 */
	public function testSetXML()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
		'This test has not been implemented yet.'
		);
	}
}
