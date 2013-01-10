<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Event
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * Test class for JEventListenerQueue
 *
 * @package     Joomla.UnitTest
 * @subpackage  Event
 * @since       13.1
 */
class JEventListenerQueueTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var  JEventListenerQueue
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
		$this->object = new JEventListenerQueue;
	}

	/**
	 * Test the constructor.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 * @covers  JEventListenerQueue::__construct
	 */
	public function test__construct()
	{
		$this->assertInstanceOf('SplPriorityQueue', TestReflection::getValue($this->object, 'queue'));
		$this->assertInstanceOf('SplObjectStorage', TestReflection::getValue($this->object, 'storage'));
	}

	/**
	 * Test the attach method.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 * @covers  JEventListenerQueue::attach
	 */
	public function testAttach()
	{
		// Attach a listener with a priority 1.
		$listener = new stdClass;
		$this->object->attach($listener, 1);

		// Get all listeners.
		$listeners = $this->object->getAll();

		// Assert it has been attached.
		$this->assertContains($listener, $listeners);

		// Attach a second listener.
		$listener2 = function() {};
		$this->object->attach($listener2, 1);

		// Get all listeners.
		$listeners = $this->object->getAll();

		// Assert it has been attached.
		$this->assertContains($listener2, $listeners);
	}

	/**
	 * Test the attach method with an already attached object.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 * @covers  JEventListenerQueue::attach
	 */
	public function testAttachExisting()
	{
		// Attach a listener with a priority 1.
		$listener = new stdClass;
		$this->object->attach($listener, 1);

		// Try attaching it twice.
		$this->object->attach($listener, 2);

		// Get all listeners.
		$listeners = $this->object->getAll();

		// Assert it hasn't been attached twice.
		$this->assertCount(1, $listeners);
		$this->assertContains($listener, $listeners);
	}

	/**
	 * Test the attach method with an already attached closure.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 * @covers  JEventListenerQueue::attach
	 */
	public function testAttachExistingClosure()
	{
		// Attach a listener with a priority 1.
		$listener = function() {};
		$this->object->attach($listener, 1);

		// Try attaching it twice.
		$this->object->attach($listener, 2);

		// Get all listeners.
		$listeners = $this->object->getAll();

		$this->assertCount(1, $listeners);
		$this->assertContains($listener, $listeners);
	}

	/**
	 * Test the detach method.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 * @covers  JEventListenerQueue::detach
	 */
	public function testDetach()
	{
		// Attach a listener.
		$listener = new stdClass;
		$this->object->attach($listener, 1);

		// Detach it.
		$this->object->detach($listener);

		// Get all listeners.
		$listeners = $this->object->getAll();

		$this->assertEmpty($listeners);
	}

	/**
	 * Test the detach method with a closure.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 * @covers  JEventListenerQueue::detach
	 */
	public function testDetachClosure()
	{
		// Attach a listener.
		$listener = function() {};
		$this->object->attach($listener, -12);

		// Detach it.
		$this->object->detach($listener);

		// Get all listeners.
		$listeners = $this->object->getAll();

		$this->assertEmpty($listeners);
	}

	/**
	 * Test detaching a non attached object.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 * @covers  JEventListenerQueue::detach
	 */
	public function testDetachNonExisting()
	{
		// Attach a listener.
		$listener = new stdClass;
		$this->object->attach($listener, 1);

		// Detach a non attached one.
		$listener1 = new stdClass;
		$this->object->detach($listener1);

		// Get all listeners.
		$listeners = $this->object->getAll();

		// Assert the first listener is still here.
		$this->assertContains($listener, $listeners);
	}

	/**
	 * Test the contains method.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 * @covers  JEventListenerQueue::contains
	 */
	public function testContains()
	{
		// Attach a listener.
		$listener = new stdClass;
		$this->object->attach($listener);

		// Attach a second one
		$listener2 = function() {};
		$this->object->attach($listener2);

		$this->assertTrue($this->object->contains($listener));
		$this->assertTrue($this->object->contains($listener2));
	}

	/**
	 * Test the contains method without any attached listener.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 * @covers  JEventListenerQueue::contains
	 */
	public function testContainsNonExisting()
	{
		$this->assertFalse($this->object->contains(new stdClass));
	}

	/**
	 * Test the getPriority method
	 *
	 * @return  void
	 *
	 * @since   13.1
	 * @covers  JEventListenerQueue::getPriority
	 */
	public function testGetPriority()
	{
		// Attach a listener with a priority = -52.
		$listener = new stdClass;
		$this->object->attach($listener, -52);

		// Attach a second one with a priority = 12.
		$listener2 = function() {};
		$this->object->attach($listener2, 12);

		$this->assertEquals(-52, $this->object->getPriority($listener));
		$this->assertEquals(12, $this->object->getPriority($listener2));
	}

	/**
	 * Test the getPriority method with a non attached listener.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 * @covers  JEventListenerQueue::getPriority
	 */
	public function testGetPriorityWithoutListener()
	{
		$this->assertFalse($this->object->getPriority(new stdClass));
	}

	/**
	 * Test the getAll method.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 * @covers  JEventListenerQueue::getAll
	 */
	public function testGetAll()
	{
		// Attach a few listeners.
		$listener2 = new stdClass;
		$this->object->attach($listener2, 4);

		$listener5 = new stdClass;
		$this->object->attach($listener5, 3);

		$listener3 = function() {};
		$this->object->attach($listener3, 4);

		$listener6 = new stdClass;
		$this->object->attach($listener6, 2);

		$listener7 = new stdClass;
		$this->object->attach($listener7, 2);

		$listener1 = function() {};
		$this->object->attach($listener1, 5);

		$listener8 = new stdClass;
		$this->object->attach($listener8, 2);

		$listener4 = new stdClass;
		$this->object->attach($listener4, 4);

		$listener9 = function() {};
		$this->object->attach($listener9);

		// Get all listeners.
		$listeners = $this->object->getAll();

		// Test they are sorted by priority.
		$this->assertSame($listener1, $listeners[0]);
		$this->assertSame($listener2, $listeners[1]);
		$this->assertSame($listener3, $listeners[2]);
		$this->assertSame($listener4, $listeners[3]);
		$this->assertSame($listener5, $listeners[4]);
		$this->assertSame($listener6, $listeners[5]);
		$this->assertSame($listener7, $listeners[6]);
		$this->assertSame($listener8, $listeners[7]);
		$this->assertSame($listener9, $listeners[8]);
	}

	/**
	 * Test the getAll method with an empty queue.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 * @covers  JEventListenerQueue::getAll
	 */
	public function testGetAllEmpty()
	{
		$this->assertEmpty($this->object->getAll());
	}

	/**
	 * Test the getIterator method.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 * @covers  JEventListenerQueue::getIterator
	 */
	public function testGetIterator()
	{
		// Attach a few listeners.
		$listener1 = new stdClass;
		$this->object->attach($listener1, 4);

		$listener2 = new stdClass;
		$this->object->attach($listener2, 3);

		$listener3 = new stdClass;
		$this->object->attach($listener3, 2);

		$listener4 = function() {};
		$this->object->attach($listener4, 1);

		$listener5 = function() {};
		$this->object->attach($listener5);

		// Get the inner queue.
		$iterator = $this->object->getIterator();

		$this->assertInstanceOf('SplPriorityQueue', $iterator);

		$listeners = array();

		// Collect all listeners in an array.
		foreach ($iterator as $listener)
		{
			$listeners[] = $listener;
		}

		// Assert they are sorted by priority.
		$this->assertSame($listener1, $listeners[0]);
		$this->assertSame($listener2, $listeners[1]);
		$this->assertSame($listener3, $listeners[2]);
		$this->assertSame($listener4, $listeners[3]);
		$this->assertSame($listener5, $listeners[4]);
	}

	/**
	 * Test the getIterator method with some listeners
	 * having the same priority.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 * @covers  JEventListenerQueue::getIterator
	 */
	public function testGetIteratorSamePriority()
	{
		// Attach a listener with a priority 1.
		$listener1 = new stdClass;
		$this->object->attach($listener1, 1);

		// Attach a second listener with a priority 1.
		$listener2 = new stdClass;
		$this->object->attach($listener2, 1);

		// Attach a third listener with a priority 1.
		$listener3 = new stdClass;
		$this->object->attach($listener3, 1);

		// Attach a third listener with a priority 2.
		$listener4 = new stdClass;
		$this->object->attach($listener4, 2);

		// Get the inner queue.
		$iterator = $this->object->getIterator();

		$listeners = array();

		// Collect all listeners in an array.
		foreach ($iterator as $listener)
		{
			$listeners[] = $listener;
		}

		// Listeners with the same priority must be sorted
		// in the order they were added.
		$this->assertSame($listener4, $listeners[0]);
		$this->assertSame($listener1, $listeners[1]);
		$this->assertSame($listener2, $listeners[2]);
		$this->assertSame($listener3, $listeners[3]);
	}

	/**
	 * Test the getIterator method can be called multiple times.
	 * JEventListenerQueue is not a heap.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 * @covers  JEventListenerQueue::getIterator
	 */
	public function testGetIteratorMultipleTimes()
	{
		// Attach a listener with a priority 1.
		$listener1 = new stdClass;
		$this->object->attach($listener1, 1);

		// Attach a second listener with a priority 2.
		$listener2 = new stdClass;
		$this->object->attach($listener2, 2);

		// Get the inner queue a first time.
		$iterator = $this->object->getIterator();

		$listeners = array();

		// Collect all listeners in an array.
		foreach ($iterator as $listener)
		{
			$listeners[] = $listener;
		}

		$this->assertSame($listener2, $listeners[0]);
		$this->assertSame($listener1, $listeners[1]);

		// Get the inner queue a second time.
		$iterator = $this->object->getIterator();

		$listeners = array();

		// Collect all listeners in an array.
		foreach ($iterator as $listener)
		{
			$listeners[] = $listener;
		}

		$this->assertSame($listener2, $listeners[0]);
		$this->assertSame($listener1, $listeners[1]);
	}

	/**
	 * Test the count method.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 * @covers  JEventListenerQueue::count
	 */
	public function testCount()
	{
		$this->assertCount(0, $this->object);

		// Attach two listeners.
		$listener = new stdClass;
		$this->object->attach($listener, 1);

		$listener1 = function() {};
		$this->object->attach($listener1, 2);

		$this->assertCount(2, $this->object);
	}
}
