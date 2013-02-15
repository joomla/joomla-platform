<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Event
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * A listener to use for the JEventDispatcher tests.
 *
 * @package     Joomla.UnitTest
 * @subpackage  Event
 * @since       12.3
 */
class BarListener
{
	public function onBeforeSomething(JEvent $e)
	{
		$foo = $e->getArgument('foo');
		$foo[] = 2;
		$e->setArgument('foo', $foo);
	}

	public function onSomething(JEvent $e)
	{
		$e->stopPropagation();
	}

	public function onAfterSomething(JEvent $e)
	{

	}
}
