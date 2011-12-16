<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Cache
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * Testable instance of a JCacheObject object.
 *
 * @package     Joomla.UnitTest
 * @subpackage  Cache
 * @since       11.4
 */
class JCacheObjectInstance extends JCacheObject
{
}

/**
 * Test class for JCacheObject.
 *
 * @package     Joomla.UnitTest
 * @subpackage  Cache
 * @since       11.4
 */
class JCacheObjectTest extends JoomlaTestCase
{
	/**
	 * @var    JCacheObjectInstance
	 * @since  11.4
	 */
	protected $instance;

	/**
	 * This method is called before a test is executed.
	 *
	 * @return  void
	 *
	 * @since   11.4
	 */
	protected function setUp()
	{
		parent::setUp();

		$this->saveFactoryState();
		JFactory::$cache = array(
			md5('JCacheObject' . 'output' . null) => $this->getMockCache(array('value' => 's:5:"saved";',)),
		);

		$this->instance = new JCacheObjectInstance;
	}

	/**
	 * This method is called after a test is executed.
	 *
	 * @return  void
	 *
	 * @since   11.4
	 */
	protected function tearDown()
	{
		$this->restoreFactoryState();

		parent::tearDown();
	}

	/**
	 * Tests the JCacheObject::getStoreId method.
	 *
	 * @return  void
	 *
	 * @since   11.4
	 */
	public function testGetStoreId()
	{
		$this->assertThat(
			ReflectionHelper::invoke($this->instance, 'getStoreId', 1),
			$this->equalTo(md5('JCacheObjectInstance:1'))
		);
	}

	/**
	 * Tests the JCacheObject::retrieve method.
	 *
	 * @return  void
	 *
	 * @since   11.4
	 */
	public function testRetrieve()
	{
		$this->assertThat(
			ReflectionHelper::invoke($this->instance, 'retrieve', 'foo', false),
			$this->isNull(),
			'Checks returns null when the cache id does not exist.'
		);

		// Preload the cache.
		ReflectionHelper::setValue(
			$this->instance,
			'cache',
			array(
				'1' => 'bingo',
			)
		);

		$this->assertThat(
			ReflectionHelper::invoke($this->instance, 'retrieve', '1', false),
			$this->equalTo('bingo'),
			'Checks a cached value it returned.'
		);

		$this->assertThat(
			ReflectionHelper::invoke($this->instance, 'retrieve', 'value', false),
			$this->isNull(),
			'Checks non-persistent value is null.'
		);

		$this->assertThat(
			ReflectionHelper::invoke($this->instance, 'retrieve', 'value', true),
			$this->equalTo('saved'),
			'Checks persistent value returns a value.'
		);
	}

	/**
	 * Tests the JCacheObject::store method.
	 *
	 * @return  void
	 *
	 * @since   11.4
	 */
	public function testStore()
	{
		$data = 'save me';
		$this->assertThat(
			ReflectionHelper::invoke($this->instance, 'store', '1', $data, false),
			$this->equalTo($data),
			'Checks that store returns the original data.'
		);

		$cache = ReflectionHelper::getValue($this->instance, 'cache');
		$this->assertThat(
			$cache['1'],
			$this->equalTo('save me')
		);

		ReflectionHelper::invoke($this->instance, 'store', '2', 'save me', true);
		$this->assertThat(
			JFactory::getCache('JCacheObject', 'output', null)->get('2'),
			$this->equalTo('s:7:"save me";'),
			'Checks the persistance code was invoked.'
		);
	}
}
