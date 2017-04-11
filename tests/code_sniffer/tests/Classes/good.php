<?php
/**
 * Joomla! Coding Standards checker.
 *
 * This file contains all the valid notations for the Joomla! coding standard.
 * Target is to create a style checker that validates all of this constructs.
 *
 * @package    Joomla.Platform
 *
 * @copyright  Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

/* Standard: Opening brace of a class must be on the line after the definition.
 (PEAR.Classes.ClassDeclaration.OpenBraceNewLine){HL:8}
*/
/**
 * Comment
 *
 * @package  Foo
 * @since    foo
 */
class Foo
{
}
/* Standard: When calling class constructors with no arguments, do not include parentheses.
 (Joomla.Classes.InstantiateNewClasses.New class)
*/
$foo = new Foo;
/* Standard: Check for spaces between classname and opening parenthesis
 (Joomla.Functions.FunctionCallSignature.SpaceBeforeOpenBracket)
*/
$foo = new Foo($i);
/* Standard: Check argument formating
 (Generic.Functions.FunctionCallArgumentSpacing.NoSpaceAfterComma)
*/
$foo = new Foo($i, $i);
/* Standard: Usage of "$this" in static methods will cause runtime errors
 (Joomla.Classes.StaticThisUsage.Found){HL:16}
*/
/**
 * Comment
 *
 * @package  Foo
 * @since    foo
 */
class Foo
{
	/**
	 * Comment
	 *
	 * @return foo
	 */
	public static function bar()
	{
		echo self::$baz;
	}
}
