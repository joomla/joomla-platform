<?php
/* Standard: File comments: You must use "/**" style comments for a file comment.
 (Joomla.Commenting.FileComment.WrongStyle)
*/
// Here goes the file comment...

/* Standard: Perl-style comments "#" are not allowed. Use "// Comment." or "/* comment" instead.
 (PEAR.Commenting.InlineComment.WrongStyle)
*/
# Discouraged comment style

/* Standard: Class comments: Only multiline comments with "/**" should be used to comment classes.
 (Joomla.Commenting.ClassComment.WrongStyle){HL:1}
*/
// Foo
class Foo
{
}

/* Standard: Comments must have content ;)
 (Joomla.Commenting.ClassComment.Empty){HL:2}
No comment...
*/
/**
 *
 */
class Foo
{
}

/* Standard: Class Docblocks.
 (Joomla.Commenting.ClassComment.MissingTag){HL:2}
The class Docblock consists of the following required and optional elements in the following order.

* Short description (required, unless the file contains more than two classes or functions), followed by a blank line).
* Long description (optional, followed by a blank line).
* @category (optional and rarely used)
* @package (required)
* @subpackage (optional)
* @author (optional but only permitted in non-Joomla source files, for example, included third-party libraries like Geshi)
* @copyright (optional unless different from the file Docblock)
* @license (optional unless different from the file Docblock)
* @deprecated (optional)
* @link (optional)
* @see (optional)
* @since (required, being the version of the software the class was introduced)
*/
/**
 * Comment
 */
class JClass extends JObject
{
}
/* Standard: @TODO: Class Property DocBlocks.
 (Joomla...){HL:11}
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
	 * Comment
	 */
	public $name;
}
/* Standard: Class Method DocBlocks.
 (Joomla.Commenting.FunctionComment...){HL:11}
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
	 * Comment
	 */
	public function getName($case = null)
	{
		// Body of method.

		return $this->name;
	}
}