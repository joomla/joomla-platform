<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Registry
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

require_once JPATH_PLATFORM . '/joomla/registry/registry.php';
require_once JPATH_PLATFORM . '/joomla/registry/format.php';

/**
 * Test class for JRegistry.
 * Generated by PHPUnit on 2009-10-27 at 15:08:41.
 *
 * @package     Joomla.UnitTest
 * @subpackage  Registry
 * @since       11.1
 */
class JRegistryTest extends PHPUnit_Framework_TestCase
{
	/**
	 * Setup for testing.
	 *
	 * @return void
	 */
	public function setUp()
	{
		include_once 'stubs/JRegistryInspector.php';
	}

	/**
	 * Test the JRegistry::__clone method.
	 *
	 * @covers  JRegistry::__clone
	 *
	 * @return void
	 */
	public function test__clone()
	{
		$a = new JRegistry(array('a' => '123', 'b' => '456'));
		$a->set('foo', 'bar');
		$b = clone $a;

		$this->assertThat(
			serialize($a),
			$this->equalTo(serialize($b))
		);

		$this->assertThat(
			$a,
			$this->logicalNot($this->identicalTo($b)),
			'Line: ' . __LINE__ . '.'
		);
	}

	/**
	 * Test the JRegistry::__toString method.
	 *
	 * @covers  JRegistry::__toString
	 *
	 * @return void
	 */
	public function test__toString()
	{
		$object = new stdClass;
		$a = new JRegistry($object);
		$a->set('foo', 'bar');

		// __toString only allows for a JSON value.
		$this->assertThat(
			(string) $a,
			$this->equalTo('{"foo":"bar"}'),
			'Line: ' . __LINE__ . '.'
		);
	}

	/**
	 * Test the JRegistry::jsonSerialize method.
	 *
	 * @covers  JRegistry::jsonSerialize
	 *
	 * @return void
	 */
	public function testJsonSerialize()
	{
		if (version_compare(PHP_VERSION, '5.4.0', '<'))
		{
			$this->markTestSkipped('This test requires PHP 5.4 or newer.');
		}

		$object = new stdClass;
		$a = new JRegistry($object);
		$a->set('foo', 'bar');

		// __toString only allows for a JSON value.
		$this->assertThat(
			json_encode($a),
			$this->equalTo('{"foo":"bar"}'),
			'Line: ' . __LINE__ . '.'
		);
	}

	/**
	 * Tests serializing JRegistry objects.
	 *
	 * @return void
	 */
	public function testSerialize()
	{
		$a = new JRegistry;
		$a->set('foo', 'bar');

		$serialized = serialize($a);
		$b = unserialize($serialized);

		// __toString only allows for a JSON value.
		$this->assertThat(
			$b,
			$this->equalTo($a),
			'Line: ' . __LINE__ . '.'
		);
	}

	/**
	 * Test the JRegistry::def method.
	 *
	 * @covers  JRegistry::def
	 *
	 * @return void
	 */
	public function testDef()
	{
		$a = new JRegistry;

		$this->assertThat(
			$a->def('foo', 'bar'),
			$this->equalTo('bar'),
			'Line: ' . __LINE__ . '. def should return default value'
		);

		$this->assertThat(
			$a->get('foo'),
			$this->equalTo('bar'),
			'Line: ' . __LINE__ . '. default should now be the current value'
		);
	}

	/**
	 * Tet the JRegistry::bindData method.
	 *
	 * @covers  JRegistry::bindData
	 *
	 * @return void
	 */
	public function testBindData()
	{
		$a = new JRegistryInspector;
		$parent = new stdClass;

		$a->bindData($parent, 'foo');
		$this->assertThat(
			$parent->{0},
			$this->equalTo('foo'),
			'Line: ' . __LINE__ . ' The input value should exist in the parent object.'
		);

		$a->bindData($parent, array('foo' => 'bar'));
		$this->assertThat(
			$parent->{'foo'},
			$this->equalTo('bar'),
			'Line: ' . __LINE__ . ' The input value should exist in the parent object.'
		);

		$a->bindData($parent, array('level1' => array('level2' => 'value2')));
		$this->assertThat(
			$parent->{'level1'}->{'level2'},
			$this->equalTo('value2'),
			'Line: ' . __LINE__ . ' The input value should exist in the parent object.'
		);

		$a->bindData($parent, array('intarray' => array(0, 1, 2)));
		$this->assertThat(
			$parent->{'intarray'},
			$this->equalTo(array(0, 1, 2)),
			'Line: ' . __LINE__ . ' The un-associative array should bind natively.'
		);
	}

	/**
	 * Test the JRegistry::exists method.
	 *
	 * @covers  JRegistry::exists
	 *
	 * @return void
	 */
	public function testExists()
	{
		$a = new JRegistry;
		$a->set('foo', 'bar1');
		$a->set('config.foo', 'bar2');
		$a->set('deep.level.foo', 'bar3');

		$this->assertThat(
			$a->exists('foo'),
			$this->isTrue(),
			'Line: ' . __LINE__ . ' The path should exist, returning true.'
		);

		$this->assertThat(
			$a->exists('config.foo'),
			$this->isTrue(),
			'Line: ' . __LINE__ . ' The path should exist, returning true.'
		);

		$this->assertThat(
			$a->exists('deep.level.foo'),
			$this->isTrue(),
			'Line: ' . __LINE__ . ' The path should exist, returning true.'
		);

		$this->assertThat(
			$a->exists('deep.level.bar'),
			$this->isFalse(),
			'Line: ' . __LINE__ . ' The path should not exist, returning false.'
		);

		$this->assertThat(
			$a->exists('bar.foo'),
			$this->isFalse(),
			'Line: ' . __LINE__ . ' The path should not exist, returning false.'
		);
	}

	/**
	 * Test the JRegistry::get method
	 *
	 * @covers  JRegistry::get
	 *
	 * @return void
	 */
	public function testGet()
	{
		$a = new JRegistry;
		$a->set('foo', 'bar');
		$this->assertEquals('bar', $a->get('foo'), 'Line: ' . __LINE__ . ' get method should work.');
		$this->assertNull($a->get('xxx.yyy'), 'Line: ' . __LINE__ . ' get should return null when not found.');
	}

	/**
	 * Test the JRegistry::getInstance method.
	 *
	 * @covers  JRegistry::getInstance
	 *
	 * @return void
	 */
	public function testGetInstance()
	{
		// Test INI format.
		$a = JRegistry::getInstance('a');
		$b = JRegistry::getInstance('a');
		$c = JRegistry::getInstance('c');

		// Check the object type.
		$this->assertThat(
			$a instanceof JRegistry,
			$this->isTrue(),
			'Line: ' . __LINE__ . '.'
		);

		// Check cache handling for same registry id.
		$this->assertThat(
			$a,
			$this->identicalTo($b),
			'Line: ' . __LINE__ . '.'
		);

		// Check cache handling for different registry id.
		$this->assertThat(
			$a,
			$this->logicalNot($this->identicalTo($c)),
			'Line: ' . __LINE__ . '.'
		);
	}

	/**
	 * Test the JRegistry::loadArray method.
	 *
	 * @covers  JRegistry::loadArray
	 *
	 * @return void
	 */
	public function testLoadArray()
	{
		$array = array(
			'foo' => 'bar'
		);
		$registry = new JRegistry;
		$result = $registry->loadArray($array);

		// Result is always true, no error checking in method.

		// Test getting a known value.
		$this->assertThat(
			$registry->get('foo'),
			$this->equalTo('bar'),
			'Line: ' . __LINE__ . '.'
		);
	}

	/**
	 * Test the JRegistry::loadFile method.
	 *
	 * @covers  JRegistry::loadFile
	 *
	 * @return void
	 */
	public function testLoadFile()
	{
		$registry = new JRegistry;

		// Result is always true, no error checking in method.

		// JSON.
		$result = $registry->loadFile(__DIR__ . '/stubs/jregistry.json');

		// Test getting a known value.
		$this->assertThat(
			$registry->get('foo'),
			$this->equalTo('bar'),
			'Line: ' . __LINE__ . '.'
		);

		// INI.
		$result = $registry->loadFile(__DIR__ . '/stubs/jregistry.ini', 'ini');

		// Test getting a known value.
		$this->assertThat(
			$registry->get('foo'),
			$this->equalTo('bar'),
			'Line: ' . __LINE__ . '.'
		);

		// INI + section.
		$result = $registry->loadFile(__DIR__ . '/stubs/jregistry.ini', 'ini', array('processSections' => true));

		// Test getting a known value.
		$this->assertThat(
			$registry->get('section.foo'),
			$this->equalTo('bar'),
			'Line: ' . __LINE__ . '.'
		);

		// XML and PHP versions do not support stringToObject.
	}

	/**
	 * Test the JRegistry::loadString() method.
	 *
	 * @covers  JRegistry::loadString
	 *
	 * @return void
	 */
	public function testLoadString()
	{
		$registry = new JRegistry;
		$result = $registry->loadString('foo="testloadini1"', 'INI');

		// Test getting a known value.
		$this->assertThat(
			$registry->get('foo'),
			$this->equalTo('testloadini1'),
			'Line: ' . __LINE__ . '.'
		);

		$result = $registry->loadString("[section]\nfoo=\"testloadini2\"", 'INI');

		// Test getting a known value.
		$this->assertThat(
			$registry->get('foo'),
			$this->equalTo('testloadini2'),
			'Line: ' . __LINE__ . '.'
		);

		$result = $registry->loadString("[section]\nfoo=\"testloadini3\"", 'INI', array('processSections' => true));

		// Test getting a known value after processing sections.
		$this->assertThat(
			$registry->get('section.foo'),
			$this->equalTo('testloadini3'),
			'Line: ' . __LINE__ . '.'
		);

		$string = '{"foo":"testloadjson"}';

		$registry = new JRegistry;
		$result = $registry->loadString($string);

		// Result is always true, no error checking in method.

		// Test getting a known value.
		$this->assertThat(
			$registry->get('foo'),
			$this->equalTo('testloadjson'),
			'Line: ' . __LINE__ . '.'
		);

	}

	/**
	 * Test the JRegistry::loadObject method.
	 *
	 * @covers  JRegistry::loadObject
	 *
	 * @return void
	 */
	public function testLoadObject()
	{
		$object = new stdClass;
		$object->foo = 'testloadobject';

		$registry = new JRegistry;
		$result = $registry->loadObject($object);

		// Result is always true, no error checking in method.

		// Test getting a known value.
		$this->assertThat(
			$registry->get('foo'),
			$this->equalTo('testloadobject'),
			'Line: ' . __LINE__ . '.'
		);

		// Test case from Tracker Issue 22444
		$registry = new JRegistry;
		$object = new JObject;
		$object2 = new JObject;
		$object2->set('test', 'testcase');
		$object->set('test', $object2);
		$this->assertTrue($registry->loadObject($object), 'Line: ' . __LINE__ . '. Should load object successfully');
	}

	/**
	 * Test the JRegistry::merge method.
	 *
	 * @covers  JRegistry::merge
	 *
	 * @return void
	 */
	public function testMerge()
	{
		$array1 = array(
			'foo' => 'bar',
			'hoo' => 'hum',
			'dum' => array(
				'dee' => 'dum'
			)
		);

		$array2 = array(
			'foo' => 'soap',
			'dum' => 'huh'
		);
		$registry1 = new JRegistry;
		$registry1->loadArray($array1);

		$registry2 = new JRegistry;
		$registry2->loadArray($array2);

		$registry1->merge($registry2);

		// Test getting a known value.
		$this->assertThat(
			$registry1->get('foo'),
			$this->equalTo('soap'),
			'Line: ' . __LINE__ . '.'
		);

		$this->assertThat(
			$registry1->get('dum'),
			$this->equalTo('huh'),
			'Line: ' . __LINE__ . '.'
		);

		// Test merge with zero and blank value
		$json1 = '{"param1":1, "param2":"value2"}';
		$json2 = '{"param1":2, "param2":"", "param3":0, "param4":-1, "param5":1}';
		$a = new JRegistry($json1);
		$b = new JRegistry;
		$b->loadString($json2, 'JSON');
		$a->merge($b);

		// New param with zero value should show in merged registry
		$this->assertEquals(2, $a->get('param1'), '$b value should override $a value');
		$this->assertEquals('value2', $a->get('param2'), '$a value should override blank $b value');
		$this->assertEquals(0, $a->get('param3'), '$b value of 0 should override $a value');
		$this->assertEquals(-1, $a->get('param4'), '$b value of -1 should override $a value');
		$this->assertEquals(1, $a->get('param5'), '$b value of 1 should override $a value');

		$a = new JRegistry;
		$b = new stdClass;
		$this->assertFalse($a->merge($b), 'Line: ' . __LINE__ . '. Attempt to merge non JRegistry should return false');
	}

	/**
	 * Test the JRegistry::set method.
	 *
	 * @covers  JRegistry::set
	 *
	 * @return void
	 */
	public function testSet()
	{
		$a = new JRegistry;
		$a->set('foo', 'testsetvalue1');

		$this->assertThat(
			$a->set('foo', 'testsetvalue2'),
			$this->equalTo('testsetvalue2'),
			'Line: ' . __LINE__ . '.'
		);
	}

	/**
	 * Test the JRegistry::toArray method.
	 *
	 * @covers  JRegistry::toArray
	 *
	 * @return void
	 */
	public function testToArray()
	{
		$a = new JRegistry;
		$a->set('foo1', 'testtoarray1');
		$a->set('foo2', 'testtoarray2');
		$a->set('config.foo3', 'testtoarray3');

		$expected = array(
			'foo1' => 'testtoarray1',
			'foo2' => 'testtoarray2',
			'config' => array('foo3' => 'testtoarray3')
		);

		$this->assertThat(
			$a->toArray(),
			$this->equalTo($expected),
			'Line: ' . __LINE__ . '.'
		);
	}

	/**
	 * Test the JRegistry::toObject method.
	 *
	 * @covers  JRegistry::toObject
	 *
	 * @return void
	 */
	public function testToObject()
	{
		$a = new JRegistry;
		$a->set('foo1', 'testtoobject1');
		$a->set('foo2', 'testtoobject2');
		$a->set('config.foo3', 'testtoobject3');

		$expected = new stdClass;
		$expected->foo1 = 'testtoobject1';
		$expected->foo2 = 'testtoobject2';
		$expected->config = new StdClass;
		$expected->config->foo3 = 'testtoobject3';

		$this->assertThat(
			$a->toObject(),
			$this->equalTo($expected),
			'Line: ' . __LINE__ . '.'
		);
	}

	/**
	 * Test the JRegistry::toString method.
	 *
	 * @covers  JRegistry::toString
	 *
	 * @return void
	 */
	public function testToString()
	{
		$a = new JRegistry;
		$a->set('foo1', 'testtostring1');
		$a->set('foo2', 'testtostring2');
		$a->set('config.foo3', 'testtostring3');

		$this->assertThat(
			trim($a->toString('JSON')),
			$this->equalTo(
				'{"foo1":"testtostring1","foo2":"testtostring2","config":{"foo3":"testtostring3"}}'
			),
			'Line: ' . __LINE__ . '.'
		);

		$this->assertThat(
			trim($a->toString('INI')),
			$this->equalTo(
				"foo1=\"testtostring1\"\nfoo2=\"testtostring2\"\n\n[config]\nfoo3=\"testtostring3\""
			),
			'Line: ' . __LINE__ . '.'
		);
	}
}
