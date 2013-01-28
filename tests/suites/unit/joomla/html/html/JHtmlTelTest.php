<?php
/**
 * @package		 Joomla.UnitTest
 * @subpackage	HTML
 *
 * @copyright	 Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		 GNU General Public License version 2 or later; see LICENSE
 */

require_once JPATH_PLATFORM . '/joomla/html/tel.php';

/**
 * Test class for JHtmlTel.
 *
 * @package		 Joomla.UnitTest
 * @subpackage	Html
 * @since			 11.3
 */
class JHtmlTelTest extends TestCase
{
	/**
	 * @var		JHtmlTel
	 * @since	11.3
	 */
	protected $object;

	/**
	 * Tests the JHtmlTel::tel method.
	 *
	 * @return	void
	 *
	 * @since	 11.3
	 */
	public function testTel()
	{
		$this->assertThat(
			JHtmlTel::tel('1.9419555555', 'US'),
			$this->StringContains('(941) 955-5555'),
			'Testing for US format'
		);

		$this->assertThat(
			JHtmlTel::tel('49.15123456789', 'EPP'),
			$this->StringContains('+49.15123456789'),
			'Testing for EPP format'
		);

		$this->assertThat(
			JHtmlTel::tel('82.12345678', 'ITU-T'),
			$this->StringContains('+82 12 34 56 78'),
			'Testing for ITU-T format'
		);

		$this->assertThat(
			JHtmlTel::tel('1.9413216789', 'ARPA'),
			$this->StringContains('+9.8.7.6.1.2.3.1.4.9.1.e164.arpa'),
			'Testing for ARPA format'
		);
	}
}
