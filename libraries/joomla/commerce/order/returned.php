<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Commerce
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Joomla Order State Interface.
 *
 * @package     Joomla.Platform
 * @subpackage  Commerce
 * @since       12.1
 */
class JCommerceOrderStateReturned implements JCommerceOrderState
{
	/**
	 * Method to cancel an order.
	 *
	 * @return  JCommerceOrderState
	 *
	 * @since   12.1
	 * @throws  LogicException
	 */
	public function cancelOrder()
	{
		throw new LogicException('Returned orders cannot be further modified.');
	}

	/**
	 * Method to complete an order.
	 *
	 * @return  JCommerceOrderState
	 *
	 * @since   12.1
	 * @throws  LogicException
	 */
	public function completeOrder()
	{
		throw new LogicException('Returned orders cannot be further modified.');
	}

	/**
	 * Method to submit an order.
	 *
	 * @return  JCommerceOrderState
	 *
	 * @since   12.1
	 * @throws  LogicException
	 */
	public function submitOrder()
	{
		throw new LogicException('Returned orders cannot be further modified.');
	}

	/**
	 * Method to pay for an order.
	 *
	 * @return  JCommerceOrderState
	 *
	 * @since   12.1
	 * @throws  LogicException
	 */
	public function payOrder()
	{
		throw new LogicException('Returned orders cannot be further modified.');
	}

	/**
	 * Method to return an order.
	 *
	 * @return  JCommerceOrderState
	 *
	 * @since   12.1
	 * @throws  LogicException
	 */
	public function returnOrder()
	{
		throw new LogicException('Returned orders cannot be further modified.');
	}

	/**
	 * Method to ship an order.
	 *
	 * @return  JCommerceOrderState
	 *
	 * @since   12.1
	 * @throws  LogicException
	 */
	public function shipOrder()
	{
		throw new LogicException('Returned orders cannot be further modified.');
	}
}
