<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Event
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * Test class for JEvent.
 *
 * @package     Joomla.UnitTest
 * @subpackage  Event
 * @since       13.1
 */
class JEventTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var  JEvent
	 */
	protected $object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 */
	protected function setUp()
	{
		$this->object = new JEvent('test');
	}

	/**
	 * Test the constructor.
	 *
	 * @since   13.1
	 *
	 * @covers  JEvent::__construct
	 */
	public function test__construct()
	{
		$name = 'test';
		$arguments = array(1, 2, 3);

		$event = new JEvent($name, $arguments);

		$this->assertEquals($name, $event->getName());
		$this->assertEquals($arguments, $event->getArguments());
	}

	/**
	 * Test the getArguments method.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 *
	 * @covers  JEvent::getArguments
	 */
	public function testGetArguments()
	{
		TestReflection::setValue($this->object, 'arguments', true);

		$this->assertTrue($this->object->getArguments());
	}

	/**
	 * Test the setArguments method.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 *
	 * @covers  JEvent::setArguments
	 */
	public function testSetArguments()
	{
		$arguments = array(
			'foo'   => 'bar',
			'test'  => 'test',
			'test1' => 'test1'
		);

		$this->object->setArguments($arguments);

		$this->assertEquals($arguments, $this->object->getArguments());
	}

	/**
	 * Test the hasArgument method.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 *
	 * @covers  JEvent::hasArgument
	 */
	public function testHasArgument()
	{
		$this->assertFalse($this->object->hasArgument('test'));

		$this->object->setArgument('test', true);
		$this->object->setArgument('foo', 1);

		$this->assertTrue($this->object->hasArgument('test'));
		$this->assertTrue($this->object->hasArgument('foo'));
	}

	/**
	 * Test the getArgument method.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 *
	 * @covers  JEvent::getArgument
	 */
	public function testGetArgument()
	{
		$this->assertNull($this->object->getArgument('non-existing'));

		// Specify a default value.
		$this->assertFalse($this->object->getArgument('non-existing', false));

		// Set some arguments.
		$arguments = array('foo' => 'bar', 'test' => 'test');

		TestReflection::setValue($this->object, 'arguments', $arguments);

		$this->assertEquals('bar', $this->object->getArgument('foo'));
		$this->assertEquals('test', $this->object->getArgument('test'));
	}

	/**
	 * Test the setArgument method.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 *
	 * @covers  JEvent::setArgument
	 */
	public function testSetArgument()
	{
		$this->object->setArgument('foo', 'bar');
		$this->object->setArgument('test', 'test');

		$this->assertEquals('bar', $this->object->getArgument('foo'));
		$this->assertEquals('test', $this->object->getArgument('test'));
	}

	/**
	 * Test the setArgument method when reseting an argument.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 *
	 * @covers  JEvent::setArgument
	 */
	public function testSetArgumentReset()
	{
		// Specify the foo argument.
		$this->object->setArgument('foo', 'bar');

		// Reset it with an other value.
		$this->object->setArgument('foo', 'test');

		$this->assertEquals('test', $this->object->getArgument('foo'));
	}

	/**
	 * Test the count method.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 *
	 * @covers  JEvent::count
	 */
	public function testCount()
	{
		$this->assertCount(0, $this->object);

		// Add a few arguments.
		$this->object->setArgument('foo', 'bar');
		$this->object->setArgument('test', 'test');

		$this->assertCount(2, $this->object);
	}

	/**
	 * Test the stopPropagation method.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 *
	 * @covers  JEvent::stopPropagation
	 */
	public function testStopPropagation()
	{
		$this->object->stopPropagation();

		$this->assertTrue($this->object->isStopped());
	}

	/**
	 * Test the isStopped method.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 *
	 * @covers  JEvent::isStopped
	 */
	public function testIsStopped()
	{
		TestReflection::setValue($this->object, 'stopped', 'foo');

		$this->assertFalse($this->object->isStopped());

		TestReflection::setValue($this->object, 'stopped', true);

		$this->assertTrue($this->object->isStopped());
	}

	/**
	 * Test the getName method.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 *
	 * @covers  JEvent::getName
	 */
	public function testGetName()
	{
		TestReflection::setValue($this->object, 'name', 'foo');

		$this->assertEquals($this->object->getName(), 'foo');
	}

	/**
	 * Test serialize, unserialize.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 *
	 * @covers  JEvent::serialize
	 * @covers  JEvent::unserialize
	 */
	public function testSerializeUnserialize()
	{
		$arguments = array(
			'foo'   => 'bar',
			'test'  => 'test',
			'test1' => 'test1'
		);

		$event = new JEvent('test', $arguments);

		$serialized = serialize($event);
		$event2 = unserialize($serialized);

		$this->assertEquals($event, $event2);
	}
}
