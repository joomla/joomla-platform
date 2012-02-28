<?php
/**
 * @package     Joomla.UnitTest
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

class JElementMock extends JElement
{
	public $name = 'Mock';
	
	function getName()
	{
		return $this->name;
	}
}