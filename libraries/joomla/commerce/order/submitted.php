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
class JCommerceOrderSubmitted implements JCommerceOrderState
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
		return new JCommerceOrderCanceled;
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
		throw new LogicException('Submitted orders cannot jump to complete.');
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
		throw new LogicException('The order has already been submitted.');
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
		// TODO Logic to pay for an order.
		return new JCommerceOrderPaid;
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
		throw new LogicException('Submitted orders cannot be returned.');
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
		throw new LogicException('Submitted orders cannot be immediately shipped.');
	}
}
