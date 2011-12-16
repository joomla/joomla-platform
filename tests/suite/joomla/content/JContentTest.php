<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Content
 *
 * @copyright   Copyright 2011 eBay, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// Load test mocks.
require_once __DIR__ . '/mocks/content.php';
require_once __DIR__ . '/mocks/helper.php';
require_once __DIR__ . '/stubs/inspector.php';

/**
 * Tests for the JContentHelperTest class.
 *
 * @package     Joomla.UnitTest
 * @subpackage  Content
 * @since       11.4
 */
class JContentTest extends JoomlaDatabaseTestCase
{
	/**
	 * Test object.
	 *
	 * @var    JContent
	 * @since  12.1
	 */
	protected $content;

	/**
     * Returns the test dataset.
     *
     * @return  PHPUnit_Extensions_Database_DataSet_IDataSet
     *
     * @since   11.4
	 */
	protected function getDataSet()
	{
		return $this->createXMLDataSet(__DIR__.'/stubs/content.xml');
	}

	/**
	 * Get an original content item.
	 *
	 * @param   integer  $contentId  The id of the content.
	 * @param   integer  $userId     The id of the user.
	 * @param   string   $type       The content type.
	 *
	 * @return  JContent
	 *
	 * @since   11.4
	 */
	protected function getOriginal($contentId = 1, $userId = 1, $type = 'Inspector')
	{
		// Get a new content factory.
		$factory = new JContentFactory('TCPrefix', null, $this->getMockWeb());

		// Get a new content object.
		$original = $factory->getContent($type)
			->load($contentId);

		// Create a guest user.
		$user = new JUser();
		$user->set('id', $userId);
		$user->set('guest', false);

		// Push the guest user into the content object.
		ReflectionHelper::setValue($original, 'user', $user);

		return $original;
	}

	/**
	 * Method to set up the tests.
	 *
	 * @return  void
	 *
	 * @since   11.4
	 */
	public function setUp()
	{
		parent::setUp();

		$this->saveFactoryState();

		JFactory::$session = $this->getMockSession();

		// Create a new content factory.
		$this->factory = new JContentFactory('TCPrefix', $this->getMockDatabase(), $this->getMockWeb());

		// Create a mock type.
		$this->type = $this->factory->getType('TCType');
		$this->type->bind(
			array(
				'type_id'	=> 1,
				'title'		=> 'Test Type',
				'alias'		=> 'tctype'
			)
		);

		// Get a mock helper.
		$this->helper = JContentHelperMock::create($this);
		$this->helper->expects($this->any())
			->method('getTypes')
			->will($this->returnValue(array('tctype' => $this->type)));

		// Get the content object.
		$this->content = $this->factory->getContent('TCType', $this->helper);
	}

	/**
	 * Tears down the fixture.
	 *
	 * This method is called after a test is executed.
	 *
	 * @return  void
	 *
     * @since   11.4
	 */
	public function tearDown()
	{
		$this->restoreFactoryState();

		parent::tearDown();
	}

	/**
	 * Method to test that JContent::checkin() will delete the content object
	 * if it is still flagged as temporary.
	 *
	 * @return  void
	 *
	 * @since   11.4
	 */
	public function testCheckinDeletesTemporaryContent()
	{
		// Get a new content factory.
		$factory = new JContentFactory('TCPrefix', null, $this->getMockWeb());

		// Get a new content object.
		$content = $factory->getContent('Inspector');

		// Create the content record.
		$content->create();

		// Get the content id.
		$contentId = $content->content_id;

		// Checkin the content record.
		$content->checkin();

		// Try to load the content id.
		try
		{
			// Get a new content object.
			$content = $factory->getContent('Inspector');

			// Load the content id.
			$content->load($contentId);

			// Fail the test.
			$this->fail('The content was not deleted during checkin.');
		}
		catch (Exception $error)
		{
			$this->assertEquals('JDATABASEOBJECT_NOT_FOUND', $error->getMessage());
		}
	}

	/**
	 * Method to test that checking out content works properly as an anonymous user
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testCheckoutWithAnonymousUser()
	{
		// Try an anonymous checkout (should work)
		$factory = new JContentFactory('TCPrefix', null, $this->getMockWeb());

		// Get a new content object.
		$content = $factory->getContent('Inspector');

		// Create the content record.
		$content->create();

		// Check out
		$content->checkout();

		// Check In
		$content->checkin();

		// Check In again
		$content->checkin();
	}

	/**
	 * Method to test that checking out content updates the database.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testCheckoutWithUser()
	{
		// create fake sessions
		$session1 = $this->getMockSession(array('getId'=>'DEEDED01', 'get.user.id'=>1, 'get.user.guest'=>0));

		// create fake users and match them up
		$user1 = $session1->get('user');

		// Get a new content factory.
		$factory = new JContentFactory('TCPrefix', null, $this->getMockWeb(array('session'=>$session1)), $user1);

		// Get a new content object.
		$content = $factory->getContent('Inspector');

		// Create the content record.
		$content->create();

		// Get the content id.
		$contentId = $content->content_id;

		// set some values
		$content->bind(array('title'=>'Testing', 'temporary'=>0))->update();

		// check that item is checked in successfully
		$content->checkin();

		// Get a new content object.
		$content = $factory->getContent('Inspector');

		// Re-load the content id.
		$content->load($contentId);

		// check that item can be re-checked out in my session
		$content->checkout();

		// check out in a new session using the same user
		$session2 = $this->getMockSession(array('getId'=>'CAFEBABE', 'get.user.id'=>1, 'get.user.guest'=>0));
		$user2 = $session2->get('user');
		$factory = new JContentFactory('TCPrefix', null, $this->getMockWeb(array('session'=>$session2)), $user2);
		$content2 = $factory->getContent('Inspector');
		$content2->load($contentId);
		$content2->checkout();

		// create a new session and try to checkout with that session (should fail)
		$session3 = $this->getMockSession(array('getId'=>'DEADBEEF', 'get.user.id'=>2, 'get.user.guest'=>0));
		$user3 = $session3->get('user');
		$factory = new JContentFactory('TCPrefix', null, $this->getMockWeb(array('session'=>$session3)), $user3);
		$content3 = $factory->getContent('Inspector');
		$content3->load($contentId);

		try {
			// try to check out
			$content3->checkout();

			// Fail the test.
			$this->fail('The content was not locked appropriately when checked out.');
		}
		catch (Exception $error)
		{
			$this->assertEquals('JCONTENT_CHECKED_OUT', $error->getMessage());
		}

		// remove old session from database for other user account
		$db = JFactory::getDbo();
		$db->setQuery('DELETE FROM #__session WHERE username = "testuser"');
		$db->Query();

		// expire old session and try to checkout with new session (should work)
		$content3->checkout();

		// byebye
		$content3->delete();
	}

	/**
	 * Method to test that JContent::cleanup() is deleting temporary content.
	 *
	 * @return  void
	 *
	 * @since   11.4
	 */
	public function testCleanup()
	{
		// Get a new content factory.
		$factory = new JContentFactory('TCPrefix', null, $this->getMockWeb());

		// Get a new content object.
		$content = $factory->getContent('Inspector');

		// Create the content record.
		$content->create();

		// Get the content id.
		$contentId = $content->content_id;

		// Clean up the content record.
		$content->cleanup();

		// Try to load the content id.
		try
		{
			// Get a new content object.
			$content = $factory->getContent('Inspector');

			// Load the content id.
			$content->load($contentId);

			// Fail the test.
			$this->fail('The content was not cleaned up correctly');
		}
		catch (Exception $error)
		{
			$this->assertEquals('JDATABASEOBJECT_NOT_FOUND', $error->getMessage());
		}
	}

	/**
	 * Method to test that JContent::copy() works.
	 *
	 * @return  void
	 *
	 * @since   11.4
	 */
	public function testCopy()
	{
		// Get a new content factory.
		$factory = new JContentFactory('TCPrefix', null, $this->getMockWeb());

		// Get a new content object.
		$original = $factory->getContent('Inspector');

		// Load the content item.
		$original->load(1);

		// Copy the content object.
		$copy = $original->copy();

		// Bind a special description.
		$copy->body = 'Test Copy';

		// Store the copy.
		$copy->update()->checkin();

		// Get the copy id.
		$copyId = $copy->content_id;

		// Get a new content object.
		$copy = $factory->getContent('Inspector');

		// Load the content item.
		$copy->load($copyId);

		// Check that the copy properties are okay.
		$this->assertEquals('Test Copy', $copy->body);

		// Check that the original unique properties are changed.
		$this->assertNotEquals($copy->title, $original->title);
		$this->assertNotEquals($copy->alias, $original->alias);
		$this->assertNotEquals($copy->content_id, $original->content_id);
		$this->assertNotEquals($copy->created_date, $original->created_date);
		$this->assertNotEquals($copy->created_user_id, $original->created_user_id);
		$this->assertEquals($copy->featured, 0);
		$this->assertEquals($copy->likes, 0);
		$this->assertEquals($copy->revision, 1);
	}

	/**
	 * Method to test that calling JContent::copy() before the content data has
	 * been loaded will throw a LogicException exception.
	 *
	 * @return  void
	 *
	 * @expectedException  LogicException
	 * @since              11.4
	 */
	public function testCopyThrowsExceptionIfContentNotLoaded()
	{
		$this->content->copy();
	}

	/**
	 * Method to test JContent::route().
	 *
	 * @param   string  $expected  The expected route.
	 * @param   array   $vars      The vars to pass to JContent::route().
	 *
	 * @return  void
	 *
	 * @since         11.4
	 * @dataProvider  seedGetRoute
	 */
	public function testRoute($expected, $vars)
	{
		// Get a new content factory.
		$factory = new JContentFactory('TCPrefix', null, $this->getMockWeb());

		// Get a new content object.
		$content = $factory->getContent('Inspector');

		// Load the content item.
		$content->load(1);

		$this->assertEquals($expected, $content->route($vars));
	}

	/**
	 * Method to seed data for testGetRoute.
	 *
	 * @return  void
	 *
	 * @since   11.4
	 */
	public function seedGetRoute()
	{
		return array(
			array(
				'index.php?type=inspector&view=item&content_id=1',
				array()
			),
			array(
				'index.php?type=inspector&content_id=1&task=hit',
				array('task' => 'hit')
			),
		);
	}

	/**
	 * Method to test that hit increments the database and internal hit count.
	 *
	 * @return  void
	 *
	 * @since   11.4
	 */
	public function testHit()
	{
		// Get a new content factory.
		$factory = new JContentFactory('TCPrefix', null, $this->getMockWeb());

		// Get a new content object.
		$content = $factory->getContent('Inspector');

		// Load the content item.
		$content->load(1);

		// Get the starting hit count.
		$startHits = $content->hits;

		// Hit the content item.
		$content->hit();

		// Check that the internal hit count was updated.
		$this->assertEquals($startHits + 1, $content->hits);

		// Reload the content item.
		$content->load(1);

		// Get the ending hit count.
		$endHits = $content->hits;

		// Check that the hit count increased by one.
		$this->assertEquals($startHits + 1, $endHits);
	}


	/**
	 * Method to test that calling JContent::hit() before the content data has
	 * been loaded will throw a LogicException exception.
	 *
	 * @return  void
	 *
	 * @expectedException  LogicException
	 * @since              11.4
	 */
	public function testHitThrowsExceptionIfContentNotLoaded()
	{
		$this->content->hit();
	}

	/**
	 * Method to test the update method.
	 *
	 * @return  void
	 *
	 * @since   11.4
	 */
	public function testUpdate()
	{
		$body = "I've been updated!\n";

		// Get a new content factory.
		$factory = new JContentFactory('TCPrefix', null, $this->getMockWeb());

		// Get a new content object.
		$content = $factory->getContent('Inspector');

		// Load the content item.
		$content->load(1);

		// Update the body.
		$content->body = $body;

		// Run the update method.
		$content->update();

		// Load the content item again.
		$content->load(1);

		// Check that the content was updated.
		$this->assertEquals($body, $content->body);
	}

	/**
	 * Method to test that the update method will generate an alias if one is not set.
	 *
	 * @return  void
	 *
	 * @since   11.4
	 */
	public function testUpdateCreatesAlias()
	{
		// Set a new title for the content item.
		$title = 'New Simple Title';
		$alias = JFilterOutput::stringUrlSafe($title);

		// Get a new content factory.
		$factory = new JContentFactory('TCPrefix', null, $this->getMockWeb());

		// Get a new content object.
		$content = $factory->getContent('Inspector');

		// Load the content item.
		$content->load(1);

		// Change the title.
		$content->title = $title;

		// Unset the alias.
		unset($content->alias);

		// Assert that the alias is null.
		$this->assertNull($content->alias);

		// Run the update method.
		$content->update();

		// Load the content item again.
		$content->load(1);

		// Check that the alias was generated.
		$this->assertEquals($title, $content->title);
		$this->assertEquals($alias, $content->alias);
	}

	/**
	 * Method to test JContent::validateAccessLevel() works with a valid value.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testUpdateWithValidAccessLevel()
	{
		// Get a content object from the database.
		$content = $this->getOriginal();

		// Set the access level to public.
		$content->access = 1;

		// Run the update method.
		$content->update();

		// Load the content item again.
		$content->load(1);

		// Check that the access level was set.
		$this->assertEquals(1, $content->access);
	}

	/**
	 * Method to test JContent::validateAccessLevel() throws an exception for an invalid value.
	 *
	 * @return  void
	 *
	 * @expectedException  UnexpectedValueException
	 * @since              12.1
	 */
	public function testUpdateWithInvalidAccessLevel()
	{
		// Get a content object from the database.
		$content = $this->getOriginal();

		// Set the access level to an invalid value.
		$content->access = 99;

		// Run the update method. This should throw an UnexpectedValueException.
		$content->update();
	}

	/**
	 * Method to test that a call to JContent::isActive() will return the
	 * expected result based on the object's state.
	 *
	 * @return  void
	 *
	 * @since   11.4
	 */
	public function testIsActive()
	{
		// Set up the content object.
		$this->content->bind(array('content_id' => 1));

		// Check that isActive returns false as expected.
		$this->content->bind(array('state' => -1));
		$this->assertFalse($this->content->isActive());

		$this->content->bind(array('state' => 0));
		$this->assertFalse($this->content->isActive());

		$this->content->bind(array('state' => 2));
		$this->assertFalse($this->content->isActive());

		// Check that isActive returns true as expected.
		$this->content->bind(array('state' => 1));
		$this->assertTrue($this->content->isActive());
	}

	/**
	 * Method to test that a call to JContent::isArchived() will return the
	 * expected result based on the object's state.
	 *
	 * @return  void
	 *
	 * @since   11.4
	 */
	public function testIsArchived()
	{
		// Set up the content object.
		$this->content->bind(array('content_id' => 1));

		// Check that isArchived returns false as expected.
		$this->content->bind(array('state' => -1));
		$this->assertFalse($this->content->isArchived());

		$this->content->bind(array('state' => 0));
		$this->assertFalse($this->content->isArchived());

		$this->content->bind(array('state' => 1));
		$this->assertFalse($this->content->isArchived());

		// Check that isArchived returns true as expected.
		$this->content->bind(array('state' => 2));
		$this->assertTrue($this->content->isArchived());
	}

	/**
	 * Method to test that a call to JContent::isDraft() will return the
	 * expected result based on the object's state.
	 *
	 * @return  void
	 *
	 * @since   11.4
	 */
	public function testIsDraft()
	{
		// Set up the content object.
		$this->content->bind(array('content_id' => 1));

		// Check that isDraft returns false as expected.
		$this->content->bind(array('state' => -1));
		$this->assertFalse($this->content->isDraft());

		$this->content->bind(array('state' => 1));
		$this->assertFalse($this->content->isDraft());

		$this->content->bind(array('state' => 2));
		$this->assertFalse($this->content->isDraft());

		// Check that isDraft returns true as expected.
		$this->content->bind(array('state' => 0));
		$this->assertTrue($this->content->isDraft());
	}

	/**
	 * Method to test that a call to JContent::isFeatured() will return the
	 * expected result based on the object's state.
	 *
	 * @return  void
	 *
	 * @since   11.4
	 */
	public function testIsFeatured()
	{
		// Set up the content object.
		$this->content->bind(array('content_id' => 1));

		// Check that isFeatured returns false as expected.
		$this->content->bind(array('featured' => false));
		$this->assertFalse($this->content->isFeatured());

		// Check that isFeatured returns true as expected.
		$this->content->bind(array('featured' => true));
		$this->assertTrue($this->content->isFeatured());
	}

	/**
	 * Method to test that a call to JContent::isTrashed() will return the
	 * expected result based on the object's state.
	 *
	 * @return  void
	 *
	 * @since   11.4
	 */
	public function testIsTrashed()
	{
		// Set up the content object.
		$this->content->bind(array('content_id' => 1));

		// Check that isTrashed returns false as expected.
		$this->content->bind(array('state' => 0));
		$this->assertFalse($this->content->isTrashed());

		$this->content->bind(array('state' => 1));
		$this->assertFalse($this->content->isTrashed());

		$this->content->bind(array('state' => 2));
		$this->assertFalse($this->content->isTrashed());

		// Check that isTrashed returns true as expected.
		$this->content->bind(array('state' => -1));
		$this->assertTrue($this->content->isTrashed());
	}

	/**
	 * Method to test that a call to JContent::isTemporary() will return the
	 * expected result based on the object's state.
	 *
	 * @return  void
	 *
	 * @since   11.4
	 */
	public function testIsTemporary()
	{
		// Set up the content object.
		$this->content->bind(array('content_id' => 1));

		// Check that isTemporary returns false as expected.
		$this->content->bind(array('temporary' => false));
		$this->assertFalse($this->content->isTemporary());

		// Check that isTemporary returns true as expected.
		$this->content->bind(array('temporary' => true));
		$this->assertTrue($this->content->isTemporary());
	}

	/**
	 * Method to test that a call to JContent::isVisible() will return the
	 * expected result based on the object's state.
	 *
	 * @return  void
	 *
	 * @since   11.4
	 */
	public function testIsVisibleWithActualNullDates()
	{
		// Set up the content object.
		$this->content->bind(
			array(
				'content_id'			=> 1,
				'state'					=> 1,
				'publish_start_date'	=> null,
				'publish_end_date'		=> null
			)
		);

		// Check that isVisible returns true as expected.
		$this->assertTrue($this->content->isVisible());
	}

	/**
	 * Method to test that a call to JContent::isVisible() will return the
	 * expected result based on the object's state.
	 *
	 * @return  void
	 *
	 * @since   11.4
	 */
	public function testIsVisibleWithDatabaseNullDates()
	{
		// Set up the content object.
		$this->content->bind(
			array(
				'content_id'			=> 1,
				'state'					=> 1,
				'publish_start_date'	=> '0000-00-00 00:00:00',
				'publish_end_date'		=> '0000-00-00 00:00:00',
			)
		);

		// Check that isVisible returns true as expected.
		$this->assertTrue($this->content->isVisible());
	}

	/**
	 * Method to test that a call to JContent::isVisible() will return the
	 * expected result based on the object's state.
	 *
	 * @return  void
	 *
	 * @since   11.4
	 */
	public function testIsVisibleWithDatesInRange()
	{
		// Set up the content object.
		$this->content->bind(
			array(
				'content_id'			=> 1,
				'state'					=> 1,
				'publish_start_date'	=> '2000-01-01 12:00:00',
				'publish_end_date'		=> '2099-01-01 12:00:00',
			)
		);

		// Check that isVisible returns true as expected.
		$this->assertTrue($this->content->isVisible());
	}

	/**
	 * Method to test that a call to JContent::isVisible() will return the
	 * expected result based on the object's state.
	 *
	 * @return  void
	 *
	 * @since   11.4
	 */
	public function testIsVisibleWithInactiveState()
	{
		// Set up the content object.
		$this->content->bind(
			array(
				'content_id'	=> 1,
				'state'			=> -1,
			)
		);

		// Check that isVisible returns false as expected.
		$this->assertFalse($this->content->isVisible());
	}

	/**
	 * Method to test that a call to JContent::isVisible() will return the
	 * expected result based on the object's state.
	 *
	 * @return  void
	 *
	 * @since   11.4
	 */
	public function testIsVisibleWithFutureStartDate()
	{
		// Set up the content object.
		$this->content->bind(
			array(
				'content_id'			=> 1,
				'state'					=> 1,
				'publish_start_date'	=> '2099-01-01 12:00:00',
				'publish_end_date'		=> '0000-00-00 00:00:00',
			)
		);

		// Check that isVisible returns false as expected.
		$this->assertFalse($this->content->isVisible());
	}

	/**
	 * Method to test that a call to JContent::isVisible() will return the
	 * expected result based on the object's state.
	 *
	 * @return  void
	 *
	 * @since   11.4
	 */
	public function testIsVisibleWithPastEndDate()
	{
		// Set up the content object.
		$this->content->bind(
			array(
				'content_id'			=> 1,
				'state'					=> 1,
				'publish_start_date'	=> '0000-00-00 00:00:00',
				'publish_end_date'		=> '2000-01-01 12:00:00',
			)
		);

		// Check that isVisible returns false as expected.
		$this->assertFalse($this->content->isVisible());
	}

	/**
	 * Method to test that JContent::canCheckout() works as expected.
	 *
	 * @return  void
	 *
	 * @since   11.4
	 */
	public function testCanCheckout()
	{
			// create fake sessions
		$session1 = $this->getMockSession(array('getId'=>'DEEDED01', 'get.user.id'=>1, 'get.user.guest'=>0));

		// create fake users and match them up
		$user1 = $session1->get('user');

		// Get a new content factory.
		$factory = new JContentFactory('TCPrefix', null, $this->getMockWeb(array('session'=>$session1)), $user1);

		// Get a new content object.
		$content = $factory->getContent('Inspector');

		// Create the content record.
		$content->create();

		// Get the content id.
		$contentId = $content->content_id;

		// set some values
		$content->bind(array('title'=>'Testing', 'temporary'=>0))->update();

		// check that item can be checked out
		$this->assertTrue($content->canCheckout(), 'Can checkout newly created item.');

		$content->checkout();

		// Get a new content object.
		$content = $factory->getContent('Inspector');

		// Re-load the content id.
		$content->load($contentId);

		// check that item can be re-checked out in my session
		$this->assertTrue($content->canCheckout(), 'Can checkout item that is already checked out to my session.');

		// check out in a new session using the same user
		$session2 = $this->getMockSession(array('getId'=>'CAFEBABE', 'get.user.id'=>1, 'get.user.guest'=>0));
		$user2 = $session2->get('user');
		$factory = new JContentFactory('TCPrefix', null, $this->getMockWeb(array('session'=>$session2)), $user2);
		$content2 = $factory->getContent('Inspector');
		$content2->load($contentId);

		$this->assertFalse($content2->canCheckout(), 'Cannot checkout item that is checked out to a different session of the same user.');
		$content2->checkout();

		// create a new session and try to checkout with that session (should fail)
		$session3 = $this->getMockSession(array('getId'=>'DEADBEEF', 'get.user.id'=>2, 'get.user.guest'=>0));
		$user3 = $session3->get('user');
		$factory = new JContentFactory('TCPrefix', null, $this->getMockWeb(array('session'=>$session3)), $user3);
		$content3 = $factory->getContent('Inspector');
		$content3->load($contentId);

		$this->assertFalse($content3->canCheckout(), 'Cannot checkout item to a different user.');

		// remove old session from database for other user account
		$db = JFactory::getDbo();
		$db->setQuery('DELETE FROM #__session WHERE username = "testuser"');
		$db->Query();

		// expire old session and try to checkout with new session (should work)
		$this->assertTrue($content3->canCheckout(), 'Can checkout an item checked out to a dead session');

		// byebye
		$content3->delete();
	}

	/**
	 * Method to test that JContent::canDelete() works as expected.
	 *
	 * @return  void
	 *
	 * @since   11.4
	 */
	public function testCanDelete()
	{
		// Set up the content object.
		$this->content->bind(array('content_id' => 1));

		// Create a guest user.
		$user = new JUser();
		$user->set('id', 1);
		$user->set('guest', false);

		// Push the guest user into the content object.
		ReflectionHelper::setValue($this->content, 'user', $user);

		// Assert that implicit deny works.
		$this->assertNull($this->content->canDelete());

		// Set the rule to approve.
		$this->content->rules = '{"delete":{"-1":1}}';

		// Assert that explicit approve works.
		$this->assertTrue($this->content->canDelete());

		// Set the rule to deny.
		$this->content->rules = '{"delete":{"-1":0}}';

		// Assert that explicit deny works.
		$this->assertFalse($this->content->canDelete());
	}

	/**
	 * Method to test that JContent::canDelete() works as expected with
	 * temporary content.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testCanDeleteTemporary()
	{
		// Get a new content factory.
		$factory = new JContentFactory('TCPrefix', null, $this->getMockWeb());

		// Create a new content object.
		$content = $factory->getContent('Inspector')->create();

		// Assert that temporary content can be deleted.
		$this->assertTrue($content->isTemporary());
		$this->assertTrue($content->canDelete());
	}

	/**
	 * Method to test that calling JContent::canDelete() will return false if
	 * a full check is requested and JContent::canCheckout() returns false.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testCanDeleteFailsWhenCanCheckoutFails()
	{
		// Get a mock content object.
		$content = JContentMock::create($this, 'Prefix', 'Type');

		// Setup canCheckout to return false.
		$content->expects($this->any())->method('canCheckout')->will($this->returnValue(false));

		// Assert that canUpdate fails when canCheckout returns false.
		$this->assertFalse($content->canDelete(true));
	}

	/**
	 * Method to test that JContent::canFeature() works as expected.
	 *
	 * @return  void
	 *
	 * @since   11.4
	 */
	public function testCanFeature()
	{
		// Set up the content object.
		$this->content->bind(array('content_id' => 1));

		// Create a guest user.
		$user = new JUser();
		$user->set('id', 1);
		$user->set('guest', false);

		// Push the guest user into the content object.
		ReflectionHelper::setValue($this->content, 'user', $user);

		// Assert that implicity deny works.
		$this->assertNull($this->content->canFeature());

		// Set the rule to approve.
		$this->content->rules = '{"feature":{"-1":1}}';

		// Assert that explicit approve works.
		$this->assertTrue($this->content->canFeature());

		// Set the rule to deny.
		$this->content->rules = '{"feature":{"-1":0}}';

		// Assert that explicit deny works.
		$this->assertFalse($this->content->canFeature());
	}

	/**
	 * Method to test that calling JContent::canFeature() will return false if
	 * a full check is requested and JContent::canCheckout() returns false.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testCanFeatureFailsWhenCanCheckoutFails()
	{
		// Get a mock content object.
		$content = JContentMock::create($this, 'Prefix', 'Type');

		// Setup canCheckout to return false.
		$content->expects($this->any())->method('canCheckout')->will($this->returnValue(false));

		// Assert that canUpdate fails when canCheckout returns false.
		$this->assertFalse($content->canFeature(true));
	}

	/**
	 * Method to test that guest users cannot like content via JContent::like().
	 * We check this because the like process logs the like to the user id and
	 * there is no way to track that an anonymous user liked a content and limit
	 * him/her to only one like per content item.
	 *
	 * @return  void
	 *
	 * @since   11.4
	 */
	public function testCanLikeWithGuestUser()
	{
		// Set up the content object.
		$this->content->bind(
			array(
				'content_id' => 1
			)
		);

		// Create a guest user.
		$user = new JUser();
		$user->set('guest', true);

		// Push the guest user into the content object.
		ReflectionHelper::setValue($this->content, 'user', $user);

		// Assert that guest users cannot like content.
		$this->assertFalse($this->content->canLike());
	}

	/**
	 * Method to test that logged in users can like content if the access
	 * rules authorize them.
	 *
	 * @return  void
	 *
	 * @since   11.4
	 */
	public function testCanLikeWithLoggedInUser()
	{
		// Set up the content object.
		$this->content->bind(
			array(
				'content_id' => 1
			)
		);

		// Create a guest user.
		$user = new JUser();
		$user->set('id', 1);
		$user->set('guest', false);

		// Push the guest user into the content object.
		ReflectionHelper::setValue($this->content, 'user', $user);

		// Assert that guest users cannot like content.
		$this->assertNull($this->content->canLike());
	}

	/**
	 * Method to test that JContent::canUpdate() works as expected.
	 *
	 * @return  void
	 *
	 * @since   11.4
	 */
	public function testCanUpdate()
	{
		// Set up the content object.
		$this->content->bind(array('content_id' => 1));

		// Create a guest user.
		$user = new JUser();
		$user->set('id', 1);
		$user->set('guest', false);

		// Push the guest user into the content object.
		ReflectionHelper::setValue($this->content, 'user', $user);

		// Assert that implicit deny works.
		$this->assertNull($this->content->canUpdate());

		// Set the rule to approve.
		$this->content->rules = '{"update":{"-1":1}}';

		// Assert that explicit approve works.
		$this->assertTrue($this->content->canUpdate());

		// Set the rule to deny.
		$this->content->rules = '{"update":{"-1":0}}';

		// Assert that explicit deny works.
		$this->assertFalse($this->content->canUpdate());
	}

	/**
	 * Method to test that calling JContent::canUpdate() will return false if
	 * a full check is requested and JContent::canCheckout() returns false.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testCanUpdateFailsWhenCanCheckoutFails()
	{
		// Get a mock content object.
		$content = JContentMock::create($this, 'Prefix', 'Type');

		// Setup canCheckout to return false.
		$content->expects($this->any())->method('canCheckout')->will($this->returnValue(false));

		// Assert that canUpdate fails when canCheckout returns false.
		$this->assertFalse($content->canUpdate(true));
	}

	/**
	 * Method to test that JContent::canView() works as expected.
	 *
	 * @return  void
	 *
	 * @since   11.4
	 */
	public function testCanView()
	{
		// Get a content object.
		$content = $this->getOriginal();

		// Assert that implicity deny works.
		$this->assertNull($content->canView());

		// Get the content user.
		$user = ReflectionHelper::getValue($content, 'user');

		// Set the user to have access levels 1, 3, 9.
		ReflectionHelper::setValue($user, '_authLevels', array(1, 3, 9));

		// Set the content user.
		ReflectionHelper::setValue($content, 'user', $user);

		// Set the content access level to 5.
		$content->access = 3;

		// Assert that the explicit deny works.
		$this->assertTrue($content->canView());

		// Set the content access level to 5.
		$content->access = 5;

		// Assert that the explicit deny works.
		$this->assertFalse($content->canView());
	}

	/**
	 * Method to test that JContent::isLiked() works as expected.
	 *
	 * @return  void
	 *
	 * @since   11.4
	 */
	public function testIsLiked()
	{
		$original = $this->getOriginal(2, 2);

		$this->assertThat(
			$original->isLiked(),
			$this->isTrue(),
			'Check a positive case.'
		);

		$original = $this->getOriginal(2, 1);

		$this->assertThat(
			$original->isLiked(),
			$this->isFalse(),
			'Check a negative case.'
		);
	}

	/**
	 * Method to test that JContent::like() works as expected.
	 *
	 * @return  void
	 *
	 * @since   11.4
	 */
	public function testLike()
	{
		$original = $this->getOriginal();

		$this->assertThat(
			$original->like(),
			$this->identicalTo($original),
			'Checks chaining.'
		);

		self::$dbo->setQuery('SELECT * FROM #__content_likes WHERE content_id = 1 AND user_id = 1');
		$r = self::$dbo->loadResultArray();

		$this->assertThat(
			count($r),
			$this->equalTo(1),
			'Check the like was added to the content_likes table.'
		);

		self::$dbo->setQuery('SELECT `likes` FROM #__content WHERE content_id = 1');
		$r = self::$dbo->loadResult();

		$this->assertThat(
			$r,
			$this->equalTo(1),
			'Check the likes incremented in the content table.'
		);

		$this->assertThat(
			$original->likes,
			$this->equalTo(1),
			'Check the likes incremented in the original object.'
		);
	}

	/**
	 * Method to test that JContent::unlike() works as expected.
	 *
	 * @return  void
	 *
	 * @since   11.4
	 */
	public function testUnlike()
	{
		$original = $this->getOriginal(2, 2);

		$this->assertThat(
			$original->unlike(),
			$this->identicalTo($original),
			'Checks chaining.'
		);

		self::$dbo->setQuery('SELECT * FROM #__content_likes WHERE content_id = 2 AND user_id = 2');
		$r = self::$dbo->loadResultArray();

		$this->assertThat(
			count($r),
			$this->equalTo(0),
			'Check the like was dropped from the content_likes table.'
		);

		self::$dbo->setQuery('SELECT `likes` FROM #__content WHERE content_id = 2');
		$r = self::$dbo->loadResult();

		$this->assertThat(
			$r,
			$this->equalTo(3),
			'Check the likes decremented in the content table.'
		);

		$this->assertThat(
			$original->likes,
			$this->equalTo(3),
			'Check the likes decremented in the original object.'
		);
	}

	/**
	 * Method to test that JContent::__toString() works as expected.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testToString()
	{
		// Get a content object.
		$content = $this->getOriginal();

		// Set the update rule to deny.
		$content->rules = '{"update":{"-1":0}}';

		// Convert the content to a string.
		$string = (string)$content;
		$object = json_decode($string);

		// Assert that the string can be decoded.
		$this->assertInstanceOf('stdClass', $object);

		// Check that the sensitive properties have been removed.
		$this->assertObjectHasAttribute('content_id', $object);
		$this->assertObjectHasAttribute('title', $object);
		$this->assertObjectNotHasAttribute('access', $object);
		$this->assertObjectNotHasAttribute('created_user_id', $object);
		$this->assertObjectNotHasAttribute('rules', $object);

		// Set the update rule to approve.
		$content->rules = '{"update":{"-1":1}}';

		// Convert the content to a string.
		$string = (string)$content;
		$object = json_decode($string);

		// Assert that the string can be decoded.
		$this->assertInstanceOf('stdClass', $object);

		// Check that the sensitive properties have been included.
		$this->assertObjectHasAttribute('content_id', $object);
		$this->assertObjectHasAttribute('title', $object);
		$this->assertObjectHasAttribute('access', $object);
		$this->assertObjectHasAttribute('created_user_id', $object);
		$this->assertObjectHasAttribute('rules', $object);
	}

	/**
	 * Method to test that the property cache is working as expected. A property
	 * getter should only be called one time regardless of how many times the
	 * property is accessed.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testPropertyCache()
	{
		// Get a new content factory.
		$factory = new JContentFactory('TCPrefix', null, $this->getMockWeb());

		// Create a new content object.
		$content = $factory->getContent('Inspector');

		// Assert that multiple uses of a property does not call the getter multiple times.
		$this->assertEquals($content->cache_test, $content->cache_test);
	}

	/**
	 * Tests the JContentType::getRules() method.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testGetRules()
	{
		ReflectionHelper::setValue($this->content, 'properties', array('rules' => '{"foo":"bar"}'));

		$this->assertThat(
			$this->content->rules,
			$this->equalTo(array('foo' => 'bar'))
		);
	}

	/**
	 * Tests the JContentType::setRules() method.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testSetRules()
	{
		$this->content->rules = 'string';
		$properties = ReflectionHelper::getValue($this->content, 'properties');

		$this->assertThat(
			$properties['rules'],
			$this->equalTo('string'),
			'Checks that a string is set.'
		);

		$this->content->rules = array('foo' => 'bar');

		$properties = ReflectionHelper::getValue($this->content, 'properties');

		$this->assertThat(
			$properties['rules'],
			$this->equalTo('{"foo":"bar"}'),
			'Checks that array input is converted to a JSON string.'
		);
	}
}
