<?php
/**
 * @package     Joomla.UnitTest
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * Test class for import.php.
 *
 * @package	 Joomla.UnitTest
 * @since    12.2
 */
class JImportTest extends PHPUnit_Framework_TestCase
{
	/**
	 * Tests the error handler when throwing an ErrorException.
	 *
	 * @return  void
	 *
	 * @expectedException ErrorException
	 *
	 * @since   12.2
	 */
	public function testErrorException()
	{
		$a = $b;
	}

	/**
	 * Tests the error handler when being silent.
	 *
	 * @return  void
	 *
	 * @since   12.2
	 */
	public function testErrorControlOperator()
	{
		ob_start();
		$a = @$b;
		$out = ob_get_clean();
		$this->assertThat(
			$out,
			$this->equalTo('')
		);
	}
}
