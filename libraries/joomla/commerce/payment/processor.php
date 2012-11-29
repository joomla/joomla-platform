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
 * Joomla Order Payment Processor Interface.
 *
 * @package     Joomla.Platform
 * @subpackage  Commerce
 * @since       12.1
 */
interface JCommercePaymentProcessor
{
	/**
	 * Constructor.
	 *
	 * @param   JCommercePayment  $payment  The payment object.
	 * @param   JCommerceAddress  $billing  The billing address object.
	 * @param   JRegistry         $options  Optional options object.
	 *
	 * @since   12.1
	 */
	public function __construct(JCommercePayment $payment, JCommerceAddress $billing, JRegistry $options = null);

	/**
	 * Method to get a JForm object for the payment gateway.
	 *
	 * @return  JForm
	 *
	 * @since   12.1
	 * @throws  Exception
	 */
	public function getForm();

	/**
	 * Method to process the order with the payment gateway.
	 *
	 * @return  JCommercePaymentResponse
	 *
	 * @since   12.1
	 * @throws  Exception
	 */
	public function process();
}
