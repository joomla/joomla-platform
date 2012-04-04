<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Base
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.base.node');

/**
 * Tree Class.
 *
 * @package     Joomla.Platform
 * @subpackage  Base
 * @since       11.1
 */
class JTree extends JObject
{
	/**
	 * Root node
	 *
	 * @var    object
	 * @since  12.1
	 */
	protected $root = null;

	/**
	 * Current working node
	 *
	 * @var    object
	 * @since  12.1
	 */
	protected $current = null;

	/**
	 * Constructor
	 *
	 * @since   11.1
	 */
	public function __construct()
	{
		$this->root = new JNode('ROOT');
		$this->current = & $this->root;
	}

	/**
	 * Method to add a child
	 *
	 * @param   array    &$node       The node to process
	 * @param   boolean  $setCurrent  True to set as current working node
	 *
	 * @return  mixed
	 *
	 * @since   11.1
	 */
	public function addChild(&$node, $setCurrent = false)
	{
		$this->current->addChild($node);
		if ($setCurrent)
		{
			$this->current = &$node;
		}
	}

	/**
	 * Method to get the parent
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	public function getParent()
	{
		$this->current = &$this->current->getParent();
	}

	/**
	 * Method to get the parent
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	public function reset()
	{
		$this->current = &$this->root;
	}
}
