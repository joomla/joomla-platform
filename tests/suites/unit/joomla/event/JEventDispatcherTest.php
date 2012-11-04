<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Event
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

require_once __DIR__ . '/stubs/foolistener.php';
require_once __DIR__ . '/stubs/barlistener.php';

/**
 * Test class for JEventDispatcher.
 *
 * @package     Joomla.UnitTest
 * @subpackage  Event
 * @since       12.3
 */
class JEventDispatcherTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var  JEventDispatcher
	 */
	protected $object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return  void
	 *
	 * @since   12.3
	 */
	protected function setUp()
	{
		$this->object = new JEventDispatcher;
	}

	/**
	 * Test the getInstance method.
	 *
	 * @since   12.3
	 *
	 * @covers  JEventDispatcher::getInstance
	 */
	public function testGetInstance()
	{
		$dispatcher = JEventDispatcher::getInstance();
		$this->assertInstanceOf('JEventDispatcher', $dispatcher);

		// Get an other instance.
		$dispatcher2 = JEventDispatcher::getInstance();

		$this->assertSame($dispatcher, $dispatcher2);
	}

	/**
	 * Test the registerEvent method.
	 *
	 * @since   12.3
	 *
	 * @covers  JEventDispatcher::registerEvent
	 */
	public function testRegisterEvent()
	{
		// Register the test event.
		$event = new JEvent('test');
		$this->object->registerEvent($event);

		// Register the foo event.
		$event1 = new JEvent('foo');
		$this->object->registerEvent($event1);

		$events = TestReflection::getValue($this->object, 'events');

		$this->assertContains($event, $events);
		$this->assertContains($event1, $events);
	}

	/**
	 * Test the registerEvent method by reseting an existing event.
	 *
	 * @since   12.3
	 *
	 * @covers  JEventDispatcher::registerEvent
	 */
	public function testRegisterEventReset()
	{
		// Register the test event.
		$event = new JEvent('test');
		$this->object->registerEvent($event);

		// Register the event one more time with a reset flag.
		$event1 = new JEvent('test', array('foo'));
		$this->object->registerEvent($event, true);

		$events = TestReflection::getValue($this->object, 'events');

		$this->assertContainsOnly($event1, $events);
	}

	/**
	 * Test the unregisterEvent method.
	 *
	 * @since   12.3
	 *
	 * @covers  JEventDispatcher::unregisterEvent
	 */
	public function testUnregisterEvent()
	{
		// Register the test event.
		$event = new JEvent('test');
		$this->object->registerEvent($event);

		// Unregister it.
		$this->object->unregisterEvent($event);

		$events = TestReflection::getValue($this->object, 'events');
		$this->assertEmpty($events);
	}

	/**
	 * Test the unregisterEvent method by using the event name.
	 *
	 * @since   12.3
	 *
	 * @covers  JEventDispatcher::unregisterEvent
	 */
	public function testUnregisterEventByName()
	{
		// Register the test event.
		$event = new JEvent('test');
		$this->object->registerEvent($event);

		// Unregister it.
		$this->object->unregisterEvent('test');

		$events = TestReflection::getValue($this->object, 'events');
		$this->assertEmpty($events);
	}

	/**
	 * Test the unregisterEvent method by unregistering
	 * a non existing event.
	 *
	 * @since   12.3
	 *
	 * @covers  JEventDispatcher::unregisterEvent
	 */
	public function testUnregisterEventNonRegistered()
	{
		// Register the test event.
		$event = new JEvent('test');
		$this->object->registerEvent($event);

		// Unregister an unexisting event.
		$this->object->unregisterEvent('foo');

		$events = TestReflection::getValue($this->object, 'events');

		// Assert the test event is still here.
		$this->assertContainsOnly($event, $events);
	}

	/**
	 * Test the registerListener method without specified event names.
	 *
	 * @since   12.3
	 *
	 * @covers  JEventDispatcher::registerListener
	 */
	public function testRegisterListenerWithoutSpecifiedEvents()
	{
		$listener = new FooListener;

		$this->object->registerListener($listener);

		// Assert the listener has been registered to all events.
		$this->assertTrue($this->object->hasListener($listener, 'onBeforeSomething'));
		$this->assertTrue($this->object->hasListener($listener, 'onSomething'));
		$this->assertTrue($this->object->hasListener($listener, 'onAfterSomething'));
	}

	/**
	 * Test the registerListener method with specified event names.
	 *
	 * @since   12.3
	 *
	 * @covers  JEventDispatcher::registerListener
	 */
	public function testRegisterListenerWithSpecifiedEvents()
	{
		$listener = new FooListener;

		$eventNames = array(
			'onBeforeSomething',
			'onSomething',
		);

		$this->object->registerListener($listener, $eventNames);

		// Assert the listener has been registered to the onBeforeSomething and onSomething events only.
		$this->assertTrue($this->object->hasListener($listener, 'onBeforeSomething'));
		$this->assertTrue($this->object->hasListener($listener, 'onSomething'));

		$this->assertFalse($this->object->hasListener($listener, 'onAfterSomething'));
	}

	/**
	 * Test the registerListener method with specified priorities / event,
	 * but unspecified event names.
	 *
	 * @since   12.3
	 *
	 * @covers  JEventDispatcher::registerListener
	 */
	public function testRegisterListenerWithPrioritiesWithoutEvents()
	{
		$listener = new FooListener;

		$priorities = array(
			'onBeforeSomething' => 8,
			'onSomething' => -50
		);

		$this->object->registerListener($listener, array(), $priorities);

		// Assert the listener has been registered to the onBeforeSomething and onSomething events only.
		$this->assertTrue($this->object->hasListener($listener, 'onBeforeSomething'));
		$this->assertTrue($this->object->hasListener($listener, 'onSomething'));

		// Assert the listener is correctly registered with the given priority.
		$this->assertEquals(8, $this->object->getListenerPriority($listener, 'onBeforeSomething'));
		$this->assertEquals(-50, $this->object->getListenerPriority($listener, 'onSomething'));
	}

	/**
	 * Test the registerListener method with specified priorities / event,
	 * and specified event names.
	 *
	 * @since   12.3
	 *
	 * @covers  JEventDispatcher::registerListener
	 */
	public function testRegisterListenerWithPrioritiesWithEvents()
	{
		$listener = new FooListener;

		$eventNames = array(
			'onBeforeSomething'
		);

		$priorities = array(
			'onBeforeSomething' => 8,
			'onSomething' => -50
		);

		$this->object->registerListener($listener, $eventNames, $priorities);

		// Assert the listener has been registered to the onBeforeSomething event only.
		$this->assertTrue($this->object->hasListener($listener, 'onBeforeSomething'));
		$this->assertFalse($this->object->hasListener($listener, 'onSomething'));

		// Assert the listener is correctly registered with the given priority.
		$this->assertEquals(8, $this->object->getListenerPriority($listener, 'onBeforeSomething'));
	}

	/**
	 * Test the registerListener method with an invalid specified event name.
	 * (the event name doesn't match any listener method).
	 *
	 * @since   12.3
	 *
	 * @covers  JEventDispatcher::registerListener
	 */
	public function testRegisterListenerInvalidSpecifiedEvent()
	{
		$listener = new FooListener;

		$eventNames = array(
			'onNothing',
		);

		$this->object->registerListener($listener, $eventNames);

		// Assert the listener is not registered.
		$this->assertFalse($this->object->hasListener($listener));
	}

	/**
	 * Test the registerListener method for a closure listener.
	 *
	 * @since   12.3
	 *
	 * @covers  JEventDispatcher::registerListener
	 */
	public function testRegisterListenerClosure()
	{
		$listener = function (JEvent $e) {};

		$this->object->registerListener($listener, array('onSomething', 'onAfterSomething'));

		$this->assertTrue($this->object->hasListener($listener, 'onSomething'));
		$this->assertTrue($this->object->hasListener($listener, 'onAfterSomething'));
	}

	/**
	 * Test the registerListener method for a closure listener.
	 *
	 * @since   12.3
	 *
	 * @covers  JEventDispatcher::registerListener
	 */
	public function testRegisterListenerClosureWithPriority()
	{
		$listener = function (JEvent $e) {};

		$this->object->registerListener($listener, array('onSomething'), array('onSomething' => 122));

		$this->assertTrue($this->object->hasListener($listener, 'onSomething'));
		$this->assertEquals(122, $this->object->getListenerPriority($listener, 'onSomething'));
	}

	/**
	 * Test the registerListener exception because of
	 * unspecified event name for a closure listener.
	 *
	 * @since   12.3
	 *
	 * @covers  JEventDispatcher::registerListener
	 *
	 * @expectedException  InvalidArgumentException
	 */
	public function testRegisterListenerClosureException()
	{
		$listener = function (JEvent $e) {};

		$this->object->registerListener($listener);
	}

	/**
	 * Test the registerListener exception
	 *
	 * @since   12.3
	 *
	 * @covers  JEventDispatcher::registerListener
	 *
	 * @expectedException  InvalidArgumentException
	 */
	public function testRegisterListenerException()
	{
		$this->object->registerListener('foo');
	}

	/**
	 * Test the unregisterListener method without specified event names.
	 *
	 * @since   12.3
	 *
	 * @covers  JEventDispatcher::unregisterListener
	 */
	public function testUnregisterListenerWithoutSpecifiedEvents()
	{
		$listener = new FooListener;

		$eventNames = array(
			'onBeforeSomething',
			'onAfterSomething'
		);

		// Register the listener for the onBeforeSomething and onAfterSomething events.
		$this->object->registerListener($listener, $eventNames);

		// Unregister the listener.
		$this->object->unregisterListener($listener);

		// Assert the listener has been unregistered from these 2 events.
		$this->assertFalse($this->object->hasListener($listener, 'onBeforeSomething'));
		$this->assertFalse($this->object->hasListener($listener, 'onAfterSomething'));
	}

	/**
	 * Test the unregisterListener method without specified event names
	 * and a closure listener.
	 *
	 * @since   12.3
	 *
	 * @covers  JEventDispatcher::unregisterListener
	 */
	public function testUnregisterListenerClosureWithoutSpecifiedEvents()
	{
		$listener = function (JEvent $e) {};

		$eventNames = array(
			'onBeforeSomething',
			'onAfterSomething'
		);

		// Register the listener for the onBeforeSomething and onAfterSomething events.
		$this->object->registerListener($listener, $eventNames);

		// Unregister the listener.
		$this->object->unregisterListener($listener);

		// Assert the listener has been unregistered from these 2 events.
		$this->assertFalse($this->object->hasListener($listener, 'onBeforeSomething'));
		$this->assertFalse($this->object->hasListener($listener, 'onAfterSomething'));
	}

	/**
	 * Test the unregisterListener method with specified event names.
	 *
	 * @since   12.3
	 *
	 * @covers  JEventDispatcher::unregisterListener
	 */
	public function testUnregisterListenerWithEvent()
	{
		$listener = new FooListener;

		$eventNames = array(
			'onBeforeSomething',
			'onAfterSomething'
		);

		// Register the listener for the onBeforeSomething and onAfterSomething events.
		$this->object->registerListener($listener, $eventNames);

		// Unregister the listener from the onAfterSomething event.
		$this->object->unregisterListener($listener, array('onAfterSomething'));

		// Assert the listener has been unregistered only from the onAfterSomething event.
		$this->assertTrue($this->object->hasListener($listener, 'onBeforeSomething'));
		$this->assertFalse($this->object->hasListener($listener, 'onAfterSomething'));
	}

	/**
	 * Test the unregisterListener method exception.
	 *
	 * @since   12.3
	 *
	 * @covers  JEventDispatcher::unregisterListener
	 *
	 * @expectedException  InvalidArgumentException
	 */
	public function testUnregisterListenerException()
	{
		$this->object->unregisterListener('foo');
	}

	/**
	 * Test the getListeners method.
	 *
	 * @since   12.3
	 *
	 * @covers  JEventDispatcher::getListeners
	 */
	public function testGetListeners()
	{
		// Register two listeners for the onBeforeSomething event.
		$listener1 = new FooListener;
		$listener2 = function (JEvent $e) {};

		$this->object->registerListener($listener1, array('onBeforeSomething'))
			->registerListener($listener2, array('onBeforeSomething'));

		// Get the event listeners.
		$listeners = $this->object->getListeners('onBeforeSomething');

		$this->assertSame($listener1, $listeners[0]);
		$this->assertSame($listener2, $listeners[1]);
	}

	/**
	 * Test the getListeners method by using an event object.
	 *
	 * @since   12.3
	 *
	 * @covers  JEventDispatcher::getListeners
	 */
	public function testGetListenersEventObject()
	{
		// Register two listeners for the onBeforeSomething event.
		$listener1 = new FooListener;
		$listener2 = function (JEvent $e) {};

		$this->object->registerListener($listener1, array('onBeforeSomething'))
			->registerListener($listener2, array('onBeforeSomething'));

		// Get the listeners using an event object.
		$listeners = $this->object->getListeners(new JEvent('onBeforeSomething'));

		$this->assertSame($listener1, $listeners[0]);
		$this->assertSame($listener2, $listeners[1]);
	}

	/**
	 * Test the getListeners method default value.
	 *
	 * @since   12.3
	 *
	 * @covers  JEventDispatcher::getListeners
	 */
	public function testGetListenersDefault()
	{
		$this->assertEmpty($this->object->getListeners('unexisting'));
	}

	/**
	 * Test the getListenerPriority method.
	 *
	 * @since   12.3
	 *
	 * @covers  JEventDispatcher::getListenerPriority
	 */
	public function testGetListenerPriority()
	{
		// Register a listener with some priorities.
		$listener1 = new FooListener;

		$this->object->registerListener($listener1,
			array('onBeforeSomething', 'onAfterSomething'),
			array('onBeforeSomething' => 22, 'onAfterSomething' => -100)
		);

		$listener2 = function (JEvent $e) {};
		$this->object->registerListener($listener2,
			array('onBeforeSomething'),
			array('onBeforeSomething' => 114)
		);

		$this->assertEquals(22, $this->object->getListenerPriority($listener1, 'onBeforeSomething'));
		$this->assertEquals(-100, $this->object->getListenerPriority($listener1, 'onAfterSomething'));
		$this->assertEquals(114, $this->object->getListenerPriority($listener2, 'onBeforeSomething'));
	}

	/**
	 * Test the getListenerPriority default value.
	 *
	 * @since   12.3
	 *
	 * @covers  JEventDispatcher::getListenerPriority
	 */
	public function testGetListenerPriorityDefault()
	{
		$this->assertFalse($this->object->getListenerPriority(new stdClass, 'onSomething'));
	}

	/**
	 * Test the countListeners method.
	 *
	 * @since   12.3
	 *
	 * @covers  JEventDispatcher::countListeners
	 */
	public function testCountListeners()
	{
		$listener1 = new FooListener;
		$listener2 = new BarListener;
		$listener3 = function (JEvent $e) {};

		$this->object->registerListener($listener1, array('onBeforeSomething'));
		$this->object->registerListener($listener2, array('onBeforeSomething'));
		$this->object->registerListener($listener3, array('onBeforeSomething'));

		$this->assertEquals(3, $this->object->countListeners('onBeforeSomething'));
		$this->assertEquals(3, $this->object->countListeners(new JEvent('onBeforeSomething')));
	}

	/**
	 * Test the countListeners method default value.
	 *
	 * @since   12.3
	 *
	 * @covers  JEventDispatcher::countListeners
	 */
	public function testCountListenersDefault()
	{
		$this->assertEquals(0, $this->object->countListeners('onSomething'));
	}

	/**
	 * Test the hasListener method.
	 *
	 * @since   12.3
	 *
	 * @covers  JEventDispatcher::hasListener
	 */
	public function testHasListener()
	{
		$listener1 = new FooListener;

		$this->object->registerListener($listener1, array('onBeforeSomething'));

		$this->assertTrue($this->object->hasListener($listener1));
		$this->assertTrue($this->object->hasListener($listener1, 'onBeforeSomething'));
		$this->assertTrue($this->object->hasListener($listener1, new JEvent('onBeforeSomething')));
	}

	/**
	 * Test the hasListener method default value.
	 *
	 * @since   12.3
	 *
	 * @covers  JEventDispatcher::hasListener
	 */
	public function testHasListenerDefault()
	{
		$this->assertFalse($this->object->hasListener(new stdClass, 'onSomething'));
		$this->assertFalse($this->object->hasListener(new stdClass, new JEvent('onSomething')));
	}

	/**
	 * Test the triggerEvent method.
	 *
	 * @since   12.3
	 *
	 * @covers  JEventDispatcher::triggerEvent
	 */
	public function testTriggerEvent()
	{
		$mockListener1 = $this->getMock('FooListener');
		$mockListener1->expects($this->once())
			->method('onBeforeSomething');

		$mockListener2 = $this->getMock('BarListener');
		$mockListener2->expects($this->once())
			->method('onBeforeSomething');

		$invoked = 0;
		$listener3 = function (JEvent $e) use (&$invoked) {
			$invoked++;
		};

		$this->object->registerListener($mockListener1, array('onBeforeSomething'));
		$this->object->registerListener($mockListener2, array('onBeforeSomething'));
		$this->object->registerListener($listener3, array('onBeforeSomething'));

		$this->object->triggerEvent('onBeforeSomething');

		$this->assertEquals(1, $invoked);
	}

	/**
	 * Test the triggerEvent method with a specified priority.
	 *
	 * @since   12.3
	 *
	 * @covers  JEventDispatcher::triggerEvent
	 */
	public function testTriggerEventWithPriority()
	{
		// The listener 1 will manipulate the foo argument $foo[] = 1
		$listener1 = new FooListener;

		// The listener 2 will manipulate the foo argument $foo[] = 2
		$listener2 = new BarListener;

		// The listener 3 will manipulate the foo argument $foo[] = 3
		$listener3 = function (JEvent $e)
		{
			$foo = $e->getArgument('foo');
			$foo[] = 3;
			$e->setArgument('foo', $foo);
		};

		// The listener 4 will manipulate the foo argument $foo[] = 4
		$listener4 = function (JEvent $e)
		{
			$foo = $e->getArgument('foo');
			$foo[] = 4;
			$e->setArgument('foo', $foo);
		};

		$this->object->registerListener($listener1, array('onBeforeSomething'), array('onBeforeSomething' => 3));
		$this->object->registerListener($listener2, array('onBeforeSomething'), array('onBeforeSomething' => 2));
		$this->object->registerListener($listener3, array('onBeforeSomething'), array('onBeforeSomething' => 1));
		$this->object->registerListener($listener4, array('onBeforeSomething'));

		// Create an event with an empty array as foo argument.
		$event = new JEvent('onBeforeSomething');
		$event->setArgument('foo', array());

		// Trigger the event.
		$event = $this->object->triggerEvent($event);

		// Assert the listeners were called in the expected order.
		$foo = $event->getArgument('foo');
		$this->assertEquals(array(1, 2, 3, 4), $foo);
	}

	/**
	 * Test the triggerEvent method with a listener stopping the event propagation.
	 *
	 * @since   12.3
	 *
	 * @covers  JEventDispatcher::triggerEvent
	 */
	public function testTriggerEventPropagationStopped()
	{
		$listener1 = new FooListener;

		// This listener will stop the event propagation.
		$listener2 = new BarListener;

		$invoked = 0;
		$listener3 = function (JEvent $e) use (&$invoked) {
			$invoked++;
		};

		$this->object->registerListener($listener1, array('onSomething'), array('onSomething' => 3));
		$this->object->registerListener($listener2, array('onSomething'), array('onSomething' => 2));
		$this->object->registerListener($listener3, array('onSomething'), array('onSomething' => 1));

		$this->object->triggerEvent('onSomething');

		// The listener 2 will stop the event propagation.
		// We don't expect the listener 3 to be called.
		$this->assertEquals(0, $invoked);
	}

	/**
	 * Test the triggerEvent method with a registered event object.
	 *
	 * @since   12.3
	 *
	 * @covers  JEventDispatcher::triggerEvent
	 */
	public function testTriggerEventRegistered()
	{
		// Register a custom event.
		$event = new JEvent('onBeforeSomething');
		$this->object->registerEvent($event);

		$listener1 = new FooListener;
		$this->object->registerListener($listener1, array('onBeforeSomething'));

		$eventReturned = $this->object->triggerEvent('onBeforeSomething');

		$this->assertSame($event, $eventReturned);
	}

	/**
	 * Test the triggerEvent method with a registered event object.
	 *
	 * @since   12.3
	 *
	 * @covers  JEventDispatcher::triggerEvent
	 */
	public function testTriggerEventObject()
	{
		$listener1 = new FooListener;

		$this->object->registerListener($listener1, array('onBeforeSomething'));

		// Trigger the event by passing a custom object.
		$event = new JEvent('onBeforeSomething');
		$eventReturned = $this->object->triggerEvent($event);

		$this->assertSame($event, $eventReturned);
	}

	/**
	 * Test the triggerEvent method with a non registered event.
	 *
	 * @since   12.3
	 *
	 * @covers  JEventDispatcher::triggerEvent
	 */
	public function testTriggerEventDefault()
	{
		$this->assertInstanceOf('JEvent', $this->object->triggerEvent('onTest'));
	}
}
