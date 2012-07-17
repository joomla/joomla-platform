<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Html
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

require_once JPATH_PLATFORM . '/joomla/html/html/string.php';

/**
 * Tests for JDate class.
 *
 * @package     Joomla.UnitTest
 * @subpackage  Html
 * @since       11.3
 */
class JHtmlStringTest extends PHPUnit_Framework_TestCase
{
	/**
	 * Test cases for truncate.
	 *
	 * @return  array
	 *
	 * @since   11.3
	 */
	function getTestAbridgeData()
	{
		return array(
			'No change case' => array(
				'Plain text',
				50,
				30,
				'Plain text',
			),
			'Normal case' => array(
				'Abridges text strings over the specified character limit. The behavior will insert an ellipsis into the text.',
				50,
				30,
				'Abridges text strings over the...is into the text.',
			),
		);
	}

	/**
	 * Test cases for truncate.
	 *
	 * @return  array
	 *
	 * @since   11.3
	 */
	function getTestTruncateData()
	{
		return array(
			'No change case' => array(
				'Plain text',
				0,
				true,
				true,
				'Plain text',
			),
			'Plain text under the limit' => array(
				'Plain text',
				100,
				true,
				true,
				'Plain text',
			),
			'Plain text at the limit' => array(
				'Plain text',
				10,
				true,
				true,
				'Plain text',
			),
			'Plain text over the limit by two words' => array(
				'Plain text test',
				7,
				true,
				true,
				'Plain...',
			),
			'Plain text over the limit by one word' => array(
				'Plain text test',
				14,
				true,
				true,
				'Plain text...',
			),
			'Plain text over the limit with short trailing words' => array(
				'Plain text a b c d',
				13,
				true,
				true,
				'Plain text...',
			),
			'Plain text over the limit splitting first word' => array(
				'Plain text',
				3,
				false,
				true,
				'Pla...',
			),
			'Plain text with word split' => array(
				'Plain split-less',
				7,
				false,
				false,
				'Plain s...',
			),
			'Plain html under the limit' => array(
				'<span>Plain text</span>',
				100,
				true,
				true,
				'<span>Plain text</span>',
			),
			'Plain html at the limit' => array(
				'<span>Plain text</span>',
				23,
				true,
				true,
				'<span>Plain text</span>',
			),
			'Plain html over the limit' => array(
				'<span>Plain text</span>',
				22,
				true,
				true,
				'<span>Plain text</span>...',
			),
			// The tags by themselves make the string too long.
			'Plain html over the limit by one word' => array(
				'<span>Plain text</span>',
				12,
				true,
				true,
				'...',
			),
			// Don't return invalid HTML
			'Plain html over the limit splitting first word' => array(
				'<span>Plain text</span>',
				1,
				false,
				true,
				'...',
			),
			// Don't return invalid HTML
			'Plain html over the limit splitting first word' => array(
				'<span>Plain text</span>',
				4,
				false,
				true,
				'...',
			),
			'Complex html over the limit' => array(
				'<div><span><i>Plain</i> <b>text</b> foo</span></div>',
				37,
				true,
				true,
				'<div><span><i>Plain</i> <b>text</b></span></div>...',
			),
			'Complex html over the limit 2' => array(
				'<div><span><i>Plain</i> <b>text</b> foo</span></div>',
				38,
				true,
				true,
				'<div><span><i>Plain</i> <b>text</b></span></div>...',
			),
			'HTML not allowed, split words' => array(
				'<div><span><i>Plain</i> <b>text</b> foo</span></div>',
				8,
				false,
				false,
				'Plain te...',
			),
			'HTML not allowed, no split' => array(
				'<div><span><i>Plain</i> <b>text</b> foo</span></div>',
				4,
				true,
				false,
				'...',
			),
				'First character is < with a maximum length of 1' => array(
				'<div><span><i>Plain</i> <b>text</b> foo</span></div>',
				1,
				true,
				false,
				'...',
			),
			'HTML not allowed, no split' => array(
				'<div><span><i>Plain</i> <b>text</b> foo</span></div>',
				5,
				true,
				false,
				'...',
			),
			'Text is the same as maxLength, no split, HTML allowed' => array(
				'<div><span><i>Plain</i></span></div>',
				5,
				true,
				true.
				'...',
			),
			'HTML not allowed, no split' => array(
				'<div><span><i>Plain</i></span></div>',
				5,
				true,
				false,
				'Plain',
			),
		);
	}
	/**
	 * Test cases for complex truncate.
	 *
	 * @return  array
	 *
	 * @since   12.2
	 */
	function getTestTruncateComplexData()
	{
		return array(
			'No change case' => array(
				'Plain text',
				10,
				true,
				'Plain text'
			),
			'Plain text under the limit' => array(
				'Plain text',
				100,
				true,
				'Plain text'
			),
			'Plain text at the limit' => array(
				'Plain text',
				10,
				true,
				'Plain text',
			),
			'Plain text over the limit by two words' => array(
				'Plain text test',
				6,
				true,
				'...',
			),
			'Plain text over the limit by one word' => array(
				'Plain text test',
				13,
				true,
				'Plain text...',
			),
			'Plain text over the limit with short trailing words' => array(
				'Plain text a b c d',
				13,
				true,
				'Plain text...',
			),
			'Plain text over the limit splitting first word' => array(
				'Plain text',
				3,
				false,
				'Pla...',
			),
			'Plain text with word split' => array(
				'Plain split-less',
				7,
				true,
				'Plain...',
			),
			'Plain html under the limit' => array(
				'<span>Plain text</span>',
				100,
				true,
				'<span>Plain text</span>',
			),
			'Plain html at the limit' => array(
				'<span>Plain text</span>',
				23,
				true,
				'<span>Plain text</span>',
			),
			'Plain html over the limit' => array(
				'<span>Plain text</span>',
				22,
				true,
				'<span>Plain text</span>',
			),
				
			'Plain html over the limit by one word' => array(
				'<span>Plain text</span>',
				8,
				true,
				'<span>Plain</span>...',
			),
			'Plain html over the limit splitting first word' => array(
				'<span>Plain text</span>',
				4,
				false,
				'<span>Plai</span>...',
			),
			'Plain html over the limit splitting first word' => array(
				'<span>Plain text</span>',
				1,
				false,
				'<span>P</span>',
			),			
			'Plain html over the limit splitting first word' => array(
				'<span>Plain text</span>',
				4,
				false,
				'<span>Plai</span>...',
			),	
			'Complex html over the limit' => array(
				'<div><span><i>Plain</i> <b>text</b> foo</span></div>',
				37,
				true,
				'<div><span><i>Plain</i> <b>text</b> foo</span></div>',
			),
			'Complex html over the limit 2' => array(
				'<div><span><i>Plain</i> <b>text</b> foo</span></div>',
				38,
				true,
				'<div><span><i>Plain</i> <b>text</b> foo</span></div>',
			),
			'Split words' => array(
				'<div><span><i>Plain</i> <b>text</b> foo</span></div>',
				8,
				false,
				'<div><span><i>Plain</i> <b>te</b></span></div>...',
			),
			'No split' => array(
				'<div><span><i>Plain</i> <b>text</b> foo</span></div>',
				8,
				true,
				'<div><span><i>Plain</i> </span></div>...',
			),
				'First character is < with a maximum length of 1, no split' => array(
				'<div><span><i>Plain</i> <b>text</b> foo</span></div>',
				1,
				true,
				'<div></div>...',
			),
				'First character is < with a maximum length of 1, split' => array(
				'<div><span><i>Plain</i> <b>text</b> foo</span></div>',
				1,
				false,
				'<div>P</div>...',
			),
				'Text is the same as maxLength, no split' => array(
				'<div><span><i>Plain</i></span></div>',
				5,
				true,
				'<div><span><i>Plain</i></span></div>',
			),
		);
	}

	/**
	 * Tests the JHtmlString::abridge method.
	 *
	 * @param   string   $text       The text to truncate.
	 * @param   integer  $length     The maximum length of the text.
	 * @param   integer  $intro      The maximum length of the intro text.
	 * @param   string   $expected   The expected result.
	 *
	 * @return  void
	 *
	 * @dataProvider  getTestAbridgeData
	 * @since   11.3
	 */
	public function testAbridge($text, $length, $intro, $expected)
	{
		$this->assertThat(
			JHtmlString::abridge($text, $length, $intro),
			$this->equalTo($expected)
		);
	}

	/**
	 * Tests the JHtmlString::truncate method.
	 *
	 * @param   string   $text      The text to truncate.
	 * @param   integer  $length    The maximum length of the text.
	 * @param   boolean  $noSplit    Don't split a word if that is where the cutoff occurs (default: true).
	 * @param   boolean  $allowHtml  Allow HTML tags in the output, and close any open tags (default: true).
	 * @param   string   $expected   The expected result.
	 *
	 * @return  void
	 *
	 * @dataProvider  getTestTruncateData
	 * @since   11.3
	 */
	public function testTruncate($text, $length, $noSplit, $allowedHtml, $expected)
	{
		$this->assertThat(
			JHtmlString::truncate($text, $length, $noSplit, $allowedHtml),
			$this->equalTo($expected)
		);
	}

	/**
	 * Tests the JHtmlString::truncateComplex method.
	 *
	 * @param   string   $text      The text to truncate.
	 * @param   integer  $length    The maximum length of the text.
	 * @param   boolean  $noSplit    Don't split a word if that is where the cutoff occurs (default: true).
	 * @param   string   $expected   The expected result.
	 *
	 * @return  void
	 *
	 * @dataProvider  getTestTruncateComplexData
	 * @since   12.2
	 */
	public function testTruncateComplex($html, $maxLength, $noSplit, $expected)
	{
		$this->assertThat(
			JHtmlString::truncateComplex($html, $maxLength, $noSplit),
			$this->equalTo($expected)
		);
	}
}
