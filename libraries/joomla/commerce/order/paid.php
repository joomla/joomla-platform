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
class JCommerceOrderStatePaid implements JCommerceOrderState
{
	/**
	 * Method to cancel an order.
	 *
	 * @return  JCommerceOrderState
	 *
	 * @since   12.1
	 */
	public function cancelOrder()
	{
		// TODO Logic to cancel an order.
		return new JCommerceOrderStateCanceled;
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
		// TODO Logic to complete an order.
		return new JCommerceOrderStateComplete;
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
		throw new LogicException('The order has already been submitted if it is now paid.');
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
		throw new LogicException('The order has already been paid.');
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
		throw new LogicException('Orders cannot be returned unless they are complete.');
	}

	/**
	 * Method to ship an order.
	 *
	 * @return  JCommerceOrderState
	 *
	 * @since   12.1
	 */
	public function shipOrder()
	{
		// TODO Logic to ship an order.
		return new JCommerceOrderStateShipped;
	}
}
