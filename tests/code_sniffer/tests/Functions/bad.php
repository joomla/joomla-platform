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

/* Standard: Space before opening parenthesis of function call prohibited.
 (Joomla.Functions.FunctionCallSignature.SpaceBeforeOpenBracket)
*/
foo ($bar);
/* Standard: The closing parenthesis of a multi-line function declaration must be on the same line
(Joomla.Functions.FunctionDeclaration.CloseBracketLine){HL:10,11}
*/
/**
 * Comment
 *
 * @param   foo  $bar  Comment.
 * @param   foo  $baz  Comment.
 *
 * @return foo
 */
function foo($bar,
	$baz
)
{
}
/* Standard: Multi-line function declarations must be indented with 1 tab
(Joomla.Functions.FunctionDeclaration.Indent){HL:10}
*/
/**
 * Comment
 *
 * @param   foo  $bar  Comment.
 * @param   foo  $baz  Comment.
 *
 * @return foo
 */
function foo($bar,
$baz)
{
}
