<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Application
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

require_once JPATH_PLATFORM.'/joomla/application/router.php';

jimport('joomla.application.helper');

class JRouterInspector extends JRouter
{
	/**
	* Method for inspecting protected variables.
	*
	* @return mixed The value of the class variable.
	*/
	public function __get($name)
	{
		if (property_exists($this, $name)) {
			return $this->$name;
		} else {
			trigger_error('Undefined or private property: ' . __CLASS__.'::'.$name, E_USER_ERROR);
			return null;
		}
	}

	/**
	* Sets any property from the class.
	*
	* @param string $property The name of the class property.
	* @param string $value The value of the class property.
	*
	* @return void
	*/
	public function __set($property, $value)
	{
		$this->$property = $value;
	}
	
	/**
	 * Calls any inaccessible method from the class.
	 * 
	 * @param string 	$name Name of the method to invoke 
	 * @param array 	$parameters Parameters to be handed over to the original method
	 * 
	 * @return mixed The return value of the method 
	 */
	public function __call($name, $parameters = false)
	{
		return call_user_func_array(array($this,$name), $parameters);
	}
	
	public function resetInstances()
	{
		JRouterInspector::$instances = array();
	}
	
	public static function routingParseFnTest1($router, $uri)
	{
		$uri->setVar('option', 'com_test');
	}
	
	public static function routingParseFnTest2()
	{
		
	}
	
	public static function routingBuildFnTest1($router, JURI $uri)
	{
		$uri->delVar('option');
	}
	
	public static function routingBuildFnTest2()
	{
		
	}
}

/**
 * Test class for JRouter.
 * Generated by PHPUnit on 2009-10-08 at 12:50:42.
 */
class JRouterTest extends PHPUnit_Framework_TestCase
{
	/**
	 * Test for JRouter::getInstance method.
	 */
	public function testGetInstance()
	{
		$router = JRouter::getInstance('Inspector');
		
		$this->assertThat(
			($router instanceof JRouter),
			$this->isTrue()
		);
		
		$this->assertThat(
			($router instanceof JRouterInspector),
			$this->isTrue()
		);
		
		$router->resetInstances();
		
		$options = array('option1' => 'value1', 'option2' => 'value2');
		$router = JRouter::getInstance('Inspector', $options);
		$this->assertThat(
			$router->options,
			$this->equalTo($options)
		);
	}

	/**
	 * Test for JRouter::__construct method.
	 */
	public function test__construct()
	{
		$options = array('option1' => 'value1', 'option2' => 'value2');
		$router = new JRouterInspector($options);
		$this->assertThat(
			$router->options,
			$this->equalTo($options)
		);
	}
	
	/**
	 * Test for JRouter::parse method.
	 */
	public function testParse()
	{
		jimport('joomla.environment.uri');
		$uri = new JURI('index.php');
		$router = JRouter::getInstance('Inspector');
		$router->parserules = array(array($router, 'routingParseFnTest1'));
		
		$vars = $router->parse($uri);
		
		$this->assertThat(
			$vars,
			$this->equalTo(array('option' => 'com_test'))
		);
	}

	/**
	 * Test JRouter::build method.
	 */
	public function testBuild()
	{
		$router = JRouter::getInstance('Inspector');
		$router->buildrules = array(array('JRouterInspector', 'routingBuildFnTest1'));
		$_SERVER['HTTP_HOST'] = 'www.example.com:80';
		$_SERVER['SCRIPT_NAME'] = '/joomla/index.php';
		$_SERVER['PHP_SELF'] = '/joomla/index.php';
		$_SERVER['REQUEST_URI'] = '/joomla/index.php?var=value 10';
		
		$uri = $router->build(array('option' => 'com_test', 'view' => 'test1'));
		
		$this->assertThat(
			$uri->getQuery(true),
			$this->equalTo(array('view' => 'test1'))
		);
		
		$uri = $router->build('index.php?option=com_test&view=test1');
		
		$this->assertThat(
			$uri->getQuery(true),
			$this->equalTo(array('view' => 'test1'))
		);
	}
	
	/**
	 * Test for JRouter::getOptions method.
	 */
	public function testGetOptions()
	{
		$router = new JRouterInspector();
		$options = array('option' => 'com_test', 'view' => 'test1');
		$router->options = $options;
		
		$this->assertThat(
			$router->getOptions(),
			$this->equalTo($options)
		);
		
		$this->assertThat(
			$router->getOptions('option', 'void'),
			$this->equalTo('com_test')
		);
		
		$this->assertThat(
			$router->getOptions('option2', 'void'),
			$this->equalTo('void')
		);
	}

	/**
	 * Test for JRouter::setOption method.
	 */
	public function testSetOption()
	{
		$router = new JRouterInspector();
		
		$router->setOption('option', 'com_test');
		$router->setOption('view', 'test1');
		
		$this->assertThat(
			$router->options,
			$this->equalTo(array('option' => 'com_test', 'view' => 'test1'))
		);
	}

	/**
	 * Test for JRouter::setOptions method.
	 */
	public function testSetOptions()
	{
		$router = new JRouterInspector();
		$options = array('option' => 'com_test', 'view' => 'test1');
		$router->setOptions($options);
		
		$this->assertThat(
			$router->options,
			$this->equalTo($options)
		);
	}

	/**
	 * Test for JRouter::getOptions method.
	 */
	public function testGetVars()
	{
		$router = new JRouterInspector();
		$vars = array('option' => 'com_test', 'view' => 'test1');
		$router->_vars = $vars;
		
		$this->assertThat(
			$router->getVars(),
			$this->equalTo($vars)
		);
		
		$this->assertThat(
			$router->getVars('option', 'void'),
			$this->equalTo('com_test')
		);
		
		$this->assertThat(
			$router->getVars('option2', 'void'),
			$this->equalTo('void')
		);
	}

	/**
	 * Test for JRouter::setOption method.
	 */
	public function testSetVar()
	{
		$router = new JRouterInspector();
		
		$router->setVar('option', 'com_test');
		$router->setVar('view', 'test1');
		
		$this->assertThat(
			$router->_vars,
			$this->equalTo(array('option' => 'com_test', 'view' => 'test1'))
		);
	}

	/**
	 * Test for JRouter::setOptions method.
	 */
	public function testSetVars()
	{
		$router = new JRouterInspector();
		$vars = array('option' => 'com_test', 'view' => 'test1');
		$router->setVars($vars);
		
		$this->assertThat(
			$router->_vars,
			$this->equalTo($vars)
		);
	}

	/**
	 * Test JRouter::attachBuildRule method.
	 */
	public function testAttachBuildRule()
	{
		$router = new JRouterInspector();
		
		$router->attachBuildRule(array('JRouterInspector', 'routingBuildFnTest1'));
		
		$this->assertThat(
			$router->buildrules,
			$this->equalTo(array(array('JRouterInspector', 'routingBuildFnTest1')))
		);
		
		$router->attachBuildRule(array('JRouterInspector', 'routingBuildFnTest2'), 'first');
		
		$this->assertThat(
			$router->buildrules,
			$this->equalTo(
				array(
					array('JRouterInspector', 'routingBuildFnTest2'),
					array('JRouterInspector', 'routingBuildFnTest1')
				)
			)
		);
	}

	/**
	 * @todo Implement testAttachParseRule().
	 */
	public function testAttachParseRule()
	{
		$router = new JRouterInspector();
		
		$router->attachParseRule(array('JRouterInspector', 'routingParseFnTest1'));

		$this->assertThat(
			$router->parserules,
			$this->equalTo(array(array('JRouterInspector', 'routingParseFnTest1')))
		);
		
		$router->attachParseRule(array('JRouterInspector', 'routingParseFnTest2'), 'first');
		
		$this->assertThat(
			$router->parserules,
			$this->equalTo(
				array(
					array('JRouterInspector', 'routingParseFnTest2'),
					array('JRouterInspector', 'routingParseFnTest1')
				)
			)
		);
	}
}
