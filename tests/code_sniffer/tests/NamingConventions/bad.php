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

/* Standard: Public properties must *not* have a prefix.
 (Joomla.NamingConventions.ValidVariableName.PublicUnderscore){HL:9}
*/
/**
 * Foo class
 *
 * @package  Foo
 * @since    foo
 */
class FunctionTest
{
	public $_foo = 1;

	/* Standard: Protected properties must *not* have a prefix.
	 (Joomla.NamingConventions.ValidVariableName.PublicUnderscore)
	*/
	protected $_foo = 1;

	/* Standard: Private properties *must* have a prefix.
	 (Joomla.NamingConventions.ValidVariableName.PrivateNoUnderscore)
	*/
	private $foo = 1;

	/* Standard: Public functions must *not* be prefixed with an underscore.
	 (Joomla.NamingConventions.ValidVariableName.PublicUnderscore){HL:6}
	*/
	/**
	 * Comment
	 *
	 * @return foo
	 */
	public function _foo()
	{
	}

	/* Standard: Protected functions must *not* be prefixed with an underscore.
	 (Joomla.NamingConventions.ValidVariableName.PublicUnderscore){HL:6}
	*/
	/**
	 * Comment
	 *
	 * @return foo
	 */
	protected function _bar()
	{
	}

	/* Standard: Private method *must* be prefixed with an underscore.
	 (Joomla.NamingConventions.ValidFunctionName.PrivateNoUnderscore){HL:6}
	*/
	/**
	 * Comment
	 *
	 * @return foo
	 */
	private function foobar()
	{
	}
	/* ENDDOC */
}

/**
 * Comment
 *
 * @package  Foo
 * @since    foo
 */
class Foo
{
	/* Standard: Only PHP magic methods should be prefixed with a double underscore.
	 (Joomla.NamingConventions.ValidFunctionName.MethodDoubleUnderscore){HL:6}
	*/
	/**
	 * Comment
	 *
	 * @return foo
	 */
	public function __bar()
	{
	}

	/* Standard: Use CamelCase notation. @TODO Not working
	 (Joomla.NamingConventions.ValidFunctionName.NotCamelCaps){HL:6}
	*/
	/**
	 * Comment
	 *
	 * @return foo
	 */
	function thefunctionname()
	{
	}

	/* ENDDOC */

}

/* Standard: Invalid function name.
 (Joomla.NamingConventions.ValidFunctionName.FunctionNameInvalid){HL:6}
Use CamelCase notation. The first word has to be lower case.
*/
/**
 * Comment
 *
 * @return foo
 */
function FOOBAR()
{
}

/* Standard: Use CamelCase notation. @TODO Not working
 (Joomla.NamingConventions.ValidFunctionName.NotCamelCaps){HL:6}
*/
/**
 * Comment
 *
 * @return foo
 */
function thefunctionfame()
{
}
