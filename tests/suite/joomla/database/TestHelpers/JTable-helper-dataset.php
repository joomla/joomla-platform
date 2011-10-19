<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Database
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

class JTableTest_DataSet
{
	public static function getGetSourceTest()
	{
		return array(
			 // array($src, $expected)
			array(
				array('attribute'=>'value'),
				array('attribute'=>'value')
			),
			array(
				(object) array('attribute'=>'value'),
				(object) array('attribute'=>'value')
			),
			array(
				'incorrect',
				null
			),
		);
	}
}

