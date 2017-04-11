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

/* Standard: Tabs must be used to indent lines; spaces are not allowed.
(Joomla.WhiteSpace.DisallowSpaceIndent.SpaceUsed){HL:3}
 */
if (true)
{
    true;
}
/* Standard: @TODO: Concatenation operator must be surrounded by spaces.
(Joomla.WhiteSpace...)
*/
$i.'test';
/* ENDDOC */
$i ."test";
$i .'test';
$i .$i;
$i .C_TEST;
$i."test";
$i.$i;
$i.C_TEST;
$i. "test";
$i. 'test';
$i. $i;
$i. C_TEST;
/* Standard: @TODO: Operators must have a space before and after.
(Joomla.WhiteSpace...)
*/
$i=0;
$i>0;
$i+=0;
/* ENDDOC */
$i= 0;
$i+= 0;
$i-= 0;
$i== 0;
$i=== 0;
$i!== 0;
$i!= 0;
$i> 0;
$i< 0;
$i>= 0;
$i<= 0;
$i=0;
$i+=0;
$i-=0;
$i==0;
$i===0;
$i!==0;
$i!=0;
$i>0;
$i<0;
$i>=0;
$i<=0;
$i =0;
$i +=0;
$i -=0;
$i ==0;
$i  ==0;
$i !=0;
$i >0;
$i <0;
$i >=0;
$i <=0;
$i  = 0;
$i  += 0;
$i  -= 0;
$i  == 0;
$i  === 0;
$i  !== 0;
$i  != 0;
$i  > 0;
$i  < 0;
$i  >= 0;
$i  <= 0;
$i =  0;
$i +=  0;
$i -=  0;
$i ==  0;
$i ===  0;
$i !==  0;
$i !=  0;
$i >  0;
$i <  0;
$i >=  0;
$i <=  0;
/* Standard: @TODO: Unary operators must not have a space.
(Joomla.WhiteSpace...)
*/
$i --;
-- $i;
$i ++;
++ $i;
/* Standard: @TODO: Casting must have one space.
(Joomla.WhiteSpace...)
*/
(int)$i;
(int)  $i;
/* Standard: No space before semicolon.
(Joomla.WhiteSpace.SemicolonSpacing.Incorrect)
That's a common leftover...
*/
$foo = 'bar' ;
/* Standard: Functions and classes must not contain multiple empty lines in a row.
(Joomla.WhiteSpace.SuperfluousWhitespace){HL:9,10}
That's a common leftover...
*/
/**
 * Comment
 *
 * @return foo
 */
function foo()
{
	$foo;


	$bar;
}
/* Standard: Please end your files with an empty line.
(Joomla.WhiteSpace.SuperfluousWhitespace)
*/