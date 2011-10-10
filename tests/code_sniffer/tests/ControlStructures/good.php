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

/* Standard: if-else: Wrong brace placement
 (Joomla.ControlStructures.ControlSignature){HL:2,4,7,9,11}
*/
if ($test)
{
	echo 'True';
}
// Comments can go here.
elseif ($test === false)
{
	echo 'Really false';
}
else
{
	echo 'A white lie';
}
/* Standard: if: Wrong spacing
 (Joomla.ControlStructures.ControlSignature){HL:1}
*/
if (true)
{
	true;
}
/* Standard: Multi-line IF statement not indented correctly.
(Joomla.ControlStructures.MultiLineCondition.Alignment){HL:2}
 */
if (true
	|| false
)
{
	true;
}
/* Standard: Closing parenthesis of a multi-line IF statement must be on a new line
(Joomla.ControlStructures.MultiLineCondition.CloseBracketNewLine){HL:3}
 */
if (true
	|| false
)
{
	true;
}
/* Standard: Each line in a multi-line IF statement must begin with a boolean operator
(Joomla.ControlStructures.MultiLineCondition.StartWithBoolean){HL:2}
 */
if (true
	|| false
)
{
	true;
}
/* Standard: do-while: Wrong brace placement / wrong spacing
 (Joomla.ControlStructures.ControlSignature){HL:2,4}
*/
do
{
	$i++;
}
while ($i < 10);
/* Standard: for: Wrong brace placement / wrong spacing
 (Joomla.ControlStructures.ControlSignature){HL:2}
*/
for ($i = 0; $i < $n; $i++)
{
	echo 'Increment = '.$i;
}
/* Standard: foreach: Wrong brace placement / wrong spacing
 (Joomla.ControlStructures.ControlSignature){HL:2}
*/
foreach ($rows as $index => $row)
{
	echo 'Index = '.$id.', Value = '.$row;
}
/* Standard: while: Wrong brace placement / wrong spacing
 (Joomla.ControlStructures.ControlSignature){HL:2}
*/
while (!$done)
{
	$done = true;
}
/* Standard: switch: Wrong brace placement / wrong spacing
 (Joomla.ControlStructures.ControlSignature){HL:2}
When using a switch statement, the case keywords are indented.
The break statement starts on a newline assuming the indent of the code within the case.
*/
switch ($value)
{
	case 'a':
		echo 'A';
		break;

	default:
		echo 'I give up';
	break;
}
/* Standard: The statement "else if" is not allowed - use "elseif"
 (Joomla.ControlStructures.ElseIfDeclaration.NotAllowed){HL:5}
*/
if (true)
{
	$i;
}
elseif (true)
{
	$i;
}
/* Standard: @TODO: References
 (Joomla.ControlStructures...)
When using references, there should be a space before the reference operator and no space between it and the function or variable name.
Note: In PHP 5, reference operators are not required for objects. All objects are handled by reference.
*/
$ref1  = &$this->sql;
/* Standard: @TODO: Arrays
 (Joomla.ControlStructures...)
Assignments (the => operator) in arrays may be aligned with tabs.
When splitting array definitions onto several lines, the last value may also have a trailing comma.
This is valid PHP syntax and helps to keep code diffs minimal.
*/
$options = array(
	'foo'	=> 'foo',
	'spam'	=> 'spam',
);
/* Standard: Inline control structures are discouraged
 (Joomla.ControlStructures.InlineControlStructure.Discouraged){HL:2,4}
*/
if (true)
{
	true;
}
