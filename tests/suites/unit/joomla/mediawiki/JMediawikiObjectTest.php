<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Client
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

require_once JPATH_PLATFORM . '/joomla/mediawiki/http.php';
require_once JPATH_PLATFORM . '/joomla/mediawiki/object.php';
require_once __DIR__ . '/stubs/JGithubObjectMock.php';

/**
 * Test class for JGithub.
 */
class JMediawikiObjectTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var JMediawikiObject
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new JMediawikiObject;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers JMediawikiObject::buildParameter
     * @todo   Implement testBuildParameter().
     */
    public function testBuildParameter()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers JMediawikiObject::validateResponse
     * @todo   Implement testValidateResponse().
     */
    public function testValidateResponse()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }
}
