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
 * Commerce system payment object for the Joomla Platform.
 *
 * @package     Joomla.Platform
 * @subpackage  Commerce
 * @since       12.1
 */
abstract class JCommercePayment
{
	/**
	 * @var    string
	 * @since  12.1
	 */
	public $gatewayToken;

	/**
	 * @var    string
	 * @since  12.1
	 */
	public $token;

	/**
	 * @var    float  The total amount of the transaction.
	 * @since  12.1
	 */
	public $amount;

	/**
	 * @var    integer  The id of the order that was sent to the gateway.
	 * @since  12.1
	 */
	public $orderId;

	/**
	 * @var    integer  The id of the customer whos order was sent to the gateway.
	 * @since  12.1
	 */
	public $customerId;

	/**
	 * Method to determine whether or not the payment method is for testing purposes or not.
	 *
	 * @return  boolean  True if the method is for testing.
	 *
	 * @since   12.1
	 */
	abstract public function isTest();

	/**
	 * Method to determine the validity of the payment method details.
	 *
	 * @return  boolean  True for a valid payment method.
	 *
	 * @since   12.1
	 */
	abstract public function isValid();
}
