<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Object
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

JLoader::register('JObjectBuran', __DIR__ . '/stubs/buran.php');
JLoader::register('JObjectCapitaliser', __DIR__ . '/stubs/capitaliser.php');

/**
 * Tests for the JObject class.
 *
 * @package     Joomla.UnitTest
 * @subpackage  Object
 * @since       12.2
 */
class JObjectTest extends TestCase
{
	/**
	 * @var    JObject
	 * @since  12.2
	 */
	private $_instance;

	/**
	 * Tests the object constructor.
	 *
	 * @return  void
	 *
	 * @covers	JObject::__construct
	 */
	public function test__construct()
	{
		$instance = new JObject(array('property1' => 'value1', 'property2' => 5));
		$this->assertThat(
			$instance->get('property1'),
			$this->equalTo('value1')
		);
	}

	/**
	 * Tests the __get method.
	 *
	 * @return  void
	 *
	 * @covers  JObject::__get
	 * @since   12.3
	 */
	public function test__get()
	{
		$this->assertNull(
			$this->_instance->foobar,
			'Unknown property should return null.'
		);
	}

	/**
	 * Tests the __isset method.
	 *
	 * @return  void
	 *
	 * @covers  JObject::__isset
	 * @since   12.3
	 */
	public function test__isset()
	{
		$this->assertFalse(isset($this->_instance->title), 'Unknown property');

		$this->_instance->bind(array('title' => true));

		$this->assertTrue(isset($this->_instance->title), 'Property is set.');
	}

	/**
	 * Tests the __set method where a custom setter is available.
	 *
	 * @return  void
	 *
	 * @covers  JObject::__set
	 * @since   12.3
	 */
	public function test__set_setter()
	{
		$instance = new JObjectCapitaliser;

		// Set the property and assert that it is the expected value.
		$instance->test_value = 'one';
		$this->assertEquals('ONE', $instance->test_value);

		$instance->bind(array('test_value' => 'two'));
		$this->assertEquals('TWO', $instance->test_value);
	}

	/**
	 * Tests the __unset method.
	 *
	 * @return  void
	 *
	 * @covers  JObject::__unset
	 * @since   12.3
	 */
	public function test__unset()
	{
		$this->_instance->bind(array('title' => true));

		$this->assertTrue(isset($this->_instance->title));

		unset($this->_instance->title);

		$this->assertFalse(isset($this->_instance->title));
	}

	/**
	 * Tests the bind method.
	 *
	 * @return  void
	 *
	 * @covers  JObject::bind
	 * @since   12.3
	 */
	public function testBind()
	{
		$properties = array('null' => null);

		$this->_instance->null = 'notNull';
		$this->_instance->bind($properties, false);
		$this->assertSame('notNull', $this->_instance->null, 'Checking binding without updating nulls works correctly.');

		$this->_instance->bind($properties);
		$this->assertSame(null, $this->_instance->null, 'Checking binding with updating nulls works correctly.');
	}

	/**
	 * Tests the bind method with array input.
	 *
	 * @return  void
	 *
	 * @covers  JObject::bind
	 * @since   12.3
	 */
	public function testBind_array()
	{
		$properties = array(
			'property_1' => 'value_1',
			'property_2' => '1',
			'property_3' => 1,
			'property_4' => false,
			'property_5' => array('foo')
		);

		// Bind an array to the object.
		$this->_instance->bind($properties);

		// Assert that the values match.
		foreach ($properties as $property => $value)
		{
			$this->assertEquals($value, $this->_instance->$property);
		}
	}

	/**
	 * Tests the bind method with input that is a traverable object.
	 *
	 * @return  void
	 *
	 * @covers  JObject::bind
	 * @since   12.3
	 */
	public function testBind_arrayObject()
	{
		$properties = array(
			'property_1' => 'value_1',
			'property_2' => '1',
			'property_3' => 1,
			'property_4' => false,
			'property_5' => array('foo')
		);

		$traversable = new ArrayObject($properties);

		// Bind an array to the object.
		$this->_instance->bind($traversable);

		// Assert that the values match.
		foreach ($properties as $property => $value)
		{
			$this->assertEquals($value, $this->_instance->$property);
		}
	}

	/**
	 * Tests the bind method with object input.
	 *
	 * @return  void
	 *
	 * @covers  JObject::bind
	 * @since   12.3
	 */
	public function testBind_object()
	{
		$properties = new stdClass;
		$properties->property_1 = 'value_1';
		$properties->property_2 = '1';
		$properties->property_3 = 1;
		$properties->property_4 = false;
		$properties->property_5 = array('foo');

		// Bind an array to the object.
		$this->_instance->bind($properties);

		// Assert that the values match.
		foreach ($properties as $property => $value)
		{
			$this->assertEquals($value, $this->_instance->$property);
		}
	}

	/**
	 * Tests the bind method for an expected exception.
	 *
	 * @return  void
	 *
	 * @covers             JObject::bind
	 * @expectedException  InvalidArgumentException
	 * @since              12.3
	 */
	public function testBind_exception()
	{
		$this->_instance->bind('foobar');
	}

	/**
	 * Tests setting the default for a property of the object.
	 *
	 * @return void
	 *
	 * @covers  JObject::def
	 * @since   12.3
	 */
	public function testDef()
	{
		$this->_instance->def('check');
		$this->assertEquals(null, $this->_instance->def('check'));
		$this->_instance->def('check', 'paint');
		$this->_instance->def('check', 'forced');
		$this->assertEquals('paint', $this->_instance->def('check'));
		$this->assertNotEquals('forced', $this->_instance->def('check'));
	}

	/**
	 * Tests the dump method.
	 *
	 * @return  void
	 *
	 * @covers  JObject::dump
	 * @since   12.3
	 */
	public function testDump()
	{
		$dump = $this->_instance->dump();

		$this->assertEquals(
			'object',
			gettype($dump),
			'Dump should return an object.'
		);

		$this->assertEmpty(
			get_object_vars($dump),
			'Empty JObject should give an empty dump.'
		);

		$properties = array(
			'scalar' => 'value_1',
			'date' => new JDate('2012-01-01'),
			'registry' => new JRegistry(array('key' => 'value')),
			'jobject' => new JObject(
				array(
					'level2' => new JObject(
						array(
							'level3' => new JObject(
								array(
									'level4' => new JObject(
										array(
											'level5' => 'deep',
										)
									)
								)
							)
						)
					)
				)
			),
		);

		// Bind an array to the object.
		$this->_instance->bind($properties);

		// Dump the object.
		$dump = $this->_instance->dump();

		$this->assertEquals($dump->scalar, 'value_1');
		$this->assertEquals($dump->date, '2012-01-01 00:00:00');
		$this->assertEquals($dump->registry, (object) array('key' => 'value'));
		$this->assertInstanceOf('stdClass', $dump->jobject->level2);
		$this->assertInstanceOf('stdClass', $dump->jobject->level2->level3);
		$this->assertInstanceOf('JObject', $dump->jobject->level2->level3->level4);

		$dump = $this->_instance->dump(0);
		$this->assertInstanceOf('JDate', $dump->date);
		$this->assertInstanceOf('JRegistry', $dump->registry);
		$this->assertInstanceOf('JObject', $dump->jobject);

		$dump = $this->_instance->dump(1);
		$this->assertEquals($dump->date, '2012-01-01 00:00:00');
		$this->assertEquals($dump->registry, (object) array('key' => 'value'));
		$this->assertInstanceOf('stdClass', $dump->jobject);
		$this->assertInstanceOf('JObject', $dump->jobject->level2);
	}

	/**
	 * Tests the dumpProperty method.
	 *
	 * @return  void
	 *
	 * @covers  JObject::dumpProperty
	 * @since   12.3
	 */
	public function testDumpProperty()
	{
		$this->_instance->bind(array('dump_test' => 'dump_test_value'));
		$this->assertEquals('dump_test_value', TestReflection::invoke($this->_instance, 'dumpProperty', 'dump_test'));
	}

	/**
	 * Tests the getIterator method.
	 *
	 * @return  void
	 *
	 * @covers	JObject::getIterator
	 * @since   12.3
	 */
	public function testGetIterator()
	{
		$this->assertInstanceOf('ArrayIterator', $this->_instance->getIterator());
	}

	/**
	 * Tests the getProperty method.
	 *
	 * @return  void
	 *
	 * @covers  JObject::getProperty
	 * @since   12.3
	 */
	public function testGetProperty()
	{
		$this->_instance->bind(array('get_test' => 'get_test_value'));
		$this->assertEquals('get_test_value', $this->_instance->get_test);
	}

	/**
	 * Tests the getProperty method.
	 *
	 * @return  void
	 *
	 * @covers             JObject::getProperty
	 * @expectedException  InvalidArgumentException
	 * @since              12.3
	 */
	public function testGetProperty_exception()
	{
		$this->_instance->bind(array('get_test' => 'get_test_value'));

		// Get the reflection property. This should throw an exception.
		$property = TestReflection::getValue($this->_instance, 'get_test');
	}

	/**
	 * Tests the jsonSerialize method.
	 *
	 * Note, this is not completely backward compatible. Previous this would just return the class name.
	 *
	 * @return  void
	 *
	 * @covers  JObject::jsonSerialize
	 * @since   12.3
	 */
	public function testJsonSerialize()
	{
		$this->assertEquals('{}', json_encode($this->_instance->jsonSerialize()), 'Empty object.');

		$this->_instance->bind(array('title' => 'Simple Object'));
		$this->assertEquals('{"title":"Simple Object"}', json_encode($this->_instance->jsonSerialize()), 'Simple object.');
	}

	/**
	 * Tests the setProperty method.
	 *
	 * @return  void
	 *
	 * @covers  JObject::setProperty
	 * @since   12.3
	 */
	public function testSetProperty()
	{
		$this->_instance->set_test = 'set_test_value';
		$this->assertEquals('set_test_value', $this->_instance->set_test);

		$object = new JObjectCapitaliser;
		$object->test_value = 'upperCase';

		$this->assertEquals('UPPERCASE', $object->test_value);
	}

	/**
	 * Tests the setProperty method.
	 *
	 * @return  void
	 *
	 * @covers             JObject::setProperty
	 * @expectedException  InvalidArgumentException
	 * @since              12.3
	 */
	public function testSetProperty_exception()
	{
		// Get the reflection property. This should throw an exception.
		$property = TestReflection::getValue($this->_instance, 'set_test');
	}

	/**
	 * Test that JObject::setProperty() will not set a property which starts with a null byte.
	 *
	 * @return  void
	 *
	 * @covers  JObject::setProperty
	 * @see     http://us3.php.net/manual/en/language.types.array.php#language.types.array.casting
	 * @since   12.3
	 */
	public function testSetPropertySkipsPropertyWithNullBytes()
	{
		// Create a property that starts with a null byte.
		$property = "\0foo";

		// Attempt to set the property.
		$this->_instance->$property = 'bar';

		// The property should not be set.
		$this->assertNull($this->_instance->$property);
	}

	//
	// Deprecated tests.
	//

	/**
	 * Tests getting a property of the object.
	 *
	 * @return void
	 *
	 * @covers	    JObject::get
	 * @deprecated  13.1
	 */
	public function testGet()
	{
		$this->_instance->goo = 'car';
		$this->assertEquals('car', $this->_instance->get('goo', 'fudge'));
		$this->assertEquals('fudge', $this->_instance->get('foo', 'fudge'));
		$this->assertNotEquals(null, $this->_instance->get('foo', 'fudge'));
		$this->assertNull($this->_instance->get('boo'));
	}

	/**
	 * Tests getting a single error.
	 *
	 * @return  void
	 *
	 * @covers	    JObject::getError
	 * @deprecated  13.1
	 */
	public function testGetError()
	{
		$this->_instance->setError(1234);
		$this->_instance->setError('Second Test Error');
		$this->_instance->setError('Third Test Error');
		$this->assertEquals(
			1234,
			$this->_instance->getError(0, false),
			'Should return the test error as number'
		);
		$this->assertEquals(
			'Second Test Error',
			$this->_instance->getError(1),
			'Should return the second test error'
		);
		$this->assertEquals(
			'Third Test Error',
			$this->_instance->getError(),
			'Should return the third test error'
		);
		$this->assertFalse(
			$this->_instance->getError(20),
			'Should return false, since the error does not exist'
		);

		$exception = new Exception('error');
		$this->_instance->setError($exception);
		$this->assertThat(
			$this->_instance->getError(3, true),
			$this->equalTo((string)$exception)
		);
	}

	/**
	 * Tests getting the array of errors.
	 *
	 * @return  void
	 *
	 * @covers	    JObject::getErrors
	 * @deprecated  13.1
	 */
	public function testGetErrors()
	{
		$errors = array(1234, 'Second Test Error', 'Third Test Error');

		foreach ($errors as $error)
		{
			$this->_instance->setError($error);
		}

		$this->assertAttributeEquals(
			$this->_instance->getErrors(),
			'_errors',
			$this->_instance
		);
		$this->assertEquals(
			$errors,
			$this->_instance->getErrors(),
			'Should return every error set'
		);
	}

	/**
	 * Tests getting the properties of the object.
	 *
	 * @return  void
	 *
	 * @covers	    JObject::getProperties
	 * @deprecated  13.1
	 */
	public function testGetProperties()
	{
		$instance = new JObjectBuran(
			array(
				'_privateProperty1' => 'valuep1',
				'property1' => 'value1'
			)
		);

		$this->assertEquals(
			array(
				'_privateProperty1',
				'property1',
				'rocket',
				'_errors',
				'_properties',
			),
			array_keys($instance->getProperties(false)),
			'Should get all properties, including private ones'
		);

		$this->assertEquals(
			array(
				'property1',
				'rocket',
			),
			array_keys($instance->getProperties()),
			'Should get all public properties'
		);
	}

	/**
	 * Tests setting a property.
	 *
	 * @return void
	 *
	 * @covers	    JObject::set
	 * @deprecated  13.1
	 */
	public function testSet()
	{
		$this->assertEquals(null, $this->_instance->set('foo', 'imintheair'));
		$this->assertEquals('imintheair', $this->_instance->set('foo', 'nojibberjabber'));
		$this->assertEquals('nojibberjabber', $this->_instance->foo);
	}

	/**
	 * Tests setting an error.
	 *
	 * @return  void
	 *
	 * @covers	    JObject::setError
	 * @deprecated  13.1
	 */
	public function testSetError()
	{
		$this->_instance->setError('A Test Error');
		$this->assertAttributeEquals(
			array('A Test Error'),
			'_errors',
			$this->_instance
		);
	}

	/**
	 * Tests setting multiple properties.
	 *
	 * @return  void
	 *
	 * @covers	    JObject::setProperties
	 * @deprecated  13.1
	 */
	public function testSetProperties()
	{
		$a = array('foo' => 'ghost', 'knife' => 'stewie');
		$f = 'foo';
		$this->assertEquals(true, $this->_instance->setProperties($a));
		$this->assertEquals(false, $this->_instance->setProperties($f));
		$this->assertEquals('ghost', $this->_instance->foo);
		$this->assertEquals('stewie', $this->_instance->knife);
	}

	/**
	 * Setup the tests.
	 *
	 * @return  void
	 *
	 * @since   11.4
	 */
	protected function setUp()
	{
		parent::setUp();

		$this->_instance = new JObject;
	}
}
