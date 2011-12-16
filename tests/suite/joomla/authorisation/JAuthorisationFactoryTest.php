<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Authorisation
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * @package     Joomla.UnitTest
 * @subpackage  Authorisation
 * @since       12.1
 */
class JAuthorisationFactoryTest extends JoomlaTestCase
{
	/**
	 * Tests the JAuthorisationFactory::getInstance method.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testGetAuthoriser()
	{
		$factory = new JAuthorisationFactory;

		$this->assertThat(
			$factory->getAuthoriser(),
			$this->isInstanceOf('JAuthorisationAuthoriserDefault'),
			'Checks that the default authoriser is loaded.'
		);

		$this->assertThat(
			$factory->getAuthoriser('root'),
			$this->isInstanceOf('JAuthorisationAuthoriserRoot'),
			'Checks that a specific authoriser is loaded.'
		);
	}

	/**
	 * Tests the JAuthorisationFactory::getInstance method.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 * @expectedException  UnexpectedValueException
	 */
	public function testGetAuthoriserException()
	{
		$factory = new JAuthorisationFactory;
		$factory->getAuthoriser('foo');
	}

	/**
	 * Tests the JAuthorisationFactory::getInstance method.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testGetInstance()
	{
		$factory = JAuthorisationFactory::getInstance();

		$this->assertThat(
			$factory,
			$this->isInstanceOf('JAuthorisationFactory')
		);
	}

	/**
	 * Tests the JAuthorisationFactory::setInstance method.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testSetInstance()
	{
		$mock = $this->getMock('JAuthorisationFactory', array('foo'));
		$mock->expects($this->any())
			->method('foo')
			->will($this->returnValue('bar'));

		JAuthorisationFactory::setInstance($mock);

		$factory = JAuthorisationFactory::getInstance();

		$this->assertThat(
			$factory,
			$this->isInstanceOf('JAuthorisationFactory'),
			'Checks object type.'
		);

		$this->assertThat(
			$factory->foo(),
			$this->equalTo('bar'),
			'Checks object integrity.'
		);
	}

	/**
	 * Tests the JAuthorisationFactory::setInstance method for an expected exception.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testSetInstanceException()
	{
		$this->setExpectedException('PHPUnit_Framework_Error');

		$factory = JAuthorisationFactory::setInstance('bla');
	}
}
