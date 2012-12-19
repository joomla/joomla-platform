<?php
/**
 * @package    Joomla.UnitTest
 *
 * @copyright  Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

/**
 * Test class for JSession.
 * Generated by PHPUnit on 2011-10-26 at 19:33:07.
 *
 * @package     Joomla.UnitTest
 * @subpackage  Session
 * @since       11.1
 */
class JSessionTest extends TestCase
{
	/**
	 * @var  JSession
	 */
	protected $object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return void
	 */
	protected function setUp()
	{
		parent::setUp();

		$this->saveFactoryState();

		$this->object = JSession::getInstance('none', array('expire' => 20, 'force_ssl' => true, 'name' => 'name', 'id' => 'id', 'security' => 'security'));
		$this->input = new JInput;
		$this->input->cookie = $this->getMock('JInputCookie', array('set', 'get'));
		$this->object->initialise($this->input);

		$this->input->cookie->expects($this->any())
			->method('set');
		$this->input->cookie->expects($this->any())
			->method('get')
			->will($this->returnValue(null));

		$this->object->start();
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @return void
	 */
	protected function tearDown()
	{
		if (session_id())
		{
			session_unset();
			session_destroy();
		}

		$this->restoreFactoryState();
	}

	/**
	 * Test cases for getInstance
	 * string    handler of type JSessionStorage: none or database
	 * array    arguments for $options in form of associative array
	 * string    message if test case fails
	 *
	 * @return array
	 */
	Public function casesGetInstance()
	{
		return array(
			'first_instance' => array(
				'none',
				array('expire' => 99),
				'Line: ' . __LINE__ . ': ' . 'Should not be a different instance and options should not change'
			),
			'second_instance' => array(
				'database',
				array(),
				'Line: ' . __LINE__ . ': ' . 'Should not be a different instance '
			)
		);
	}

	/**
	 * Test getInstance
	 *
	 * @param   string  $store    @todo
	 * @param   array   $options  @todo
	 *
	 * @dataProvider casesGetInstance
	 * @covers  JSession::getInstance
	 *
	 * @return void
	 */
	public function testGetInstance($store, $options)
	{
		$oldSession = $this->object;
		$newSession = JSession::getInstance($store, $options);
		// The properties and values should be identical to each other.
		$this->assertThat(
			$oldSession,
			$this->identicalTo($newSession)
		);

		// They should be the same object.
		$this->assertSame($oldSession,$newSession);
	}

	/**
	 * Test getState
	 *
	 * @covers  JSession::getState
	 *
	 * @return void
	 */
	public function testGetState()
	{
		$this->assertEquals(
			TestReflection::getValue($this->object, '_state'),
			$this->object->getState(),
			'Session state should be the same'
		);
	}

	/**
	 * Test getExpire()
	 *
	 * @covers  JSession::getExpire
	 *
	 * @return void
	 */
	public function testGetExpire()
	{
		$this->assertEquals(
			TestReflection::getValue($this->object, '_expire'),
			$this->object->getExpire(),
			'Session expire time should be the same'
		);
	}

	/**
	 * Test getToken
	 *
	 * @covers  JSession::getToken
	 *
	 * @return void
	 */
	public function testGetToken()
	{
		$this->object->set('session.token', 'abc');
		$this->assertEquals('abc', $this->object->getToken(), 'Token should be abc');

		$this->object->set('session.token', null);
		$token = $this->object->getToken();
		$this->assertEquals(32, strlen($token), 'Line: ' . __LINE__ . ' Token should be length 32');

		$token2 = $this->object->getToken(true);
		$this->assertNotEquals($token, $token2, 'Line: ' . __LINE__ . ' New token should be different');
	}

	/**
	 * Test hasToken
	 *
	 * @covers  JSession::hasToken
	 *
	 * @return void
	 */
	public function testHasToken()
	{
		$token = $this->object->getToken();
		$this->assertTrue($this->object->hasToken($token), 'Line: ' . __LINE__ . ' Correct token should be true');

		$this->assertFalse($this->object->hasToken('abc', false), 'Line: ' . __LINE__ . ' Should return false with wrong token');
		$this->assertEquals('active', $this->object->getState(), 'Line: ' . __LINE__ . ' State should not be set to expired');

		$this->assertFalse($this->object->hasToken('abc'), 'Line: ' . __LINE__ . ' Should return false with wrong token');
		$this->assertEquals('expired', $this->object->getState(), 'Line: ' . __LINE__ . ' State should be set to expired by default');
	}

	/**
	 * Test getFormToken
	 *
	 * @covers  JSession::getFormToken
	 *
	 * @return void
	 */
	public function testGetFormToken()
	{
		$user = JFactory::getUser();

		JFactory::$application = $this->getMock('JInputCookie', array('set', 'get'));
		JFactory::$application->expects($this->once())
			->method('get')
			->with($this->equalTo('secret'))
			->will($this->returnValue('abc'));

		$this->object->set('secret','abc');
		$expected = md5('abc' . $user->get('id', 0) . $this->object->getToken(false));
		$this->assertEquals($expected, $this->object->getFormToken(), 'Form token should be calculated as above.');
	}

	/**
	 * Test getName
	 *
	 * @covers  JSession::getName
	 *
	 * @return void
	 */
	public function testGetName()
	{
		$this->assertEquals(session_name(), $this->object->getName(), 'Session names should match.');
	}

	/**
	 * Test getId
	 *
	 * @covers  JSession::getId
	 *
	 * @return void
	 */
	public function testGetId()
	{
		$this->assertEquals(session_id(), $this->object->getId(), 'Session ids should match.');
	}

	/**
	 * Test getStores
	 *
	 * @covers  JSession::getStores
	 *
	 * @return void
	 */
	public function testGetStores()
	{
		$return = JSession::getStores();

		$this->assertTrue(
			is_array($return),
			'Line: ' . __LINE__ . ' JSession::getStores must return an array.'
		);
		$this->assertContains(
			'database',
			$return,
			'Line: ' . __LINE__ . ' session storage database should always be available.'
		);
		$this->assertContains(
			'none',
			$return,
			'Line: ' . __LINE__ . ' session storage "none" should always be available.'
		);
	}

	/**
	 * Test addExternalConnector
	 *
	 * @covers  JSession::addExternalConnector
	 *
	 * @return void
	 */
	public function testAddExternalConnector()
	{

		$validStoreClasses = array('bogus' => 'JSessionStorageBogus',
			// needs further testing	'object' => 'TestStorageObject'
			'fake' => 'JSessionStorageFake'
		);
		$invalidStoreClasses = array('fail' => 'JSessionStorageFail',
			'fakity' => 'JSessionStorageFakity',
			'incomplete' => 'JSessionStorageIncomplete',
			);
		$extraStoresClasses = array_merge($validStoreClasses, $invalidStoreClasses
		);

		$path = dirname(__FILE__) . '/stubs/storage/';

		// get original stores
		$return = JSession::getStores();
		// Reset the external connectors
		$initialConnectors = JSession::addExternalConnector(false, false);
		// Setup our external storage classes
		foreach ($extraStoresClasses as $filename => $classname)
		{
			include $path.$filename.'.php';
		}
		// get defined external connectors
		$externalConnectors = JSession::addExternalConnector(false);

		// get working session stores including external
		$extraStores = JSession::getStores();

		// reset defined connectors
		$resetConnectors = JSession::addExternalConnector(false, true);

		// get working session stores without external
		$stores = JSession::getStores();


		// sanity check initial state
		$this->assertTrue(
			is_array($return),
			$return,
			'Line: ' . __LINE__ . ' JSession::getStores must return an array.'
		);
		$this->assertContains(
			'database',
			$return,
			'Line: ' . __LINE__ . ' session storage database should always be available.'
		);
		$this->assertContains(
			'none',
			$return,
			'Line: ' . __LINE__ . ' session storage "none" should always be available.'
		);

		// sanity check the initial connector state
		$this->assertTrue(
			is_array($initialConnectors),
			'Line: ' . __LINE__ . ' JSession::addExternalConnector must return an array.'
		);
		$this->assertTrue(
			(count($initialConnectors) == 0),
			'Line: ' . __LINE__ . ' JSession::$extraConnectors should initalliy be empty.'
		);

		// sanity check imported state
		$this->assertTrue(
			is_array($externalConnectors),
			'Line: ' . __LINE__ . ' JSession::addExternalConnector must return an array.'
		);

		// Check that all our new classes were registered
		foreach ($extraStoresClasses as $filename => $classname)
		{
			$this->assertContains(
				$classname,
				$externalConnectors,
				'Line: ' . __LINE__ . ' session classname "'. $classname.'" should be defined.'
			);
		}

		// Check that all our new valid stores exist
		foreach ($validStoreClasses as $filename => $classname)
		{
			$this->assertContains(
				$classname,
				$extraStores,
				'Line: ' . __LINE__ . ' session store "' . $filename . '" should be valid.'
			);
		}

		// Check that all our invalid stores do not exist
		foreach ($invalidStoreClasses as $filename => $classname)
		{
			$this->assertFalse(
				array_key_exists($filename, $extraStores),
				'Line: ' . __LINE__ . ' session store "' . $filename . '" should not be valid.'
			);
		}

		// Check that we reset our extra connections
		foreach ($externalConnectors as $filename => $classname)
		{
			$this->assertContains(
				$classname,
				$resetConnectors,
				'Line: ' . __LINE__ . ' session classname "' . $classname . '" should be defined.'
			);
		}

		// sanity check initial reset stores
		$this->assertTrue(
			is_array($stores),
			$stores,
			'Line: ' . __LINE__ . ' JSession::getStores must return an array.'
		);
		$this->assertContains(
			'database',
			$stores,
			'Line: ' . __LINE__ . ' session storage database should always be available.'
		);
		$this->assertContains(
			'none',
			$stores,
			'Line: ' . __LINE__ . ' session storage "none" should always be available.'
		);

		// Check that all our invalid stores do not exist
		foreach ($extraStoresClasses as $filename => $classname)
		{
			$this->assertFalse(
				array_key_exists($filename, $stores),
				'Line: ' . __LINE__ . ' session store "' . $filename . '" should no longer be valid.'
			);
		}

	}

	/**
	 * Test isNew
	 *
	 * @return void
	 */
	public function testIsNew()
	{
		$this->object->set('session.counter', 1);

		$this->assertEquals(true, $this->object->isNew(), '$isNew should be true.');
	}

	/**
	 * Test...
	 *
	 * @todo Implement testGet().
	 *
	 * @return void
	 */
	public function testGet()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Test...
	 *
	 * @todo Implement testSet().
	 *
	 * @return void
	 */
	public function testSet()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Test...
	 *
	 * @todo Implement testHas().
	 *
	 * @return void
	 */
	public function testHas()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Test...
	 *
	 * @todo Implement testClear().
	 *
	 * @return void
	 */
	public function testClear()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Test...
	 *
	 * @todo Implement testDestroy().
	 *
	 * @return void
	 */
	public function testDestroy()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Test...
	 *
	 * @todo Implement testRestart().
	 *
	 * @return void
	 */
	public function testRestart()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Test...
	 *
	 * @todo Implement testFork().
	 *
	 * @return void
	 */
	public function testFork()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Test...
	 *
	 * @todo Implement testClose().
	 *
	 * @return void
	 */
	public function testClose()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

}
