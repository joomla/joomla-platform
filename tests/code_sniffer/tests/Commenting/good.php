<?php
// @codingStandardsIgnoreStart
// We have to ignore the standards here, because they require to
// begin a file with a file comment..

/* Standard: You must use "/**" style comments for a file comment
 (Joomla.Commenting.FileComment.WrongStyle)
*/
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

/* ENDDOC */

// @codingStandardsIgnoreEnd

/* Standard: Perl-style comments are not allowed. Use "// Comment." or "/* comment" instead.
 (PEAR.Commenting.InlineComment.WrongStyle)
*/
// Good comment style

/*
 * Good comment style
 */

/* Standard: Only multiline comments with /** should be used to comment classes
 (Joomla.Commenting.ClassComment.WrongStyle){HL:1}
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

/* Standard: Comments must have content ;)
 (Joomla.Commenting.ClassComment.Empty){HL:2,4,5}
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

/* Standard: Class Docblocks
 (Joomla.Commenting.ClassComment.MissingTag){HL:2,4,5,6}
 */
/**
 * A utility class.
 *
 * @package     Joomla.Framework
 * @subpackage  XBase
 * @since       1.6
 */
class JClass extends JObject
{
}
/* Standard: Class Property DocBlocks
 (Joomla...){HL:11,13,14}
The class property Docblock consists of the following required and optional elements in the following order.
* Short description (required, followed by a blank line)
* @var (required, followed by the property type)
* @deprecated (optional)
* @since (required)
*/
/**
 * A utility class.
 *
 * @package     Joomla.Framework
 * @subpackage  XBase
 * @since       1.6
 */
class JClass extends JObject
{
	/**
	 * Human readable name
	 *
	 * @var    string
	 * @since  1.6
	 */
	public $name;
}
/* Standard: Class Method DocBlocks
 (Joomla...){HL:11,13,15,17}
The DocBlock for class methods follows the same convention as for PHP functions (see above).
*/
/**
 * A utility class.
 *
 * @package     Joomla.Framework
 * @subpackage  XBase
 * @since       1.6
 */
class JClass extends JObject
{
	/**
	 * Method to get the name of the class.
	 *
	 * @param   string  $case  Optionally return in upper/lower case.
	 *
	 * @return  boolean  True if successfully loaded, false otherwise.
	 *
	 * @since   1.6
	 */
	public function getName($case = null)
	{
		// Body of method.

		return $this->name;
	}
}
