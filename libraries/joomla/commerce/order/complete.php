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
class JCommerceOrderStateComplete implements JCommerceOrderState
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
		throw new LogicException('Completed orders cannot be canceled.');
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
		throw new LogicException('The order is already complete.');
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
		throw new LogicException('Completed orders cannot be submitted.');
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
		throw new LogicException('Completed orders cannot be paid.');
	}

	/**
	 * Method to return an order.
	 *
	 * @return  JCommerceOrderState
	 *
	 * @since   12.1
	 * @throws  InvalidArgumentException
	 */
	public function returnOrder()
	{
		// TODO: Logic to return an order.
		return new JCommerceOrderStateReturned;
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
		throw new LogicException('Completed orders cannot be shipped.');
	}
}
