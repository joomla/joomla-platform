<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Registry
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

require_once JPATH_PLATFORM.'/joomla/registry/format.php';

/**
 * Test class for JRegistryFormatINI.
 * Generated by PHPUnit on 2009-10-27 at 15:13:57.
 */
class JRegistryFormatINITest extends PHPUnit_Framework_TestCase
{
	/**
	 * Test the JRegistryFormatINI::objectToString method.
	 */
	public function testObjectToString()
	{
		$class = JRegistryFormat::getInstance('INI');
		$options = null;
		$object = new stdClass;
		$object->foo = 'bar';
		$object->booleantrue = true;
		$object->booleanfalse = false;
		$object->numericint = 42;
		$object->numericfloat = 3.1415;
		$object->section = new stdClass();
		$object->section->key = 'value';

		// Test basic object to string.
		$string = $class->objectToString($object, $options);
		$this->assertThat(
			trim($string),
			$this->equalTo("foo=\"bar\"\nbooleantrue=true\nbooleanfalse=false\nnumericint=42\nnumericfloat=3.1415\n\n[section]\nkey=\"value\"")
		);
	}

	/**
	 * Test the JRegistryFormatINI::stringToObject method.
	 */
	public function testStringToObject()
	{
		$class = JRegistryFormat::getInstance('INI');

		$string2 = "[section]\nfoo=bar";

		$object1 = new stdClass;
		$object1->foo = 'bar';

		$object2 = new stdClass;
		$object2->section = $object1;

		// Test INI format string without sections.
		$object = $class->stringToObject($string2, false);
		$this->assertThat(
			$object,
			$this->equalTo($object1)
		);

		// Test INI format string with sections.
		$object = $class->stringToObject($string2, true);
		$this->assertThat(
			$object,
			$this->equalTo($object2)
		);

		//Test empty string
		$this->assertThat(
			$class->stringToObject(null),
			$this->equalTo(new stdClass())
		);
		
		$string3 = "[section]\nfoo=bar\n;Testcomment\nkey=value\n\n/brokenkey=)brokenvalue";
		$object2->section->key = 'value';
		
		$this->assertThat(
			$class->stringToObject($string3, true),
			$this->equalTo($object2)
		);
		
		$string4 = "boolfalse=false\nbooltrue=true\nkeywithoutvalue\nnumericfloat=3.1415\nnumericint=42\nkey=\"value\"";
		$object3 = new stdClass();
		$object3->boolfalse = false;
		$object3->booltrue = true;
		$object3->numericfloat = 3.1415;
		$object3->numericint = 42;
		$object3->key = 'value';
		
		$this->assertThat(
			$class->stringToObject($string4),
			$this->equalTo($object3)
		);
		
		//Trigger the cache - Doing this only to achieve 100% code coverage. ;-)
		$this->assertThat(
			$class->stringToObject($string4),
			$this->equalTo($object3)
		);
	}
}
