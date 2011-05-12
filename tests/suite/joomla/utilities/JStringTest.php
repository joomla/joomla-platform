<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Utilities
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

require_once JPATH_PLATFORM.'/joomla/utilities/string.php';
require_once 'TestHelpers/JString-helper-dataset.php';

/**
 * Test class for JString.
 * Generated by PHPUnit on 2009-10-26 at 22:29:34.
 */
class JStringTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var JString
	 */
	protected $object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp()
	{

	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown()
	{
	}

	static public function strposData() {
		return JStringTest_DataSet::$strposTests;
	}

	static public function strrposData() {
		return JStringTest_DataSet::$strrposTests;
	}

	static public function substrData() {
		return JStringTest_DataSet::$substrTests;
	}

	static public function strtolowerData() {
		return JStringTest_DataSet::$strtolowerTests;
	}

	static public function strtoupperData() {
		return JStringTest_DataSet::$strtoupperTests;
	}

	static public function strlenData() {
		return JStringTest_DataSet::$strlenTests;
	}

	static public function str_ireplaceData() {
		return JStringTest_DataSet::$str_ireplaceTests;
	}

	static public function str_splitData() {
		return JStringTest_DataSet::$str_splitTests;
	}

	static public function strcasecmpData() {
		return JStringTest_DataSet::$strcasecmpTests;
	}

	static public function strcmpData() {
		return JStringTest_DataSet::$strcmpTests;
	}

	static public function strcspnData() {
		return JStringTest_DataSet::$strcspnTests;
	}

	static public function stristrData() {
		return JStringTest_DataSet::$stristrTests;
	}

	static public function strrevData() {
		return JStringTest_DataSet::$strrevTests;
	}

	static public function strspnData() {
		return JStringTest_DataSet::$strspnTests;
	}

	static public function substr_replaceData() {
		return JStringTest_DataSet::$substr_replaceTests;
	}

	static public function ltrimData() {
		return JStringTest_DataSet::$ltrimTests;
	}

	static public function rtrimData() {
		return JStringTest_DataSet::$rtrimTests;
	}

	static public function trimData() {
		return JStringTest_DataSet::$trimTests;
	}

	static public function ucfirstData() {
		return JStringTest_DataSet::$ucfirstTests;
	}

	static public function ucwordsData() {
		return JStringTest_DataSet::$ucwordsTests;
	}

	static public function transcodeData() {
		return JStringTest_DataSet::$transcodeTests;
	}

	static public function validData() {
		return JStringTest_DataSet::$validTests;
	}

	/**
	 * @group String
	 * @covers JString::strpos
	 * @dataProvider strposData
	 */
	public function testStrpos($haystack, $needle, $offset = 0, $expect)
	{
		$actual = JString::strpos($haystack, $needle, $offset);
		$this->assertEquals($expect, $actual);
	}

	/**
	 * @group String
	 * @covers JString::strrpos
	 * @dataProvider strrposData
	 */
	public function testStrrpos($haystack, $needle, $offset = 0, $expect)
	{
		$actual = JString::strrpos($haystack, $needle, $offset);
		$this->assertEquals($expect, $actual);
	}


	/**
	 * @group String
	 * @covers JString::substr
	 * @dataProvider substrData
	 */
	public function testSubstr($string, $start, $length = false, $expect)
	{
		$actual = JString::substr($string, $start, $length);
		$this->assertEquals($expect, $actual);
	}

	/**
	 * @group String
	 * @covers JString::strtolower
	 * @dataProvider strtolowerData
	 */
	public function testStrtolower($string, $expect)
	{
		$actual = JString::strtolower($string);
		$this->assertEquals($expect, $actual);
	}

	/**
	 * @group String
	 * @covers JString::strtoupper
	 * @dataProvider strtoupperData
	 */
	public function testStrtoupper($string, $expect)
	{
		$actual = JString::strtoupper($string);
		$this->assertEquals($expect, $actual);
	}

	/**
	 * @group String
	 * @covers JString::strlen
	 * @dataProvider strlenData
	 */
	public function testStrlen($string, $expect)
	{
		$actual = JString::strlen($string);
		$this->assertEquals($expect, $actual);
	}

	/**
	 * @group String
	 * @covers JString::str_ireplace
	 * @dataProvider str_ireplaceData
	 */
	public function testStr_ireplace($search, $replace, $subject, $count, $expect)
	{
		$actual = JString::str_ireplace($search, $replace, $subject, $count);
		$this->assertEquals($expect, $actual);
	}

	/**
	 * @group String
	 * @covers JString::str_split
	 * @dataProvider str_splitData
	 */
	public function testStr_split($string, $split_length, $expect)
	{
		$actual = JString::str_split($string, $split_length);
		$this->assertEquals($expect, $actual);
	}

	/**
	 * @group String
	 * @covers JString::strcasecmp
	 * @dataProvider strcasecmpData
	 */
	public function testStrcasecmp($string1, $string2, $locale, $expect)
	{
		if (substr(php_uname(), 0, 6) != 'Darwin') {
			$actual = JString::strcasecmp ($string1, $string2, $locale);
			if ($actual != 0) {
				$actual = $actual/abs($actual);
			}
			$this->assertEquals($expect, $actual);
		}
	}

	/**
	 * @group String
	 * @covers JString::strcmp
	 * @dataProvider strcmpData
	 */
	public function testStrcmp($string1, $string2, $locale, $expect)
	{
		$actual = JString::strcmp ($string1, $string2, $locale);
		if ($actual != 0) {
			$actual = $actual/abs($actual);
		}
		$this->assertEquals($expect, $actual);
	}

	/**
	 * @group String
	 * @covers JString::strcspn
	 * @dataProvider strcspnData
	 */
	public function testStrcspn($haystack, $needles, $start, $len, $expect)
	{
		$actual = JString::strcspn ($haystack, $needles, $start, $len);
		$this->assertEquals($expect, $actual);
	}

	/**
	 * @group String
	 * @covers JString::stristr
	 * @dataProvider stristrData
	 */
	public function testStristr($haystack, $needle, $expect)
	{
		$actual = JString::stristr ($haystack, $needle);
		$this->assertEquals($expect, $actual);
	}

	/**
	 * @group String
	 * @covers JString::strrev
	 * @dataProvider strrevData
	 */
	public function testStrrev($string, $expect)
	{
		$actual = JString::strrev ($string);
		$this->assertEquals($expect, $actual);
	}

	/**
	 * @group String
	 * @covers JString::strspn
	 * @dataProvider strspnData
	 */
	public function testStrspn($subject, $mask, $start, $length, $expect)
	{
		$actual = JString::strspn ($subject, $mask, $start, $length);
		$this->assertEquals($expect, $actual);
	}

	/**
	 * @group String
	 * @covers JString::substr_replace
	 * @dataProvider substr_replaceData
	 */
	public function testSubstr_replace($string, $replacement, $start, $length, $expect)
	{
		$actual = JString::substr_replace ($string, $replacement, $start, $length);
		$this->assertEquals($expect, $actual);
	}

	/**
	 * @group String
	 * @covers JString::ltrim
	 * @dataProvider ltrimData
	 */
	public function testLtrim($string, $charlist, $expect)
	{
		if ($charlist === null) {
			$actual = JString::ltrim ($string);
		}
		else {
			$actual = JString::ltrim ($string, $charlist);
		}
		$this->assertEquals($expect, $actual);
	}

	/**
	 * @group String
	 * @covers JString::rtrim
	 * @dataProvider rtrimData
	 */
	public function testRtrim($string, $charlist, $expect)
	{
		if ($charlist === null) {
			$actual = JString::rtrim ($string);
		}
		else {
			$actual = JString::rtrim ($string, $charlist);
		}
		$this->assertEquals($expect, $actual);
	}

	/**
	 * @group String
	 * @covers JString::trim
	 * @dataProvider trimData
	 */
	public function testTrim($string, $charlist, $expect)
	{
		if ($charlist === null) {
			$actual = JString::trim ($string);
		}
		else {
			$actual = JString::trim ($string, $charlist);
		}
		$this->assertEquals($expect, $actual);
	}

	/**
	 * @group String
	 * @covers JString::ucfirst
	 * @dataProvider ucfirstData
	 */
	public function testUcfirst($string, $expect)
	{
		$actual = JString::ucfirst ($string);
		$this->assertEquals($expect, $actual);
	}

	/**
	 * @group String
	 * @covers JString::ucwords
	 * @dataProvider ucwordsData
	 */
	public function testUcwords($string, $expect)
	{
		$actual = JString::ucwords ($string);
		$this->assertEquals($expect, $actual);
	}

	/**
	 * @group String
	 * @covers JString::transcode
	 * @dataProvider transcodeData
	 */
	public function testTranscode($source, $from_encoding, $to_encoding, $expect)
	{
		$actual = JString::transcode ($source, $from_encoding, $to_encoding);
		$this->assertEquals($expect, $actual);
	}

	/**
	 * @group String
	 * @covers JString::valid
	 * @dataProvider validData
	 */
	public function testValid($string, $expect)
	{
		$actual = JString::valid ($string);
		$this->assertEquals($expect, $actual);
	}

	/**
	 * @group String
	 * @covers JString::compliant
	 * @dataProvider validData
	 */
	public function testCompliant($string, $expect)
	{
		$actual = JString::compliant ($string);
		$this->assertEquals($expect, $actual);
	}

	/**
	 * @group String
	 * @covers JString::parse_url
	 */
	public function testParse_Url() {
		$url = 'http://localhost/joomla_development/j16_trunk/administrator/index.php?option=com_contact&view=contact&layout=edit&id=5';
		$expected = parse_url($url);
		$actual = JString::parse_url($url);
		$this->assertEquals($expected, $actual, 'Line: ' . __LINE__ . ' Results should be equal');

		$url = 'http://joomla.org/mytestpath/È';
		$expected = parse_url($url);
		// Fix up path for UTF-8 characters
		$expected['path'] = '/mytestpath/È';
		$actual = JString::parse_url($url);
		$this->assertEquals($expected, $actual, 'Line: ' . __LINE__ . ' Results should be equal');

		// Test special characters in URL
		$url = 'http://mydomain.com/!*\'();:@&=+$,/?%#[]';
		$expected = parse_url($url);
		$actual = JString::parse_url($url);
		$this->assertEquals($expected, $actual, 'Line: ' . __LINE__ . ' Results should be equal');

	}
}
?>
